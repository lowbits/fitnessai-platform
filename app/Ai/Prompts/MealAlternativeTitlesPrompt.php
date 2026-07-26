<?php

namespace App\Ai\Prompts;

use App\Models\Meal;
use App\Models\User;
use App\Models\UserProfile;
use Stringable;

/**
 * System prompt for the meal-alternatives endpoint. Builds the instructions
 * the AI uses to invent fresh meal-title hints when our recipe DB doesn't
 * have enough surfaced candidates for a swap.
 */
class MealAlternativeTitlesPrompt implements Stringable
{
    /**
     * @param  list<string>  $avoidNames  Recipe titles already shown to the user (don't repeat)
     */
    public function __construct(
        private readonly Meal $meal,
        private readonly User $user,
        private readonly UserProfile $profile,
        private readonly int $count,
        private readonly array $avoidNames = [],
    ) {}

    public function __toString(): string
    {
        $meal = $this->meal;
        $count = $this->count;
        $language = $this->user->locale === 'de' ? 'German language' : 'English language';
        $bodyGoal = $this->profile->body_goal->value;
        $dietary = $this->profile->getDietaryInfo();
        $avoid = empty($this->avoidNames)
            ? ''
            : "\n\nDO NOT suggest any of these titles (already shown to the user): ".implode(', ', $this->avoidNames);

        return <<<PROMPT
You are a world-class nutritionist specializing in personalized meal alternatives.

**User Profile:**
- Body Goal: {$bodyGoal}
- Diet Type: {$dietary}

**Original Meal Context:**
- Meal Type: {$meal->type}
- Current Meal: {$meal->name}
- Target Calories: {$meal->calories} kcal
- Target Protein: {$meal->protein_g}g
- Target Carbs: {$meal->carbs_g}g
- Target Fat: {$meal->fat_g}g

**Your Mission:**
Generate {$count} appealing meal TITLES ONLY as alternatives for this {$meal->type}.

**Requirements:**
1. Similar macros (±15%) to the original
2. STRICTLY follow {$dietary} diet principles
3. Match the meal type ({$meal->type})
4. Diverse: different proteins, cooking methods, cuisines
5. Use {$language} for all titles
6. Make titles appetizing (e.g., "Grilled Salmon with Lemon Herb Quinoa"){$avoid}

Generate exactly {$count} creative meal titles that match the nutritional profile.
PROMPT;
    }
}
