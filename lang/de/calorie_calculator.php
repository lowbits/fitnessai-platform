<?php

return [
    'meta' => [
        'title' => 'Kalorienrechner: Täglichen Kalorienbedarf berechnen',
        'description' => 'Berechne deinen täglichen Kalorienbedarf in 30 Sekunden — zum Abnehmen, Zunehmen oder Gewicht halten. Kostenloser Kalorienrechner, wissenschaftlich fundiert.',
        'og_image_alt' => 'Ausgewogene Mahlzeit mit Hähnchen, Reis und Gemüse neben einem Smartphone mit Kalorien-App und einer Küchenwaage',
    ],

    'schema' => [
        'name' => 'fytrr Kalorienrechner',
    ],

    'reviewed_date' => '2026-08-30',

    'howto' => [
        'name' => 'Kalorienbedarf berechnen',
        'description' => 'In drei Schritten vom Grundumsatz zum persönlichen Tagesziel.',
        'steps' => [
            [
                'name' => 'Grundumsatz berechnen',
                'text' => 'Berechne deinen Grundumsatz nach der Mifflin-St-Jeor-Formel aus Geschlecht, Alter, Gewicht und Größe.',
            ],
            [
                'name' => 'Mit dem Aktivitätsfaktor multiplizieren',
                'text' => 'Multipliziere den Grundumsatz mit deinem Aktivitätsfaktor (1,2 sitzend bis 1,9 sehr aktiv). Das ergibt deinen Gesamtumsatz.',
            ],
            [
                'name' => 'An dein Ziel anpassen',
                'text' => 'Ziehe etwa 300 bis 500 kcal ab zum Abnehmen oder addiere 200 bis 400 kcal für den Muskelaufbau.',
            ],
        ],
    ],

    'faqs' => [
        [
            'question' => 'Wie viele Kalorien soll ich am Tag zu mir nehmen?',
            'answer' => 'So viele, wie dein Gesamtumsatz vorgibt, angepasst an dein Ziel. Für die meisten Erwachsenen liegt der Wert zwischen 1.800 und 2.800 kcal pro Tag. Nutze den Rechner oben für deinen persönlichen Wert.',
        ],
        [
            'question' => 'Wie genau ist ein Kalorienrechner?',
            'answer' => 'Die Mifflin-St-Jeor-Formel liefert eine gute Schätzung mit etwa 5 bis 10 Prozent Abweichung. Für die Praxis reicht das: Beobachte dein Gewicht über zwei bis drei Wochen und passe die Kalorien bei Bedarf an.',
        ],
        [
            'question' => 'Muss ich jeden Tag Kalorien zählen?',
            'answer' => 'Nein. Am Anfang hilft es, ein Gefühl für Portionsgrößen zu bekommen. Mit fytrr fotografierst du dein Essen und die KI übernimmt das Zählen — das spart den manuellen Aufwand.',
        ],
        [
            'question' => 'Was ist der Unterschied zwischen Grundumsatz und Gesamtumsatz?',
            'answer' => 'Der Grundumsatz (BMR) ist die Energie, die dein Körper in völliger Ruhe verbraucht. Der Gesamtumsatz (TDEE) ist der Grundumsatz plus alle Bewegung im Alltag und beim Sport. Zum Planen deiner Ernährung zählt der Gesamtumsatz.',
        ],
    ],
];
