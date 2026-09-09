<?php

namespace App\Ai\Tools;

use App\Ai\Tools\Concerns\NormalizesTerms;
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
    use NormalizesTerms;

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

        $add = $this->normalizeTerms($request['add_areas'] ?? [], self::AREAS);
        $remove = $this->normalizeTerms($request['remove_areas'] ?? [], self::AREAS);
        $hasNote = isset($request['note']);

        if ($add === [] && $remove === [] && ! $hasNote) {
            return json_encode(['error' => 'nothing_to_change', 'message' => 'Ask the user which limitation to add, remove or describe.']);
        }

        $current = $this->normalizeTerms($profile->physical_limitations ?? [], self::AREAS);
        $areas = array_values(array_diff(array_unique([...$current, ...$add]), $remove));

        $newNote = null;
        if ($hasNote) {
            $note = mb_substr(trim((string) $request['note']), 0, 2000);
            $newNote = $note === '' ? null : $note;
        }

        $noteUnchanged = ! $hasNote || $newNote === $profile->physical_limitations_note;

        if ($this->sameTerms($areas, $current) && $noteUnchanged) {
            return json_encode(['updated' => false, 'areas' => $current, 'note' => $profile->physical_limitations_note, 'message' => 'Already up to date — nothing changed.']);
        }

        $payload = ['physical_limitations' => $areas];
        if ($hasNote) {
            $payload['physical_limitations_note'] = $newNote;
        }

        $profile->update($payload);

        return json_encode([
            'updated' => true,
            'areas' => $areas,
            'note' => $profile->physical_limitations_note,
        ]);
    }
}
