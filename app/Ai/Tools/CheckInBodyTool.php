<?php

namespace App\Ai\Tools;

use App\Ai\Tools\Support\ToolResult;
use App\Models\User;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

/**
 * Second check-in step: offers optional body measurements (waist, hip, chest,
 * arm, thigh) as a light "add if you want" card, prefilled with the user's last
 * values. Purely the form; the picked values come back as a message that Mona
 * saves with update_check_in.
 */
class CheckInBodyTool implements Tool
{
    /** @var array<string, string> measurement key => body_progress column */
    private const FIELDS = [
        'waist' => 'waist_circumference_cm',
        'hip' => 'hip_circumference_cm',
        'chest' => 'chest_circumference_cm',
        'arm' => 'arm_circumference_cm',
        'thigh' => 'thigh_circumference_cm',
    ];

    public function __construct(private readonly User $user) {}

    public function description(): Stringable|string
    {
        return 'Second step of the check-in: opens the optional body-measurements card (waist, hip, chest, arm, thigh) after the weight is logged. Call it once you have reflected on their weight. STOP after it — their measurements (or a skip) arrive as a follow-up you then save with update_check_in.';
    }

    /**
     * @return array<string, mixed>
     */
    public function schema(JsonSchema $schema): array
    {
        return [];
    }

    public function handle(Request $request): Stringable|string
    {
        $last = $this->user->bodyProgress()->orderByDesc('recorded_at')->first();

        $fields = [];
        foreach (self::FIELDS as $key => $column) {
            $value = $last?->{$column};
            $fields[$key] = $value !== null ? (float) $value : null;
        }

        return ToolResult::widget('check_in_body', ['last' => $fields]);
    }
}
