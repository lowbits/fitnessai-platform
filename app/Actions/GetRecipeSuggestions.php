<?php

namespace App\Actions;

use App\Ai\Agents\FoodTranslatorAgent;
use App\Enums\Allergen;
use App\Enums\DietaryPreference;
use App\Enums\PrimaryProtein;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Meilisearch\Client;

class GetRecipeSuggestions
{
    public function __construct(private readonly Client $client) {}

    public function execute(
        ?string $dietaryPreference = null,
        array $dislikes = [],
        ?string $cookingTime = null,
        ?string $mealType = null,
        int $limit = 8,
        string $locale = 'en',
    ): Collection {
        $cacheKey = 'recipe_suggestions:v1:'.md5(json_encode([
            'dietary_preference' => $dietaryPreference,
            'dislikes' => $dislikes,
            'cooking_time' => $cookingTime,
            'meal_type' => $mealType,
            'limit' => $limit,
            'locale' => $locale,
        ]));

        if (! empty($dislikes)) {
            Log::channel('single')->info('[RecipeSuggestions] Dislikes requested', [
                'dislikes' => $dislikes,
                'dietary_preference' => $dietaryPreference,
                'locale' => $locale,
            ]);
        }

        return Cache::remember($cacheKey, now()->addHours(24), function () use (
            $dietaryPreference, $dislikes, $cookingTime, $mealType, $limit, $locale
        ) {
            $filters = $this->buildFilters($dietaryPreference, $dislikes, $cookingTime, $mealType);

            $results = $this->client->index('recipes')->search('', [
                'filter' => implode(' AND ', $filters),
                'limit' => $limit * 2,
            ]);

            return collect($results->getHits())
                ->unique('id')
                ->shuffle()
                ->take($limit)
                ->map(fn (array $hit) => $this->formatHit($hit, $locale))
                ->values();
        });
    }

    private function buildFilters(
        ?string $dietaryPreference,
        array $dislikes,
        ?string $cookingTime,
        ?string $mealType,
    ): array {
        $filters = [];

        if ($mealType) {
            $filters[] = "meal_types = '".$this->sanitize($mealType)."'";
        }

        if ($cookingTime) {
            $maxMinutes = match ($cookingTime) {
                'quick' => 15,
                'normal' => 30,
                default => null,
            };

            if ($maxMinutes) {
                $filters[] = "total_time_minutes <= {$maxMinutes}";
            }
        }

        $proteinEnumValues = array_column(PrimaryProtein::cases(), 'value');
        $allergenEnumValues = array_column(Allergen::cases(), 'value');

        $dietary = DietaryPreference::tryFrom($dietaryPreference ?? '');
        if ($dietary && $dietary !== DietaryPreference::OMNIVORE) {
            $filters[] = 'tags = '.json_encode($dietary->value);
        }

        foreach ($dislikes as $dislike) {
            $dislike = strtolower(trim($dislike));
            if (! $dislike) {
                continue;
            }

            $english = $this->toEnglish($dislike);
            foreach ($this->synonyms($english) as $term) {
                $sanitized = $this->sanitize($term);
                $quoted = json_encode($sanitized);
                if (in_array($sanitized, $proteinEnumValues, true)) {
                    $filters[] = "primary_protein != {$quoted}";
                }
                if (in_array($sanitized, $allergenEnumValues, true)) {
                    $filters[] = "allergens != {$quoted}";
                }
                $filters[] = "ingredient_names != {$quoted}";
            }
        }

        return $filters;
    }

    /**
     * Expand a dislike to its synonyms (same thing, different words).
     *
     * @return list<string>
     */
    private function synonyms(string $dislike): array
    {
        $map = [
            // Dairy
            'lactose' => ['lactose', 'dairy', 'milk'],
            'dairy' => ['dairy', 'lactose', 'milk'],
            'milk' => ['milk', 'dairy', 'lactose'],

            // Eggs
            'egg' => ['egg', 'eggs'],
            'eggs' => ['eggs', 'egg'],

            // Nuts
            'peanut' => ['peanut', 'peanuts'],
            'peanuts' => ['peanuts', 'peanut'],

            // Soy
            'soy' => ['soy', 'soya', 'tofu'],
            'soya' => ['soya', 'soy', 'tofu'],
            'tofu' => ['tofu', 'soy', 'soya'],

            // Seafood
            'shrimp' => ['shrimp', 'prawns'],
            'prawns' => ['prawns', 'shrimp'],
            'fish' => ['fish', 'salmon', 'cod', 'tuna'],
            'salmon' => ['salmon', 'fish'],
            'cod' => ['cod', 'fish'],
            'tuna' => ['tuna', 'fish'],

            // Meat
            'pork' => ['pork', 'schwein'],
            'schwein' => ['schwein', 'pork'],
            'beef' => ['beef', 'rind'],
            'rind' => ['rind', 'beef'],
            'chicken' => ['chicken', 'hähnchen', 'huhn'],

            // Gluten
            'wheat' => ['wheat', 'gluten'],
            'gluten' => ['gluten', 'wheat'],
        ];

        return $map[$dislike] ?? [$dislike];
    }

