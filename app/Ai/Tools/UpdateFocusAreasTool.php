<?php

namespace App\Ai\Tools;

use App\Models\User;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

/**
 * Persists the muscle groups the user wants their training to emphasize, so
 * generated workouts prioritize them on top of the normal split. Saving it
 * shapes future workouts — applying it to the current plan is a separate,
 * user-confirmed regeneration. Returns a plain result the coach verbalizes.
 */
class UpdateFocusAreasTool implements Tool
{
    /** Coarse, user-facing muscle groups — kept simple on purpose. */
    private const AREAS = ['chest', 'back', 'shoulders', 'arms', 'legs', 'glutes', 'core'];

    public function __construct(private readonly User $user) {}

    public function description(): Stringable|string
    {
        return 'Saves the muscle groups the user wants to emphasize in training ("ich will mehr Arme", "focus on glutes"), so future workouts prioritize them. Pass add and/or remove using only: '.implode(', ', self::AREAS).'. Confirm the focus in one short sentence. Saving shapes upcoming workouts — offer to apply it to their current plan only via the confirmed plan update, and never claim you rewrote the existing plan yourself.';
    }

    /**
     * @return array<string, mixed>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'add' => $schema->array()
                ->items($schema->string()->enum(self::AREAS))
                ->description('Muscle groups to emphasize. Only: '.implode(', ', self::AREAS).'.'),
            'remove' => $schema->array()
                ->items($schema->string()->enum(self::AREAS))
                ->description('Muscle groups to stop emphasizing.'),
        ];
    }

    public function handle(Request $request): Stringable|string
    {
        $profile = $this->user->profile;

        if ($profile === null) {
            return json_encode(['error' => 'no_profile', 'message' => 'The user has not completed onboarding yet.']);
        }

        $add = $this->normalize($request['add'] ?? []);
        $remove = $this->normalize($request['remove'] ?? []);

        if ($add === [] && $remove === []) {
            return json_encode(['error' => 'nothing_to_change', 'message' => 'Ask the user which muscle group to focus on.']);
        }

        $current = $this->normalize($profile->focus_areas ?? []);
        $focusAreas = array_values(array_diff(array_unique([...$current, ...$add]), $remove));

        $profile->update(['focus_areas' => $focusAreas]);

        return json_encode(['updated' => true, 'focus_areas' => $focusAreas]);
    }

    /**
     * @param  mixed  $areas
     * @return list<string>
     */
    private function normalize($areas): array
    {
        return collect(is_array($areas) ? $areas : [])
            ->map(fn ($area) => mb_strtolower(trim((string) $area)))
            ->filter(fn (string $area) => in_array($area, self::AREAS, true))
            ->unique()
            ->values()
            ->all();
    }
}
