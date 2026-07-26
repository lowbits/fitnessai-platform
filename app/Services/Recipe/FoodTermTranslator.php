<?php

namespace App\Services\Recipe;

use App\Ai\Agents\FoodTranslatorAgent;
use App\Models\FoodTranslation;

class FoodTermTranslator
{
    public function toEnglish(string $term): string
    {
        $term = mb_strtolower(trim($term));

        if ($term === '') {
            return $term;
        }

        if (isset(self::BASE[$term])) {
            return self::BASE[$term];
        }

        $learned = FoodTranslation::where('term', $term)->value('translation');
        if ($learned) {
            return $learned;
        }

        try {
            $english = mb_strtolower(trim((string) (new FoodTranslatorAgent)->prompt($term)));
        } catch (\Throwable) {
            return $term;
        }

        $english = $english ?: $term;
        FoodTranslation::firstOrCreate(['term' => $term], ['translation' => $english]);

        return $english;
    }

    /**
     * @param  list<string>  $terms
     * @return list<string>
     */
    public function toEnglishMany(array $terms): array
    {
        return collect($terms)
            ->map(fn (string $term) => $this->toEnglish($term))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    /**
     * Curated German -> English seed. A free in-memory first tier so the common
     * allergens/intolerances never touch the DB or the model; unknown terms in
     * any language fall through to the learned dictionary + AI.
     */
    private const BASE = [
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
        'käse' => 'cheese', 'joghurt' => 'yogurt', 'quark' => 'quark',
        'sahne' => 'cream', 'butter' => 'butter', 'schmand' => 'sour cream',
        'frischkäse' => 'cream cheese', 'mozzarella' => 'mozzarella',
        'parmesan' => 'parmesan', 'feta' => 'feta',
        'hüttenkäse' => 'cottage cheese', 'ricotta' => 'ricotta',
        'skyr' => 'skyr',
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
        'reis' => 'rice', 'nudeln' => 'pasta', 'brot' => 'bread',
        'couscous' => 'couscous', 'quinoa' => 'quinoa', 'bulgur' => 'bulgur',
        'hirse' => 'millet', 'buchweizen' => 'buckwheat',
        'honig' => 'honey', 'zucker' => 'sugar', 'zimt' => 'cinnamon',
        'ingwer' => 'ginger', 'kurkuma' => 'turmeric',
        'tofu' => 'tofu', 'tempeh' => 'tempeh', 'seitan' => 'seitan',
        'kokosmilch' => 'coconut milk', 'erdnussbutter' => 'peanut butter',
        'mandelmus' => 'almond butter', 'tahini' => 'tahini',
        'olivenöl' => 'olive oil', 'kokosöl' => 'coconut oil',
    ];
}
