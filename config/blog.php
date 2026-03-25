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
            'og_image' => '/assets/images/og/kalorienbedarf-berechnen.webp',
            'og_image_alt' => 'Teller mit ausgewogener Mahlzeit, Smartphone mit Kalorienrechner und Küchenwaage auf weißer Arbeitsfläche',
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

        'hyrox-krafttraining' => [
            'title' => 'Krafttraining für Hyrox: Warum Ausdauer nicht reicht | Fytrr',
            'description' => 'Du willst Hyrox machen? Dann reicht Laufen allein nicht. Erfahre, warum Krafttraining die Basis für deine Hyrox-Vorbereitung ist — und wie du startest.',
            'h1' => 'Krafttraining für Hyrox: Warum Ausdauer allein nicht reicht',
            'keywords' => ['hyrox krafttraining', 'krafttraining für hyrox', 'hyrox vorbereitung', 'hyrox training kraft', 'kraft aufbauen hyrox', 'hyrox anfänger kraft', 'hyrox trainingsplan krafttraining', 'wie stark muss man für hyrox sein'],
            'internal_slug' => 'hyrox-strength-training',
            'og_image' => '/assets/images/og/hyrox-og-image.webp',
            'og_image_alt' => 'Mann schiebt schweren Gewichtsschlitten im Fitnessstudio als Vorbereitung auf Hyrox',
            'published_at' => '2026-03-25',
            'last_updated_at' => '2026-03-25',

            'intro' => 'Du hast dich für einen Hyrox angemeldet. Oder du überlegst noch. So oder so — du weißt wahrscheinlich schon: Da wird gelaufen. Viel gelaufen. 8 Kilometer insgesamt, aufgeteilt in 8 Runden à 1 km. Und was machen die meisten? Sie laufen. Logisch. Aber hier ist das Ding: Die 8 km sind nicht das Problem. Was zwischen den Läufen passiert, entscheidet über dein Ergebnis. Sled Push. Sled Pull. Wall Balls. Farmer\'s Carry. Sandbag Lunges. Das alles ist Kraft. Und wenn du die nicht hast, hilft dir die beste Ausdauer nichts.',

            'sections' => [
                [
                    'heading' => 'Was Hyrox von deinem Körper verlangt',
                    'content' => 'Hyrox besteht aus 8 Laufrunden à 1 km, jeweils gefolgt von einer Kraftstation. SkiErg (1.000m) braucht Oberkörper-Ausdauer und Zugkraft. Sled Push (152/102 kg) verlangt Beinkraft und Core-Stabilität. Sled Pull (103/78 kg) fordert Rücken, Bizeps und Griffkraft. Burpee Broad Jumps (80m) verlangen Ganzkörper-Explosivität. Rowing (1.000m) braucht Beine, Rücken und Ausdauer. Farmer\'s Carry (200m, 2×24/2×16 kg) erfordert Griffkraft und Rumpfstabilität. Sandbag Lunges (100m, 20/10 kg) brauchen Beinkraft und Balance. Wall Balls (75/100 Wiederholungen, 6/4 kg) verlangen Beinkraft, Schulterkraft und Ausdauer. Fast überall steht Kraft. Nicht Ausdauer. Kraft.',
                ],
                [
                    'heading' => 'Warum die meisten zu wenig Kraft mitbringen',
                    'content' => 'Das typische Hyrox-Vorbereitungsmuster: Anmeldung, Panik, viel laufen, vielleicht mal ein paar Burpees üben — und am Race Day beim Sled Push sterben. Die meisten verlieren nicht beim Laufen ihre Zeit, sondern an den Stationen. Besonders Sled Push und Sled Pull sind für viele der Punkt, an dem es kippt. Der Grund: Laufen trainiert dein Herz-Kreislauf-System. Aber es baut keine Kraft in den Beinen, im Rücken oder im Griff auf. Zumindest nicht die Art von Kraft, die du brauchst, um einen 152 kg schweren Schlitten über eine Bahn zu schieben.',
                ],
                [
                    'heading' => 'Welche Art von Kraft du für Hyrox brauchst',
                    'content' => 'Du musst kein Powerlifter werden. Hyrox verlangt keine Maximalkraft für eine einzelne Wiederholung. Was du brauchst, ist Kraftausdauer — die Fähigkeit, moderate Lasten über viele Wiederholungen oder eine längere Dauer zu bewegen. Die wichtigsten Bereiche: Beinkraft (Sled Push, Sled Pull, Lunges, Wall Balls, Rowing) — das Fundament. Kniebeugen, Ausfallschritte, Beinpresse. Oberkörper-Zugkraft (Sled Pull, SkiErg, Rowing) — Rudern, Lat Pulldowns, Klimmzüge. Oberkörper-Druckkraft (Wall Balls, Sled Push) — Schulterdrücken, Push-Ups, Dips. Griffkraft (Farmer\'s Carry, Sled Pull, SkiErg) — wird unterschätzt, aber wenn dein Griff nachlässt, kannst du nichts mehr halten. Core-Stabilität (alles) — dein Rumpf verbindet Ober- und Unterkörper.',
                ],
                [
                    'heading' => 'Ein realistischer Ansatz: Erst Kraft, dann Hyrox-spezifisch',
                    'content' => 'Wenn du 12-16 Wochen bis zu deinem Hyrox hast, hier ein sinnvoller Aufbau: Wochen 1-6 — Kraft aufbauen. 3-4× pro Woche Krafttraining mit Fokus auf Kniebeugen, Kreuzheben, Rudern, Schulterdrücken, Ausfallschritte und Farmer\'s Carry. Dazu 2× pro Woche lockeres Laufen (5-8 km). Wochen 7-12 — Übergang zu Kraftausdauer. Gewichte leicht reduzieren, Wiederholungen erhöhen (15-20 statt 8-12). Supersets und Circuits einbauen, Laufumfang leicht erhöhen. Wochen 13-16 — Hyrox-spezifisch (wenn möglich). Stationen simulieren, Sled-Training wenn verfügbar, Race-Pace üben. Der Punkt ist: Die ersten 6 Wochen sind reines Krafttraining. Kein Hyrox-spezifisches Training nötig. Einfach stärker werden.',
                ],
                [
                    'heading' => 'Was Fytrr für deine Hyrox-Vorbereitung tun kann',
                    'content' => 'Ehrlich gesagt: Fytrr ist keine Hyrox-App. Wir generieren keine Hyrox-spezifischen Trainingspläne mit Sled-Stationen und SkiErg-Intervallen. Was Fytrr kann: Dir einen soliden Krafttrainingsplan erstellen, der genau die Grundlagen abdeckt, die du für Hyrox brauchst. Personalisierter Kraftplan — sag der App dein Ziel, dein Level und wie oft du trainieren willst. Verschiedene Splits — Ganzkörper, Oberkörper/Unterkörper, Push/Pull/Legs. Ernährungsplan dazu — genug Protein für den Muskelaufbau, genug Kohlenhydrate für die Ausdauer. Für 3,99€/Monat. Für das Hyrox-spezifische Training in den letzten Wochen brauchst du dann ein Hyrox Gym oder einen spezialisierten Coach. Aber die Kraft-Basis? Die kannst du jetzt aufbauen.',
                ],
                [
                    'heading' => '5 Übungen, die dich am meisten auf Hyrox vorbereiten',
                    'content' => 'Wenn du nur 5 Übungen machen könntest: 1. Kniebeugen — Basis für Sled Push, Lunges, Wall Balls. Kein Hyrox ohne starke Beine. 2. Kreuzheben — Trainiert den gesamten hinteren Körper plus Griffkraft. 3. Kurzhantel-Rudern — Zugkraft für Sled Pull, SkiErg, Rowing. Einarmig für zusätzliche Core-Arbeit. 4. Schulterdrücken — Stehend mit Kurzhanteln. Direkte Übertragung auf Wall Balls. 5. Farmer\'s Carry — Griffkraft, Core, Ganzkörperstabilität. Und es ist buchstäblich eine Hyrox-Station. Alle 5 findest du in einem typischen Fytrr-Kraftplan.',
                ],
            ],

            'faqs' => [
                [
                    'question' => 'Wie stark muss ich für Hyrox sein?',
                    'answer' => 'Es gibt keine Mindestanforderung. Aber als Richtwert: Wenn du dein eigenes Körpergewicht für 5 Wiederholungen kniebeugen kannst und 60 Sekunden im Dead Hang hängen kannst, hast du eine gute Basis. Für den Sled Push hilft es, wenn du die Beinpresse mit dem 1,5-fachen deines Körpergewichts für 10 Wiederholungen schaffst.',
                ],
                [
                    'question' => 'Kann ich nur mit Laufen einen Hyrox schaffen?',
                    'answer' => 'Ja, du wirst ins Ziel kommen. Aber du wirst an den Stationen viel Zeit verlieren und es wird sich deutlich härter anfühlen. Die meisten Erstläufer*innen sagen hinterher: "Ich hätte mehr Kraft trainieren sollen."',
                ],
                [
                    'question' => 'Wie oft pro Woche sollte ich Kraft trainieren für Hyrox?',
                    'answer' => '3-4× ist ideal. 2× reicht für den Anfang, wenn du zusätzlich 2-3× laufen gehst. Weniger als 2× Kraft pro Woche bringt zu wenig Fortschritt.',
                ],
                [
                    'question' => 'Brauche ich ein Hyrox-Gym für die Vorbereitung?',
                    'answer' => 'Für die Kraft-Phase: Nein. Jedes normale Fitnessstudio reicht. Kniebeugen, Kreuzheben, Rudern — das geht überall. Für die letzten 4 Wochen vor dem Rennen ist es hilfreich, mal an einem Schlitten zu trainieren, aber nicht zwingend nötig.',
                ],
                [
                    'question' => 'Soll ich zuerst Kraft oder Ausdauer trainieren?',
                    'answer' => 'Wenn du beides am selben Tag machst: Kraft zuerst. Du brauchst frische Muskeln für die Technik bei schweren Übungen. Laufen kannst du auch müde. Noch besser: Trenne die Einheiten (morgens/abends oder verschiedene Tage).',
                ],
            ],
        ],
    ],

    'en' => [

        'calorie-needs' => [
            'title' => 'How to Calculate Your Daily Calorie Needs: Complete Guide',
            'description' => 'Learn how to calculate your daily calorie needs correctly — with the Mifflin-St Jeor equation, activity factors and goals. Step-by-step guide.',
            'h1' => 'How to Calculate Your Calorie Needs — The Complete Guide',
            'keywords' => ['calculate calorie needs', 'daily calorie needs', 'calorie calculator formula', 'how many calories do I need', 'TDEE calculator'],
            'internal_slug' => 'calorie-needs',
            'og_image' => '/assets/images/og/kalorienbedarf-berechnen.webp',
            'og_image_alt' => 'Balanced meal plate with smartphone calorie calculator and kitchen scale on white countertop',
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
