<?php

return [
    'default_author' => [
        'de' => [
            'name' => 'Tobias Lobitz',
            'title' => 'Gründer & Software-Entwickler',
            'bio' => 'Entwickelt seit 2018 Fitness-Software. Eigene Trainings-Erfahrung: 15 Jahre Krafttraining.',
            'image' => '/assets/authors/tobias.webp',
        ],
        'en' => [
            'name' => 'Tobias Lobitz',
            'title' => 'Founder & Software Developer',
            'bio' => 'Developing fitness software since 2018. Personal training experience: 15 years of strength training.',
            'image' => '/assets/authors/tobias.webp',
        ],
    ],

    'de' => [

        'kalorienbedarf-berechnen' => [
            'title' => 'Kalorienbedarf berechnen: Die komplette Anleitung',
            'description' => 'Lerne, wie du deinen täglichen Kalorienbedarf korrekt berechnest — mit der Mifflin-St-Jeor-Formel, Aktivitätsfaktoren und Zielen. Schritt-für-Schritt erklärt.',
            'h1' => 'Kalorienbedarf berechnen — Die komplette Anleitung',
            'keywords' => ['kalorienbedarf berechnen', 'täglicher kalorienbedarf', 'kalorienbedarf formel', 'kalorienverbrauch berechnen', 'kalorienbedarf frau', 'kalorienbedarf mann'],
            'internal_slug' => 'calorie-needs',
            'published_at' => '2026-03-25',
            'last_updated_at' => '2026-03-25',

            'intro' => 'Wie viele Kalorien brauchst du pro Tag? Diese Frage ist der erste und wichtigste Schritt zu jedem Ernährungsziel — egal ob Abnehmen, Muskelaufbau oder Gewicht halten. In dieser Anleitung erfährst du Schritt für Schritt, wie du deinen individuellen Kalorienbedarf berechnest, welche Formeln wirklich funktionieren und welche Fehler du vermeiden solltest.',

            'sections' => [
                [
                    'heading' => 'Was ist der Kalorienbedarf?',
                    'content' => 'Dein Kalorienbedarf ist die Menge an Energie (gemessen in Kilokalorien, kcal), die dein Körper pro Tag benötigt, um alle Funktionen aufrechtzuerhalten und dein aktuelles Gewicht zu halten. Er setzt sich aus zwei Komponenten zusammen: dem Grundumsatz (Basal Metabolic Rate, BMR) und dem Leistungsumsatz durch körperliche Aktivität. Zusammen ergeben sie deinen Gesamtumsatz (Total Daily Energy Expenditure, TDEE). Wenn du mehr Kalorien isst als dein TDEE, nimmst du zu. Wenn du weniger isst, nimmst du ab. So einfach ist die Grundregel — die Umsetzung ist der schwierige Teil.',
                ],
                [
                    'heading' => 'Schritt 1: Grundumsatz berechnen',
                    'content' => 'Der Grundumsatz ist die Energie, die dein Körper in völliger Ruhe verbraucht — für Atmung, Herzschlag, Gehirnfunktion, Zellreparatur und Körpertemperatur. Er macht bei den meisten Menschen 60–75% des täglichen Gesamtverbrauchs aus. Die genaueste Formel zur Berechnung des Grundumsatzes ist die Mifflin-St-Jeor-Formel, die 1990 entwickelt und in zahlreichen Studien als überlegen gegenüber der älteren Harris-Benedict-Formel bestätigt wurde. Die Formel lautet: Für Männer: BMR = 10 × Gewicht (kg) + 6,25 × Größe (cm) − 5 × Alter − 5. Für Frauen: BMR = 10 × Gewicht (kg) + 6,25 × Größe (cm) − 5 × Alter − 161. Beispiel: Ein 30-jähriger Mann mit 80 kg und 180 cm hat einen Grundumsatz von 10 × 80 + 6,25 × 180 − 5 × 30 − 5 = 1.775 kcal.',
                ],
                [
                    'heading' => 'Schritt 2: Aktivitätsfaktor bestimmen',
                    'content' => 'Der Grundumsatz allein reicht nicht — du musst ihn mit einem Aktivitätsfaktor (PAL-Wert) multiplizieren, um deinen tatsächlichen Tagesbedarf zu erhalten. Dieser Faktor berücksichtigt nicht nur Sport, sondern auch deinen Beruf, Alltagsbewegung und Hausarbeit. Die gängigen Aktivitätsfaktoren sind: Kaum aktiv (1,2) für Bürojob ohne Sport. Leicht aktiv (1,375) für 1–2 Trainingseinheiten pro Woche. Moderat aktiv (1,55) für 3–5 Trainingseinheiten pro Woche — das häufigste Level für regelmäßige Sportler. Sehr aktiv (1,725) für tägliches intensives Training oder körperlich fordernden Beruf plus Training. Extrem aktiv (1,9) für körperliche Arbeit kombiniert mit täglichem Training. Der häufigste Fehler: Den Aktivitätsfaktor zu hoch einschätzen. Eine Stunde Training am Tag macht dich nicht „sehr aktiv", wenn du die restlichen 23 Stunden sitzt. Sei ehrlich bei der Einschätzung.',
                ],
                [
                    'heading' => 'Schritt 3: Gesamtumsatz (TDEE) berechnen',
                    'content' => 'Dein Gesamtumsatz ist einfach: Grundumsatz × Aktivitätsfaktor = TDEE. Für unseren Beispielmann (BMR 1.775 kcal, moderat aktiv): 1.775 × 1,55 = 2.751 kcal pro Tag. Das ist die Kalorienmenge, bei der er sein Gewicht hält — weder zu- noch abnimmt. Von diesem Wert aus kannst du je nach Ziel anpassen.',
                ],
                [
                    'heading' => 'Schritt 4: An dein Ziel anpassen',
                    'content' => 'Jetzt wird es praktisch. Je nach Ziel musst du deinen TDEE anpassen: Zum Abnehmen: Ziehe 300–500 kcal von deinem TDEE ab. Das ergibt einen Fettverlust von etwa 0,3–0,5 kg pro Woche — langsam genug, um Muskelmasse zu erhalten, schnell genug, um Fortschritte zu sehen. Ein größeres Defizit (über 750 kcal) klingt verlockend, führt aber häufig zu Muskelabbau, Heißhunger und Jo-Jo-Effekt. Zum Muskelaufbau: Addiere 200–400 kcal zu deinem TDEE. Mehr führt hauptsächlich zu Fettanstieg — dein Körper kann nur begrenzt schnell Muskeln aufbauen. Kombiniere den Überschuss mit Krafttraining und 1,6–2,0 g Protein pro kg Körpergewicht. Zum Gewicht halten: Iss auf deinem TDEE-Level. Das klingt einfach, erfordert aber anfangs Tracking, um ein Gefühl für die richtigen Portionsgrößen zu entwickeln.',
                ],
                [
                    'heading' => 'Kalorienbedarf für Frauen vs. Männer',
                    'content' => 'Frauen haben in der Regel einen niedrigeren Kalorienbedarf als Männer — typischerweise 1.600–2.400 kcal vs. 2.000–3.000 kcal pro Tag. Der Unterschied liegt hauptsächlich an der geringeren Muskelmasse (Muskeln verbrennen mehr Energie als Fettgewebe) und dem kleineren Körperbau. Aber Vorsicht vor pauschalen Empfehlungen: Eine 70 kg schwere Frau, die 5× pro Woche trainiert, braucht mehr Kalorien als ein 70 kg schwerer Mann, der den ganzen Tag sitzt. Deshalb ist eine individuelle Berechnung immer besser als Durchschnittswerte.',
                ],
                [
                    'heading' => 'Die häufigsten Fehler beim Kalorienbedarf berechnen',
                    'content' => 'Aktivitätslevel überschätzen: Die Mehrheit der Menschen stuft sich eine Kategorie zu hoch ein. 3× die Woche ins Gym bei einem Bürojob ist „moderat aktiv", nicht „sehr aktiv". Kalorien falsch tracken: Studien zeigen, dass Menschen ihre Kalorienaufnahme durchschnittlich um 30–50% unterschätzen. Öl beim Kochen, Snacks zwischendurch und Getränke werden oft vergessen. Zu selten neu berechnen: Dein Kalorienbedarf ändert sich mit deinem Gewicht, deinem Alter und deiner Aktivität. Berechne ihn alle 4–6 Wochen neu, besonders wenn du aktiv abnimmst. Netto-Kalorien zählen: Manche ziehen verbrannte Sport-Kalorien von ihrem Defizit ab und essen sie „zurück". Das führt fast immer zu überschätztem Verbrauch und gebremsten Ergebnissen.',
                ],
                [
                    'heading' => 'Warum eine Formel nur der Anfang ist',
                    'content' => 'Eine Kalorienberechnung gibt dir einen wissenschaftlich fundierten Startwert — aber jeder Körper ist anders. Genetik, Hormonstatus, Schlafqualität und Stress beeinflussen deinen tatsächlichen Verbrauch. Deshalb empfehlen wir: Berechne deinen Bedarf mit dem Kalorienrechner, tracke 2–3 Wochen dein Gewicht und deine Aufnahme, und passe dann an. Wenn du nach 2 Wochen kein Gewicht verlierst, reduziere um weitere 100–200 kcal. Wenn du zu schnell abnimmst (über 1 kg/Woche), erhöhe leicht.',
                ],
                [
                    'heading' => 'Vom Kalorienwert zum Ernährungsplan',
                    'content' => 'Du kennst jetzt deinen Kalorienbedarf — aber was isst du konkret? Genau hier wird es für die meisten schwierig. Die Theorie ist klar, die Umsetzung im Alltag nicht. Fytrr löst genau dieses Problem: Unsere KI erstellt dir einen personalisierten Ernährungsplan basierend auf deinem berechneten Kalorienziel. Jede Mahlzeit ist durchgerechnet, jedes Makro berücksichtigt — und du kannst Mahlzeiten jederzeit tauschen, wenn dir etwas nicht schmeckt. Dazu bekommst du einen passenden Trainingsplan. 7 Tage kostenlos testen.',
                ],
            ],

            'faqs' => [
                [
                    'question' => 'Wie oft sollte ich meinen Kalorienbedarf neu berechnen?',
                    'answer' => 'Alle 4–6 Wochen, oder wenn sich dein Gewicht, deine Aktivität oder dein Ziel wesentlich verändert hat. Bei aktivem Abnehmen sinkt dein Bedarf mit jedem verlorenen Kilogramm.',
                ],
                [
                    'question' => 'Ist Kalorien zählen wirklich nötig?',
                    'answer' => 'Nicht dauerhaft. Wir empfehlen 2–3 Wochen Tracking, um ein Gefühl für Portionsgrößen zu entwickeln. Danach kannst du intuitiver essen — mit gelegentlichen Check-ins.',
                ],
                [
                    'question' => 'Welche Formel ist besser: Harris-Benedict oder Mifflin-St Jeor?',
                    'answer' => 'Mifflin-St Jeor. Studien zeigen, dass sie bei den meisten Menschen genauer ist als die ältere Harris-Benedict-Formel, besonders bei übergewichtigen Personen.',
                ],
                [
                    'question' => 'Warum nehme ich nicht ab, obwohl ich im Defizit bin?',
                    'answer' => 'Häufigste Gründe: Kalorien werden unterschätzt (versteckte Kalorien in Öl, Saucen, Snacks), der Aktivitätsfaktor ist zu hoch angesetzt, oder Wassereinlagerungen maskieren den Fettverlust. Tracke 2 Wochen genau — meistens liegt es am Tracking.',
                ],
            ],

            'calorie_calculator_slug' => 'kostenlose-tools/kalorienrechner',
        ],
    ],

    'en' => [

        'calorie-needs' => [
            'title' => 'How to Calculate Your Daily Calorie Needs: Complete Guide',
            'description' => 'Learn how to calculate your daily calorie needs correctly — with the Mifflin-St Jeor equation, activity factors and goals. Step-by-step guide.',
            'h1' => 'How to Calculate Your Calorie Needs — The Complete Guide',
            'keywords' => ['calculate calorie needs', 'daily calorie needs', 'calorie calculator formula', 'how many calories do I need', 'TDEE calculator'],
            'internal_slug' => 'calorie-needs',
            'published_at' => '2026-03-25',
            'last_updated_at' => '2026-03-25',

            'intro' => 'How many calories do you need per day? This question is the first and most important step towards any nutrition goal — whether you want to lose weight, build muscle or maintain. In this guide, you\'ll learn step by step how to calculate your individual calorie needs, which formulas actually work and which mistakes to avoid.',

            'sections' => [
                [
                    'heading' => 'What are calorie needs?',
                    'content' => 'Your calorie needs are the amount of energy (measured in kilocalories, kcal) your body requires per day to maintain all functions and sustain your current weight. They consist of two components: your basal metabolic rate (BMR) and the additional energy burned through physical activity. Together, they form your total daily energy expenditure (TDEE). If you eat more calories than your TDEE, you gain weight. If you eat less, you lose weight. The principle is simple — the execution is the hard part.',
                ],
                [
                    'heading' => 'Step 1: Calculate your basal metabolic rate',
                    'content' => 'Your basal metabolic rate is the energy your body uses at complete rest — for breathing, heart function, brain activity, cell repair and body temperature. For most people, it accounts for 60–75% of total daily energy expenditure. The most accurate formula for calculating BMR is the Mifflin-St Jeor equation, developed in 1990 and confirmed by numerous studies as superior to the older Harris-Benedict equation. The formula is: For men: BMR = 10 × weight (kg) + 6.25 × height (cm) − 5 × age − 5. For women: BMR = 10 × weight (kg) + 6.25 × height (cm) − 5 × age − 161. Example: A 30-year-old man weighing 80 kg at 180 cm has a BMR of 10 × 80 + 6.25 × 180 − 5 × 30 − 5 = 1,775 kcal.',
                ],
                [
                    'heading' => 'Step 2: Determine your activity factor',
                    'content' => 'Your BMR alone isn\'t enough — you need to multiply it by an activity factor (PAL value) to get your actual daily needs. This factor accounts not just for exercise, but also your job, daily movement and household tasks. The standard activity factors are: Sedentary (1.2) for an office job with no exercise. Lightly active (1.375) for 1–2 workouts per week. Moderately active (1.55) for 3–5 workouts per week — the most common level for regular exercisers. Very active (1.725) for daily intense training or a physically demanding job plus training. Extremely active (1.9) for physical labour combined with daily training. The most common mistake: overestimating your activity level. One hour of training per day doesn\'t make you "very active" if you sit for the other 23 hours. Be honest with your assessment.',
                ],
                [
                    'heading' => 'Step 3: Calculate your TDEE',
                    'content' => 'Your total daily energy expenditure is simply: BMR × activity factor = TDEE. For our example man (BMR 1,775 kcal, moderately active): 1,775 × 1.55 = 2,751 kcal per day. This is the calorie amount at which he maintains his weight — neither gaining nor losing. From this number, you adjust based on your goal.',
                ],
                [
                    'heading' => 'Step 4: Adjust for your goal',
                    'content' => 'Now it gets practical. Depending on your goal, you need to adjust your TDEE: For weight loss: subtract 300–500 kcal from your TDEE. This produces fat loss of approximately 0.3–0.5 kg per week — slow enough to preserve muscle mass, fast enough to see progress. A larger deficit (over 750 kcal) sounds tempting but often leads to muscle loss, cravings and yo-yo dieting. For muscle gain: add 200–400 kcal to your TDEE. More leads primarily to fat gain — your body can only build muscle at a limited rate. Combine the surplus with strength training and 1.6–2.0 g protein per kg bodyweight. For maintenance: eat at your TDEE level. It sounds simple but initially requires tracking to develop a feel for correct portion sizes.',
                ],
                [
                    'heading' => 'Calorie needs for women vs. men',
                    'content' => 'Women generally have lower calorie needs than men — typically 1,600–2,400 kcal vs. 2,000–3,000 kcal per day. The difference is mainly due to lower muscle mass (muscle burns more energy than fat tissue) and smaller body frames. But beware of blanket recommendations: a 70 kg woman who trains 5 times per week needs more calories than a 70 kg man who sits all day. That\'s why an individual calculation is always better than averages.',
                ],
                [
                    'heading' => 'The most common mistakes when calculating calorie needs',
                    'content' => 'Overestimating activity level: The majority of people rate themselves one category too high. Going to the gym 3 times a week with an office job is "moderately active", not "very active". Inaccurate calorie tracking: Studies show people underestimate their calorie intake by an average of 30–50%. Oil when cooking, snacks in between and drinks are often forgotten. Recalculating too rarely: Your calorie needs change with your weight, age and activity. Recalculate every 4–6 weeks, especially when actively losing weight. Counting net calories: Some subtract burned exercise calories from their deficit and "eat them back". This almost always leads to overestimated expenditure and stalled results.',
                ],
                [
                    'heading' => 'Why a formula is only the starting point',
                    'content' => 'A calorie calculation gives you a science-based starting point — but every body is different. Genetics, hormone status, sleep quality and stress all influence your actual expenditure. That\'s why we recommend: calculate your needs with the calorie calculator, track your weight and intake for 2–3 weeks, then adjust. If you\'re not losing weight after 2 weeks, reduce by another 100–200 kcal. If you\'re losing too fast (over 1 kg/week), increase slightly.',
                ],
                [
                    'heading' => 'From calorie number to nutrition plan',
                    'content' => 'You now know your calorie needs — but what do you actually eat? This is where most people struggle. The theory is clear, but putting it into practice daily is not. Fytrr solves exactly this problem: our AI creates a personalised nutrition plan based on your calculated calorie target. Every meal is calculated, every macro accounted for — and you can swap meals anytime if something doesn\'t suit you. Plus you get a matching workout plan. Try it free for 7 days.',
                ],
            ],

            'faqs' => [
                [
                    'question' => 'How often should I recalculate my calorie needs?',
                    'answer' => 'Every 4–6 weeks, or whenever your weight, activity or goal changes significantly. When actively losing weight, your needs decrease with every kilogram lost.',
                ],
                [
                    'question' => 'Do I really need to count calories?',
                    'answer' => 'Not permanently. We recommend 2–3 weeks of tracking to develop a feel for portion sizes. After that, you can eat more intuitively — with occasional check-ins.',
                ],
                [
                    'question' => 'Which formula is better: Harris-Benedict or Mifflin-St Jeor?',
                    'answer' => 'Mifflin-St Jeor. Studies show it is more accurate for most people than the older Harris-Benedict equation, especially for overweight individuals.',
                ],
                [
                    'question' => 'Why am I not losing weight despite being in a deficit?',
                    'answer' => 'Most common reasons: calories are underestimated (hidden calories in oil, sauces, snacks), the activity factor is set too high, or water retention is masking fat loss. Track accurately for 2 weeks — it\'s usually a tracking issue.',
                ],
            ],

            'calorie_calculator_slug' => 'free-tools/calorie-calculator',
        ],
    ],
];
