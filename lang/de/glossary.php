<?php

return [
    'meta' => [
        'title' => 'Fitness-Glossar: Trainings- und Ernährungsbegriffe einfach erklärt',
        'description' => 'RPE, Tempo, TDEE, progressive Überlastung und mehr. Alle Begriffe aus deinem Trainings- und Ernährungsplan verständlich erklärt, mit Beispielen.',
        'og_image_alt' => 'fytrr Fitness-Glossar',
    ],

    'hero' => [
        'eyebrow' => 'Fitness-Glossar',
        'h1' => 'Trainingsbegriffe, einfach erklärt',
        'subtitle' => 'RPE, Tempo, TDEE, Volumen. Alle Begriffe, die in deinem Plan auftauchen, in einem Satz erklärt, mit Beispiel.',
    ],

    'ui' => [
        'search_placeholder' => 'Begriff suchen, z. B. RPE oder Defizit',
        'no_results' => 'Kein Begriff gefunden. Versuch es mit einem anderen Wort.',
        'all' => 'Alle',
        'example' => 'Beispiel',
        'related_heading' => 'Passende kostenlose Tools',
    ],

    'categories' => [
        'training' => 'Training & Programmierung',
        'muscles' => 'Muskelgruppen',
        'nutrition' => 'Ernährung',
        'tracking' => 'Tracking & Fortschritt',
    ],

    'terms' => [
        'rpe' => [
            'term' => 'RPE',
            'expansion' => 'Rate of Perceived Exertion',
            'category' => 'training',
            'definition' => 'Wie anstrengend ein Satz war, auf einer Skala von 1 bis 10, gemessen daran wie viele Wiederholungen du noch geschafft hättest.',
            'example' => 'RPE 8 heißt: ungefähr 2 Wiederholungen wären noch drin gewesen.',
        ],
        'rir' => [
            'term' => 'RIR',
            'expansion' => 'Reps in Reserve',
            'category' => 'training',
            'definition' => 'Die Anzahl der Wiederholungen, die du am Satzende noch in Reserve hast. Das Gegenstück zu RPE: RIR 2 entspricht RPE 8.',
            'example' => '2 RIR bedeutet, du hättest noch 2 saubere Wiederholungen geschafft.',
        ],
        'tempo' => [
            'term' => 'Tempo',
            'expansion' => 'z. B. 3-0-1-0',
            'category' => 'training',
            'definition' => 'Vier Zahlen für die Sekunden einer Wiederholung: Ablassen (exzentrisch), Pause unten, Hochdrücken (konzentrisch), Pause oben.',
            'example' => '3-0-1-0 heißt: 3 Sekunden ablassen, keine Pause, 1 Sekunde hoch, keine Pause.',
        ],
        'time-under-tension' => [
            'term' => 'Time under Tension',
            'expansion' => 'Zeit unter Spannung',
            'category' => 'training',
            'definition' => 'Wie lange ein Muskel während eines Satzes unter Last steht. Ein langsames Ablassen erhöht sie und kann den Wachstumsreiz verstärken.',
            'example' => 'Ein langsames 3-Sekunden-Ablassen erhöht die Time under Tension deutlich.',
        ],
        'one-rm' => [
            'term' => '1RM',
            'expansion' => 'One-Rep Max',
            'category' => 'training',
            'definition' => 'Das maximale Gewicht, das du bei einer Übung genau einmal sauber bewegen kannst. Trainingsgewichte werden oft als Prozent davon angegeben.',
            'example' => '80 % von 1RM bei einem 1RM von 100 kg sind 80 kg.',
        ],
        'progressive-overload' => [
            'term' => 'Progressive Überlastung',
            'expansion' => 'Progressive Overload',
            'category' => 'training',
            'definition' => 'Das Grundprinzip für Fortschritt: den Trainingsreiz über die Zeit steigern, durch mehr Gewicht, mehr Wiederholungen oder mehr Sätze.',
            'example' => 'Schaffst du alle Sätze am oberen Ende des Wiederholungsbereichs, erhöhe nächste Woche das Gewicht.',
        ],
        'volume' => [
            'term' => 'Trainingsvolumen',
            'expansion' => 'harte Sätze pro Woche',
            'category' => 'training',
            'definition' => 'Die Menge an harten Arbeitssätzen, die ein Muskel pro Woche bekommt. Der wichtigste Hebel für Muskelaufbau.',
            'example' => 'Für Muskelaufbau gelten rund 10 bis 20 harte Sätze pro Muskel und Woche als produktiv.',
        ],
        'frequency' => [
            'term' => 'Trainingsfrequenz',
            'expansion' => '',
            'category' => 'training',
            'definition' => 'Wie oft du einen Muskel pro Woche trainierst. Bei gleichem Volumen ist zweimal pro Woche meist besser als einmal.',
            'example' => 'Brust montags und donnerstags trainieren heißt: Frequenz 2 für die Brust.',
        ],
        'compound-isolation' => [
            'term' => 'Grund- vs. Isolationsübung',
            'expansion' => 'Compound vs. Isolation',
            'category' => 'training',
            'definition' => 'Grundübungen bewegen mehrere Gelenke und Muskeln zugleich (Kniebeuge, Bankdrücken). Isolationsübungen treffen gezielt einen Muskel (Bizeps-Curl).',
            'example' => 'Kniebeuge ist eine Grundübung, die Beinstrecker-Maschine eine Isolationsübung.',
        ],
        'set-rep' => [
            'term' => 'Satz & Wiederholung',
            'expansion' => 'Set & Rep',
            'category' => 'training',
            'definition' => 'Eine Wiederholung ist eine komplette Bewegung, ein Satz ist eine Gruppe Wiederholungen am Stück, gefolgt von einer Pause.',
            'example' => '3 Sätze à 10 Wiederholungen sind dreimal 10 Wiederholungen mit Pause dazwischen.',
        ],
        'superset' => [
            'term' => 'Supersatz',
            'expansion' => 'Superset',
            'category' => 'training',
            'definition' => 'Zwei Übungen direkt hintereinander ohne Pause dazwischen, oft für Gegenspieler-Muskeln, um Zeit zu sparen.',
            'example' => 'Bizeps-Curl direkt gefolgt von Trizeps-Drücken ist ein Supersatz.',
        ],
        'drop-set' => [
            'term' => 'Dropsatz',
            'expansion' => 'Drop Set',
            'category' => 'training',
            'definition' => 'Nach dem letzten Satz das Gewicht sofort reduzieren und ohne Pause weitermachen, für einen zusätzlichen Reiz bis zur Erschöpfung.',
            'example' => 'Nach 10 schweren Wiederholungen 30 % abziehen und direkt bis zum Versagen weiter.',
        ],
        'training-to-failure' => [
            'term' => 'Muskelversagen',
            'expansion' => 'Training bis nahe ans Versagen',
            'category' => 'training',
            'definition' => 'Ein Satz nahe am Punkt, an dem keine saubere Wiederholung mehr möglich ist. Nahe ans Versagen (nicht immer bis) ist ein starker Wachstumsreiz.',
            'example' => 'Bei RPE 9 bis 10 trainierst du nahe am oder bis zum Muskelversagen.',
        ],
        'hypertrophy' => [
            'term' => 'Hypertrophie',
            'expansion' => 'Muskelaufbau',
            'category' => 'training',
            'definition' => 'Das Wachstum der Muskelfasern durch Training. Das Ziel, wenn du größere, kräftigere Muskeln aufbauen willst.',
            'example' => 'Hypertrophie-Training nutzt meist 8 bis 12 Wiederholungen und moderate Pausen.',
        ],
        'hiit' => [
            'term' => 'HIIT',
            'expansion' => 'High-Intensity Interval Training',
            'category' => 'training',
            'definition' => 'Intervalltraining mit kurzen, sehr intensiven Belastungen im Wechsel mit kurzen Erholungsphasen. Effizient für Kalorienverbrauch und Ausdauer.',
            'example' => '30 Sekunden Sprint, 30 Sekunden Gehen, mehrfach wiederholt.',
        ],
        'split' => [
            'term' => 'Trainingssplit',
            'expansion' => 'z. B. Push/Pull/Legs, Upper/Lower, Full Body',
            'category' => 'training',
            'definition' => 'Wie du deine Muskelgruppen auf die Trainingstage aufteilst. Die richtige Wahl hängt vor allem davon ab, wie oft du pro Woche trainierst.',
            'example' => 'Bei 3 Tagen trifft Full Body jeden Muskel öfter als Push/Pull/Legs.',
        ],
        'deload' => [
            'term' => 'Deload',
            'expansion' => 'Entlastungswoche',
            'category' => 'training',
            'definition' => 'Eine bewusst leichtere Woche mit weniger Volumen oder Intensität, damit sich Körper und Nervensystem erholen und du danach stärker weitermachst.',
            'example' => 'Nach mehreren harten Wochen kann eine Deload-Woche mit halbem Volumen sinnvoll sein.',
        ],
        'mobility' => [
            'term' => 'Mobility',
            'expansion' => 'Beweglichkeit',
            'category' => 'training',
            'definition' => 'Die aktive Beweglichkeit eines Gelenks über seinen vollen Bewegungsradius. Verbessert Technik und beugt Verletzungen vor.',
            'example' => 'Hüft-Mobility-Übungen vor dem Kniebeugen verbessern die Tiefe.',
        ],

        'lats' => [
            'term' => 'Latissimus',
            'expansion' => 'Lats',
            'category' => 'muscles',
            'definition' => 'Der große, breite Rückenmuskel an den Flanken. Sorgt für die V-Form und zieht bei Klimmzügen und Rudern.',
            'example' => 'Klimmzüge und Rudern treffen vor allem den Latissimus.',
        ],
        'rear-delts' => [
            'term' => 'Hintere Schulter',
            'expansion' => 'Rear Delts',
            'category' => 'muscles',
            'definition' => 'Der hintere Anteil des Schultermuskels. Oft unterentwickelt, wichtig für gesunde Schultern und aufrechte Haltung.',
            'example' => 'Reverse Flys treffen gezielt die hintere Schulter.',
        ],
        'posterior-chain' => [
            'term' => 'Hintere Kette',
            'expansion' => 'Posterior Chain',
            'category' => 'muscles',
            'definition' => 'Alle Muskeln entlang der Körperrückseite: Waden, hintere Oberschenkel, Gesäß und Rückenstrecker. Zentral für Kraft und Haltung.',
            'example' => 'Kreuzheben trainiert die gesamte hintere Kette.',
        ],
        'core' => [
            'term' => 'Rumpf',
            'expansion' => 'Core',
            'category' => 'muscles',
            'definition' => 'Die Muskeln rund um deine Körpermitte: Bauch, seitliche Bauchmuskeln und tiefliegende Stabilisatoren. Stabilisiert dich bei fast jeder Übung.',
            'example' => 'Planks und schwere Grundübungen fordern den Rumpf stark.',
        ],

        'tdee' => [
            'term' => 'TDEE',
            'expansion' => 'Total Daily Energy Expenditure',
            'category' => 'nutrition',
            'definition' => 'Dein gesamter täglicher Kalorienverbrauch, also Grundumsatz plus Bewegung und Alltag. Die Basis für Zu- oder Abnehmen.',
            'example' => 'Isst du unter deinem TDEE, nimmst du ab, darüber nimmst du zu.',
        ],
        'bmr' => [
            'term' => 'BMR',
            'expansion' => 'Grundumsatz',
            'category' => 'nutrition',
            'definition' => 'Die Kalorien, die dein Körper in völliger Ruhe allein zum Funktionieren verbraucht. Wird meist mit der Mifflin-St-Jeor-Formel geschätzt.',
            'example' => 'Der BMR macht den größten Teil deines täglichen Verbrauchs aus.',
        ],
        'mifflin-st-jeor' => [
            'term' => 'Mifflin-St-Jeor-Formel',
            'expansion' => '',
            'category' => 'nutrition',
            'definition' => 'Die gängige Formel zur Schätzung des Grundumsatzes aus Gewicht, Größe, Alter und Geschlecht. Genauer als ältere Formeln.',
            'example' => 'Unser Kalorienrechner nutzt Mifflin-St-Jeor für den BMR.',
        ],
        'activity-factor' => [
            'term' => 'Aktivitätsfaktor',
            'expansion' => 'PAL',
            'category' => 'nutrition',
            'definition' => 'Ein Multiplikator, der deinen Grundumsatz je nach Alltagsbewegung erhöht, um auf deinen TDEE zu kommen. Von rund 1,2 (sitzend) bis 1,9 (sehr aktiv).',
            'example' => 'Grundumsatz mal Aktivitätsfaktor ergibt deinen TDEE.',
        ],
        'macros' => [
            'term' => 'Makronährstoffe',
            'expansion' => 'Makros',
            'category' => 'nutrition',
            'definition' => 'Die drei energieliefernden Nährstoffe: Protein, Kohlenhydrate und Fett. Protein und Kohlenhydrate haben rund 4 kcal pro Gramm, Fett rund 9.',
            'example' => 'Deine Makros teilen deine tägliche Kalorienzahl auf Protein, Kohlenhydrate und Fett auf.',
        ],
        'calorie-deficit' => [
            'term' => 'Kaloriendefizit',
            'expansion' => 'und Überschuss',
            'category' => 'nutrition',
            'definition' => 'Weniger Kalorien essen als du verbrauchst (Defizit) führt zum Abnehmen, mehr (Überschuss) zum Zunehmen.',
            'example' => 'Ein Defizit von rund 500 kcal am Tag bedeutet etwa 0,5 kg Abnahme pro Woche.',
        ],
        'maintenance-calories' => [
            'term' => 'Erhaltungskalorien',
            'expansion' => 'Maintenance',
            'category' => 'nutrition',
            'definition' => 'Die Kalorienmenge, bei der dein Gewicht stabil bleibt. Sie entspricht deinem TDEE.',
            'example' => 'Auf Erhaltungskalorien hältst du dein Gewicht, ohne zu- oder abzunehmen.',
        ],
        'lean-body-mass' => [
            'term' => 'Fettfreie Masse',
            'expansion' => 'Lean Body Mass',
            'category' => 'nutrition',
            'definition' => 'Dein Körpergewicht ohne Körperfett, also Muskeln, Knochen, Organe und Wasser. Wird manchmal genutzt, um den Proteinbedarf zu berechnen.',
            'example' => 'Beim Abnehmen orientiert sich der Proteinbedarf oft an der fettfreien Masse.',
        ],

        'check-in' => [
            'term' => 'Check-in',
            'expansion' => 'wöchentlicher Check-in',
            'category' => 'tracking',
            'definition' => 'Dein kurzes wöchentliches Ritual in der App: Gewicht, optionale Maße und wie du dich gefühlt hast. So sieht dein Coach echten Fortschritt.',
            'example' => 'Beim Check-in trägst du dein Gewicht ein und Mona reagiert auf deinen Verlauf.',
        ],
    ],

    'faqs' => [
        [
            'question' => 'Was ist ein guter RPE-Wert beim Krafttraining?',
            'answer' => 'Für Muskelaufbau liegen die meisten Arbeitssätze bei RPE 7 bis 9, also 1 bis 3 Wiederholungen in Reserve. So ist der Reiz hoch genug, ohne dass die Technik leidet oder du zu stark ermüdest.',
        ],
        [
            'question' => 'Wie lese ich die Tempo-Angabe wie 3-0-1-0?',
            'answer' => 'Die vier Zahlen sind Sekunden für die Phasen einer Wiederholung: Ablassen, Pause unten, Hochdrücken, Pause oben. 3-0-1-0 heißt also 3 Sekunden ablassen, keine Pause, 1 Sekunde hoch, keine Pause.',
        ],
        [
            'question' => 'Was ist der Unterschied zwischen BMR und TDEE?',
            'answer' => 'Der BMR ist dein Verbrauch in völliger Ruhe. Der TDEE ist dein gesamter Tagesverbrauch inklusive Bewegung und Alltag. Man kommt vom BMR zum TDEE, indem man mit dem Aktivitätsfaktor multipliziert.',
        ],
        [
            'question' => 'Wie viele Sätze pro Muskel und Woche sind sinnvoll?',
            'answer' => 'Als produktiver Bereich für Muskelaufbau gelten rund 10 bis 20 harte Sätze pro Muskel und Woche. Für Einsteiger reichen oft schon weniger, verteilt auf zwei Einheiten pro Woche.',
        ],
    ],

    'cta' => [
        'headline' => 'Schluss mit Rätselraten. Hol dir einen Plan, der diese Begriffe für dich einsetzt.',
        'button' => 'fytrr kostenlos testen',
        'trust' => 'Kostenlos starten, keine Kreditkarte nötig.',
    ],

    'links' => [
        'planRoast' => 'Trainingsplan-Check: Lass deinen Plan bewerten',
        'calorie' => 'Kalorienrechner: Finde deinen TDEE',
        'macro' => 'Makro-Rechner: Berechne deine Makros',
        'workoutPlans' => 'Kostenlose Trainingspläne',
    ],
];