    /**
     * Translate a food term to English. Cached forever per term.
     * Uses AI for unknown terms, handles any language.
     */
    private function toEnglish(string $term): string
    {
        // Fast lookup from static map
        if (isset(self::FOOD_TRANSLATIONS[$term])) {
            return self::FOOD_TRANSLATIONS[$term];
        }

        // Already English
        if (preg_match('/^[a-z ]+$/', $term)) {
            return $term;
        }

        // Check if AI already translated this before
        $cached = Cache::get('food_translate:'.md5($term));
        if ($cached) {
            return $cached;
        }

        // Unknown non-English term — translate via AI (fast, cheapest model)
        try {
            $translated = strtolower(trim((string) (new FoodTranslatorAgent)->prompt($term)));
            Cache::forever('food_translate:'.md5($term), $translated);

            return $translated;
        } catch (\Throwable) {
            return $term;
        }
    }

    /**
     * German → English food translation map.
     * Covers the ~100 most common allergens, intolerances, and food dislikes.
     */
    private const FOOD_TRANSLATIONS = [
        // Allergens & intolerances
        'laktose' => 'lactose', 'milch' => 'milk', 'milcheiweiß' => 'dairy',
        'kasein' => 'casein', 'molke' => 'whey',
        'gluten' => 'gluten', 'weizen' => 'wheat', 'dinkel' => 'spelt',
        'roggen' => 'rye', 'gerste' => 'barley', 'hafer' => 'oats',
        'ei' => 'egg', 'eier' => 'eggs', 'hühnerei' => 'egg',
        'soja' => 'soy', 'sojabohne' => 'soybean',
        'erdnuss' => 'peanut', 'erdnüsse' => 'peanuts',
        'nüsse' => 'nuts', 'schalenfrüchte' => 'tree nuts',
        'haselnuss' => 'hazelnut', 'haselnüsse' => 'hazelnuts',
        'walnuss' => 'walnut', 'walnüsse' => 'walnuts',
        'mandel' => 'almond', 'mandeln' => 'almonds',
        'cashew' => 'cashew', 'cashewnuss' => 'cashew',
        'pistazie' => 'pistachio', 'pistazien' => 'pistachios',
        'paranuss' => 'brazil nut', 'macadamia' => 'macadamia',
        'pekannuss' => 'pecan',
        'sesam' => 'sesame', 'sellerie' => 'celery', 'senf' => 'mustard',
        'lupine' => 'lupin', 'weichtiere' => 'mollusks',
        'sulfite' => 'sulfites', 'schwefeldioxid' => 'sulfites',
        'fructose' => 'fructose', 'fruktose' => 'fructose',
        'histamin' => 'histamine', 'sorbit' => 'sorbitol',

        // Meat & fish
        'schwein' => 'pork', 'schweinefleisch' => 'pork',
        'rind' => 'beef', 'rindfleisch' => 'beef',
        'hähnchen' => 'chicken', 'huhn' => 'chicken', 'hühnchen' => 'chicken',
        'pute' => 'turkey', 'truthahn' => 'turkey',
        'lamm' => 'lamb', 'lammfleisch' => 'lamb',
        'wild' => 'game', 'wildfleisch' => 'game',
        'ente' => 'duck', 'gans' => 'goose',
        'wurst' => 'sausage', 'schinken' => 'ham', 'speck' => 'bacon',
        'fleisch' => 'meat',
        'fisch' => 'fish', 'lachs' => 'salmon', 'thunfisch' => 'tuna',
        'kabeljau' => 'cod', 'forelle' => 'trout', 'hering' => 'herring',
        'makrele' => 'mackerel', 'sardine' => 'sardine', 'sardinen' => 'sardines',
        'garnelen' => 'shrimp', 'krabben' => 'crab', 'krebstiere' => 'shellfish',
        'muscheln' => 'mussels', 'tintenfisch' => 'squid',
        'meeresfrüchte' => 'seafood', 'krustentiere' => 'crustaceans',
        'anchovis' => 'anchovies',

        // Dairy
        'käse' => 'cheese', 'joghurt' => 'yogurt', 'quark' => 'quark',
        'sahne' => 'cream', 'butter' => 'butter', 'schmand' => 'sour cream',
        'frischkäse' => 'cream cheese', 'mozzarella' => 'mozzarella',
        'parmesan' => 'parmesan', 'feta' => 'feta',
        'hüttenkäse' => 'cottage cheese', 'ricotta' => 'ricotta',
        'skyr' => 'skyr',

        // Fruits
        'apfel' => 'apple', 'banane' => 'banana', 'erdbeere' => 'strawberry',
        'erdbeeren' => 'strawberries', 'himbeere' => 'raspberry',
        'heidelbeere' => 'blueberry', 'heidelbeeren' => 'blueberries',
        'kirsche' => 'cherry', 'kirschen' => 'cherries',
        'zitrone' => 'lemon', 'orange' => 'orange', 'birne' => 'pear',
        'pfirsich' => 'peach', 'pflaume' => 'plum', 'traube' => 'grape',
        'trauben' => 'grapes', 'ananas' => 'pineapple', 'mango' => 'mango',
        'kiwi' => 'kiwi', 'wassermelone' => 'watermelon',
        'grapefruit' => 'grapefruit', 'kokosnuss' => 'coconut',
        'kokos' => 'coconut', 'dattel' => 'date', 'datteln' => 'dates',
        'feige' => 'fig', 'feigen' => 'figs', 'avocado' => 'avocado',

        // Vegetables
        'tomate' => 'tomato', 'tomaten' => 'tomatoes',
        'kartoffel' => 'potato', 'kartoffeln' => 'potatoes',
        'süßkartoffel' => 'sweet potato',
        'zwiebel' => 'onion', 'zwiebeln' => 'onions',
        'knoblauch' => 'garlic', 'lauch' => 'leek',
        'pilze' => 'mushrooms', 'pilz' => 'mushroom', 'champignons' => 'mushrooms',
        'paprika' => 'bell pepper', 'chili' => 'chili',
        'gurke' => 'cucumber', 'spinat' => 'spinach',
        'brokkoli' => 'broccoli', 'blumenkohl' => 'cauliflower',
        'kohl' => 'cabbage', 'rotkohl' => 'red cabbage',
        'aubergine' => 'eggplant', 'zucchini' => 'zucchini',
        'kürbis' => 'pumpkin', 'erbsen' => 'peas',
        'bohnen' => 'beans', 'linsen' => 'lentils',
        'kichererbsen' => 'chickpeas', 'mais' => 'corn',
        'rote bete' => 'beetroot', 'spargel' => 'asparagus',
        'grüne bohnen' => 'green beans', 'rosenkohl' => 'brussels sprouts',

        // Grains & carbs
        'reis' => 'rice', 'nudeln' => 'pasta', 'brot' => 'bread',
        'couscous' => 'couscous', 'quinoa' => 'quinoa', 'bulgur' => 'bulgur',
        'hirse' => 'millet', 'buchweizen' => 'buckwheat',

        // Other
        'honig' => 'honey', 'zucker' => 'sugar', 'zimt' => 'cinnamon',
        'ingwer' => 'ginger', 'kurkuma' => 'turmeric',
        'tofu' => 'tofu', 'tempeh' => 'tempeh', 'seitan' => 'seitan',
        'kokosmilch' => 'coconut milk', 'erdnussbutter' => 'peanut butter',
        'mandelmus' => 'almond butter', 'tahini' => 'tahini',
        'olivenöl' => 'olive oil', 'kokosöl' => 'coconut oil',
    ];

    private function sanitize(string $value): string
    {
        return preg_replace('/[^a-zA-Z0-9_ -]/', '', $value);
    }

    private function formatHit(array $hit, string $locale): array
    {
        $baseUrl = config('services.r2.public_url');
        $name = $hit['translations'][$locale] ?? $hit['name'];

        return [
            'id' => $hit['id'],
            'name' => $name,
            'name_en' => $hit['name'],
            'image' => $hit['image_full'] ? "{$baseUrl}/{$hit['image_full']}" : null,
            'image_full' => $hit['image_full'] ?? null,
            'image_isolated' => $hit['image_isolated'] ?? null,
            'calories' => $hit['calories'],
            'protein_g' => (int) ($hit['protein_g'] ?? 0),
            'cooking_time_minutes' => $hit['total_time_minutes'] ?? 0,
            'meal_types' => $hit['meal_types'] ?? [],
        ];
    }
}
