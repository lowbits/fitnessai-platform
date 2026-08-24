<?php

namespace App\Ai\Tools;

use App\Ai\Tools\Support\ToolResult;
use App\Models\User;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

/**
 * Fills in the rest of the check-in after log_weight. Measurements and the
 * free-text note attach to today's body_progress row (the weight-chart's source
 * of truth); mood and energy are the structured wellbeing signal and live on the
 * check_ins event row instead, so they can be queried on their own.
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
        return 'Adds body measurements and/or the wellbeing pulse to the check-in entry the user just weighed in on. Pass any of waist_cm, hip_cm, chest_cm, arm_cm, thigh_cm, body_fat_percent, muscle_mass_kg for the measurements step. For the mood step, pass mood and energy as integers 1-5 (mood: 1 rough … 5 great; energy: 1 very low … 5 very high) plus note for anything they wrote. Requires a weight to have been logged first.';
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
            'mood' => $schema->integer()->description('Mood as an integer 1-5 (1 rough, 5 great).'),
            'energy' => $schema->integer()->description('Energy as an integer 1-5 (1 very low, 5 very high).'),
            'note' => $schema->string()->description('Anything the user wrote about their week, in their words.'),
        ];
    }

    public function handle(Request $request): Stringable|string
    {
        $entry = $this->user->bodyProgress()->whereDate('recorded_at', today())->orderByDesc('recorded_at')->first();

        $measurements = [];
        foreach (self::MEASUREMENTS as $arg => $column) {
            if (isset($request[$arg]) && is_numeric($request[$arg])) {
                $measurements[$column] = round((float) $request[$arg], 2);
            }
        }

        $note = trim((string) ($request['note'] ?? ''));

        $pulse = [];
        foreach (['mood', 'energy'] as $signal) {
            if (isset($request[$signal]) && is_numeric($request[$signal])) {
                $pulse[$signal] = max(1, min(5, (int) $request[$signal]));
            }
        }

        if ($measurements === [] && $note === '' && $pulse === []) {
            return ToolResult::error('nothing_to_update', 'No measurements, mood or note were provided.');
        }

        if (($measurements !== [] || $note !== '') && ! $entry) {
            return ToolResult::error('no_checkin_entry', 'Log the weight first with log_weight, then add measurements or the note.');
        }

        $entryChanges = $measurements;
        if ($note !== '') {
            $entryChanges['notes'] = $entry->notes ? $entry->notes."\n".$note : $note;
        }
        if ($entryChanges !== []) {
            $entry->update($entryChanges);
        }

        if ($pulse !== []) {
            $this->user->checkIns()->updateOrCreate(
                ['checked_in_at' => today()],
                [...$pulse, 'body_progress_id' => $entry?->id],
            );
        }

        return ToolResult::info('check_in_saved', [
            'measurements' => count($measurements),
            'has_mood' => array_key_exists('mood', $pulse),
            'has_note' => $note !== '',
        ]);
    }
}
