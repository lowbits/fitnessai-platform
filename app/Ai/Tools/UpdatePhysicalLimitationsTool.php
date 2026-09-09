<?php

namespace App\Ai\Tools;

use App\Models\User;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

/**
 * Persists the injuries / physical limitations a user mentions in chat onto
 * their profile, so training advice and future plans work around them. Affected
 * areas are a fixed set; anything more specific (an injury's history, a
 * surgery date) is captured in the free-text note. Returns a plain result the
 * coach verbalizes — no widget.
 */
class UpdatePhysicalLimitationsTool implements Tool
{
    /** Areas the app models as structured limitations. Anything else goes in the note. */
    private const AREAS = ['back', 'knee', 'shoulder', 'hip', 'wrist', 'neck', 'ankle'];

    public function __construct(private readonly User $user) {}

    public function description(): Stringable|string
    {
        return 'Saves an injury or physical limitation that should shape the user\'s training, so exercises and future plans avoid loading it. Call it when they mention pain, an injury, a surgery or a restriction ("meine Schulter zwickt", "Bandscheibenvorfall", "trigger finger OP"). Put clear body areas in add_areas (only: back, knee, shoulder, hip, wrist, neck, ankle) and capture the specifics or history in note. Use remove_areas once they have recovered. Confirm what is saved in one short sentence.';
    }

    /**
     * @return array<string, mixed>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'add_areas' => $schema->array()
                ->items($schema->string()->enum(self::AREAS))
                ->description('Body areas to flag as limited. Only: '.implode(', ', self::AREAS).'.'),
            'remove_areas' => $schema->array()
                ->items($schema->string()->enum(self::AREAS))
                ->description('Body areas to clear once the user has recovered.'),
            'note' => $schema->string()
                ->description('Specifics or history that don\'t fit an area, e.g. "trigger finger surgery 5 weeks ago — go easy on grip and pressing". Replaces the existing note.'),
        ];
    }

    public function handle(Request $request): Stringable|string
    {
        $profile = $this->user->profile;

        if ($profile === null) {
            return json_encode(['error' => 'no_profile', 'message' => 'The user has not completed onboarding yet.']);
        }

        $add = $this->normalizeAreas($request['add_areas'] ?? []);
        $remove = $this->normalizeAreas($request['remove_areas'] ?? []);
        $hasNote = isset($request['note']);

        if ($add === [] && $remove === [] && ! $hasNote) {
            return json_encode(['error' => 'nothing_to_change', 'message' => 'Ask the user which limitation to add, remove or describe.']);
        }

        $current = $this->normalizeAreas($profile->physical_limitations ?? []);
        $areas = array_values(array_diff(array_unique([...$current, ...$add]), $remove));

        $payload = ['physical_limitations' => $areas];

        if ($hasNote) {
            $note = mb_substr(trim((string) $request['note']), 0, 2000);
            $payload['physical_limitations_note'] = $note === '' ? null : $note;
        }

        $profile->update($payload);

        return json_encode([
            'updated' => true,
            'areas' => $areas,
            'note' => $profile->physical_limitations_note,
        ]);
    }

    /**
     * @param  mixed  $areas
     * @return list<string>
     */
    private function normalizeAreas($areas): array
    {
        return collect(is_array($areas) ? $areas : [])
            ->map(fn ($area) => mb_strtolower(trim((string) $area)))
            ->filter(fn (string $area) => in_array($area, self::AREAS, true))
            ->unique()
            ->values()
            ->all();
    }
}
