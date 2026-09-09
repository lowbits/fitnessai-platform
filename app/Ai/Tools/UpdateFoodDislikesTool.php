<?php

namespace App\Ai\Tools;

use App\Ai\Tools\Concerns\NormalizesTerms;
use App\Ai\Tools\Support\ToolResult;
use App\Models\User;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

/**
 * Persists foods the user says they dislike or can't eat onto their profile's
 * food_dislikes, so every future meal plan and swap excludes them. Without this
 * a dislike stated in chat ("ich hasse Tofu") only lived for one message and
 * the plan kept serving it. Returns a plain result the coach verbalizes — no
 * widget.
 */
class UpdateFoodDislikesTool implements Tool
{
    use NormalizesTerms;

    public function __construct(private readonly User $user) {}

    public function description(): Stringable|string
    {
        return 'Saves foods the user dislikes, is allergic to, or cannot eat, so future meal plans and swaps avoid them for good. Call it whenever they express a lasting food aversion or allergy ("ich hasse Tofu", "keine Nüsse", "I can\'t eat shellfish"). Pass add (the foods in their own words) and optionally remove (foods to take back off the list). After it runs, confirm what is now on their no-go list in one short sentence.';
    }

    /**
     * @return array<string, mixed>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'add' => $schema->array()
                ->items($schema->string())
                ->description('Foods to add to the no-go list, in the user\'s own words (e.g. ["tofu", "bulgur"]).'),
            'remove' => $schema->array()
                ->items($schema->string())
                ->description('Foods to remove from the no-go list, if the user no longer wants them excluded.'),
        ];
    }

    public function handle(Request $request): Stringable|string
    {
        $profile = $this->user->profile;

        if ($profile === null) {
            return ToolResult::error('no_profile', 'The user has not completed onboarding yet.');
        }

        $add = $this->normalizeTerms($request['add'] ?? []);
        $remove = $this->normalizeTerms($request['remove'] ?? []);

        if ($add === [] && $remove === []) {
            return ToolResult::error('nothing_to_change', 'Ask the user which food to add or remove.');
        }

        $current = $this->normalizeTerms($profile->food_dislikes ?? []);
        $updated = array_values(array_diff(array_unique([...$current, ...$add]), $remove));

        if ($this->sameTerms($updated, $current)) {
            return ToolResult::data(['updated' => false, 'dislikes' => $current, 'message' => 'Already up to date — nothing changed.']);
        }

        $profile->update(['food_dislikes' => $updated]);

        return ToolResult::data([
            'updated' => true,
            'added' => array_values(array_diff($updated, $current)),
            'removed' => array_values(array_diff($current, $updated)),
            'dislikes' => $updated,
        ]);
    }
}
