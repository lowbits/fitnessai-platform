<?php

namespace App\Ai\Tools;

use App\Ai\Tools\Support\ToolResult;
use App\Models\User;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

/**
 * Final check-in step: the wellbeing pulse — mood, energy and an optional note.
 * The user's answer comes back as a message Mona saves onto the check-in entry
 * with update_check_in, then closes the check-in warmly.
 */
class CheckInMoodTool implements Tool
{
    public function __construct(private readonly User $user) {}

    public function description(): Stringable|string
    {
        return 'Final step of the check-in: opens the mood & energy card (how their week felt) after weight and any measurements. Call it to ask how they are doing. STOP after it — their mood, energy and note arrive as a follow-up you save with update_check_in (as the note), then close the check-in with warm encouragement.';
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
        return ToolResult::widget('check_in_mood', []);
    }
}
