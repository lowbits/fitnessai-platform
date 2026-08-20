<?php

namespace App\Ai\Tools;

use App\Ai\Tools\Support\ToolResult;
use App\Models\User;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

/**
 * Opens the weekly check-in card so the user can log their weight and how they
 * feel (mood, energy, a note) in one premium moment. It renders the form; the
 * user's filled-in entry comes back as a normal message that Mona then records
 * with log_weight.
 */
class StartCheckInTool implements Tool
{
    public function __construct(private readonly User $user) {}

    public function description(): Stringable|string
    {
        return 'Starts the weekly check-in by opening the WEIGHT step — a friendly weight dial prefilled with their last value. Use it when they want to check in or do their weekly weigh-in ("Check-in", "ich will mich einchecken", "wiegen", "wie läuft meine Woche"). It renders the step — STOP after calling it and wait; their weight arrives as a follow-up message you then record with log_weight before moving on.';
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
        $startWeight = $this->user->profile?->weight_kg
            ?? $this->user->bodyProgress()->orderBy('recorded_at')->value('weight_kg');
        $lastWeight = $this->user->bodyProgress()->orderByDesc('recorded_at')->value('weight_kg');

        return ToolResult::widget('check_in_weight', [
            'last_weight' => $lastWeight !== null ? (float) $lastWeight : null,
            'start_weight' => $startWeight !== null ? (float) $startWeight : null,
        ]);
    }
}
