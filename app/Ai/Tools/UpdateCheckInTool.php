<?php

namespace App\Ai\Tools;

use App\Ai\Tools\Support\ToolResult;
use App\Models\User;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

/**
 * Adds body measurements and/or a wellbeing note onto the check-in's weight
 * entry (today's most recent body_progress row, created by log_weight). Used by
 * the measurements and mood steps so the whole check-in lives on one entry.
 */
class UpdateCheckInTool implements Tool
{
    /** @var array<string, string> schema arg => body_progress column */
    private const MEASUREMENTS = [
        'waist_cm' => 'waist_circumference_cm',
        'hip_cm' => 'hip_circumference_cm',
        'chest_cm' => 'chest_circumference_cm',
        'arm_cm' => 'arm_circumference_cm',
        'thigh_cm' => 'thigh_circumference_cm',
        'body_fat_percent' => 'body_fat_percentage',
        'muscle_mass_kg' => 'muscle_mass_kg',
    ];

    public function __construct(private readonly User $user) {}

    public function description(): Stringable|string
    {
        return 'Adds body measurements and/or a wellbeing note to the check-in entry the user just weighed in on. Pass any of waist_cm, hip_cm, chest_cm, arm_cm, thigh_cm, body_fat_percent, muscle_mass_kg, and/or note (their mood, energy and how the week felt). Call it after the measurements step and after the mood step. Requires a weight to have been logged first.';
    }

    /**
     * @return array<string, mixed>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'waist_cm' => $schema->number()->description('Waist circumference in cm.'),
            'hip_cm' => $schema->number()->description('Hip circumference in cm.'),
            'chest_cm' => $schema->number()->description('Chest circumference in cm.'),
            'arm_cm' => $schema->number()->description('Arm circumference in cm.'),
            'thigh_cm' => $schema->number()->description('Thigh circumference in cm.'),
            'body_fat_percent' => $schema->number()->description('Body fat percentage.'),
            'muscle_mass_kg' => $schema->number()->description('Muscle mass in kg.'),
            'note' => $schema->string()->description('Their mood, energy and how the week felt, in their words.'),
        ];
    }

    public function handle(Request $request): Stringable|string
    {
        $entry = $this->user->bodyProgress()->whereDate('recorded_at', today())->orderByDesc('recorded_at')->first();

        if (! $entry) {
            return ToolResult::error('no_checkin_entry', 'Log the weight first with log_weight, then add measurements or the note.');
        }

        $changes = [];
        foreach (self::MEASUREMENTS as $arg => $column) {
            if (isset($request[$arg]) && is_numeric($request[$arg])) {
                $changes[$column] = round((float) $request[$arg], 2);
            }
        }

        $note = trim((string) ($request['note'] ?? ''));
        if ($note !== '') {
            $changes['notes'] = $entry->notes ? $entry->notes."\n".$note : $note;
        }

        if ($changes === []) {
            return ToolResult::error('nothing_to_update', 'No measurements or note were provided.');
        }

        $entry->update($changes);

        return ToolResult::info('check_in_saved', [
            'measurements' => count(array_diff_key($changes, ['notes' => true])),
            'has_note' => array_key_exists('notes', $changes),
        ]);
    }
}
