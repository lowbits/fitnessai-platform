<?php

return [
    'meta' => [
        'title' => 'Trainingsplan-Check: kostenloser, ehrlicher Roast deines Plans',
        'description' => 'Füg deinen Trainingsplan ein oder lade ihn hoch und bekomm in Sekunden ein ehrliches, evidenzbasiertes Urteil: Volumen, Frequenz, Progression, Muskelabdeckung und Regeneration.',
        'og_image_alt' => 'Trainingsplan-Check von fytrr',
    ],

    'schema' => [
        'name' => 'Trainingsplan-Check',
    ],

    'reviewed_date' => '2026-09-09',

    'howto' => [
        'name' => 'So prüfst du deinen Trainingsplan',
        'description' => 'Bekomm in drei Schritten ein evidenzbasiertes Urteil über deinen Trainingsplan.',
        'steps' => [
            ['name' => 'Plan hinzufügen', 'text' => 'Text einfügen, txt oder PDF reinziehen oder Fotos hochladen. Auch mehrere Tage auf einmal.'],
            ['name' => 'Score bekommen', 'text' => 'Wir bewerten Muskelabdeckung, Wochenvolumen, Frequenz, Progression und Regeneration gegen die Trainingswissenschaft.'],
            ['name' => 'Urteil lesen', 'text' => 'Ein Coach-Urteil erklärt, was stark ist, was schwach ist und was du zuerst fixen solltest.'],
        ],
    ],

    'faqs' => [
        [
            'question' => 'Ist der Trainingsplan-Check kostenlos?',
            'answer' => 'Ja, komplett kostenlos und ohne Anmeldung. Plan einfügen oder hochladen und sofort einen evidenzbasierten Score bekommen.',
        ],
        [
            'question' => 'Was wird geprüft?',
            'answer' => 'Muskelabdeckung, Wochenvolumen, Trainingsfrequenz, progressive Belastung und Regeneration. Das sind die Faktoren, die entscheiden, ob ein Plan wirklich Muskeln und Kraft aufbaut.',
        ],
        [
            'question' => 'Wie wird der Score berechnet?',
            'answer' => 'Deterministisch, gegen veröffentlichte Trainingswissenschaft: rund 10 bis 20 harte Sätze pro Muskel und Woche, jeden Muskel etwa zweimal pro Woche und progressive Belastung. Derselbe Plan bekommt immer denselben Score, ohne zufällige Meinungen.',
        ],
        [
            'question' => 'Kann ich einen Screenshot oder ein PDF hochladen?',
            'answer' => 'Ja. Text einfügen, txt oder PDF reinziehen oder Fotos hochladen, auch mehrere Fotos eines Mehrtagesplans auf einmal. JPG, PNG und WebP werden unterstützt.',
        ],
        [
            'question' => 'Speichert ihr meinen Plan?',
            'answer' => 'Wir speichern den Plan anonym, um das Tool zu verbessern und zu zeigen, wie Pläne im Vergleich abschneiden. Es gibt keinen Account und er wird niemals geteilt.',
        ],
    ],

    'dimensions' => [
        'coverage' => [
            'label' => 'Muskelabdeckung',
            'evidence' => 'Ausgewogene Pläne trainieren alle großen Muskelgruppen. Beine oder Rücken auszulassen führt zu Dysbalancen und höherem Verletzungsrisiko.',
        ],
        'volume' => [
            'label' => 'Wochenvolumen',
            'evidence' => 'Rund 10 bis 20 harte Sätze pro Muskel und Woche maximieren den Muskelaufbau; deutlich weniger bringt kaum etwas, deutlich mehr nur Ermüdung (Schoenfeld et al., 2017).',
        ],
        'balance' => [
            'label' => 'Muskel-Balance',
            'evidence' => 'Das Volumen sollte über den Körper verteilt sein. Eine Gruppe zu überladen, während eine andere kaum trainiert wird, baut Kraft- und Optik-Dysbalancen auf.',
        ],
        'intensity' => [
            'label' => 'Intensität',
            'evidence' => 'Harte Sätze nahe am Versagen treiben das Wachstum. Leichte, submaximale Arbeit ist ein schwacher Reiz, egal wie viele Sätze du machst.',
        ],
        'progression' => [
            'label' => 'Progressive Belastung',
            'evidence' => 'Ohne progressive Steigerung von Gewicht, Wiederholungen oder RPE stagniert die Anpassung schnell (ACSM, 2009).',
        ],
        'frequency' => [
            'label' => 'Trainingsfrequenz',
            'evidence' => 'Einen Muskel etwa zweimal pro Woche zu trainieren schlägt einmal bei gleichem Wochenvolumen (Schoenfeld et al., 2016).',
        ],
        'recovery' => [
            'label' => 'Regeneration',
            'evidence' => 'Muskeln wachsen in der Erholung; dauerhaftes Training ohne Ruhetag erhöht Verletzungs- und Burnout-Risiko.',
        ],
    ],

    'sources' => [
        [
            'authors' => 'Schoenfeld BJ, Ogborn D, Krieger JW',
            'title' => 'Dose-response relationship between weekly resistance training volume and increases in muscle mass: a systematic review and meta-analysis',
            'publication' => 'Journal of Sports Sciences',
            'year' => '2017',
            'url' => 'https://pubmed.ncbi.nlm.nih.gov/27433992/',
        ],
        [
            'authors' => 'Schoenfeld BJ, Ogborn D, Krieger JW',
            'title' => 'Effects of resistance training frequency on measures of muscle hypertrophy: a systematic review and meta-analysis',
            'publication' => 'Sports Medicine',
            'year' => '2016',
            'url' => 'https://pubmed.ncbi.nlm.nih.gov/27102172/',
        ],
        [
            'authors' => 'American College of Sports Medicine',
            'title' => 'Progression models in resistance training for healthy adults (position stand)',
            'publication' => 'Medicine & Science in Sports & Exercise',
            'year' => '2009',
            'url' => 'https://pubmed.ncbi.nlm.nih.gov/19204579/',
        ],
    ],
];
