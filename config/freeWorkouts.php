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
    'index_labels' => [
        'de' => [
            'heading' => 'Kostenlose Trainingspläne',
            'intro' => 'Entdecke unsere wissenschaftlich fundierten Trainingspläne für jedes Ziel. Kostenlos, personalisiert und sofort verfügbar.',
            'viewPlan' => 'Plan ansehen',
            'ctaHeading' => 'Bereit für dein Training?',
            'ctaText' => 'Wähle den passenden Plan für dein Ziel und starte noch heute mit deinem kostenlosen, wissenschaftlich fundierten Trainingsprogramm.',
            'ctaButton' => 'Personalisierten Plan erstellen',
        ],
        'en' => [
            'heading' => 'Free Workout Plans',
            'intro' => 'Discover our science-based workout plans for every goal. Free, personalized and available immediately.',
            'viewPlan' => 'View Plan',
            'ctaHeading' => 'Ready for Your Training?',
            'ctaText' => 'Choose the right plan for your goal and start today with your free, science-based training program.',
            'ctaButton' => 'Generate Personalized Plan',
        ],
    ],

    'de' => [
        /* ============================
           Abnehmen
        ============================ */
        'abnehmen' => [
            'title' => 'Kostenloser Trainingsplan zum Abnehmen – 8 Wochen',
            'description' => 'Kostenloser 8-Wochen-Trainingsplan zum Abnehmen. Kraft- & Cardio-Training für nachhaltigen Fettabbau – Zuhause & Gym.',
            'h1' => 'Trainingsplan zum Abnehmen – nachhaltig Fett verlieren',
            'intro' => 'Dieser strukturierte 8-Wochen-Trainingsplan kombiniert Krafttraining und Cardio, um Fett abzubauen, Muskeln zu erhalten und den Stoffwechsel nachhaltig zu steigern.',
            'internal_type' => 'weight_loss',
            'published_at' => '2025-12-24',
            'last_updated_at' => '2025-12-24',
            'why_it_works' => [
                'title' => 'Warum dieser Trainingsplan beim Abnehmen funktioniert',
                'content' => [
                    [
                        'heading' => 'Kombination aus Kraft und Cardio maximiert Fettabbau',
                        'text' => 'Dieser Plan nutzt eine bewährte Kombination: Krafttraining erhält deine Muskelmasse während des Kaloriendefizits, während HIIT-Einheiten den Kalorienverbrauch maximieren. Studien zeigen, dass diese Kombination bis zu 40% effektiver ist als reines Cardio-Training beim Fettabbau.',
                    ],
                    [
                        'heading' => 'Der Nachbrenneffekt (EPOC) arbeitet für dich',
                        'text' => 'Durch intensives Krafttraining und HIIT-Intervalle erzeugst du einen erhöhten Sauerstoffverbrauch nach dem Training (EPOC). Dein Körper verbrennt dadurch bis zu 48 Stunden nach dem Training zusätzliche Kalorien – selbst im Ruhezustand. Dieser "Nachbrenneffekt" kann den Gesamtkalorienverbrauch um 6-15% erhöhen.',
                    ],
                    [
                        'heading' => 'Muskelerhalt schützt vor Jojo-Effekt',
                        'text' => 'Im Gegensatz zu reinen Diäten ohne Training erhält dieser Plan deine Muskelmasse. Das ist entscheidend: Jedes Kilogramm Muskelmasse verbrennt täglich etwa 13 kcal im Ruhezustand. Verlierst du Muskeln durch falsche Diäten, sinkt dein Grundumsatz – der Jojo-Effekt ist vorprogrammiert.',
                    ],
                    [
                        'heading' => 'Progressive Steigerung verhindert Plateaus',
                        'text' => 'Der 8-Wochen-Aufbau mit systematischer Progression (mehr Wiederholungen, kürzere Pausen, höhere Intensität) sorgt dafür, dass dein Körper sich kontinuierlich anpassen muss. So vermeidest du das typische Plateau nach 3-4 Wochen, wo viele andere Pläne stagnieren.',
                    ],
                    [
                        'heading' => 'Wissenschaftlich fundierte Frequenz',
                        'text' => '3 Trainingseinheiten pro Woche bieten das optimale Verhältnis zwischen Belastung und Regeneration. Studien der American College of Sports Medicine zeigen: Diese Frequenz ermöglicht nachhaltigen Fettabbau von 0,5-1 kg pro Woche, ohne den Körper zu überlasten oder Muskelabbau zu riskieren.',
                    ],
                ],
            ],

            'common_mistakes' => [
                'title' => 'Die 7 häufigsten Fehler beim Abnehmen – und wie du sie vermeidest',
                'mistakes' => [
                    [
                        'title' => 'Zu großes Kaloriendefizit',
                        'problem' => 'Viele setzen auf extreme Diäten mit 800-1000 kcal täglich und erwarten schnelle Erfolge.',
                        'consequence' => 'Dein Körper schaltet in den "Hungermodus", Stoffwechsel verlangsamt sich, Muskelmasse wird abgebaut, Energie fehlt für Training.',
                        'solution' => 'Halte ein moderates Defizit von 300-500 kcal. Das bedeutet: 0,5-0,7 kg Gewichtsverlust pro Woche – nachhaltig und ohne Muskelabbau.',
                        'example' => 'Bei einem Grundumsatz von 2000 kcal: Esse 1500-1700 kcal statt 1000 kcal.',
                    ],
                    [
                        'title' => 'Zu wenig Protein',
                        'problem' => 'Protein wird unterschätzt und durch zu viele Kohlenhydrate ersetzt.',
                        'consequence' => 'Muskelmasse geht verloren, Hunger-Attacken nehmen zu, Stoffwechsel sinkt. Du verlierst zwar Gewicht, aber das falsche (Muskeln statt Fett).',
                        'solution' => 'Ziel: 1,6-2,0g Protein pro kg Körpergewicht täglich. Priorisiere proteinreiche Lebensmittel bei jeder Mahlzeit.',
                        'example' => 'Bei 75 kg Körpergewicht: 120-150g Protein täglich (z.B. 200g Hähnchen, 200g Magerquark, 3 Eier, 1 Shake).',
                    ],
                    [
                        'title' => 'Cardio ohne Krafttraining',
                        'problem' => 'Ausschließlich Joggen oder Radfahren, kein Krafttraining.',
                        'consequence' => 'Zwar Kalorienverbrauch, aber kein Muskelschutz. Ergebnis: "Skinny Fat" – niedriges Gewicht, aber hoher Körperfettanteil und schwache Muskulatur.',
                        'solution' => 'Priorisiere Krafttraining (mindestens 2x/Woche), nutze Cardio als Ergänzung. Unser Plan macht genau das.',
                        'example' => 'Statt 5x Joggen: 3x dieser Trainingsplan (Kraft + HIIT) + 2x lockeres Spazieren.',
                    ],
                    [
                        'title' => 'Inkonsistentes Training',
                        'problem' => 'Montag: Hochmotiviert. Donnerstag: Keine Lust. Nächste Woche: Wieder bei Null.',
                        'consequence' => 'Keine Anpassung des Körpers, kein Muskelaufbau, kein Fortschritt. Fettabbau braucht Kontinuität über Wochen.',
                        'solution' => 'Setze feste Trainingstage (z.B. Mo/Mi/Fr). Auch ein 20-Minuten-Training ist besser als ausfallen lassen. Nutze Habit-Stacking: Training direkt nach Feierabend.',
                        'example' => 'Statt "wenn ich Zeit habe": Kalendereintrag "Mo 18:00 Training" wie ein wichtiger Termin.',
                    ],
                    [
                        'title' => 'Zu wenig Schlaf',
                        'problem' => 'Nur 5-6 Stunden Schlaf pro Nacht, aber hartes Training und Diät.',
                        'consequence' => 'Cortisol-Spiegel steigt (Stresshormon), Testosteron sinkt, Hunger-Hormone geraten durcheinander (mehr Ghrelin = mehr Hunger). Fettabbau stoppt.',
                        'solution' => '7-9 Stunden Schlaf pro Nacht. Studien zeigen: Guter Schlaf kann Fettabbau um bis zu 55% steigern bei gleichem Training.',
                        'example' => 'Bei 6h Schlaf: 60% des Gewichtsverlusts ist Muskelmasse. Bei 8h Schlaf: 80% des Verlusts ist Fett.',
                    ],
                    [
                        'title' => 'Zu lange Trainingspausen zwischen Sätzen',
                        'problem' => 'Am Handy scrollen oder quatschen – aus 60s Pause werden 3-5 Minuten.',
                        'consequence' => 'Kalorienverbrauch sinkt drastisch, Trainingseffekt (besonders EPOC) reduziert sich, 45-Minuten-Training wird zu 90 Minuten.',
                        'solution' => 'Timer nutzen! Halte dich strikt an die Pausenzeiten (60s Kraft, 30-45s HIIT). Das macht den Unterschied zwischen durchschnittlich und effektiv.',
                        'example' => 'Smartphone-Timer oder Gym-Uhr: Nach jeder Übung Timer starten.',
                    ],
                    [
                        'title' => 'Keine Alltagsbewegung',
                        'problem' => 'Training 3x/Woche, aber sonst 12 Stunden täglich sitzen (Büro, Auto, Couch).',
                        'consequence' => 'NEAT (Non-Exercise Activity Thermogenesis) ist minimal. Der "3x Training" Effekt verpufft bei <3.000 Schritten täglich.',
                        'solution' => 'Ziel: 8.000-10.000 Schritte täglich. Das sind zusätzlich 200-400 kcal Verbrauch – ohne extra Training.',
                        'example' => 'Mittagspause: 15 Min Spaziergang. Telefonate: Im Gehen. Treppe statt Aufzug. Parken: 500m weiter weg.',
                    ],
                ],
                'summary' => 'Die meisten scheitern nicht am Training, sondern an diesen versteckten Fehlern. Vermeide sie und dein Erfolg ist fast garantiert.',
            ],

            'workout' => [
                'weeks' => 8,
                'workouts_per_week' => 3,
                'duration_minutes' => 45,
                'level' => 'Anfänger bis Fortgeschritten',
                'equipment' => ['Kurzhanteln (optional)', 'Matte'],

                'schedule' => [

                    [
                        'day' => 'Tag 1 – Ganzkörper Kraft',
                        'focus' => 'Große Muskelgruppen & Kalorienverbrauch',
                        'exercises' => [
                            ['name' => 'Kniebeugen', 'sets' => 3, 'reps' => '12–15', 'rest' => '60s'],
                            ['name' => 'Liegestütze', 'sets' => 3, 'reps' => '8–12', 'rest' => '60s'],
                            ['name' => 'Kurzhantel Rudern', 'sets' => 3, 'reps' => '12–15', 'rest' => '60s'],
                            ['name' => 'Plank', 'sets' => 3, 'reps' => '30–45s', 'rest' => '45s'],
                            ['name' => 'Dead Bug', 'sets' => 3, 'reps' => '10 pro Seite', 'rest' => '45s'],
                        ],
                    ],

                    [
                        'day' => 'Tag 2 – Cardio & HIIT',
                        'focus' => 'Fettverbrennung & Kondition',
                        'exercises' => [
                            ['name' => 'Jumping Jacks', 'sets' => 4, 'reps' => '30s', 'rest' => '30s'],
                            ['name' => 'Mountain Climbers', 'sets' => 4, 'reps' => '30s', 'rest' => '30s'],
                            ['name' => 'Burpees', 'sets' => 3, 'reps' => '8–12', 'rest' => '60s'],
                            ['name' => 'Bodyweight Squats', 'sets' => 3, 'reps' => '20', 'rest' => '45s'],
                        ],
                    ],

                    [
                        'day' => 'Tag 3 – Unterkörper & Core',
                        'focus' => 'Beine, Gesäß, Rücken & Rumpfstabilität',
                        'exercises' => [
                            ['name' => 'Ausfallschritte', 'sets' => 3, 'reps' => '10 pro Bein', 'rest' => '60s'],
                            ['name' => 'Glute Bridges', 'sets' => 3, 'reps' => '15–20', 'rest' => '45s'],
                            ['name' => 'Reverse Snow Angels', 'sets' => 3, 'reps' => '12–15', 'rest' => '45s'],
                            ['name' => 'Russian Twists', 'sets' => 3, 'reps' => '20', 'rest' => '45s'],
                        ],
                    ],

                ],

                'progression' => 'Woche 1–2: Technik & moderates Tempo | Woche 3–4: mehr Wiederholungen | Woche 5–6: kürzere Pausen | Woche 7–8: höhere Intensität oder Zusatzgewicht',
                'tips' => [
                    'Moderates Kaloriendefizit von 300–500 kcal',
                    'Krafttraining priorisieren für Muskelerhalt',
                    '7–9 Stunden Schlaf für optimale Regeneration',
                    'Alltagsbewegung erhöhen (z. B. 8.000–10.000 Schritte)',
                ],
            ],

            'faqs' => [
                [
                    'question' => 'Wie oft sollte ich trainieren, um abzunehmen?',
                    'answer' => '3–4 Trainingseinheiten pro Woche sind ideal, um Fett abzubauen und gleichzeitig ausreichend zu regenerieren.',
                ],
                [
                    'question' => 'Ist Krafttraining sinnvoll zum Abnehmen?',
                    'answer' => 'Ja. Krafttraining erhält Muskelmasse, erhöht den Grundumsatz und verbessert langfristig den Fettabbau.',
                ],
                [
                    'question' => 'Wann sehe ich erste Ergebnisse?',
                    'answer' => 'Viele spüren bereits nach 2–3 Wochen mehr Energie. Sichtbare Veränderungen zeigen sich meist nach 4–6 Wochen.',
                ],
            ],
        ],

        /* ============================
           MUSKELAUFBAU
        ============================ */
        'muskelaufbau' => [
            'title' => 'Trainingsplan Muskelaufbau – 12 Wochen',
            'description' => 'Kostenloser 12-Wochen-Trainingsplan für Muskelaufbau mit progressiver Belastungssteigerung. Ideal für Anfänger und Fortgeschrittene.',
            'h1' => 'Muskelaufbau Trainingsplan – systematisch stärker werden',
            'intro' => 'Dieser 12-Wochen-Trainingsplan basiert auf progressiver Überlastung und kombiniert Muskelwachstum, Kraftsteigerung und ausreichende Regeneration.',
            'internal_type' => 'muscle_gain',
            'published_at' => '2025-12-24',
            'last_updated_at' => '2025-12-24',

            'why_it_works' => [
                'title' => 'Warum dieser Muskelaufbau-Trainingsplan funktioniert',
                'content' => [
                    [
                        'heading' => 'Progressive Überlastung treibt Muskelwachstum',
                        'text' => 'Muskelwachstum entsteht nur, wenn der Körper regelmäßig mit steigenden Belastungen konfrontiert wird. Dieser Plan basiert auf progressiver Überlastung – also dem gezielten Steigern von Gewicht, Wiederholungen oder Trainingsvolumen. Dieses Prinzip gilt als der wichtigste Faktor für Hypertrophie und ist wissenschaftlich eindeutig belegt.',
                    ],
                    [
                        'heading' => 'Optimales Trainingsvolumen und Frequenz',
                        'text' => 'Jede Muskelgruppe wird etwa zwei Mal pro Woche trainiert. Studien zeigen, dass diese Frequenz das beste Verhältnis aus Trainingsreiz und Regeneration bietet und deutlich effektiver ist als einmalige, sehr hohe Belastungen.',
                    ],
                    [
                        'heading' => 'Grundübungen maximieren den Wachstumsreiz',
                        'text' => 'Der Trainingsplan setzt bewusst auf komplexe Mehrgelenksübungen wie Kniebeugen, Bankdrücken, Rudern und Klimmzüge. Diese aktivieren große Muskelgruppen, erzeugen hohe mechanische Spannung und fördern einen starken Wachstumsreiz.',
                    ],
                    [
                        'heading' => 'Geplante Regeneration verhindert Übertraining',
                        'text' => 'Muskeln wachsen nicht im Training, sondern in der Erholung. Durch sinnvolle Splits, Pausentage und kontrollierte Trainingsfrequenz erhält dein Körper ausreichend Zeit zur Regeneration – ein entscheidender Faktor für langfristigen Muskelaufbau.',
                    ],
                    [
                        'heading' => 'Training und Ernährung sind aufeinander abgestimmt',
                        'text' => 'Der Plan ist darauf ausgelegt, mit einem moderaten Kalorienüberschuss und ausreichender Proteinzufuhr kombiniert zu werden. So stehen deinem Körper alle notwendigen Bausteine zur Verfügung, um Muskulatur effektiv aufzubauen.',
                    ],
                ],
            ],

            'common_mistakes' => [
                'title' => 'Die 7 häufigsten Fehler beim Muskelaufbau – und wie du sie vermeidest',
                'mistakes' => [
                    [
                        'title' => 'Kein progressiver Trainingsfortschritt',
                        'problem' => 'Wochenlang mit denselben Gewichten und Wiederholungen trainieren.',
                        'consequence' => 'Der Körper passt sich an, Muskelwachstum stagniert und Fortschritte bleiben aus.',
                        'solution' => 'Dokumentiere dein Training und steigere gezielt Gewicht, Wiederholungen oder Volumen.',
                        'example' => 'Woche 1: Bankdrücken 3×8 mit 60 kg → Woche 2: 3×9 oder 62,5 kg.',
                    ],
                    [
                        'title' => 'Zu geringe Kalorienzufuhr',
                        'problem' => 'Muskelaufbau bei Erhaltungsbedarf oder sogar Kaloriendefizit.',
                        'consequence' => 'Dem Körper fehlt Energie für Regeneration und Muskelaufbau.',
                        'solution' => 'Halte einen moderaten Kalorienüberschuss von 300–500 kcal pro Tag.',
                        'example' => 'Bei Erhaltungsbedarf von 2.400 kcal: Ziel sind 2.700–2.900 kcal.',
                    ],
                    [
                        'title' => 'Zu wenig Protein',
                        'problem' => 'Unregelmäßige oder zu niedrige Proteinzufuhr.',
                        'consequence' => 'Muskelregeneration verlangsamt sich, Muskelaufbau bleibt hinter dem Potenzial zurück.',
                        'solution' => 'Ziel: 2,0–2,2 g Protein pro kg Körpergewicht täglich, gleichmäßig verteilt.',
                        'example' => 'Bei 80 kg Körpergewicht: etwa 160–175 g Protein pro Tag.',
                    ],
                    [
                        'title' => 'Schlechte Übungsausführung',
                        'problem' => 'Zu hohe Gewichte auf Kosten der Technik.',
                        'consequence' => 'Zielmuskulatur wird schlechter belastet, Verletzungsrisiko steigt.',
                        'solution' => 'Saubere Technik und voller Bewegungsumfang haben immer Vorrang vor Gewicht.',
                        'example' => 'Gewicht reduzieren, wenn Schwung oder verkürzte Bewegung nötig wird.',
                    ],
                    [
                        'title' => 'Zu viel Trainingsvolumen, zu wenig Erholung',
                        'problem' => 'Täglich hartes Training ohne ausreichende Pausen.',
                        'consequence' => 'Überlastung, Leistungsabfall, stagnierender Muskelaufbau.',
                        'solution' => 'Halte dich an strukturierte Trainingspläne und respektiere Ruhetage.',
                        'example' => '4 fokussierte Trainingstage sind effektiver als 6 schlecht regenerierte.',
                    ],
                    [
                        'title' => 'Schlaf wird unterschätzt',
                        'problem' => 'Weniger als 6–7 Stunden Schlaf bei intensivem Training.',
                        'consequence' => 'Schlechtere Regeneration, niedrigere Testosteronwerte, geringerer Muskelaufbau.',
                        'solution' => '7–9 Stunden Schlaf pro Nacht unterstützen Hormone und Muskelwachstum.',
                        'example' => 'Studien zeigen deutlich reduzierte Muskelproteinsynthese bei Schlafmangel.',
                    ],
                    [
                        'title' => 'Ständiger Trainingsplan-Wechsel',
                        'problem' => 'Alle paar Wochen ein neues Trainingsprogramm beginnen.',
                        'consequence' => 'Keine messbare Progression, keine Anpassung, kein konstanter Muskelaufbau.',
                        'solution' => 'Bleibe mindestens 8–12 Wochen bei einem strukturierten Plan.',
                        'example' => 'Erst nach Abschluss des 12-Wochen-Zyklus bewerten und anpassen.',
                    ],
                ],
                'summary' => 'Muskelaufbau scheitert selten an fehlenden Übungen, sondern fast immer an fehlender Struktur, Regeneration und Konsequenz. Vermeidest du diese Fehler, wird Fortschritt planbar.',
            ],

            'workout' => [
                'weeks' => 12,
                'workouts_per_week' => 4,
                'duration_minutes' => 60,
                'level' => 'Anfänger bis Fortgeschritten',
                'equipment' => ['Langhantel', 'Kurzhanteln', 'Bank', 'Klimmzugstange (optional)'],

                'schedule' => [

                    [
                        'day' => 'Tag 1 – Push',
                        'focus' => 'Brust, Schultern & Trizeps',
                        'exercises' => [
                            ['name' => 'Bankdrücken', 'sets' => 4, 'reps' => '6–10', 'rest' => '120s'],
                            ['name' => 'Schrägbank Kurzhanteldrücken', 'sets' => 3, 'reps' => '8–12', 'rest' => '90s'],
                            ['name' => 'Schulterdrücken', 'sets' => 3, 'reps' => '8–12', 'rest' => '90s'],
                            ['name' => 'Seitheben', 'sets' => 3, 'reps' => '12–15', 'rest' => '60s'],
                            ['name' => 'Trizepsdrücken', 'sets' => 3, 'reps' => '10–12', 'rest' => '75s'],
                        ],
                    ],

                    [
                        'day' => 'Tag 2 – Pull',
                        'focus' => 'Rücken & Bizeps',
                        'exercises' => [
                            ['name' => 'Klimmzüge', 'sets' => 4, 'reps' => '6–10', 'rest' => '120s'],
                            ['name' => 'Langhantel Rudern', 'sets' => 3, 'reps' => '8–12', 'rest' => '90s'],
                            ['name' => 'Latzug (oder Bandzug)', 'sets' => 3, 'reps' => '10–12', 'rest' => '75s'],
                            ['name' => 'Face Pulls', 'sets' => 3, 'reps' => '12–15', 'rest' => '60s'],
                            ['name' => 'Bizepscurls', 'sets' => 3, 'reps' => '10–12', 'rest' => '60s'],
                        ],
                    ],

                    [
                        'day' => 'Tag 3 – Beine',
                        'focus' => 'Quadrizeps, Gesäß & Beinbeuger',
                        'exercises' => [
                            ['name' => 'Kniebeugen', 'sets' => 4, 'reps' => '6–10', 'rest' => '120s'],
                            ['name' => 'Rumänisches Kreuzheben', 'sets' => 3, 'reps' => '8–12', 'rest' => '90s'],
                            ['name' => 'Ausfallschritte', 'sets' => 3, 'reps' => '10 pro Bein', 'rest' => '75s'],
                            ['name' => 'Beinbeuger (Band oder Maschine)', 'sets' => 3, 'reps' => '10–12', 'rest' => '75s'],
                            ['name' => 'Wadenheben', 'sets' => 4, 'reps' => '12–15', 'rest' => '60s'],
                        ],
                    ],

                    [
                        'day' => 'Tag 4 – Core & Schultergesundheit',
                        'focus' => 'Rumpfstabilität & Prävention',
                        'exercises' => [
                            ['name' => 'Plank', 'sets' => 3, 'reps' => '45–60s', 'rest' => '45s'],
                            ['name' => 'Hanging Leg Raises / Dead Bug', 'sets' => 3, 'reps' => '10–15', 'rest' => '45s'],
                            ['name' => 'Face Pulls', 'sets' => 3, 'reps' => '12–15', 'rest' => '60s'],
                            ['name' => 'Außenrotation Schulter (Band)', 'sets' => 2, 'reps' => '15–20', 'rest' => '45s'],
                        ],
                    ],

                ],

                'progression' => 'Woche 1–4: Technik & moderates Volumen | Woche 5–8: Gewichte steigern | Woche 9–12: höheres Volumen & Intensität (RIR 1–2)',
                'tips' => [
                    'Kalorienüberschuss von 300–500 kcal einhalten',
                    'Proteinaufnahme 2,0–2,2 g pro kg Körpergewicht',
                    'Gewichte oder Wiederholungen wöchentlich steigern',
                    'Regeneration ernst nehmen (Schlaf & Pausen)',
                ],
            ],

            'faqs' => [
                [
                    'question' => 'Wie oft sollte ich für Muskelaufbau trainieren?',
                    'answer' => '4 Trainingseinheiten pro Woche sind ideal, um ausreichend Volumen zu erreichen und gleichzeitig gut zu regenerieren.',
                ],
                [
                    'question' => 'Wie viel Protein brauche ich für Muskelaufbau?',
                    'answer' => 'Empfohlen sind etwa 2,0–2,2 Gramm Protein pro Kilogramm Körpergewicht täglich.',
                ],
                [
                    'question' => 'Wann sehe ich Muskelwachstum?',
                    'answer' => 'Kraftsteigerungen treten oft nach 2–3 Wochen auf, sichtbare Muskelzuwächse meist nach 6–8 Wochen.',
                ],
            ],
        ],

        /* ============================
           Anfänger
        ============================ */
        'anfaenger' => [
            'title' => 'Trainingsplan für Anfänger – 6 Wochen',
            'description' => 'Der perfekte Einstieg ins Training. Sicher, strukturiert und nachhaltig – ideal für Anfänger ohne Vorerfahrung.',
            'h1' => 'Trainingsplan für Anfänger – sicher & effektiv starten',
            'intro' => 'Dieser 6-Wochen-Anfängerplan hilft dir, grundlegende Bewegungen zu erlernen, Kraft aufzubauen und eine stabile Trainingsroutine zu entwickeln.',
            'internal_type' => 'beginner',
            'published_at' => '2025-12-24',
            'last_updated_at' => '2025-12-24',

            'why_it_works' => [
                'title' => 'Warum dieser Trainingsplan für Anfänger funktioniert',
                'content' => [
                    [
                        'heading' => 'Sicherer Einstieg ohne Überforderung',
                        'text' => 'Der Plan ist speziell für Einsteiger konzipiert. Die Übungen sind technisch einfach, gut skalierbar und belasten Gelenke sowie das Nervensystem nicht übermäßig. So kann sich dein Körper schrittweise an Bewegung und Krafttraining gewöhnen.',
                    ],
                    [
                        'heading' => 'Grundbewegungen statt komplizierter Übungen',
                        'text' => 'Statt isolierter oder komplexer Übungen setzt der Plan auf grundlegende Bewegungsmuster wie Kniebeugen, Drücken, Ziehen und Stabilisation. Diese bilden die Basis für jedes weitere Training und verbessern Kraft, Koordination und Körpergefühl.',
                    ],
                    [
                        'heading' => 'Optimale Trainingsfrequenz für Anpassung',
                        'text' => 'Mit 2–3 Trainingseinheiten pro Woche erhält dein Körper genügend Reize, ohne überfordert zu werden. Studien zeigen, dass diese Frequenz für Anfänger ideal ist, um Fortschritte zu erzielen und gleichzeitig ausreichend zu regenerieren.',
                    ],
                    [
                        'heading' => 'Progression ohne Leistungsdruck',
                        'text' => 'Der Plan steigert sich behutsam über Wiederholungen, Dauer oder Übungsvarianten – nicht über hohe Gewichte. So entsteht Fortschritt ohne Stress oder Verletzungsrisiko.',
                    ],
                    [
                        'heading' => 'Fokus auf Gewohnheiten statt Perfektion',
                        'text' => 'Langfristiger Trainingserfolg entsteht durch Regelmäßigkeit. Dieser Plan hilft dir, eine feste Trainingsroutine aufzubauen – ein entscheidender Faktor für nachhaltige Fitness.',
                    ],
                ],
            ],

            'common_mistakes' => [
                'title' => 'Die häufigsten Anfängerfehler – und wie du sie vermeidest',
                'mistakes' => [
                    [
                        'title' => 'Zu schnell zu viel wollen',
                        'problem' => 'Motiviert starten viele Anfänger mit zu hoher Intensität oder zu vielen Einheiten.',
                        'consequence' => 'Muskelkater, Erschöpfung oder Frust führen oft dazu, dass das Training wieder abgebrochen wird.',
                        'solution' => 'Starte bewusst langsam und halte dich an den Plan – Fortschritt kommt automatisch.',
                        'example' => 'Lieber 3 kurze Einheiten pro Woche als 5 überambitionierte.',
                    ],
                    [
                        'title' => 'Falsche oder unsaubere Technik',
                        'problem' => 'Übungen werden ohne Körperkontrolle oder mit Schwung ausgeführt.',
                        'consequence' => 'Geringerer Trainingseffekt und erhöhtes Verletzungsrisiko.',
                        'solution' => 'Führe jede Bewegung kontrolliert und sauber aus – Qualität vor Quantität.',
                        'example' => 'Lieber 10 saubere Kniebeugen als 20 unsaubere.',
                    ],
                    [
                        'title' => 'Zu seltenes Training',
                        'problem' => 'Große Pausen zwischen den Einheiten verhindern Anpassung.',
                        'consequence' => 'Der Körper beginnt jedes Mal wieder bei Null.',
                        'solution' => 'Plane feste Trainingstage pro Woche ein.',
                        'example' => 'Montag, Mittwoch, Freitag als feste Termine.',
                    ],
                    [
                        'title' => 'Zu wenig Erholung',
                        'problem' => 'Kein Schlaf oder Training an aufeinanderfolgenden Tagen ohne Pause.',
                        'consequence' => 'Müdigkeit, Leistungsabfall und fehlende Motivation.',
                        'solution' => 'Mindestens ein Ruhetag zwischen den Einheiten einhalten.',
                        'example' => 'Training jeden zweiten Tag.',
                    ],
                    [
                        'title' => 'Ungeduld bei Ergebnissen',
                        'problem' => 'Erwartung sichtbarer Veränderungen nach wenigen Tagen.',
                        'consequence' => 'Frust und vorzeitiger Abbruch.',
                        'solution' => 'Konzentriere dich auf Energie, Beweglichkeit und Routine – sichtbare Ergebnisse folgen.',
                        'example' => 'Erste Verbesserungen nach 2–3 Wochen sind völlig normal.',
                    ],
                ],
                'summary' => 'Als Anfänger zählt nicht Perfektion, sondern Kontinuität. Wer diese typischen Fehler vermeidet, legt die beste Grundlage für langfristige Fitness.',
            ],

            'workout' => [
                'weeks' => 6,
                'workouts_per_week' => 3,
                'duration_minutes' => 30,
                'level' => 'Anfänger',
                'equipment' => ['Keine', 'Optional: Matte oder Widerstandsband'],

                'schedule' => [

                    [
                        'day' => 'Tag 1 – Ganzkörper Basics',
                        'focus' => 'Grundbewegungen & Körperspannung',
                        'exercises' => [
                            ['name' => 'Kniebeugen', 'sets' => 2, 'reps' => '10–12', 'rest' => '90s'],
                            ['name' => 'Wandliegestütze', 'sets' => 2, 'reps' => '8–10', 'rest' => '90s'],
                            ['name' => 'Glute Bridges', 'sets' => 2, 'reps' => '12–15', 'rest' => '75s'],
                            ['name' => 'Plank', 'sets' => 2, 'reps' => '20–30s', 'rest' => '60s'],
                        ],
                    ],

                    [
                        'day' => 'Tag 2 – Ganzkörper Kraft',
                        'focus' => 'Haltung, Rücken & Core',
                        'exercises' => [
                            ['name' => 'Rudern mit Band oder Handtuch', 'sets' => 2, 'reps' => '10–12', 'rest' => '75s'],
                            ['name' => 'Ausfallschritte', 'sets' => 2, 'reps' => '8 pro Bein', 'rest' => '75s'],
                            ['name' => 'Schulterdrücken (leicht)', 'sets' => 2, 'reps' => '10–12', 'rest' => '75s'],
                            ['name' => 'Dead Bug', 'sets' => 2, 'reps' => '8–10', 'rest' => '60s'],
                        ],
                    ],

                    [
                        'day' => 'Tag 3 – Ganzkörper & Mobility',
                        'focus' => 'Beweglichkeit & Technik',
                        'exercises' => [
                            ['name' => 'Kniebeugen (langsames Tempo)', 'sets' => 2, 'reps' => '10', 'rest' => '90s'],
                            ['name' => 'Liegestütze erhöht (z. B. Bank)', 'sets' => 2, 'reps' => '8–10', 'rest' => '90s'],
                            ['name' => 'Bird Dog', 'sets' => 2, 'reps' => '8 pro Seite', 'rest' => '60s'],
                            ['name' => 'Mobility Flow (Ganzkörper)', 'sets' => 1, 'reps' => '8–10 Min', 'rest' => '-'],
                        ],
                    ],

                ],

                'progression' => 'Woche 1–2: Bewegungen lernen | Woche 3–4: Wiederholungen steigern | Woche 5–6: mehr Kontrolle & Spannung',
                'tips' => [
                    'Saubere Technik ist wichtiger als Intensität',
                    'Mindestens ein Ruhetag zwischen den Einheiten',
                    'Regelmäßigkeit schlägt Perfektion',
                ],
            ],

            'faqs' => [
                [
                    'question' => 'Wie oft sollten Anfänger trainieren?',
                    'answer' => '2–3 Trainingseinheiten pro Woche sind ideal, um Fortschritte zu machen und gleichzeitig ausreichend zu regenerieren.',
                ],
                [
                    'question' => 'Brauche ich ein Fitnessstudio?',
                    'answer' => 'Nein. Dieser Plan ist so aufgebaut, dass du komplett ohne Geräte oder im Home-Workout starten kannst.',
                ],
                [
                    'question' => 'Wann sehe ich erste Fortschritte?',
                    'answer' => 'Viele Anfänger spüren bereits nach 1–2 Wochen mehr Energie, bessere Beweglichkeit und steigende Kraft.',
                ],
            ],
        ],

        /* ============================
           Zuhause
        ============================ */
        'zuhause' => [
            'title' => 'Trainingsplan für Zuhause – Ohne Geräte effektiv trainieren',
            'description' => 'Effektives Home Workout ohne Geräte. Bodyweight Training für jedes Fitnesslevel – 8 Wochen strukturiert & kostenlos.',
            'h1' => 'Trainingsplan für Zuhause: Effektiv ohne Geräte',
            'intro' => 'Dieser 8-Wochen-Trainingsplan zeigt dir, wie du mit reinem Körpergewicht zuhause Kraft, Ausdauer und Muskulatur aufbaust – ganz ohne Fitnessstudio.',
            'internal_type' => 'home',
            'published_at' => '2025-12-24',
            'last_updated_at' => '2025-12-24',

            'why_it_works' => [
                'title' => 'Warum dieser Trainingsplan für Zuhause funktioniert',
                'content' => [
                    [
                        'heading' => 'Körpergewichtstraining ist hochwirksam',
                        'text' => 'Dieser Plan nutzt dein eigenes Körpergewicht als Widerstand. Studien zeigen, dass Bodyweight-Training Kraft, Muskelspannung und Ausdauer effektiv verbessert – vorausgesetzt, Intensität und Ausführung stimmen.',
                    ],
                    [
                        'heading' => 'Ganzkörperbelastung ohne Geräte',
                        'text' => 'Durch Mehrgelenksübungen wie Liegestütze, Squats und Ausfallschritte werden mehrere Muskelgruppen gleichzeitig trainiert. Das erhöht den Kalorienverbrauch und spart Zeit – ideal für Home Workouts.',
                    ],
                    [
                        'heading' => 'Progression ohne zusätzliche Gewichte',
                        'text' => 'Der Plan steigert sich über Wiederholungen, Tempo, Pausenlänge und Übungsvarianten. So erzielst du Fortschritt, auch ohne Hanteln oder Maschinen.',
                    ],
                    [
                        'heading' => 'Konstante Trainingsfrequenz ohne Hürden',
                        'text' => 'Da du weder Anfahrt noch Equipment brauchst, fällt die größte Trainingshürde weg. Das erhöht die Wahrscheinlichkeit, dass du regelmäßig trainierst – der wichtigste Faktor für Ergebnisse.',
                    ],
                    [
                        'heading' => 'Gelenkschonend und alltagstauglich',
                        'text' => 'Alle Übungen lassen sich an dein Fitnesslevel anpassen. Dadurch ist der Plan sowohl für Anfänger als auch für Fortgeschrittene geeignet – ohne unnötiges Verletzungsrisiko.',
                    ],
                ],
            ],

            'common_mistakes' => [
                'title' => 'Die häufigsten Fehler beim Training zuhause – und wie du sie vermeidest',
                'mistakes' => [
                    [
                        'title' => 'Zu geringe Intensität',
                        'problem' => 'Viele unterschätzen Bodyweight-Training und trainieren zu locker.',
                        'consequence' => 'Der Trainingsreiz ist zu gering, Fortschritte bleiben aus.',
                        'solution' => 'Trainiere kontrolliert, halte Spannungen und verkürze Pausen.',
                        'example' => 'Langsame Liegestütze mit Körperspannung statt schnelles „Abspulen“.',
                    ],
                    [
                        'title' => 'Fehlende Progression',
                        'problem' => 'Wochenlang dieselben Übungen mit gleicher Intensität.',
                        'consequence' => 'Der Körper passt sich an, Fortschritt stagniert.',
                        'solution' => 'Steigere Wiederholungen, Tempo oder wähle schwierigere Varianten.',
                        'example' => 'Von normalen Squats zu Squats mit Pause unten.',
                    ],
                    [
                        'title' => 'Ablenkung während des Trainings',
                        'problem' => 'Training nebenbei mit Handy, Fernsehen oder Unterbrechungen.',
                        'consequence' => 'Geringerer Trainingseffekt und längere Einheiten.',
                        'solution' => 'Plane feste, ungestörte Trainingszeiten.',
                        'example' => '30 Minuten Fokus-Training statt 60 Minuten mit Ablenkung.',
                    ],
                    [
                        'title' => 'Unsaubere Technik',
                        'problem' => 'Bewegungen werden schnell und ohne Kontrolle ausgeführt.',
                        'consequence' => 'Erhöhtes Verletzungsrisiko und geringere Wirkung.',
                        'solution' => 'Saubere Technik hat Priorität – auch ohne Spiegel.',
                        'example' => 'Plank mit Körperspannung statt durchhängendem Rücken.',
                    ],
                    [
                        'title' => 'Zu wenig Regeneration',
                        'problem' => 'Tägliches Training ohne Pausen.',
                        'consequence' => 'Erschöpfung, Leistungsabfall, Motivationsverlust.',
                        'solution' => 'Mindestens 1–2 Resttage pro Woche einplanen.',
                        'example' => '4 Trainingstage + 3 aktive Erholungstage.',
                    ],
                ],
                'summary' => 'Zuhause erfolgreich zu trainieren hängt nicht von Geräten ab, sondern von Struktur, Intensität und Konsequenz. Wer diese Fehler vermeidet, erzielt auch ohne Fitnessstudio starke Ergebnisse.',
            ],

            'workout' => [
                'weeks' => 8,
                'workouts_per_week' => 4,
                'duration_minutes' => 40,
                'level' => 'Alle Level',
                'equipment' => ['Keine'],
                'schedule' => [

                    [
                        'day' => 'Tag 1 – Push',
                        'focus' => 'Brust, Schultern, Trizeps',
                        'exercises' => [
                            ['name' => 'Liegestütze', 'sets' => 4, 'reps' => '10–15', 'rest' => '60s'],
                            ['name' => 'Enge Liegestütze', 'sets' => 3, 'reps' => '8–12', 'rest' => '60s'],
                            ['name' => 'Pike Push-ups', 'sets' => 3, 'reps' => '8–10', 'rest' => '60s'],
                            ['name' => 'Dips am Stuhl', 'sets' => 3, 'reps' => '10–12', 'rest' => '60s'],
                            ['name' => 'Plank Shoulder Taps', 'sets' => 3, 'reps' => '20', 'rest' => '45s'],
                        ],
                    ],

                    [
                        'day' => 'Tag 2 – Pull & Core',
                        'focus' => 'Rücken, hintere Schulter & Bauch',
                        'exercises' => [
                            ['name' => 'Reverse Snow Angels', 'sets' => 3, 'reps' => '12–15', 'rest' => '60s'],
                            ['name' => 'Superman Hold', 'sets' => 3, 'reps' => '30–40s', 'rest' => '45s'],
                            ['name' => 'Reverse Plank', 'sets' => 3, 'reps' => '30–45s', 'rest' => '45s'],
                            ['name' => 'Dead Bug', 'sets' => 3, 'reps' => '10 pro Seite', 'rest' => '45s'],
                            ['name' => 'Russian Twists', 'sets' => 3, 'reps' => '20', 'rest' => '45s'],
                        ],
                    ],

                    [
                        'day' => 'Tag 3 – Beine & Gesäß',
                        'focus' => 'Unterkörper Kraft & Stabilität',
                        'exercises' => [
                            ['name' => 'Kniebeugen', 'sets' => 4, 'reps' => '15–20', 'rest' => '60s'],
                            ['name' => 'Ausfallschritte', 'sets' => 3, 'reps' => '10 pro Bein', 'rest' => '60s'],
                            ['name' => 'Bulgarian Split Squats', 'sets' => 3, 'reps' => '8–10 pro Bein', 'rest' => '75s'],
                            ['name' => 'Glute Bridges', 'sets' => 3, 'reps' => '15–20', 'rest' => '45s'],
                            ['name' => 'Wandsitzen', 'sets' => 2, 'reps' => '45–60s', 'rest' => '60s'],
                        ],
                    ],

                    [
                        'day' => 'Tag 4 – Conditioning & Mobility',
                        'focus' => 'Fettverbrennung, Herz-Kreislauf & Beweglichkeit',
                        'exercises' => [
                            ['name' => 'Jumping Jacks', 'sets' => 4, 'reps' => '30s', 'rest' => '30s'],
                            ['name' => 'Mountain Climbers', 'sets' => 4, 'reps' => '30s', 'rest' => '30s'],
                            ['name' => 'High Knees', 'sets' => 3, 'reps' => '30s', 'rest' => '30s'],
                            ['name' => 'Dynamic Stretching Flow', 'sets' => 1, 'reps' => '8–10 Min', 'rest' => '-'],
                            ['name' => 'Atemübungen', 'sets' => 1, 'reps' => '3–5 Min', 'rest' => '-'],
                        ],
                    ],
                ],

                'progression' => 'Woche 1–2: Technik & Volumen | Woche 3–5: Wiederholungen steigern | Woche 6–8: schwierigere Varianten & Tempo',
                'tips' => [
                    'Fokus auf saubere Technik',
                    'Langsame exzentrische Phase erhöht Trainingsreiz',
                    'Mindestens 1 Resttag pro Woche einplanen',
                ],
            ],

            'faqs' => [
                [
                    'question' => 'Kann ich ohne Geräte wirklich Muskeln aufbauen?',
                    'answer' => 'Ja. Durch progressive Überlastung (mehr Wiederholungen, langsameres Tempo, schwierigere Varianten) ist effektiver Muskelaufbau mit Bodyweight möglich.',
                ],
                [
                    'question' => 'Wie lange dauert ein Home Workout?',
                    'answer' => 'Die Einheiten dauern ca. 35–45 Minuten und sind ideal in den Alltag integrierbar.',
                ],
            ],
        ],

        /* ============================
           Frauen
        ============================ */
        'frauen' => [
            'title' => 'Trainingsplan für Frauen – Gezielt & Effektiv',
            'description' => 'Strukturierter Kraft- & Cardio-Trainingsplan speziell für Frauen. Fokus auf Straffung, Definition & Fettabbau – 8 Wochen.',
            'h1' => 'Trainingsplan für Frauen: Straff & Stark',
            'intro' => 'Dieser 8-Wochen-Trainingsplan kombiniert Krafttraining und Cardio gezielt für Straffung, Definition und einen starken, femininen Körper.',
            'internal_type' => 'women',
            'published_at' => '2025-12-24',
            'last_updated_at' => '2025-12-24',

            'why_it_works' => [
                'title' => 'Warum dieser Trainingsplan für Frauen funktioniert',
                'content' => [
                    [
                        'heading' => 'Krafttraining formt statt aufzublähen',
                        'text' => 'Frauen haben deutlich niedrigere Testosteronwerte als Männer. Krafttraining führt daher nicht zu „massigen“ Muskeln, sondern zu Straffung, Definition und einer verbesserten Körperform. Genau darauf ist dieser Plan ausgelegt.',
                    ],
                    [
                        'heading' => 'Gezielte Übungsauswahl für typische Zielzonen',
                        'text' => 'Der Trainingsplan setzt Schwerpunkte auf Beine, Gesäß, Core und Oberkörper. Diese Muskelgruppen beeinflussen Haltung, Figur und Kraft im Alltag besonders stark.',
                    ],
                    [
                        'heading' => 'Kombination aus Kraft und Cardio',
                        'text' => 'Krafttraining erhöht den Grundumsatz und formt den Körper, Cardio unterstützt die Fettverbrennung. Die Kombination sorgt für sichtbare Ergebnisse ohne extremes Training.',
                    ],
                    [
                        'heading' => 'Hormonfreundliche Trainingsstruktur',
                        'text' => 'Moderate Intensität, ausreichende Pausen und sinnvolle Trainingsfrequenz unterstützen einen stabilen Hormonhaushalt – besonders wichtig für langfristige Ergebnisse und Wohlbefinden.',
                    ],
                    [
                        'heading' => 'Stärkung von Selbstvertrauen und Körpergefühl',
                        'text' => 'Regelmäßiges Krafttraining verbessert nicht nur die körperliche Leistungsfähigkeit, sondern auch das Selbstbewusstsein. Viele Frauen berichten von mehr Energie, besserer Haltung und höherem Körpervertrauen.',
                    ],
                ],
            ],

            'common_mistakes' => [
                'title' => 'Die häufigsten Trainingsfehler bei Frauen – und wie du sie vermeidest',
                'mistakes' => [
                    [
                        'title' => 'Angst vor Krafttraining',
                        'problem' => 'Viele Frauen meiden Gewichte aus Sorge, zu muskulös zu werden.',
                        'consequence' => 'Der Körper wird zwar leichter, aber nicht straffer oder stärker.',
                        'solution' => 'Krafttraining bewusst integrieren – es ist der Schlüssel zu Form und Stabilität.',
                        'example' => '2–3 Krafteinheiten pro Woche statt ausschließlich Cardio.',
                    ],
                    [
                        'title' => 'Zu viel Cardio, zu wenig Kraft',
                        'problem' => 'Stundenlanges Ausdauertraining ohne Krafttraining.',
                        'consequence' => 'Muskelabbau, stagnierender Stoffwechsel und wenig Körperform.',
                        'solution' => 'Krafttraining priorisieren, Cardio ergänzend einsetzen.',
                        'example' => '3 Kraft-Einheiten + 1–2 lockere Cardio-Sessions.',
                    ],
                    [
                        'title' => 'Zu geringe Trainingsintensität',
                        'problem' => 'Sehr leichte Gewichte oder kaum muskuläre Ermüdung.',
                        'consequence' => 'Der Trainingsreiz reicht nicht aus, um Veränderungen auszulösen.',
                        'solution' => 'Übungen sollten fordern, aber technisch sauber bleiben.',
                        'example' => 'Letzte 2 Wiederholungen sollten anstrengend sein.',
                    ],
                    [
                        'title' => 'Vernachlässigung des Oberkörpers',
                        'problem' => 'Fokus nur auf Beine und Po.',
                        'consequence' => 'Haltungsprobleme, Schulter- und Nackenschmerzen.',
                        'solution' => 'Oberkörpertraining gezielt integrieren.',
                        'example' => 'Rudern, Schulterdrücken und Planks regelmäßig einbauen.',
                    ],
                    [
                        'title' => 'Zu wenig Regeneration',
                        'problem' => 'Training trotz Erschöpfung oder Schlafmangel.',
                        'consequence' => 'Leistungsabfall, hormonelle Dysbalance, Demotivation.',
                        'solution' => 'Ausreichend Schlaf und Ruhetage einplanen.',
                        'example' => 'Mindestens 1–2 trainingsfreie Tage pro Woche.',
                    ],
                ],
                'summary' => 'Erfolgreiches Training für Frauen bedeutet nicht „mehr Cardio“, sondern smartere Kombination aus Kraft, Bewegung und Regeneration.',
            ],

            'workout' => [
                'weeks' => 8,
                'workouts_per_week' => 4,
                'duration_minutes' => 45,
                'level' => 'Alle Level',
                'equipment' => ['Kurzhanteln (optional)', 'Matte'],

                'schedule' => [

                    [
                        'day' => 'Tag 1 – Unterkörper & Po',
                        'focus' => 'Straffung & Kraft im Unterkörper',
                        'exercises' => [
                            ['name' => 'Squats', 'sets' => 4, 'reps' => '12–15', 'rest' => '60s'],
                            ['name' => 'Hip Thrusts', 'sets' => 3, 'reps' => '15', 'rest' => '60s'],
                            ['name' => 'Ausfallschritte', 'sets' => 3, 'reps' => '10 pro Bein', 'rest' => '60s'],
                            ['name' => 'Glute Bridges', 'sets' => 3, 'reps' => '20', 'rest' => '45s'],
                            ['name' => 'Wandsitzen', 'sets' => 2, 'reps' => '45–60s', 'rest' => '60s'],
                        ],
                    ],

                    [
                        'day' => 'Tag 2 – Oberkörper & Core',
                        'focus' => 'Haltung, Arme & Bauch',
                        'exercises' => [
                            ['name' => 'Kurzhantel Rudern', 'sets' => 3, 'reps' => '12–15', 'rest' => '60s'],
                            ['name' => 'Schulterdrücken', 'sets' => 3, 'reps' => '10–12', 'rest' => '60s'],
                            ['name' => 'Liegestütze (knie)', 'sets' => 3, 'reps' => '8–12', 'rest' => '60s'],
                            ['name' => 'Plank', 'sets' => 3, 'reps' => '30–45s', 'rest' => '45s'],
                            ['name' => 'Dead Bug', 'sets' => 3, 'reps' => '10 pro Seite', 'rest' => '45s'],
                        ],
                    ],

                    [
                        'day' => 'Tag 3 – Cardio & Fettverbrennung',
                        'focus' => 'Kalorienverbrauch & Ausdauer',
                        'exercises' => [
                            ['name' => 'Jumping Jacks', 'sets' => 4, 'reps' => '30s', 'rest' => '30s'],
                            ['name' => 'High Knees', 'sets' => 4, 'reps' => '30s', 'rest' => '30s'],
                            ['name' => 'Mountain Climbers', 'sets' => 3, 'reps' => '30s', 'rest' => '30s'],
                            ['name' => 'Bodyweight Squats', 'sets' => 3, 'reps' => '20', 'rest' => '45s'],
                        ],
                    ],

                    [
                        'day' => 'Tag 4 – Ganzkörper & Mobility',
                        'focus' => 'Form, Balance & Regeneration',
                        'exercises' => [
                            ['name' => 'Goblet Squats', 'sets' => 3, 'reps' => '12', 'rest' => '60s'],
                            ['name' => 'Step-Ups', 'sets' => 3, 'reps' => '10 pro Bein', 'rest' => '60s'],
                            ['name' => 'Bird Dog', 'sets' => 3, 'reps' => '10 pro Seite', 'rest' => '45s'],
                            ['name' => 'Dynamic Stretching', 'sets' => 1, 'reps' => '8–10 Min', 'rest' => '-'],
                        ],
                    ],
                ],

                'progression' => 'Woche 1–2 Technik & Grundlagentraining | Woche 3–5 Wiederholungen steigern | Woche 6–8 Intensität & Kontrolle',
                'tips' => [
                    'Krafttraining formt den Körper – keine Angst vor Gewichten',
                    'Kombination aus Kraft & Cardio bringt beste Resultate',
                    'Regelmäßigkeit ist wichtiger als Perfektion',
                ],
            ],

            'faqs' => [
                [
                    'question' => 'Werde ich durch Krafttraining zu muskulös?',
                    'answer' => 'Nein. Frauen haben deutlich weniger Testosteron. Krafttraining strafft, definiert und formt den Körper, ohne „bulky“ zu machen.',
                ],
                [
                    'question' => 'Wie schnell sehe ich Ergebnisse?',
                    'answer' => 'Mehr Energie und Kraft meist nach 2–3 Wochen, sichtbare Straffung nach etwa 4–6 Wochen.',
                ],
            ],
        ],

        /* ============================
           NEUJAHR
        ============================ */
        'neujahrs-trainingsplan' => [
            'title' => 'Neujahrs Trainingsplan – 6 Wochen Fitness Reset',
            'description' => 'Der ideale Trainingsplan für deinen Neustart ins neue Jahr. ✓ 6 Wochen Reset ✓ Kraft, Cardio & Mobility ✓ Nachhaltig & sicher',
            'h1' => 'Neujahrs Trainingsplan: Dein 6-Wochen Fitness Reset',
            'intro' => 'Dieser 6-Wochen Fitness Reset hilft dir, nach Pausen oder einem Neustart wieder in eine nachhaltige Trainingsroutine zu finden. Fokus auf Kraft, Ausdauer, Beweglichkeit und langfristigen Fortschritt.',
            'internal_type' => 'new_year_reset',
            'published_at' => '2025-12-24',
            'last_updated_at' => '2025-12-24',
            'why_it_works' => [
                'title' => 'Warum der Neujahrs-Trainingsplan funktioniert',
                'content' => [
                    [
                        'heading' => 'Reset statt Überforderung',
                        'text' => 'Nach längeren Pausen braucht der Körper keinen Extremplan, sondern Struktur. Dieser 6-Wochen-Reset setzt bewusst auf kontrollierte Belastung, um Kraft, Ausdauer und Beweglichkeit wieder aufzubauen – ohne Verletzungsrisiko.',
                    ],
                    [
                        'heading' => 'Feste Struktur schlägt Motivation',
                        'text' => 'Motivation schwankt, Routinen bleiben. Mit klar definierten Trainingstagen und überschaubarem Umfang wird Training zu einem festen Bestandteil deines Alltags – unabhängig von Tagesform.',
                    ],
                    [
                        'heading' => 'Ganzheitlicher Ansatz',
                        'text' => 'Der Plan kombiniert Krafttraining, Cardio, Core-Stabilität und Mobility. So werden nicht nur Muskeln aufgebaut, sondern auch Herz-Kreislauf-System, Gelenke und Beweglichkeit verbessert.',
                    ],
                    [
                        'heading' => 'Progression ohne Leistungsdruck',
                        'text' => 'Die Intensität steigt schrittweise über Volumen, Übungsauswahl und Belastung. Das verhindert Plateaus und sorgt für messbare Fortschritte – auch nach längeren Trainingspausen.',
                    ],
                    [
                        'heading' => 'Ideal für Körperkomposition',
                        'text' => 'Krafttraining erhält Muskulatur, Cardio erhöht den Kalorienverbrauch. In Kombination mit moderater Ernährung unterstützt der Plan gleichzeitig Fettabbau und Muskelaufbau.',
                    ],
                ],
            ],

            'common_mistakes' => [
                'title' => 'Die häufigsten Fehler beim Neustart – und wie du sie vermeidest',
                'mistakes' => [
                    [
                        'title' => 'Zu ambitionierter Start',
                        'problem' => 'Viele starten im Januar mit 5–6 Trainingstagen pro Woche.',
                        'consequence' => 'Überlastung, Muskelkater, Motivationseinbruch nach 1–2 Wochen.',
                        'solution' => 'Starte kontrolliert mit 3–4 Einheiten pro Woche.',
                        'example' => 'Lieber konstant 4 Wochen trainieren als nach 10 Tagen abbrechen.',
                    ],
                    [
                        'title' => 'Alles auf einmal ändern',
                        'problem' => 'Training, Diät, Schlaf, Alltag – alles gleichzeitig.',
                        'consequence' => 'Überforderung und schnelle Erschöpfung.',
                        'solution' => 'Fokus zuerst auf Training, danach Ernährung optimieren.',
                        'example' => 'Erst Trainingsroutine etablieren, dann Kalorien feinjustieren.',
                    ],
                    [
                        'title' => 'Fehlende Regeneration',
                        'problem' => 'Kein Ruhetag trotz Trainingspause in den letzten Monaten.',
                        'consequence' => 'Leistungsabfall, Verletzungsrisiko, Müdigkeit.',
                        'solution' => 'Regeneration als festen Bestandteil des Plans sehen.',
                        'example' => 'Mindestens 1 Mobility- oder Resttag pro Woche.',
                    ],
                    [
                        'title' => 'Unrealistische Erwartungen',
                        'problem' => 'Erwartung sichtbarer Transformation nach 1–2 Wochen.',
                        'consequence' => 'Frust und Abbruch.',
                        'solution' => 'Fortschritt an Energie, Routine und Leistungsfähigkeit messen.',
                        'example' => 'Besser schlafen, stärker fühlen, mehr Bewegung im Alltag.',
                    ],
                    [
                        'title' => 'Kein klarer Trainingsrhythmus',
                        'problem' => 'Training „wenn Zeit ist“.',
                        'consequence' => 'Unregelmäßigkeit und fehlende Anpassung.',
                        'solution' => 'Feste Trainingstage einplanen.',
                        'example' => 'Montag, Mittwoch, Freitag als feste Termine.',
                    ],
                ],
                'summary' => 'Ein erfolgreicher Neustart scheitert selten am Willen, sondern an falschen Erwartungen. Dieser Plan setzt auf Struktur, Geduld und nachhaltigen Fortschritt.',
            ],

            'workout' => [
                'weeks' => 6,
                'workouts_per_week' => 4,
                'duration_minutes' => 40,
                'level' => 'Anfänger bis Fortgeschritten',
                'equipment' => ['Eigengewicht', 'Matte'],

                'schedule' => [

                    [
                        'day' => 'Tag 1 – Ganzkörper Kraft',
                        'focus' => 'Grundkraft & Stoffwechsel aktivieren',
                        'exercises' => [
                            ['name' => 'Kniebeugen', 'sets' => 3, 'reps' => '12–15', 'rest' => '60s'],
                            ['name' => 'Liegestütze (knie)', 'sets' => 3, 'reps' => '8–12', 'rest' => '60s'],
                            ['name' => 'Rudern am Tisch / Band', 'sets' => 3, 'reps' => '10–12', 'rest' => '60s'],
                            ['name' => 'Plank', 'sets' => 3, 'reps' => '30–45s', 'rest' => '45s'],
                        ],
                    ],

                    [
                        'day' => 'Tag 2 – Cardio & Kondition',
                        'focus' => 'Herz-Kreislauf & Fettverbrennung',
                        'exercises' => [
                            ['name' => 'Jumping Jacks', 'sets' => 4, 'reps' => '30s', 'rest' => '30s'],
                            ['name' => 'High Knees', 'sets' => 3, 'reps' => '30s', 'rest' => '30s'],
                            ['name' => 'Mountain Climbers', 'sets' => 3, 'reps' => '30s', 'rest' => '30s'],
                            ['name' => 'Bodyweight Squats', 'sets' => 3, 'reps' => '20', 'rest' => '45s'],
                        ],
                    ],

                    [
                        'day' => 'Tag 3 – Unterkörper & Core',
                        'focus' => 'Stabilität, Beine & Rumpf',
                        'exercises' => [
                            ['name' => 'Ausfallschritte', 'sets' => 3, 'reps' => '10 pro Bein', 'rest' => '60s'],
                            ['name' => 'Glute Bridges', 'sets' => 3, 'reps' => '15–20', 'rest' => '45s'],
                            ['name' => 'Dead Bug', 'sets' => 3, 'reps' => '10 pro Seite', 'rest' => '45s'],
                            ['name' => 'Seitstütz', 'sets' => 2, 'reps' => '20–30s pro Seite', 'rest' => '45s'],
                        ],
                    ],

                    [
                        'day' => 'Tag 4 – Mobility & Regeneration',
                        'focus' => 'Beweglichkeit & Verletzungsprävention',
                        'exercises' => [
                            ['name' => 'Dynamisches Ganzkörper-Stretching', 'sets' => 1, 'reps' => '10–15 Min', 'rest' => '-'],
                            ['name' => 'Hüftmobilisation', 'sets' => 1, 'reps' => '5 Min', 'rest' => '-'],
                            ['name' => 'Atemübungen', 'sets' => 1, 'reps' => '5 Min', 'rest' => '-'],
                        ],
                    ],
                ],

                'progression' => 'Woche 1–2 Grundlagen & Technik | Woche 3–4 mehr Volumen | Woche 5–6 höhere Intensität & Kontrolle',
                'tips' => [
                    'Fokus auf Regelmäßigkeit, nicht Perfektion',
                    'Kein Crash-Diäten – Erhalt oder leichtes Defizit',
                    '7–9 Stunden Schlaf unterstützen Regeneration',
                    'Trainingsfortschritt wichtiger als Waage',
                ],
            ],

            'faqs' => [
                [
                    'question' => 'Ist dieser Plan für Wiedereinsteiger geeignet?',
                    'answer' => 'Ja. Der Plan ist bewusst moderat aufgebaut und ideal für Neustarts nach Pausen oder längerer Inaktivität.',
                ],
                [
                    'question' => 'Kann ich damit gleichzeitig Fett verlieren?',
                    'answer' => 'Ja. Durch die Kombination aus Krafttraining, Cardio und Bewegung im Alltag unterstützt der Plan Fettabbau und Muskelaufbau.',
                ],
                [
                    'question' => 'Muss ich alle vier Tage trainieren?',
                    'answer' => 'Nein. Drei Einheiten reichen aus. Der vierte Tag ist optional und fokussiert auf Regeneration.',
                ],
            ],
        ],

        /* ============================
           Krafttraining
        ============================ */
        'krafttraining' => [
            'title' => 'Kostenloser Krafttrainingsplan – 10 Wochen',
            'description' => 'Kostenloser 10-Wochen-Krafttrainingsplan. Maximale Kraft mit Grundübungen, progressiver Überlastung und strukturierter Periodisierung – fürs Gym.',
            'h1' => 'Krafttrainingsplan – In 10 Wochen stärker werden',
            'intro' => 'Dieser 10-Wochen-Krafttrainingsplan konzentriert sich auf die Langhantel- und Kurzhantelübungen, die wirklich zählen. Aufgebaut auf progressiver Überlastung und einem 4-Tage-Oberkörper/Unterkörper-Split entwickelst du systematisch Ganzkörperkraft.',
            'internal_type' => 'strength',
            'published_at' => '2026-03-12',
            'last_updated_at' => '2026-03-12',

            'workout' => [
                'weeks' => 10,
                'workouts_per_week' => 4,
                'duration_minutes' => 55,
                'level' => 'Anfänger bis Fortgeschritten',
                'equipment' => ['Langhantel', 'Kurzhanteln', 'Klimmzugstange', 'Flachbank'],

                'schedule' => [

                    [
                        'day' => 'Tag 1 – Oberkörper Kraft',
                        'focus' => 'Horizontales & vertikales Drücken, oberer Rücken',
                        'exercises' => [
                            ['name' => 'Langhantel Bankdrücken', 'sets' => 4, 'reps' => '5–6', 'rest' => '120s'],
                            ['name' => 'Langhantel Schulterdrücken', 'sets' => 3, 'reps' => '6–8', 'rest' => '90s'],
                            ['name' => 'Kurzhantel Rudern', 'sets' => 3, 'reps' => '8–10', 'rest' => '90s'],
                            ['name' => 'Klimmzüge oder Latzug', 'sets' => 3, 'reps' => '6–10', 'rest' => '90s'],
                            ['name' => 'Face Pulls', 'sets' => 3, 'reps' => '12–15', 'rest' => '60s'],
                        ],
                    ],

                    [
                        'day' => 'Tag 2 – Unterkörper Kraft',
                        'focus' => 'Kniebeuge-Muster, hintere Kette, Core',
                        'exercises' => [
                            ['name' => 'Langhantel Kniebeugen', 'sets' => 4, 'reps' => '5–6', 'rest' => '120s'],
                            ['name' => 'Rumänisches Kreuzheben', 'sets' => 3, 'reps' => '8–10', 'rest' => '90s'],
                            ['name' => 'Bulgarische Split Squats', 'sets' => 3, 'reps' => '8–10 pro Bein', 'rest' => '90s'],
                            ['name' => 'Langhantel Hip Thrust', 'sets' => 3, 'reps' => '8–12', 'rest' => '90s'],
                            ['name' => 'Hängendes Beinheben', 'sets' => 3, 'reps' => '10–12', 'rest' => '60s'],
                        ],
                    ],

                    [
                        'day' => 'Tag 3 – Oberkörper Power',
                        'focus' => 'Druckkraft, Zugvolumen, Schultern',
                        'exercises' => [
                            ['name' => 'Langhantel Schulterdrücken', 'sets' => 4, 'reps' => '5–6', 'rest' => '120s'],
                            ['name' => 'Schrägbank Kurzhanteldrücken', 'sets' => 3, 'reps' => '8–10', 'rest' => '90s'],
                            ['name' => 'Langhantel Vorgebeugtes Rudern', 'sets' => 4, 'reps' => '6–8', 'rest' => '90s'],
                            ['name' => 'Kurzhantel Seitheben', 'sets' => 3, 'reps' => '12–15', 'rest' => '60s'],
                            ['name' => 'Langhantel Bizepscurls', 'sets' => 3, 'reps' => '8–10', 'rest' => '60s'],
                        ],
                    ],

                    [
                        'day' => 'Tag 4 – Unterkörper Power',
                        'focus' => 'Kreuzheben-Muster, einbeinige Kraft, Stabilität',
                        'exercises' => [
                            ['name' => 'Konventionelles Kreuzheben', 'sets' => 4, 'reps' => '4–6', 'rest' => '150s'],
                            ['name' => 'Frontkniebeugen', 'sets' => 3, 'reps' => '6–8', 'rest' => '120s'],
                            ['name' => 'Ausfallschritte im Gehen', 'sets' => 3, 'reps' => '10 pro Bein', 'rest' => '90s'],
                            ['name' => 'Wadenheben', 'sets' => 4, 'reps' => '12–15', 'rest' => '60s'],
                            ['name' => 'Plank', 'sets' => 3, 'reps' => '45–60s', 'rest' => '45s'],
                        ],
                    ],

                ],

                'progression' => 'Woche 1–3: Bewegungsabläufe erlernen, Arbeitsgewichte festlegen | Woche 4–6: Alle 1–2 Wochen 2,5–5 kg auf die Hauptübungen steigern | Woche 7–9: Intensität erhöhen, Wiederholungszahlen bei Grundübungen senken | Woche 10: Deload bei 60% Intensität zur Konsolidierung',
                'tips' => [
                    '1,6–2,2 g Protein pro kg Körpergewicht täglich für optimale Regeneration',
                    '7–9 Stunden Schlaf – Wachstumshormon wird vor allem im Tiefschlaf ausgeschüttet',
                    'Jede Einheit protokollieren: Gewicht, Wiederholungen und RPE (subjektive Belastung)',
                    'Vor den Arbeitssätzen bei Grundübungen 2–3 Aufwärmsätze mit steigendem Gewicht',
                ],
            ],

            'why_it_works' => [
                'title' => 'Warum dieser Krafttrainingsplan funktioniert',
                'content' => [
                    [
                        'heading' => 'Progressive Überlastung ist die Grundlage von Kraftzuwachs',
                        'text' => 'Kraftzuwachs entsteht nur durch systematische Steigerung der Belastung. Dieser Plan nutzt ein strukturiertes Belastungsmodell – alle 1–2 Wochen mehr Gewicht – basierend auf dem SAID-Prinzip (Specific Adaptation to Imposed Demands). Ohne progressive Überlastung hat dein Körper keinen Grund, stärker zu werden.',
                    ],
                    [
                        'heading' => 'Grundübungen aktivieren maximale Muskelmasse',
                        'text' => 'Der Plan setzt auf Mehrgelenksübungen wie Kniebeugen, Kreuzheben, Bankdrücken und Schulterdrücken. Diese Übungen aktivieren große Muskelgruppen gleichzeitig und erzeugen hohe mechanische Spannung – der wichtigste Treiber für Kraftanpassungen laut Trainingsforschung.',
                    ],
                    [
                        'heading' => 'Oberkörper/Unterkörper-Split optimiert Frequenz und Regeneration',
                        'text' => 'Jede Muskelgruppe wird zweimal pro Woche trainiert – das optimale Verhältnis aus Trainingsreiz und Erholung für Kraftentwicklung. Meta-Analysen zeigen, dass eine Frequenz von mindestens zweimal wöchentlich deutlich bessere Kraftzuwächse bringt als einmaliges Training pro Woche.',
                    ],
                    [
                        'heading' => 'Längere Pausen maximieren die Kraftleistung',
                        'text' => 'Satzpausen von 90–150 Sekunden ermöglichen eine nahezu vollständige Regeneration der Phosphokreatinspeicher – dem primären Energieträger bei maximalen Anstrengungen. Studien zeigen, dass 2–3 Minuten Pause zwischen schweren Verbundsätzen zu deutlich mehr Kraftzuwachs führen als kürzere Intervalle.',
                    ],
                    [
                        'heading' => 'Eingeplanter Deload verhindert Übertraining',
                        'text' => 'Woche 10 reduziert die Intensität auf 60% und erlaubt dem Zentralnervensystem sowie dem Bindegewebe vollständige Erholung. Periodisierungsforschung zeigt konsistent, dass geplante Deloads die langfristigen Kraftergebnisse verbessern, indem sie angestaute Ermüdung abbauen.',
                    ],
                ],
            ],

            'common_mistakes' => [
                'title' => 'Die 7 häufigsten Krafttraining-Fehler – und wie du sie vermeidest',
                'mistakes' => [
                    [
                        'title' => 'Ego-Lifting mit schlechter Form',
                        'problem' => 'Mehr Gewicht auflegen, als du technisch sauber bewältigen kannst – besonders bei Kniebeugen und Kreuzheben.',
                        'consequence' => 'Erhöhtes Verletzungsrisiko für unteren Rücken, Knie und Schultern. Weniger Muskelaktivierung, weil Schwung die kontrollierte Kraft ersetzt.',
                        'solution' => 'Nutze ein Gewicht, das du über den vollen Bewegungsumfang kontrollieren kannst. Bricht deine Form vor der letzten Wiederholung ein, ist das Gewicht zu schwer.',
                        'example' => 'Wenn deine Kniebeuge-Form bei 100 kg einbricht, trainiere mit 85–90 kg bis Tiefe und Kontrolle bei allen Wiederholungen stimmen.',
                    ],
                    [
                        'title' => 'Aufwärmen überspringen',
                        'problem' => 'Direkt mit Arbeitssätzen starten, ohne progressive Aufwärmsätze.',
                        'consequence' => 'Kalte Muskeln und Gelenke sind steifer und schwächer. Du hebst weniger Gewicht und riskierst Zerrungen und Gelenkschmerzen.',
                        'solution' => '2–3 Aufwärmsätze mit steigendem Gewicht vor jeder Grundübung plus 5 Minuten allgemeine Mobilisation.',
                        'example' => 'Vor 100 kg Kniebeugen: Leere Stange × 10, 60 kg × 5, 80 kg × 3, dann Arbeitssätze.',
                    ],
                    [
                        'title' => 'Hintere Kette vernachlässigen',
                        'problem' => 'Fokus auf Spiegelmuskeln (Brust, Bizeps), während Rücken, hintere Oberschenkel und Gesäß vernachlässigt werden.',
                        'consequence' => 'Muskuläre Dysbalancen entstehen: runde Schultern, Schmerzen im unteren Rücken und geringeres Kraftpotenzial insgesamt.',
                        'solution' => 'Gleiche oder übertreffe dein Druckvolumen mit Zugvolumen. Dieser Plan enthält Rudern, Kreuzheben und Face Pulls für Balance.',
                        'example' => 'Für jede 4 Sätze Bankdrücken mindestens 4 Sätze Rudern oder Klimmzüge.',
                    ],
                    [
                        'title' => 'Programme zu oft wechseln',
                        'problem' => 'Alle 2–3 Wochen ein neues Programm starten, weil der Fortschritt langsam erscheint.',
                        'consequence' => 'Kein konsistenter Reiz für Anpassung. Kraftzuwächse erfordern 6–10 Wochen konsequentes, progressives Training mit denselben Übungen.',
                        'solution' => 'Bleib die volle Dauer bei einem Programm. Protokolliere deine Gewichte, um objektiven Fortschritt zu sehen – verlass dich nicht auf dein Gefühl.',
                        'example' => '10 kg mehr bei der Kniebeuge in 10 Wochen (1 kg/Woche) ist hervorragender Fortschritt – vertraue dem Prozess.',
                    ],
                    [
                        'title' => 'Satzpausen zu kurz halten',
                        'problem' => 'Nur 30–60 Sekunden Pause zwischen schweren Grundübungen, um „den Puls oben zu halten".',
                        'consequence' => 'Phosphokreatinspeicher regenerieren sich nicht vollständig. Kraftleistung sinkt in Folgesätzen um 10–20%.',
                        'solution' => '2–3 Minuten Pause zwischen schweren Grundübungen. Kürzere Pausen (60s) sind bei Isolationsübungen in Ordnung.',
                        'example' => 'Nach einem schweren Satz Kreuzheben mit 5 Wiederholungen: 150s Pause, nicht 60s. Timer nutzen.',
                    ],
                    [
                        'title' => 'Zu wenig Protein',
                        'problem' => 'Weniger als 1,2 g Protein pro kg Körpergewicht bei Krafttraining.',
                        'consequence' => 'Muskelreparatur ist beeinträchtigt, Regeneration zwischen Einheiten dauert länger und Kraftzuwächse stagnieren frühzeitig.',
                        'solution' => '1,6–2,2 g Protein pro kg Körpergewicht täglich, verteilt auf 3–4 Mahlzeiten.',
                        'example' => 'Bei 80 kg Körpergewicht: 130–175 g Protein täglich (z.B. 250 g Hähnchen, 200 g Griechischer Joghurt, 3 Eier, 1 Proteinshake).',
                    ],
                    [
                        'title' => 'Nie deloaden',
                        'problem' => 'Wochen- und monatelang maximale Intensität ohne geplante Erholungswochen.',
                        'consequence' => 'Angestaute Ermüdung führt zu stagnierenden Gewichten, Gelenkbeschwerden, schlechtem Schlaf und letztlich Burnout oder Verletzung.',
                        'solution' => 'Alle 6–8 Wochen eine Deload-Woche einplanen. Volumen und Intensität auf 50–60% reduzieren.',
                        'example' => 'Wenn deine Arbeits-Kniebeuge 100 kg × 5 ist: Deload-Woche 60 kg × 5 für 3 Sätze. Du kommst stärker zurück.',
                    ],
                ],
                'summary' => 'Die meisten Trainierende stagnieren nicht wegen eines schlechten Plans, sondern wegen mangelhafter Ausführung. Beherrsche die Technik, iss genug Protein, pausiere richtig und folge dem Plan – die Kraft kommt.',
            ],

            'faqs' => [
                [
                    'question' => 'Mit wie viel Gewicht sollte ich starten?',
                    'answer' => 'Beginne mit einem Gewicht, das du für alle vorgeschriebenen Wiederholungen mit sauberer Technik kontrollieren kannst. Im Zweifel starte bei ca. 60–70% deines geschätzten 1-Wiederholungsmaximums.',
                ],
                [
                    'question' => 'Kann ich mit nur 3 Trainingstagen pro Woche Kraft aufbauen?',
                    'answer' => 'Ja, aber 4 Tage ermöglichen eine bessere Verteilung von Volumen und Regeneration. Bei nur 3 Tagen kombiniere eine Ober- und eine Unterkörpereinheit zu einem Ganzkörpertag.',
                ],
                [
                    'question' => 'Wann werde ich Kraftverbesserungen bemerken?',
                    'answer' => 'Anfänger spüren oft schon nach 2–3 Wochen messbare Kraftzuwächse. Fortgeschrittene können nach 4–6 Wochen konsequentem Training deutlichen Fortschritt erwarten.',
                ],
            ],
        ],

        /* ============================
           Fettabbau
        ============================ */
        'fettabbau' => [
            'title' => 'Kostenloser Fettabbau Trainingsplan – 8 Wochen',
            'description' => 'Kostenloser 8-Wochen-Trainingsplan für Fettabbau. Supersatz-basiertes Kraft- und Konditionstraining für maximale Fettverbrennung bei Muskelerhalt.',
            'h1' => 'Fettabbau Trainingsplan – Fett verbrennen, Muskeln behalten',
            'intro' => 'Dieser 8-Wochen-Trainingsplan nutzt Supersatz-basiertes Krafttraining und metabolisches Konditionstraining, um den Kalorienverbrauch zu maximieren und gleichzeitig Muskelmasse zu erhalten. 4 Einheiten pro Woche à 40 Minuten.',
            'internal_type' => 'fat_loss',
            'published_at' => '2026-03-12',
            'last_updated_at' => '2026-03-12',

            'workout' => [
                'weeks' => 8,
                'workouts_per_week' => 4,
                'duration_minutes' => 40,
                'level' => 'Anfänger bis Fortgeschritten',
                'equipment' => ['Kurzhanteln', 'Kettlebell (optional)', 'Matte'],

                'schedule' => [

                    [
                        'day' => 'Tag 1 – Push & Pull Supersätze',
                        'focus' => 'Oberkörperkraft bei erhöhter Herzfrequenz',
                        'exercises' => [
                            ['name' => 'Kurzhantel Bankdrücken → Kurzhantel Rudern (Supersatz)', 'sets' => 4, 'reps' => '10–12 je Übung', 'rest' => '60s'],
                            ['name' => 'Schulterdrücken → Face Pulls (Supersatz)', 'sets' => 3, 'reps' => '10–12 je Übung', 'rest' => '60s'],
                            ['name' => 'Liegestütze', 'sets' => 3, 'reps' => '12–15', 'rest' => '45s'],
                            ['name' => 'Renegade Rows', 'sets' => 3, 'reps' => '8–10 pro Seite', 'rest' => '60s'],
                            ['name' => 'Plank', 'sets' => 3, 'reps' => '30–45s', 'rest' => '30s'],
                        ],
                    ],

                    [
                        'day' => 'Tag 2 – Metabolisches HIIT',
                        'focus' => 'Maximaler Kalorienverbrauch und Herz-Kreislauf-Kondition',
                        'exercises' => [
                            ['name' => 'Kettlebell Swings (oder Kurzhantel Swings)', 'sets' => 4, 'reps' => '15–20', 'rest' => '45s'],
                            ['name' => 'Burpees', 'sets' => 4, 'reps' => '8–10', 'rest' => '45s'],
                            ['name' => 'Mountain Climbers', 'sets' => 4, 'reps' => '30s', 'rest' => '30s'],
                            ['name' => 'Jump Squats', 'sets' => 3, 'reps' => '12–15', 'rest' => '45s'],
                            ['name' => 'Dead Bug', 'sets' => 3, 'reps' => '10 pro Seite', 'rest' => '30s'],
                        ],
                    ],

                    [
                        'day' => 'Tag 3 – Beine & Core Supersätze',
                        'focus' => 'Unterkörperkraft mit Core-Aktivierung',
                        'exercises' => [
                            ['name' => 'Goblet Squat → Reverse Lunges (Supersatz)', 'sets' => 4, 'reps' => '10–12 je Übung', 'rest' => '60s'],
                            ['name' => 'Rumänisches Kreuzheben → Glute Bridges (Supersatz)', 'sets' => 3, 'reps' => '10–12 je Übung', 'rest' => '60s'],
                            ['name' => 'Step-ups', 'sets' => 3, 'reps' => '10 pro Bein', 'rest' => '60s'],
                            ['name' => 'Russian Twists', 'sets' => 3, 'reps' => '20', 'rest' => '30s'],
                            ['name' => 'Hängendes Beinheben oder liegendes Beinheben', 'sets' => 3, 'reps' => '10–12', 'rest' => '45s'],
                        ],
                    ],

                    [
                        'day' => 'Tag 4 – Ganzkörper Kondition',
                        'focus' => 'Ganzkörper-Zirkel für Ausdauer und Fettverbrennung',
                        'exercises' => [
                            ['name' => 'Kurzhantel Thrusters', 'sets' => 4, 'reps' => '10–12', 'rest' => '45s'],
                            ['name' => 'Renegade Row zu Liegestütz', 'sets' => 3, 'reps' => '8–10', 'rest' => '60s'],
                            ['name' => 'Seitliche Ausfallschritte', 'sets' => 3, 'reps' => '10 pro Seite', 'rest' => '45s'],
                            ['name' => 'High Knees', 'sets' => 3, 'reps' => '30s', 'rest' => '30s'],
                            ['name' => 'Plank zu Liegestütz', 'sets' => 3, 'reps' => '8–10', 'rest' => '45s'],
                        ],
                    ],

                ],

                'progression' => 'Woche 1–2: Supersatz-Rhythmus erlernen, moderate Intensität | Woche 3–4: Satzpausen um 10–15s verkürzen | Woche 5–6: Gewicht bei allen Kraftübungen steigern | Woche 7–8: Einen zusätzlichen Satz bei Verbund-Supersätzen',
                'tips' => [
                    'Kaloriendefizit von 300–500 kcal einhalten – aggressive Diäten bremsen den Stoffwechsel und kosten Muskelmasse',
                    '1,8–2,2 g Protein pro kg Körpergewicht täglich, um Muskelmasse im Defizit zu schützen',
                    '8.000–10.000 Schritte täglich außerhalb des Trainings für höheren NEAT',
                    '7–9 Stunden Schlaf – schlechter Schlaf erhöht Cortisol und Hungerhormone und bremst den Fettabbau',
                ],
            ],

            'why_it_works' => [
                'title' => 'Warum dieser Fettabbau-Trainingsplan funktioniert',
                'content' => [
                    [
                        'heading' => 'Supersätze halten deine Herzfrequenz oben',
                        'text' => 'Das Paaren gegenüberliegender Muskelgruppen mit minimaler Pause hält die Herzfrequenz durchgehend in der Fettverbrennungszone. Studien zeigen, dass Supersatz-Training den Kalorienverbrauch um 30–40% steigert im Vergleich zu klassischen Einzelsätzen – ohne längere Trainingszeiten.',
                    ],
                    [
                        'heading' => 'Krafttraining schützt Muskeln im Defizit',
                        'text' => 'Wenn du weniger Kalorien isst als du verbrauchst, kann dein Körper Muskelmasse als Energiequelle abbauen. Das Widerstandstraining in diesem Plan sendet ein starkes Signal zum Muskelerhalt. Studien zeigen konsistent: Krafttraining kombiniert mit Kaloriendefizit führt zu deutlich mehr Fett- und weniger Muskelverlust als Diät allein.',
                    ],
                    [
                        'heading' => 'EPOC verlängert die Kalorienverbrennung über Stunden',
                        'text' => 'Hochintensive Supersätze und metabolisches Konditionstraining erzeugen einen erhöhten Sauerstoffverbrauch nach dem Training (EPOC). Das bedeutet: Dein Stoffwechsel bleibt 12–24 Stunden nach jeder Einheit erhöht und verbrennt zusätzlich 50–150 kcal über das Training hinaus.',
                    ],
                    [
                        'heading' => 'Kurze Einheiten verbessern die Regelmäßigkeit',
                        'text' => 'Mit 40 Minuten pro Einheit beseitigt dieser Plan die häufigste Hürde für Konsistenz: fehlende Zeit. Verhaltensforschung zeigt, dass kürzere, häufigere Trainingseinheiten langfristig zu besserer Regelmäßigkeit führen – und Regelmäßigkeit ist der stärkste Prädiktor für erfolgreichen Fettabbau.',
                    ],
                    [
                        'heading' => 'Metabolisches Konditionstraining verbessert die Insulinsensitivität',
                        'text' => 'Die HIIT- und Konditionstage verbessern, wie dein Körper Kohlenhydrate verarbeitet, indem sie die Insulinsensitivität erhöhen. Bessere Insulinsensitivität bedeutet: Dein Körper nutzt Nahrung effizienter als Energie statt sie als Fett zu speichern – ein entscheidender Stoffwechselvorteil für langfristige Körperkomposition.',
                    ],
                ],
            ],

            'common_mistakes' => [
                'title' => 'Die 7 häufigsten Fettabbau-Fehler – und wie du sie vermeidest',
                'mistakes' => [
                    [
                        'title' => 'Nur auf Cardio setzen',
                        'problem' => '60+ Minuten auf dem Laufband oder Crosstrainer ohne jegliches Krafttraining.',
                        'consequence' => 'Du verlierst Gewicht, aber ein erheblicher Teil davon ist Muskelmasse. Der Stoffwechsel sinkt, Fettabbau wird schwieriger und Gewicht wird leichter wieder zugenommen.',
                        'solution' => 'Priorisiere kraftbasiertes Training mindestens 3× pro Woche. Nutze Cardio als Ergänzung, nicht als Fundament.',
                        'example' => 'Ersetze 2 von 5 wöchentlichen Cardio-Einheiten durch die Supersatz-Krafttage aus diesem Plan.',
                    ],
                    [
                        'title' => 'Zu aggressives Kaloriendefizit',
                        'problem' => 'Auf 1.000–1.200 kcal pro Tag heruntergehen, um Ergebnisse zu beschleunigen.',
                        'consequence' => 'Metabolische Anpassung setzt innerhalb von 1–2 Wochen ein. Hungerhormone steigen, Energie bricht ein und Muskelabbau beschleunigt sich. Die meisten verfallen in Essanfälle und nehmen innerhalb eines Monats wieder zu.',
                        'solution' => 'Moderates Defizit von 300–500 kcal unter dem Erhaltungsbedarf. Das ermöglicht 0,5–0,7 kg Fettabbau pro Woche ohne Stoffwechselkollaps.',
                        'example' => 'Bei einem Erhaltungsbedarf von 2.400 kcal: 1.900–2.100 kcal essen, nicht 1.200.',
                    ],
                    [
                        'title' => 'Proteinzufuhr vernachlässigen',
                        'problem' => 'Kohlenhydratreiche, proteinarme Mahlzeiten im Kaloriendefizit.',
                        'consequence' => 'Muskelabbau nimmt zu, Regeneration leidet und Hunger ist schwerer zu kontrollieren. Protein hat den höchsten thermischen Effekt aller Makronährstoffe – wer es weglässt, verschenkt einen Stoffwechselvorteil.',
                        'solution' => '1,8–2,2 g Protein pro kg Körpergewicht täglich anpeilen. Auf 3–4 Mahlzeiten verteilen für optimale Muskelproteinsynthese.',
                        'example' => 'Bei 70 kg: 125–155 g Protein täglich (z.B. 200 g Hähnchen, 150 g Griechischer Joghurt, 2 Eier, 1 Proteinshake).',
                    ],
                    [
                        'title' => 'Zu lange Pausen zwischen Supersätzen',
                        'problem' => '2–3 Minuten Pause zwischen Supersatz-Paaren, was den metabolischen Vorteil eliminiert.',
                        'consequence' => 'Herzfrequenz sinkt, Kalorienverbrauch reduziert sich um 30–40% und die Einheit verliert ihren Konditionseffekt. Du bekommst ein Krafttraining, verpasst aber den Fettabbau-Vorteil.',
                        'solution' => '30–60 Sekunden Pause zwischen Supersatz-Paaren. Timer nutzen. Das Unbehagen ist gewollt – es treibt den EPOC.',
                        'example' => 'Bankdrücken → Rudern ohne Pause dazwischen ausführen, dann 60s Pause vor dem nächsten Paar.',
                    ],
                    [
                        'title' => 'Alltagsbewegung nicht tracken',
                        'problem' => '4× pro Woche trainieren, aber an Ruhetagen 10+ Stunden sitzen.',
                        'consequence' => 'NEAT (Non-Exercise Activity Thermogenesis) kann 15–30% des täglichen Gesamtenergieverbrauchs ausmachen. Niedrige Schrittzahlen an Ruhetagen können das durch Training aufgebaute Kaloriendefizit komplett zunichtemachen.',
                        'solution' => 'Schritte tracken und an Trainings- wie Ruhetagen auf 8.000–10.000 kommen.',
                        'example' => 'Ein 15-minütiger Spaziergang nach jeder Mahlzeit bringt ca. 3.000 Schritte und 150 kcal – ohne sich wie Training anzufühlen.',
                    ],
                    [
                        'title' => 'Training nach einer schlechten Mahlzeit ausfallen lassen',
                        'problem' => 'Nach einem großen Essen oder einem „Cheat Day" die nächste Einheit aus Schuldgefühl ausfallen lassen.',
                        'consequence' => 'Eine einzelne Übermahlzeit bringt vielleicht 500–1.000 kcal extra. Eine ausgelassene Einheit entfernt 200–400 kcal Verbrauch plus die metabolischen Vorteile. Die Lücke wird größer.',
                        'solution' => 'Niemals bestrafen oder kompensieren. Ein einzelner Ausrutscher ist über 8 Wochen statistisch irrelevant. Erscheine zur nächsten Einheit wie geplant.',
                        'example' => 'Auch nach einem 3.000-kcal-Abendessen: am nächsten Tag wie geplant trainieren. Eine Mahlzeit definiert kein 8-Wochen-Programm.',
                    ],
                    [
                        'title' => 'Weniger als 6 Stunden schlafen',
                        'problem' => 'Chronisch zu wenig Schlaf bei hartem Training und Kaloriendefizit.',
                        'consequence' => 'Cortisol steigt, Testosteron und Wachstumshormon sinken, Ghrelin (Hungerhormon) erhöht sich um bis zu 28% und Leptin (Sättigungshormon) sinkt. Fettabbau stagniert trotz perfektem Training und Ernährung.',
                        'solution' => '7–9 Stunden Schlaf sicherstellen. Schlaf ist in einer Fettabbau-Phase nicht optional – er ist vermutlich die wichtigste Erholungsvariable.',
                        'example' => 'Bei 5,5 Stunden Schlaf: bis zu 70% des Gewichtsverlusts ist Muskelmasse. Bei 8 Stunden: bis zu 80% ist Fett.',
                    ],
                ],
                'summary' => 'Fettabbau scheitert, wenn man sich nur auf das Training konzentriert. Dieser Plan funktioniert, weil er Training, Ernährung, Regeneration und Alltagsbewegung als ein System behandelt.',
            ],

            'faqs' => [
                [
                    'question' => 'Was ist der Unterschied zwischen Fettabbau und Abnehmen?',
                    'answer' => 'Abnehmen umfasst Muskeln, Wasser und Fett. Fettabbau zielt gezielt auf Körperfett bei gleichzeitigem Muskelerhalt – genau das erreicht dieser Plan durch kraftbasiertes Training kombiniert mit moderatem Kaloriendefizit.',
                ],
                [
                    'question' => 'Kann ich diesen Plan zuhause machen?',
                    'answer' => 'Ja. Du brauchst nur ein Paar Kurzhanteln und eine Matte. Eine Kettlebell ist optional – Kurzhantel-Swings funktionieren als Ersatz.',
                ],
                [
                    'question' => 'Wie schnell sehe ich Ergebnisse?',
                    'answer' => 'Die meisten bemerken nach 2 Wochen mehr Energie und bessere Leistung. Sichtbarer Fettabbau wird typischerweise nach 4–6 Wochen konsequentem Training und Ernährung erkennbar.',
                ],
            ],
        ],

        /* ============================
           Bauchmuskeltraining
        ============================ */
        'bauchmuskeltraining' => [
            'title' => 'Kostenloser Bauchmuskel-Trainingsplan – 8 Wochen',
            'description' => 'Kostenloser 8-Wochen-Trainingsplan für Bauchmuskeln. Gezieltes Core-Training mit Anti-Bewegungs-, Rotations- und Flexionsübungen – Zuhause & Gym.',
            'h1' => 'Bauchmuskel-Trainingsplan – Starker Core in 8 Wochen',
            'intro' => 'Dieser 8-Wochen-Trainingsplan entwickelt deine gesamte Rumpfmuskulatur systematisch. Drei fokussierte Einheiten pro Woche trainieren Stabilität, Rotationskraft und Flexion – für sichtbare Bauchmuskeln und funktionale Kernkraft.',
            'internal_type' => 'abs',
            'published_at' => '2026-03-12',
            'last_updated_at' => '2026-03-12',

            'workout' => [
                'weeks' => 8,
                'workouts_per_week' => 3,
                'duration_minutes' => 35,
                'level' => 'Anfänger bis Fortgeschritten',
                'equipment' => ['Matte', 'Klimmzugstange (optional)', 'Kurzhanteln (optional)'],

                'schedule' => [

                    [
                        'day' => 'Tag 1 – Anti-Bewegung & Stabilität',
                        'focus' => 'Isometrische Core-Stabilität, Anti-Extension, Anti-Lateralflexion',
                        'exercises' => [
                            ['name' => 'Plank', 'sets' => 3, 'reps' => '30–60s', 'rest' => '45s'],
                            ['name' => 'Side Plank', 'sets' => 3, 'reps' => '25–40s pro Seite', 'rest' => '45s'],
                            ['name' => 'Dead Bug', 'sets' => 3, 'reps' => '10–12 pro Seite', 'rest' => '45s'],
                            ['name' => 'Bird Dog', 'sets' => 3, 'reps' => '10–12 pro Seite', 'rest' => '45s'],
                            ['name' => 'Hollow Body Hold', 'sets' => 3, 'reps' => '20–30s', 'rest' => '45s'],
                        ],
                    ],

                    [
                        'day' => 'Tag 2 – Rotation & Dynamik',
                        'focus' => 'Rotationskraft, Anti-Rotation, schräge Bauchmuskeln',
                        'exercises' => [
                            ['name' => 'Pallof Press', 'sets' => 3, 'reps' => '10–12 pro Seite', 'rest' => '45s'],
                            ['name' => 'Russian Twist', 'sets' => 3, 'reps' => '12–16 gesamt', 'rest' => '45s'],
                            ['name' => 'Bicycle Crunches', 'sets' => 3, 'reps' => '12–16 pro Seite', 'rest' => '45s'],
                            ['name' => 'Mountain Climbers', 'sets' => 3, 'reps' => '20–30s', 'rest' => '45s'],
                            ['name' => 'Suitcase Carry', 'sets' => 3, 'reps' => '30–40m pro Seite', 'rest' => '60s'],
                        ],
                    ],

                    [
                        'day' => 'Tag 3 – Flexion & Untere Bauchmuskeln',
                        'focus' => 'Obere und untere Rektusabdominis, Hüftbeuger-Kontrolle',
                        'exercises' => [
                            ['name' => 'Hängendes Beinheben oder Knieheben', 'sets' => 3, 'reps' => '8–12', 'rest' => '60s'],
                            ['name' => 'Reverse Crunch', 'sets' => 3, 'reps' => '12–15', 'rest' => '45s'],
                            ['name' => 'Ab Wheel Rollout oder Plank Walk-Out', 'sets' => 3, 'reps' => '8–12', 'rest' => '60s'],
                            ['name' => 'Toe Touches', 'sets' => 3, 'reps' => '12–15', 'rest' => '45s'],
                            ['name' => 'L-Sit Hold oder Tuck Hold', 'sets' => 3, 'reps' => '15–25s', 'rest' => '60s'],
                        ],
                    ],

                ],

                'progression' => 'Woche 1–2: Grundhaltungen erlernen, kürzere Haltezeiten und einfachere Varianten | Woche 3–4: Haltezeiten um 10–15s erhöhen, Wiederholungen steigern | Woche 5–6: Gewichtete Varianten einführen (z.B. gewichtete Planks, Kurzhantel bei Russian Twist) | Woche 7–8: Fortgeschrittene Varianten (Hanging Leg Raise statt Knieheben, Ab Wheel statt Walk-Out)',
                'tips' => [
                    'Körperfettanteil ist entscheidend: Sichtbare Bauchmuskeln erfordern typischerweise unter 15% bei Männern und unter 22% bei Frauen',
                    '1,6–2,0 g Protein pro kg Körpergewicht täglich für Muskelaufbau im Kaloriendefizit',
                    'Atme bei jeder Wiederholung vollständig aus, um die tiefe Core-Muskulatur maximal zu aktivieren',
                    'Trainiere den Core nie direkt vor schwerem Kniebeugen oder Kreuzheben – platziere diese Einheiten an getrennten Tagen',
                ],
            ],

            'why_it_works' => [
                'title' => 'Warum dieser Bauchmuskel-Trainingsplan funktioniert',
                'content' => [
                    [
                        'heading' => 'Drei Core-Funktionen statt nur Crunches',
                        'text' => 'Dein Core hat drei Hauptfunktionen: Stabilisierung (Plank), Rotation (Russian Twist) und Flexion (Beinheben). Dieser Plan trainiert alle drei gezielt an separaten Tagen. Forschung zeigt, dass multidirektionales Core-Training die Rumpfkraft um 29% mehr steigert als reine Flexionsübungen.',
                    ],
                    [
                        'heading' => 'Anti-Bewegungsübungen bauen funktionale Stabilität auf',
                        'text' => 'Übungen wie Plank, Dead Bug und Pallof Press trainieren deinen Core, Bewegung zu widerstehen – genau das, was er im Alltag und beim Sport tun muss. Studien zur Wirbelsäulenstabilität bestätigen, dass diese Anti-Bewegungsmuster das Verletzungsrisiko im unteren Rücken signifikant senken.',
                    ],
                    [
                        'heading' => 'Progressive Überlastung gilt auch für Bauchmuskeln',
                        'text' => 'Bauchmuskeln sind Skelettmuskeln und reagieren auf dasselbe Wachstumsprinzip: progressive Überlastung. Der Plan steigert systematisch von Körpergewicht über längere Haltezeiten zu gewichteten und fortgeschrittenen Varianten – so wachsen deine Core-Muskeln tatsächlich.',
                    ],
                    [
                        'heading' => 'Hohe Frequenz beschleunigt Core-Entwicklung',
                        'text' => 'Core-Muskeln erholen sich schneller als große Muskelgruppen, weil sie kleiner sind und auf Ausdauerarbeit ausgelegt. Drei Einheiten pro Woche mit jeweils unterschiedlichem Schwerpunkt bieten genug Volumen für Wachstum bei ausreichender Regeneration zwischen den Einheiten.',
                    ],
                    [
                        'heading' => 'Kurze Einheiten erhöhen die Konsistenz',
                        'text' => '35 Minuten pro Session senken die Einstiegshürde drastisch. Adherence-Studien zeigen, dass kürzere Trainingseinheiten zu 40% höherer Regelmäßigkeit führen als längere Programme – und Konsistenz ist der stärkste Prädiktor für sichtbare Ergebnisse.',
                    ],
                ],
            ],

            'common_mistakes' => [
                'title' => 'Die 7 häufigsten Bauchmuskel-Fehler – und wie du sie vermeidest',
                'mistakes' => [
                    [
                        'title' => 'Nur Crunches machen',
                        'problem' => 'Das gesamte Core-Training besteht aus Crunches und Sit-ups – immer dieselbe Bewegungsebene.',
                        'consequence' => 'Nur der obere Rektusabdominis wird angesprochen. Schräge Bauchmuskeln, unterer Core und tiefe Stabilisatoren bleiben unterentwickelt.',
                        'solution' => 'Trainiere alle Core-Funktionen: Stabilität (Plank), Rotation (Russian Twist), Anti-Rotation (Pallof Press) und Flexion (Beinheben).',
                        'example' => 'Statt 100 Crunches: 3 Sätze Plank + 3 Sätze Pallof Press + 3 Sätze Hängendes Beinheben.',
                    ],
                    [
                        'title' => 'Körperfett ignorieren',
                        'problem' => 'Intensives Bauchmuskeltraining ohne auf Ernährung und Kaloriendefizit zu achten.',
                        'consequence' => 'Die Bauchmuskeln werden stärker, aber bleiben unter einer Fettschicht verborgen. Du siehst keine Definition, trotz hartem Training.',
                        'solution' => 'Kombiniere Core-Training mit einem moderaten Kaloriendefizit (300–500 kcal). Kein Training der Welt kann eine schlechte Ernährung kompensieren.',
                        'example' => 'Bei einem Bedarf von 2400 kcal: esse 1900–2100 kcal mit 1,6–2,0 g Protein pro kg Körpergewicht.',
                    ],
                    [
                        'title' => 'Hüftbeuger statt Core aktivieren',
                        'problem' => 'Bei Beinheben und Sit-ups die Beine mit den Hüftbeugern heben statt mit dem Core kontrollieren.',
                        'consequence' => 'Schmerzen im unteren Rücken, überaktive Hüftbeuger und minimale Bauchmuskelaktivierung trotz hohem Trainingsvolumen.',
                        'solution' => 'Drücke den unteren Rücken in den Boden, bewege die Beine langsam und kontrolliert. Spürst du es im unteren Rücken, beuge die Knie stärker.',
                        'example' => 'Beim Reverse Crunch: Hebe das Becken vom Boden statt nur die Beine zu schwingen. 12 kontrollierte Wiederholungen schlagen 30 unkontrollierte.',
                    ],
                    [
                        'title' => 'Zu viel Volumen, zu wenig Intensität',
                        'problem' => '50–100 Wiederholungen pro Übung mit Körpergewicht, aber nie progressive Steigerung.',
                        'consequence' => 'Du trainierst Ausdauer, nicht Muskelwachstum. Die Bauchmuskeln werden nicht dicker oder definierter, nur ausdauernder.',
                        'solution' => 'Halte die Wiederholungszahlen bei 8–15 und steigere die Schwierigkeit über Gewicht oder Hebelvarianten.',
                        'example' => 'Statt 50 Crunches: 3 × 12 gewichtete Cable Crunches oder 3 × 10 Ab Wheel Rollouts.',
                    ],
                    [
                        'title' => 'Core-Training vor schweren Verbundübungen',
                        'problem' => 'Direkt vor Kniebeugen oder Kreuzheben intensives Bauchmuskeltraining absolvieren.',
                        'consequence' => 'Ein vorermüdeter Core kann die Wirbelsäule bei schweren Lasten nicht stabilisieren. Leistung sinkt und Verletzungsrisiko steigt.',
                        'solution' => 'Trainiere Core am Ende der Einheit oder an separaten Tagen. Niemals direkt vor schwerem Heben.',
                        'example' => 'Montag: Kniebeugen + Beintraining. Dienstag: Core-Einheit. Nicht umgekehrt.',
                    ],
                    [
                        'title' => 'Atem anhalten',
                        'problem' => 'Während Core-Übungen die Luft anhalten oder nur flach atmen.',
                        'consequence' => 'Der Transversus abdominis – die tiefste Core-Schicht – wird nicht aktiviert. Schwindel und erhöhter Blutdruck sind weitere Folgen.',
                        'solution' => 'Atme bei jeder Anstrengungsphase bewusst und vollständig aus. Das forcierte Ausatmen aktiviert den tiefen Core automatisch.',
                        'example' => 'Beim Crunch: beim Hochkommen vollständig ausatmen, beim Senken kontrolliert einatmen.',
                    ],
                    [
                        'title' => 'Regeneration vernachlässigen',
                        'problem' => 'Täglich Bauchmuskeltraining ohne Pausentage, weil „Bauchmuskeln sich schnell erholen".',
                        'consequence' => 'Auch wenn Core-Muskeln schneller regenerieren, brauchen sie mindestens 24–48 Stunden. Tägliches Training führt zu Übertraining und stagnierenden Ergebnissen.',
                        'solution' => '3–4 Core-Einheiten pro Woche mit mindestens einem freien Tag dazwischen. Dieser Plan nutzt 3 Tage mit wechselndem Fokus.',
                        'example' => 'Montag: Stabilität, Mittwoch: Rotation, Freitag: Flexion. Dienstag/Donnerstag/Wochenende: Pause oder anderes Training.',
                    ],
                ],
                'summary' => 'Sichtbare Bauchmuskeln erfordern das Zusammenspiel von gezieltem Training aller Core-Funktionen, progressiver Überlastung und kontrollierter Ernährung. Dieser Plan liefert die Trainingsstruktur – deine Ernährung entscheidet über die Sichtbarkeit.',
            ],

            'faqs' => [
                [
                    'question' => 'Kann ich mit diesem Plan ein Sixpack bekommen?',
                    'answer' => 'Der Plan baut die Muskulatur auf, aber Sichtbarkeit hängt vom Körperfettanteil ab. Unter 15% (Männer) bzw. 22% (Frauen) werden die Muskeln sichtbar. Kombiniere den Plan mit einem moderaten Kaloriendefizit.',
                ],
                [
                    'question' => 'Kann ich das Core-Training mit meinem normalen Krafttraining kombinieren?',
                    'answer' => 'Ja. Plane die Core-Einheiten an Tagen ohne schwere Kniebeugen oder Kreuzheben, oder hänge sie ans Ende deiner regulären Einheit an. Vermeide Core-Training direkt vor schweren Verbundübungen.',
                ],
                [
                    'question' => 'Brauche ich Geräte für diesen Plan?',
                    'answer' => 'Nein. Der Großteil funktioniert mit Körpergewicht und einer Matte. Eine Klimmzugstange für hängendes Beinheben und Kurzhanteln für gewichtete Varianten sind optional, aber empfohlen ab Woche 5.',
                ],
            ],
        ],

        /* ============================
           Ü40 Training
        ============================ */
        'ueber-40-training' => [
            'title' => 'Kostenloser Trainingsplan ab 40 – 10 Wochen',
            'description' => 'Kostenloser 10-Wochen-Trainingsplan für Ü40. Gelenkschonendes Kraft- & Mobilitätstraining gegen Muskelverlust, Steifheit und Knochendichteverlust.',
            'h1' => 'Trainingsplan ab 40 – Stark und beweglich bleiben',
            'intro' => 'Dieser 10-Wochen-Trainingsplan wurde speziell für Trainierende ab 40 entwickelt. Drei ausgewogene Einheiten pro Woche kombinieren Krafttraining, Mobilität und Gleichgewicht – gelenkschonend, progressiv und alltagstauglich.',
            'internal_type' => 'over_40',
            'published_at' => '2026-03-12',
            'last_updated_at' => '2026-03-12',

            'workout' => [
                'weeks' => 10,
                'workouts_per_week' => 3,
                'duration_minutes' => 45,
                'level' => 'Anfänger bis Fortgeschritten',
                'equipment' => ['Kurzhanteln', 'Matte', 'Stuhl oder Bank'],

                'schedule' => [

                    [
                        'day' => 'Tag 1 – Ganzkörper Kraft',
                        'focus' => 'Grundübungen mit kontrolliertem Tempo, Schwerpunkt auf Haltung und Stabilität',
                        'exercises' => [
                            ['name' => 'Goblet Squat', 'sets' => 3, 'reps' => '10–12', 'rest' => '90s'],
                            ['name' => 'Kurzhantel Brustpresse (Flachbank oder Boden)', 'sets' => 3, 'reps' => '10–12', 'rest' => '90s'],
                            ['name' => 'Kurzhantel Rudern einarmig', 'sets' => 3, 'reps' => '10–12 pro Seite', 'rest' => '60s'],
                            ['name' => 'Rumänisches Kreuzheben (Kurzhanteln)', 'sets' => 3, 'reps' => '10–12', 'rest' => '90s'],
                            ['name' => 'Plank', 'sets' => 3, 'reps' => '25–45s', 'rest' => '45s'],
                        ],
                    ],

                    [
                        'day' => 'Tag 2 – Mobilität & Gleichgewicht',
                        'focus' => 'Gelenkbeweglichkeit, einbeinige Stabilität, Sturzprävention',
                        'exercises' => [
                            ['name' => 'Ausfallschritte (alternierend)', 'sets' => 3, 'reps' => '8–10 pro Bein', 'rest' => '60s'],
                            ['name' => 'Kurzhantel Schulterdrücken (sitzend)', 'sets' => 3, 'reps' => '10–12', 'rest' => '90s'],
                            ['name' => 'Bird Dog', 'sets' => 3, 'reps' => '8–10 pro Seite', 'rest' => '45s'],
                            ['name' => 'Step-Ups (Stuhl oder Bank)', 'sets' => 3, 'reps' => '8–10 pro Bein', 'rest' => '60s'],
                            ['name' => 'Brustöffner an der Wand (Wall Chest Stretch)', 'sets' => 3, 'reps' => '30–40s pro Seite', 'rest' => '30s'],
                        ],
                    ],

                    [
                        'day' => 'Tag 3 – Kraft & Ausdauer',
                        'focus' => 'Höheres Volumen mit moderatem Gewicht, metabolischer Reiz',
                        'exercises' => [
                            ['name' => 'Sumo Squat (Kurzhantel)', 'sets' => 3, 'reps' => '12–15', 'rest' => '60s'],
                            ['name' => 'Liegestütze (angepasst nach Niveau)', 'sets' => 3, 'reps' => '8–15', 'rest' => '60s'],
                            ['name' => 'Kurzhantel Reverse Fly', 'sets' => 3, 'reps' => '12–15', 'rest' => '60s'],
                            ['name' => 'Glute Bridge (gewichtet)', 'sets' => 3, 'reps' => '12–15', 'rest' => '60s'],
                            ['name' => 'Dead Bug', 'sets' => 3, 'reps' => '10–12 pro Seite', 'rest' => '45s'],
                        ],
                    ],

                ],

                'progression' => 'Woche 1–3: Bewegungsqualität und Tempo erlernen, leichte Gewichte | Woche 4–6: Gewicht um 1–2 kg steigern, Haltezeiten verlängern | Woche 7–9: Wiederholungszahlen reduzieren (8–10), Gewicht erhöhen | Woche 10: Deload bei 60% Intensität, Fokus auf Mobilität und Technikverfeinerung',
                'tips' => [
                    '1,6–2,0 g Protein pro kg Körpergewicht täglich – der Proteinbedarf steigt ab 40 aufgrund sinkender Muskelsynthese',
                    '5–10 Minuten Aufwärmen vor jeder Einheit: Gelenke mobilisieren, Puls leicht anheben',
                    'Kontrolliertes Tempo: 2 Sekunden heben, 3 Sekunden senken – schont Gelenke und maximiert Muskelspannung',
                    '7–9 Stunden Schlaf – Regeneration dauert ab 40 nachweislich länger',
                ],
            ],

            'why_it_works' => [
                'title' => 'Warum dieser Trainingsplan ab 40 funktioniert',
                'content' => [
                    [
                        'heading' => 'Krafttraining ist die beste Medizin gegen Muskelverlust',
                        'text' => 'Ab 30 verliert der Körper ohne gezieltes Training 3–8% Muskelmasse pro Jahrzehnt (Sarkopenie). Dieser Plan setzt auf die wirksamste Gegenmaßnahme: progressives Krafttraining. Studien zeigen, dass auch Trainierende über 60 noch signifikant Muskelmasse und Kraft aufbauen können.',
                    ],
                    [
                        'heading' => 'Gelenkschonendes Training verhindert Überlastung',
                        'text' => 'Im Gegensatz zu Plänen für jüngere Trainierende vermeidet dieser Plan hohe Stoßbelastungen und extreme Gelenkpositionen. Kontrolliertes Tempo (2-3-1 Kadenz) reduziert die Spitzenbelastung auf Sehnen und Bänder um bis zu 40%, während die Muskelaktivierung sogar steigt.',
                    ],
                    [
                        'heading' => 'Mobilität und Gleichgewicht senken das Sturzrisiko',
                        'text' => 'Ab 40 nimmt die propriozeptive Leistung messbar ab. Ein dedizierter Mobilitätstag mit einbeinigen Übungen, Gleichgewichtstraining und Dehnungen trainiert genau die neuromuskulären Fähigkeiten, die im Alltag vor Stürzen und Verletzungen schützen.',
                    ],
                    [
                        'heading' => 'Drei Einheiten pro Woche optimieren Regeneration',
                        'text' => 'Die Regenerationszeit verlängert sich mit dem Alter nachweislich. Drei Einheiten mit jeweils mindestens 48 Stunden Pause geben Muskeln, Sehnen und dem Nervensystem genug Zeit zur vollständigen Erholung – der entscheidende Faktor für kontinuierlichen Fortschritt ab 40.',
                    ],
                    [
                        'heading' => 'Knochendichte steigt durch Widerstandstraining',
                        'text' => 'Osteoporose-Risiko steigt ab dem 40. Lebensjahr deutlich, besonders bei Frauen. Krafttraining erzeugt mechanische Belastung auf den Knochen, was die Osteoblasten-Aktivität stimuliert. Meta-Analysen bestätigen: 2–3 Krafteinheiten pro Woche können den altersbedingten Knochendichteverlust signifikant verlangsamen.',
                    ],
                ],
            ],

            'common_mistakes' => [
                'title' => 'Die 7 häufigsten Trainingsfehler ab 40 – und wie du sie vermeidest',
                'mistakes' => [
                    [
                        'title' => 'Trainieren wie mit 25',
                        'problem' => 'Gleiche Übungen, Intensität und Frequenz wie vor 15 Jahren, ohne Anpassung an veränderte Regeneration und Gelenksituation.',
                        'consequence' => 'Chronische Gelenkschmerzen, Sehnenentzündungen und Motivationsverlust durch ständige Beschwerden.',
                        'solution' => 'Passe Tempo, Volumen und Übungsauswahl an. Kontrolliertes Tempo und moderate Gewichte mit voller Bewegungsamplitude sind effektiver als schwere Maximalversuche.',
                        'example' => 'Statt schwere Langhantel-Kniebeuge mit 3 Wiederholungen: Goblet Squat mit 3 × 12 bei kontrolliertem 2-3-1 Tempo.',
                    ],
                    [
                        'title' => 'Aufwärmen überspringen',
                        'problem' => 'Direkt mit Arbeitssätzen starten, weil die Zeit knapp ist.',
                        'consequence' => 'Gelenke sind steifer, Synovialflüssigkeit nicht verteilt, Verletzungsrisiko steigt deutlich. Ab 40 sind Sehnen und Bänder weniger elastisch.',
                        'solution' => 'Mindestens 5–10 Minuten Aufwärmen: Gelenke kreisen, dynamische Dehnungen, 1–2 leichte Aufwärmsätze pro Übung.',
                        'example' => 'Vor dem Goblet Squat: 20 Kniebeugen mit Körpergewicht, Hüftkreise, dann 1 Satz mit 50% des Arbeitsgewichts.',
                    ],
                    [
                        'title' => 'Mobilität ignorieren',
                        'problem' => 'Ausschließlich Krafttraining, kein gezieltes Beweglichkeits- oder Gleichgewichtstraining.',
                        'consequence' => 'Zunehmende Steifheit in Hüfte, Schultern und Brustwirbelsäule. Alltagsbewegungen werden eingeschränkt, Sturzrisiko steigt.',
                        'solution' => 'Mindestens eine Einheit pro Woche mit Mobilitäts- und Gleichgewichtsschwerpunkt. Dieser Plan integriert das am Tag 2.',
                        'example' => 'Bird Dog, Step-Ups und Brustöffner am Mobilitätstag kosten nur 15 Minuten, schützen aber langfristig vor Verletzungen.',
                    ],
                    [
                        'title' => 'Zu wenig Protein',
                        'problem' => 'Gleiche Ernährung wie mit 30, obwohl der Proteinbedarf ab 40 nachweislich steigt.',
                        'consequence' => 'Der Körper braucht ab 40 mehr Protein pro Mahlzeit, um die Muskelsynthese auszulösen (anabole Resistenz). Ohne Anpassung beschleunigt sich der Muskelverlust.',
                        'solution' => 'Mindestens 1,6–2,0 g Protein pro kg Körpergewicht, verteilt auf 3–4 Mahlzeiten mit jeweils mindestens 30 g Protein.',
                        'example' => 'Bei 80 kg Körpergewicht: 130–160 g Protein täglich. Jede Mahlzeit: 200 g Hähnchen oder 250 g Magerquark oder 3 Eier + Shake.',
                    ],
                    [
                        'title' => 'Schmerzen ignorieren',
                        'problem' => 'Bei Gelenkschmerzen einfach weitermachen, weil „es eben zum Alter gehört".',
                        'consequence' => 'Aus akutem Schmerz wird chronische Entzündung. Kompensationsbewegungen führen zu Folgeverletzungen an anderen Gelenken.',
                        'solution' => 'Unterscheide Muskelkater (24–72h, symmetrisch, dumpf) von Gelenkschmerz (einseitig, stechend, direkt am Gelenk). Gelenkschmerz = Übung anpassen oder pausieren.',
                        'example' => 'Schmerzen im Knie bei tiefen Kniebeugen: Tiefe auf 90° begrenzen oder auf Goblet Squat mit erhöhten Fersen wechseln.',
                    ],
                    [
                        'title' => 'Nur Ausdauer trainieren',
                        'problem' => 'Ausschließlich Joggen, Radfahren oder Schwimmen – kein Krafttraining.',
                        'consequence' => 'Ausdauertraining allein schützt nicht vor Sarkopenie oder Osteoporose. Muskelmasse und Knochendichte sinken trotz hoher Fitness.',
                        'solution' => 'Krafttraining ist ab 40 wichtiger als Ausdauer. Ideale Kombination: 3× Kraft + 2× moderate Ausdauer (Spazierengehen, Radfahren).',
                        'example' => 'Statt 5× Joggen: 3× dieser Trainingsplan + 2× 30 Min. zügiges Gehen. Ergebnis: mehr Muskeln, stärkere Knochen und trotzdem gute Ausdauer.',
                    ],
                    [
                        'title' => 'Regeneration vernachlässigen',
                        'problem' => 'Jeden Tag trainieren oder bei schlechtem Schlaf trotzdem Vollgas geben.',
                        'consequence' => 'Ab 40 sinken Testosteron und Wachstumshormonspiegel. Regeneration dauert 20–30% länger als mit 25. Übertraining führt zu Leistungseinbrüchen und Infektanfälligkeit.',
                        'solution' => 'Mindestens 48 Stunden zwischen Krafteinheiten. Schlaf priorisieren: 7–9 Stunden. An trainingsfreien Tagen leichte Bewegung (Spazieren, Dehnen).',
                        'example' => 'Montag: Kraft, Dienstag: Spaziergang, Mittwoch: Mobilität, Donnerstag: Pause, Freitag: Kraft, Samstag/Sonntag: aktive Erholung.',
                    ],
                ],
                'summary' => 'Training ab 40 erfordert kein komplett neues System – sondern intelligente Anpassungen: kontrolliertes Tempo, Mobilität, ausreichend Protein und respektierte Regeneration. Dieser Plan liefert genau das.',
            ],

            'faqs' => [
                [
                    'question' => 'Ist Krafttraining ab 40 noch sicher?',
                    'answer' => 'Ja. Krafttraining ist laut WHO und Sportmedizin die wichtigste Trainingsform ab 40 zur Prävention von Muskelverlust, Osteoporose und Stoffwechselerkrankungen. Entscheidend ist kontrolliertes Tempo und progressive Steigerung.',
                ],
                [
                    'question' => 'Kann ich den Plan auch ohne Gym machen?',
                    'answer' => 'Ja. Du brauchst nur Kurzhanteln, eine Matte und einen stabilen Stuhl oder eine Bank. Alle Übungen funktionieren zuhause.',
                ],
                [
                    'question' => 'Wie unterscheidet sich dieser Plan von einem normalen Trainingsplan?',
                    'answer' => 'Kontrolliertes Tempo statt Maximallast, ein dedizierter Mobilitätstag, längere Pausen und ein Deload in Woche 10. Diese Anpassungen respektieren die veränderte Regeneration und Gelenksituation ab 40.',
                ],
            ],
        ],
    ],

    'en' => [
        'weight-loss' => [
            'title' => 'Free Weight Loss Workout Plan – 8 Weeks',
            'description' => 'Free 8-week weight loss workout plan. Structured strength and cardio training for sustainable fat loss – suitable for home or gym.',
            'h1' => 'Weight Loss Workout Plan – Lose Fat Sustainably',
            'intro' => 'This structured 8-week workout plan combines strength training and cardio to reduce body fat, preserve muscle mass, and improve metabolic health.',
            'internal_type' => 'weight_loss',
            'published_at' => '2025-12-24',
            'last_updated_at' => '2025-12-24',
            'workout' => [
                'weeks' => 8,
                'workouts_per_week' => 3,
                'duration_minutes' => 45,
                'level' => 'Beginner to Advanced',
                'equipment' => ['Dumbbells (optional)', 'Mat'],

                'schedule' => [
                    [
                        'day' => 'Day 1 – Full Body Strength',
                        'focus' => 'Large muscle groups & calorie expenditure',
                        'exercises' => [
                            ['name' => 'Squats', 'sets' => 3, 'reps' => '12–15', 'rest' => '60s'],
                            ['name' => 'Push-ups', 'sets' => 3, 'reps' => '8–12', 'rest' => '60s'],
                            ['name' => 'Dumbbell Rows', 'sets' => 3, 'reps' => '12–15', 'rest' => '60s'],
                            ['name' => 'Plank', 'sets' => 3, 'reps' => '30–45s', 'rest' => '45s'],
                            ['name' => 'Dead Bug', 'sets' => 3, 'reps' => '8–10 per side', 'rest' => '45s'],
                        ],
                    ],
                    [
                        'day' => 'Day 2 – Cardio & HIIT',
                        'focus' => 'Fat loss & cardiovascular fitness',
                        'exercises' => [
                            ['name' => 'Jumping Jacks', 'sets' => 4, 'reps' => '30s', 'rest' => '30s'],
                            ['name' => 'Mountain Climbers', 'sets' => 4, 'reps' => '30s', 'rest' => '30s'],
                            ['name' => 'Burpees', 'sets' => 3, 'reps' => '8–12', 'rest' => '60s'],
                            ['name' => 'Bodyweight Squats', 'sets' => 3, 'reps' => '15–20', 'rest' => '45s'],
                        ],
                    ],
                    [
                        'day' => 'Day 3 – Lower Body & Core',
                        'focus' => 'Leg strength, glutes & core stability',
                        'exercises' => [
                            ['name' => 'Lunges', 'sets' => 3, 'reps' => '10 per leg', 'rest' => '60s'],
                            ['name' => 'Glute Bridges', 'sets' => 3, 'reps' => '15–20', 'rest' => '45s'],
                            ['name' => 'Reverse Snow Angels', 'sets' => 3, 'reps' => '12–15', 'rest' => '45s'],
                            ['name' => 'Russian Twists', 'sets' => 3, 'reps' => '20', 'rest' => '45s'],
                        ],
                    ],
                ],

                'progression' => 'Weeks 1–2: Learn technique | Weeks 3–4: Increase volume | Weeks 5–6: Higher intensity | Weeks 7–8: Performance phase',
                'tips' => [
                    'Maintain a moderate calorie deficit of 300–500 kcal',
                    'Prioritize strength training to preserve muscle mass',
                    'Aim for 7–9 hours of sleep per night',
                    'Increase daily movement outside of workouts',
                ],
            ],
            'why_it_works' => [
                'title' => 'Why This Weight Loss Workout Plan Works',
                'content' => [
                    [
                        'heading' => 'Combining Strength and Cardio Maximizes Fat Loss',
                        'text' => 'This plan uses a proven approach: strength training preserves muscle mass during a calorie deficit, while HIIT sessions maximize calorie burn. Studies show that this combination can be up to 40% more effective for fat loss than cardio-only training.',
                    ],
                    [
                        'heading' => 'The Afterburn Effect (EPOC) Works in Your Favor',
                        'text' => 'Intense strength training and HIIT intervals increase post-exercise oxygen consumption (EPOC). This means your body continues to burn additional calories for up to 48 hours after training — even at rest. This so-called "afterburn effect" can increase total calorie expenditure by 6–15%.',
                    ],
                    [
                        'heading' => 'Preserving Muscle Prevents the Yo-Yo Effect',
                        'text' => 'Unlike diet-only approaches, this plan maintains muscle mass. This is crucial: each kilogram of muscle burns roughly 13 kcal per day at rest. Losing muscle through extreme dieting lowers your metabolic rate and significantly increases the risk of weight regain.',
                    ],
                    [
                        'heading' => 'Progressive Structure Prevents Plateaus',
                        'text' => 'The 8-week structure with systematic progression (more reps, shorter rest periods, increased intensity) continuously challenges your body. This helps prevent the common plateau many people experience after 3–4 weeks with less structured plans.',
                    ],
                    [
                        'heading' => 'Scientifically Supported Training Frequency',
                        'text' => 'Three training sessions per week provide the optimal balance between stimulus and recovery. Research from the American College of Sports Medicine shows that this frequency supports sustainable fat loss of 0.5–1 kg per week without overloading the body or risking muscle loss.',
                    ],
                ],
            ],

            'common_mistakes' => [
                'title' => 'The 7 Most Common Weight Loss Mistakes — and How to Avoid Them',
                'mistakes' => [
                    [
                        'title' => 'An Excessive Calorie Deficit',
                        'problem' => 'Many people rely on extreme diets with only 800–1,000 kcal per day, expecting rapid results.',
                        'consequence' => 'Your body enters “starvation mode”: metabolism slows down, muscle mass is lost, and energy levels drop — making training ineffective.',
                        'solution' => 'Maintain a moderate calorie deficit of 300–500 kcal. This results in a sustainable weight loss of 0.5–0.7 kg per week without muscle loss.',
                        'example' => 'If your maintenance level is 2,000 kcal: eat 1,500–1,700 kcal instead of 1,000.',
                    ],
                    [
                        'title' => 'Too Little Protein Intake',
                        'problem' => 'Protein is underestimated and replaced by excessive carbohydrates.',
                        'consequence' => 'Muscle mass decreases, hunger increases, and metabolic rate drops. You lose weight — but mainly muscle instead of fat.',
                        'solution' => 'Aim for 1.6–2.0 g of protein per kg of body weight daily. Prioritize protein-rich foods at every meal.',
                        'example' => 'At 75 kg body weight: 120–150 g protein daily (e.g. 200 g chicken, 200 g low-fat quark, 3 eggs, 1 protein shake).',
                    ],
                    [
                        'title' => 'Cardio Without Strength Training',
                        'problem' => 'Only jogging or cycling, with no strength training.',
                        'consequence' => 'Calories are burned, but muscles are not preserved. The result is “skinny fat”: low body weight but high body fat percentage and weak musculature.',
                        'solution' => 'Prioritize strength training (at least twice per week) and use cardio as a supplement. This plan is built exactly that way.',
                        'example' => 'Instead of running 5 times per week: 3 sessions of this plan (strength + HIIT) plus 2 light walks.',
                    ],
                    [
                        'title' => 'Inconsistent Training',
                        'problem' => 'Monday: highly motivated. Thursday: no motivation. Next week: starting over again.',
                        'consequence' => 'No physical adaptation, no muscle development, no progress. Fat loss requires consistency over several weeks.',
                        'solution' => 'Schedule fixed training days (e.g. Mon/Wed/Fri). Even a 20-minute session is better than skipping. Use habit stacking: train immediately after work.',
                        'example' => 'Instead of “when I have time”: block “Monday 6:00 PM – Training” in your calendar like an important meeting.',
                    ],
                    [
                        'title' => 'Lack of Sleep',
                        'problem' => 'Only 5–6 hours of sleep per night while training hard and dieting.',
                        'consequence' => 'Cortisol (stress hormone) increases, testosterone decreases, and hunger hormones become imbalanced (higher ghrelin = more hunger). Fat loss stalls.',
                        'solution' => 'Aim for 7–9 hours of sleep per night. Studies show that adequate sleep can improve fat loss by up to 55% with identical training.',
                        'example' => 'With 6 hours of sleep: ~60% of weight loss comes from muscle. With 8 hours: ~80% comes from fat.',
                    ],
                    [
                        'title' => 'Excessively Long Rest Periods',
                        'problem' => 'Scrolling on your phone or chatting — turning 60 seconds of rest into 3–5 minutes.',
                        'consequence' => 'Calorie burn drops significantly, training effectiveness (especially EPOC) decreases, and a 45-minute workout turns into 90 minutes.',
                        'solution' => 'Use a timer and stick to prescribed rest periods (60s for strength, 30–45s for HIIT). This is a major difference-maker.',
                        'example' => 'Use your smartphone timer or gym clock — start the timer after every set.',
                    ],
                    [
                        'title' => 'Lack of Daily Movement',
                        'problem' => 'Training 3 times per week, but sitting for 12 hours per day (office, car, couch).',
                        'consequence' => 'NEAT (Non-Exercise Activity Thermogenesis) remains minimal. The effect of “3 workouts per week” is lost with fewer than 3,000 daily steps.',
                        'solution' => 'Aim for 8,000–10,000 steps per day. That adds an extra 200–400 kcal burn without additional workouts.',
                        'example' => '15-minute walk during lunch, walking while on calls, stairs instead of elevators, parking 500 m farther away.',
                    ],
                ],
                'summary' => 'Most people fail not because of training, but because of these hidden mistakes. Avoid them, and your success becomes highly predictable.',
            ],
            'faqs' => [
                [
                    'question' => 'How often should I train to lose weight?',
                    'answer' => 'Training 3–4 times per week is ideal to promote fat loss while allowing proper recovery.',
                ],
                [
                    'question' => 'Is strength training important for weight loss?',
                    'answer' => 'Yes. Strength training preserves lean muscle mass and helps maintain a higher metabolic rate.',
                ],
                [
                    'question' => 'When can I expect results?',
                    'answer' => 'Improved energy and performance often appear after 2–3 weeks. Visible fat loss typically follows after 4–6 weeks.',
                ],
            ],
        ],

        'muscle-gain' => [
            'title' => 'Free Muscle Building Workout Plan – 12 Weeks',
            'description' => 'Free 12-week muscle building workout plan. Structured strength training with progressive overload for sustainable muscle growth.',
            'h1' => 'Muscle Building Workout Plan – Build Strength & Muscle',
            'intro' => 'This 12-week muscle building program is designed to systematically increase strength and muscle mass through progressive overload and structured training.',
            'keywords' => ['muscle building workout plan', 'hypertrophy training', 'strength training program', 'build muscle'],
            'internal_type' => 'muscle_gain',
            'published_at' => '2025-12-24',
            'last_updated_at' => '2025-12-24',
            'why_it_works' => [
                'title' => 'Why This Muscle Building Plan Works',
                'content' => [
                    [
                        'heading' => 'Progressive Overload Drives Muscle Growth',
                        'text' => 'Muscle growth only happens when your body is exposed to increasing demands over time. This plan is built around progressive overload — gradually increasing weights, reps, or training volume — which is the most proven driver of hypertrophy according to decades of strength training research.',
                    ],
                    [
                        'heading' => 'Optimal Training Volume and Frequency',
                        'text' => 'Training each muscle group 2 times per week provides the optimal balance between stimulus and recovery. Scientific reviews show that moderate-to-high weekly volume distributed across multiple sessions leads to significantly more muscle growth than single, high-volume workouts.',
                    ],
                    [
                        'heading' => 'Compound Exercises Maximize Hormonal Response',
                        'text' => 'The plan prioritizes compound movements like squats, presses, rows, and pull-ups. These exercises recruit large amounts of muscle mass, increase mechanical tension, and stimulate anabolic hormones — all key factors for efficient muscle growth.',
                    ],
                    [
                        'heading' => 'Sufficient Recovery Is Built In',
                        'text' => 'Muscles grow during recovery, not during training. Rest days, intelligent splits, and controlled training frequency allow your nervous system and muscles to recover fully, reducing injury risk and supporting consistent long-term progress.',
                    ],
                    [
                        'heading' => 'Nutrition and Training Are Aligned',
                        'text' => 'The plan is designed to work alongside a moderate calorie surplus and adequate protein intake. This alignment ensures your body has the necessary building blocks to repair and grow muscle tissue efficiently after each training session.',
                    ],
                ],
            ],

            'common_mistakes' => [
                'title' => 'The 7 Most Common Muscle Building Mistakes — and How to Avoid Them',
                'mistakes' => [
                    [
                        'title' => 'Training Without Progressive Overload',
                        'problem' => 'Using the same weights and reps week after week without increasing difficulty.',
                        'consequence' => 'Your body adapts quickly and muscle growth stalls. Without progression, training becomes maintenance rather than growth.',
                        'solution' => 'Track your workouts and aim to improve one variable each week: weight, reps, sets, or tempo.',
                        'example' => 'Week 1: Bench Press 3×8 at 60 kg → Week 2: 3×9 or 62.5 kg.',
                    ],
                    [
                        'title' => 'Too Little Food Intake',
                        'problem' => 'Trying to build muscle while eating at maintenance or in a calorie deficit.',
                        'consequence' => 'The body lacks energy and building material, leading to slow or nonexistent muscle gains.',
                        'solution' => 'Maintain a calorie surplus of 300–500 kcal per day to support muscle protein synthesis.',
                        'example' => 'If maintenance is 2,400 kcal, aim for 2,700–2,900 kcal daily.',
                    ],
                    [
                        'title' => 'Insufficient Protein Intake',
                        'problem' => 'Protein intake is inconsistent or too low to support muscle repair.',
                        'consequence' => 'Recovery is impaired and muscle protein synthesis remains suboptimal.',
                        'solution' => 'Consume 2.0–2.2 g of protein per kg of body weight daily, spread evenly across meals.',
                        'example' => 'At 80 kg body weight: 160–175 g protein per day.',
                    ],
                    [
                        'title' => 'Poor Exercise Technique',
                        'problem' => 'Using excessive weight at the cost of proper form.',
                        'consequence' => 'Target muscles are under-stimulated while injury risk increases significantly.',
                        'solution' => 'Prioritize controlled execution and full range of motion before increasing weight.',
                        'example' => 'Lower the weight if range of motion shortens or momentum takes over.',
                    ],
                    [
                        'title' => 'Too Much Volume, Not Enough Recovery',
                        'problem' => 'Training every muscle group hard every day without adequate rest.',
                        'consequence' => 'Chronic fatigue, stalled progress, joint pain, and increased injury risk.',
                        'solution' => 'Follow a structured split and respect rest days. More is not always better.',
                        'example' => '4 focused training days outperform 6 poorly recovered sessions.',
                    ],
                    [
                        'title' => 'Ignoring Sleep Quality',
                        'problem' => 'Sleeping less than 6–7 hours per night while training intensely.',
                        'consequence' => 'Reduced testosterone, impaired recovery, slower muscle growth.',
                        'solution' => 'Aim for 7–9 hours of quality sleep per night to maximize hormonal recovery.',
                        'example' => 'Studies show up to 30% lower muscle protein synthesis with sleep deprivation.',
                    ],
                    [
                        'title' => 'Constant Program Hopping',
                        'problem' => 'Switching training programs every 2–3 weeks.',
                        'consequence' => 'No measurable progression, no adaptation, no reliable muscle growth.',
                        'solution' => 'Stick to one structured plan for at least 8–12 weeks before making changes.',
                        'example' => 'Finish the full 12-week cycle before evaluating results.',
                    ],
                ],
                'summary' => 'Muscle building fails not because of bad exercises, but because of poor execution, recovery, and consistency. Avoid these mistakes and your progress becomes predictable.',
            ],

            'workout' => [
                'weeks' => 12,
                'workouts_per_week' => 4,
                'duration_minutes' => 60,
                'level' => 'Beginner to Advanced',
                'equipment' => ['Barbell', 'Dumbbells', 'Bench', 'Pull-up Bar'],

                'schedule' => [

                    [
                        'day' => 'Day 1 – Push',
                        'focus' => 'Chest, shoulders & triceps',
                        'exercises' => [
                            ['name' => 'Bench Press', 'sets' => 4, 'reps' => '6–10', 'rest' => '120s'],
                            ['name' => 'Incline Dumbbell Press', 'sets' => 3, 'reps' => '8–12', 'rest' => '90s'],
                            ['name' => 'Overhead Shoulder Press', 'sets' => 3, 'reps' => '8–12', 'rest' => '90s'],
                            ['name' => 'Lateral Raises', 'sets' => 3, 'reps' => '12–15', 'rest' => '60s'],
                            ['name' => 'Triceps Pushdowns or Dips', 'sets' => 3, 'reps' => '10–12', 'rest' => '75s'],
                        ],
                    ],

                    [
                        'day' => 'Day 2 – Pull',
                        'focus' => 'Back & biceps',
                        'exercises' => [
                            ['name' => 'Pull-ups or Lat Pulldown', 'sets' => 4, 'reps' => '6–10', 'rest' => '120s'],
                            ['name' => 'Barbell or Dumbbell Rows', 'sets' => 3, 'reps' => '8–12', 'rest' => '90s'],
                            ['name' => 'Face Pulls', 'sets' => 3, 'reps' => '12–15', 'rest' => '60s'],
                            ['name' => 'Biceps Curls', 'sets' => 3, 'reps' => '10–12', 'rest' => '60s'],
                        ],
                    ],

                    [
                        'day' => 'Day 3 – Legs',
                        'focus' => 'Lower body strength & stability',
                        'exercises' => [
                            ['name' => 'Squats', 'sets' => 4, 'reps' => '6–10', 'rest' => '120s'],
                            ['name' => 'Romanian Deadlifts', 'sets' => 3, 'reps' => '8–12', 'rest' => '90s'],
                            ['name' => 'Lunges', 'sets' => 3, 'reps' => '10 per leg', 'rest' => '75s'],
                            ['name' => 'Calf Raises', 'sets' => 3, 'reps' => '12–15', 'rest' => '60s'],
                        ],
                    ],

                    [
                        'day' => 'Day 4 – Core & Balance',
                        'focus' => 'Core stability & injury prevention',
                        'exercises' => [
                            ['name' => 'Plank', 'sets' => 3, 'reps' => '45–60s', 'rest' => '45s'],
                            ['name' => 'Hanging or Lying Leg Raises', 'sets' => 3, 'reps' => '10–15', 'rest' => '60s'],
                            ['name' => 'Back Extensions', 'sets' => 3, 'reps' => '12–15', 'rest' => '60s'],
                            ['name' => 'Mobility & Stretching', 'sets' => 1, 'reps' => '8–10 min', 'rest' => '-'],
                        ],
                    ],

                ],

                'progression' => 'Weeks 1–4: Learn technique | Weeks 5–8: Increase load | Weeks 9–12: Higher volume & intensity',
                'tips' => [
                    'Maintain a calorie surplus of 300–500 kcal',
                    'Protein intake of 2.0–2.2 g per kg of body weight',
                    'Track weights and aim for gradual progression',
                    'Allow at least one full rest day per week',
                ],
            ],

            'faqs' => [
                [
                    'question' => 'How often should I train for muscle growth?',
                    'answer' => 'Training 3–5 times per week is ideal, depending on recovery and overall training volume.',
                ],
                [
                    'question' => 'How much protein do I need?',
                    'answer' => 'Around 2.0–2.2 grams of protein per kilogram of body weight per day supports optimal muscle growth.',
                ],
                [
                    'question' => 'When will I see muscle gains?',
                    'answer' => 'Strength improvements often appear within 2–3 weeks. Visible muscle growth usually follows after 6–8 weeks.',
                ],
            ],
        ],

        'beginner' => [
            'title' => 'Beginner Workout Plan – 6 Weeks',
            'description' => 'The ideal beginner workout plan. Safe, structured and easy to follow – perfect for starting fitness training at home or in the gym.',
            'h1' => 'Beginner Workout Plan – Start Training Safely',
            'intro' => 'This 6-week beginner workout plan helps you learn fundamental movements, build basic strength and establish a sustainable training routine.',
            'keywords' => ['beginner workout plan', 'fitness for beginners', 'starter workout plan', 'beginner fitness'],
            'internal_type' => 'beginner',
            'published_at' => '2025-12-24',
            'last_updated_at' => '2025-12-24',

            'why_it_works' => [
                'title' => 'Why this beginner workout plan works',
                'content' => [
                    [
                        'heading' => 'Safe and sustainable entry into training',
                        'text' => 'This plan is designed specifically for beginners. Exercises are simple, scalable and joint-friendly, allowing your body to adapt gradually to regular training without overload.',
                    ],
                    [
                        'heading' => 'Focus on fundamental movement patterns',
                        'text' => 'Instead of complex exercises, the plan emphasizes basic movements like squatting, pushing, pulling and core stability. These movements build a solid foundation for all future training.',
                    ],
                    [
                        'heading' => 'Optimal training frequency for beginners',
                        'text' => 'With 2–3 workouts per week, your body receives enough stimulus to improve while still having sufficient time to recover. This frequency is considered ideal for beginners by sports science research.',
                    ],
                    [
                        'heading' => 'Progression without pressure',
                        'text' => 'Progress is achieved through small increases in repetitions, duration or exercise difficulty—not heavy weights. This ensures steady improvement without unnecessary stress.',
                    ],
                    [
                        'heading' => 'Building habits, not chasing perfection',
                        'text' => 'Consistency is the key to long-term success. This plan helps you establish a regular training routine, which is far more important than short-term intensity.',
                    ],
                ],
            ],

            'common_mistakes' => [
                'title' => 'The most common beginner mistakes – and how to avoid them',
                'mistakes' => [
                    [
                        'title' => 'Doing too much too soon',
                        'problem' => 'Many beginners start with excessive intensity or too many sessions.',
                        'consequence' => 'Soreness, fatigue and frustration often lead to quitting early.',
                        'solution' => 'Start slow and stick to the plan. Progress comes with consistency.',
                        'example' => '3 short, consistent workouts beat 5 overly intense sessions.',
                    ],
                    [
                        'title' => 'Poor exercise technique',
                        'problem' => 'Exercises are performed with momentum instead of control.',
                        'consequence' => 'Reduced results and higher injury risk.',
                        'solution' => 'Focus on controlled, clean movement execution.',
                        'example' => '10 quality reps are better than 20 sloppy ones.',
                    ],
                    [
                        'title' => 'Training too infrequently',
                        'problem' => 'Long gaps between workouts prevent adaptation.',
                        'consequence' => 'Your body keeps restarting from scratch.',
                        'solution' => 'Schedule fixed workout days each week.',
                        'example' => 'Monday, Wednesday and Friday as non-negotiable training days.',
                    ],
                    [
                        'title' => 'Neglecting recovery',
                        'problem' => 'Too little sleep or training on consecutive days without rest.',
                        'consequence' => 'Fatigue, loss of motivation and stalled progress.',
                        'solution' => 'Allow at least one rest day between workouts.',
                        'example' => 'Training every other day works best for beginners.',
                    ],
                    [
                        'title' => 'Expecting instant results',
                        'problem' => 'Beginners expect visible changes within days.',
                        'consequence' => 'Disappointment and early dropout.',
                        'solution' => 'Focus on energy levels, mobility and consistency first.',
                        'example' => 'Noticeable improvements usually appear after 2–3 weeks.',
                    ],
                ],
                'summary' => 'Beginners don’t fail because of lack of effort, but because of unrealistic expectations. Avoid these mistakes and your progress will follow naturally.',
            ],

            'workout' => [
                'weeks' => 6,
                'workouts_per_week' => 3,
                'duration_minutes' => 30,
                'level' => 'Beginner',
                'equipment' => ['No equipment', 'Optional: Yoga mat or resistance band'],

                'schedule' => [

                    [
                        'day' => 'Day 1 – Full Body Basics',
                        'focus' => 'Learn fundamental movement patterns',
                        'exercises' => [
                            ['name' => 'Squats', 'sets' => 2, 'reps' => '10–12', 'rest' => '90s'],
                            ['name' => 'Wall Push-ups', 'sets' => 2, 'reps' => '8–10', 'rest' => '90s'],
                            ['name' => 'Glute Bridges', 'sets' => 2, 'reps' => '12–15', 'rest' => '75s'],
                            ['name' => 'Plank', 'sets' => 2, 'reps' => '20–30s', 'rest' => '60s'],
                        ],
                    ],

                    [
                        'day' => 'Day 2 – Full Body Strength',
                        'focus' => 'Posture, back strength & core stability',
                        'exercises' => [
                            ['name' => 'Resistance Band or Towel Rows', 'sets' => 2, 'reps' => '10–12', 'rest' => '75s'],
                            ['name' => 'Reverse Lunges', 'sets' => 2, 'reps' => '8 per leg', 'rest' => '75s'],
                            ['name' => 'Seated Shoulder Press (light)', 'sets' => 2, 'reps' => '10–12', 'rest' => '75s'],
                            ['name' => 'Dead Bug', 'sets' => 2, 'reps' => '8–10 per side', 'rest' => '60s'],
                        ],
                    ],

                    [
                        'day' => 'Day 3 – Full Body & Mobility',
                        'focus' => 'Movement quality & flexibility',
                        'exercises' => [
                            ['name' => 'Slow Tempo Squats', 'sets' => 2, 'reps' => '10', 'rest' => '90s'],
                            ['name' => 'Incline Push-ups', 'sets' => 2, 'reps' => '8–10', 'rest' => '90s'],
                            ['name' => 'Bird Dog', 'sets' => 2, 'reps' => '8 per side', 'rest' => '60s'],
                            ['name' => 'Full Body Mobility Flow', 'sets' => 1, 'reps' => '8–10 min', 'rest' => '-'],
                        ],
                    ],

                ],

                'progression' => 'Weeks 1–2: Learn movements | Weeks 3–4: Increase repetitions | Weeks 5–6: Improve control and endurance',
                'tips' => [
                    'Focus on consistency rather than intensity',
                    'Use controlled movements and proper form',
                    'Allow at least one rest day between sessions',
                ],
            ],

            'faqs' => [
                [
                    'question' => 'How often should beginners train?',
                    'answer' => 'Training 2–3 times per week is ideal for beginners to adapt safely and recover properly.',
                ],
                [
                    'question' => 'Do I need a gym to start?',
                    'answer' => 'No. This beginner plan is designed to be effective at home using bodyweight exercises.',
                ],
                [
                    'question' => 'When will I notice progress?',
                    'answer' => 'Most beginners feel stronger and more confident within the first 1–2 weeks. Visible changes usually follow after a few weeks.',
                ],
            ],
        ],

        'home' => [
            'title' => 'Home Workout Plan – Train Effectively Without Equipment',
            'description' => 'Effective 8-week home workout plan with no equipment required. Structured bodyweight training for all fitness levels.',
            'h1' => 'Home Workout Plan – Train Effectively Without a Gym',
            'intro' => 'This 8-week home workout plan uses bodyweight exercises only. It helps you build strength, improve endurance and stay consistent without needing a gym.',
            'keywords' => ['home workout plan', 'bodyweight training', 'no equipment workout', 'home fitness'],
            'internal_type' => 'home',
            'published_at' => '2025-12-24',
            'last_updated_at' => '2025-12-24',

            'why_it_works' => [
                'title' => 'Why this home workout plan works',
                'content' => [
                    [
                        'heading' => 'Bodyweight training is highly effective',
                        'text' => 'This plan uses your own body weight as resistance. Research shows that bodyweight training can significantly improve strength, muscle tone and endurance when performed with proper intensity.',
                    ],
                    [
                        'heading' => 'Full-body stimulus without equipment',
                        'text' => 'Compound movements like push-ups, squats and lunges activate multiple muscle groups at once. This increases calorie burn and efficiency—perfect for home workouts.',
                    ],
                    [
                        'heading' => 'Progression without weights',
                        'text' => 'Progress is achieved through more repetitions, controlled tempo, shorter rest periods and harder exercise variations—no equipment required.',
                    ],
                    [
                        'heading' => 'Lower barriers, higher consistency',
                        'text' => 'Without commuting or equipment setup, training becomes easier to integrate into daily life. This significantly increases long-term consistency.',
                    ],
                    [
                        'heading' => 'Joint-friendly and scalable',
                        'text' => 'All exercises can be adapted to your fitness level, making the plan suitable for beginners and advanced trainees alike while minimizing injury risk.',
                    ],
                ],
            ],

            'common_mistakes' => [
                'title' => 'The most common home workout mistakes – and how to avoid them',
                'mistakes' => [
                    [
                        'title' => 'Training with too little intensity',
                        'problem' => 'Bodyweight workouts are often underestimated and performed too easily.',
                        'consequence' => 'The training stimulus is insufficient, resulting in little to no progress.',
                        'solution' => 'Use controlled movements, maintain tension and shorten rest periods.',
                        'example' => 'Slow, controlled push-ups instead of rushing through reps.',
                    ],
                    [
                        'title' => 'No progression over time',
                        'problem' => 'Repeating the same exercises with the same intensity for weeks.',
                        'consequence' => 'The body adapts and progress stalls.',
                        'solution' => 'Increase reps, slow down tempo or choose more challenging variations.',
                        'example' => 'Adding pauses at the bottom of squats.',
                    ],
                    [
                        'title' => 'Distractions during workouts',
                        'problem' => 'Training while checking your phone or watching TV.',
                        'consequence' => 'Reduced effectiveness and longer workout times.',
                        'solution' => 'Schedule focused, distraction-free sessions.',
                        'example' => '30 minutes of focused training instead of 60 minutes of interruptions.',
                    ],
                    [
                        'title' => 'Poor movement quality',
                        'problem' => 'Exercises are performed without control or proper alignment.',
                        'consequence' => 'Higher injury risk and reduced results.',
                        'solution' => 'Prioritize clean, controlled movement.',
                        'example' => 'A solid plank with core tension instead of a sagging lower back.',
                    ],
                    [
                        'title' => 'Insufficient recovery',
                        'problem' => 'Training every day without rest.',
                        'consequence' => 'Fatigue, declining performance and loss of motivation.',
                        'solution' => 'Plan at least 1–2 rest days per week.',
                        'example' => '4 workout days combined with active recovery.',
                    ],
                ],
                'summary' => 'Effective home training is not about equipment—it’s about structure, intensity and consistency. Avoid these mistakes and you can achieve excellent results at home.',
            ],

            'workout' => [
                'weeks' => 8,
                'workouts_per_week' => 4,
                'duration_minutes' => 40,
                'level' => 'All Levels',
                'equipment' => ['No equipment required'],

                'schedule' => [

                    [
                        'day' => 'Day 1 – Push Workout',
                        'focus' => 'Chest, shoulders & triceps',
                        'exercises' => [
                            ['name' => 'Push-ups', 'sets' => 4, 'reps' => '10–15', 'rest' => '60s'],
                            ['name' => 'Incline Push-ups', 'sets' => 3, 'reps' => '8–12', 'rest' => '60s'],
                            ['name' => 'Pike Push-ups', 'sets' => 3, 'reps' => '6–10', 'rest' => '75s'],
                            ['name' => 'Plank Shoulder Taps', 'sets' => 3, 'reps' => '20 taps', 'rest' => '45s'],
                        ],
                    ],

                    [
                        'day' => 'Day 2 – Pull & Core',
                        'focus' => 'Back, posture & core stability',
                        'exercises' => [
                            ['name' => 'Reverse Snow Angels', 'sets' => 3, 'reps' => '12–15', 'rest' => '60s'],
                            ['name' => 'Towel or Band Rows', 'sets' => 3, 'reps' => '10–12', 'rest' => '75s'],
                            ['name' => 'Dead Bug', 'sets' => 3, 'reps' => '8–10 per side', 'rest' => '60s'],
                            ['name' => 'Superman Hold', 'sets' => 3, 'reps' => '20–30s', 'rest' => '45s'],
                        ],
                    ],

                    [
                        'day' => 'Day 3 – Lower Body',
                        'focus' => 'Legs & glutes',
                        'exercises' => [
                            ['name' => 'Bodyweight Squats', 'sets' => 4, 'reps' => '15–20', 'rest' => '60s'],
                            ['name' => 'Reverse Lunges', 'sets' => 3, 'reps' => '10 per leg', 'rest' => '60s'],
                            ['name' => 'Glute Bridges', 'sets' => 3, 'reps' => '15–20', 'rest' => '45s'],
                            ['name' => 'Wall Sit', 'sets' => 3, 'reps' => '30–45s', 'rest' => '45s'],
                        ],
                    ],

                    [
                        'day' => 'Day 4 – Conditioning & Mobility',
                        'focus' => 'Cardio, coordination & recovery',
                        'exercises' => [
                            ['name' => 'Jumping Jacks', 'sets' => 4, 'reps' => '30s', 'rest' => '30s'],
                            ['name' => 'Mountain Climbers', 'sets' => 3, 'reps' => '30s', 'rest' => '30s'],
                            ['name' => 'High Knees', 'sets' => 3, 'reps' => '30s', 'rest' => '30s'],
                            ['name' => 'Full Body Mobility Flow', 'sets' => 1, 'reps' => '8–10 min', 'rest' => '-'],
                        ],
                    ],

                ],

                'progression' => 'Weeks 1–2: Learn technique | Weeks 3–4: Increase reps | Weeks 5–6: Harder variations | Weeks 7–8: Higher intensity',
                'tips' => [
                    'Consistency matters more than intensity',
                    'Slow tempo increases effectiveness',
                    'Focus on full range of motion',
                    'Rest days are part of progress',
                ],
            ],

            'faqs' => [
                [
                    'question' => 'Can I train effectively without equipment?',
                    'answer' => 'Yes. Bodyweight training is highly effective for building strength, endurance and muscle when exercises are structured properly.',
                ],
                [
                    'question' => 'Is this suitable for beginners?',
                    'answer' => 'Yes. All exercises can be scaled to match your fitness level, making this plan suitable for beginners and advanced users.',
                ],
                [
                    'question' => 'How long are the workouts?',
                    'answer' => 'Each workout takes approximately 35–45 minutes, including warm-up and short recovery periods.',
                ],
            ],
        ],

        'women' => [
            'title' => 'Workout Plan for Women – Targeted & Effective',
            'description' => 'Structured 8-week workout plan for women. Strength and cardio training for toning, definition and overall fitness.',
            'h1' => 'Workout Plan for Women – Strength, Tone & Balance',
            'intro' => 'This 8-week workout plan is designed specifically for women and combines strength training and cardio to improve muscle tone, definition and overall fitness.',
            'keywords' => ['workout plan for women', 'women fitness training', 'female workout plan', 'toning workout'],
            'internal_type' => 'women',
            'published_at' => '2025-12-24',
            'last_updated_at' => '2025-12-24',

            'why_it_works' => [
                'title' => 'Why this workout plan for women works',
                'content' => [
                    [
                        'heading' => 'Strength training shapes instead of bulks',
                        'text' => 'Women have significantly lower testosterone levels than men. As a result, strength training leads to toning and definition—not bulky muscles. This plan is designed specifically with that in mind.',
                    ],
                    [
                        'heading' => 'Targeted exercises for key areas',
                        'text' => 'The program focuses on legs, glutes, core and upper body—areas that strongly influence posture, body shape and everyday strength.',
                    ],
                    [
                        'heading' => 'Balanced combination of strength and cardio',
                        'text' => 'Strength training increases resting metabolism and shapes the body, while cardio supports fat loss. Together, they deliver visible and sustainable results.',
                    ],
                    [
                        'heading' => 'Hormone-friendly training structure',
                        'text' => 'Moderate intensity, adequate recovery and a sensible training frequency help support hormonal balance—crucial for long-term success and overall well-being.',
                    ],
                    [
                        'heading' => 'Improved confidence and body awareness',
                        'text' => 'Regular strength training not only improves physical performance but also boosts confidence. Many women experience higher energy levels, better posture and stronger body awareness.',
                    ],
                ],
            ],

            'common_mistakes' => [
                'title' => 'The most common training mistakes women make – and how to avoid them',
                'mistakes' => [
                    [
                        'title' => 'Fear of lifting weights',
                        'problem' => 'Many women avoid strength training out of fear of getting bulky.',
                        'consequence' => 'Weight may drop, but the body does not become toned or strong.',
                        'solution' => 'Include strength training consistently—it is essential for shaping and strength.',
                        'example' => '2–3 strength workouts per week instead of cardio-only routines.',
                    ],
                    [
                        'title' => 'Too much cardio, not enough strength',
                        'problem' => 'Excessive cardio with little or no resistance training.',
                        'consequence' => 'Muscle loss, slower metabolism and limited body shaping.',
                        'solution' => 'Prioritize strength training and use cardio as a supplement.',
                        'example' => '3 strength sessions plus 1–2 light cardio workouts.',
                    ],
                    [
                        'title' => 'Training with too little intensity',
                        'problem' => 'Using very light weights or stopping far from muscle fatigue.',
                        'consequence' => 'Insufficient stimulus for physical change.',
                        'solution' => 'Exercises should feel challenging while maintaining good form.',
                        'example' => 'The last 2 reps should feel demanding.',
                    ],
                    [
                        'title' => 'Neglecting upper body training',
                        'problem' => 'Only focusing on legs and glutes.',
                        'consequence' => 'Poor posture and increased risk of neck and shoulder discomfort.',
                        'solution' => 'Include upper body exercises regularly.',
                        'example' => 'Rows, shoulder presses and planks in weekly workouts.',
                    ],
                    [
                        'title' => 'Insufficient recovery',
                        'problem' => 'Training despite fatigue or lack of sleep.',
                        'consequence' => 'Declining performance, hormonal imbalance and loss of motivation.',
                        'solution' => 'Plan adequate sleep and rest days.',
                        'example' => 'At least 1–2 non-training days per week.',
                    ],
                ],
                'summary' => 'Effective training for women is not about endless cardio—it’s about a smart balance of strength, movement and recovery.',
            ],

            'workout' => [
                'weeks' => 8,
                'workouts_per_week' => 4,
                'duration_minutes' => 50,
                'level' => 'All Levels',
                'equipment' => ['Dumbbells', 'Resistance Bands', 'Mat'],

                'schedule' => [

                    [
                        'day' => 'Day 1 – Lower Body & Glutes',
                        'focus' => 'Leg strength, glutes & lower body tone',
                        'exercises' => [
                            ['name' => 'Squats', 'sets' => 4, 'reps' => '12–15', 'rest' => '60s'],
                            ['name' => 'Glute Bridges', 'sets' => 3, 'reps' => '15–20', 'rest' => '45s'],
                            ['name' => 'Reverse Lunges', 'sets' => 3, 'reps' => '10 per leg', 'rest' => '60s'],
                            ['name' => 'Band Kickbacks', 'sets' => 3, 'reps' => '12–15', 'rest' => '45s'],
                        ],
                    ],

                    [
                        'day' => 'Day 2 – Upper Body',
                        'focus' => 'Arms, back & posture',
                        'exercises' => [
                            ['name' => 'Dumbbell Rows', 'sets' => 3, 'reps' => '12–15', 'rest' => '60s'],
                            ['name' => 'Shoulder Press', 'sets' => 3, 'reps' => '10–12', 'rest' => '60s'],
                            ['name' => 'Lateral Raises', 'sets' => 3, 'reps' => '12–15', 'rest' => '45s'],
                            ['name' => 'Triceps Extensions', 'sets' => 3, 'reps' => '12–15', 'rest' => '45s'],
                        ],
                    ],

                    [
                        'day' => 'Day 3 – Cardio & Core',
                        'focus' => 'Fat burning & core stability',
                        'exercises' => [
                            ['name' => 'Jumping Jacks', 'sets' => 4, 'reps' => '30s', 'rest' => '30s'],
                            ['name' => 'Mountain Climbers', 'sets' => 3, 'reps' => '30s', 'rest' => '30s'],
                            ['name' => 'Russian Twists', 'sets' => 3, 'reps' => '20', 'rest' => '45s'],
                            ['name' => 'Plank', 'sets' => 3, 'reps' => '30–45s', 'rest' => '45s'],
                        ],
                    ],

                    [
                        'day' => 'Day 4 – Full Body & Mobility',
                        'focus' => 'Overall strength, balance & recovery',
                        'exercises' => [
                            ['name' => 'Bodyweight Squats', 'sets' => 3, 'reps' => '15', 'rest' => '60s'],
                            ['name' => 'Incline Push-ups', 'sets' => 3, 'reps' => '8–12', 'rest' => '60s'],
                            ['name' => 'Dead Bug', 'sets' => 3, 'reps' => '8–10 per side', 'rest' => '45s'],
                            ['name' => 'Full Body Stretching', 'sets' => 1, 'reps' => '8–10 min', 'rest' => '-'],
                        ],
                    ],

                ],

                'progression' => 'Weeks 1–3: Build base strength | Weeks 4–6: Increase volume | Weeks 7–8: Improve control and intensity',
                'tips' => [
                    'Strength training improves tone without excessive muscle bulk',
                    'Focus on compound movements for best results',
                    'Combine training with sufficient recovery and nutrition',
                ],
            ],

            'faqs' => [
                [
                    'question' => 'Will strength training make me bulky?',
                    'answer' => 'No. Women typically have much lower testosterone levels. Strength training tones and shapes the body without excessive muscle mass.',
                ],
                [
                    'question' => 'Is this plan suitable for beginners?',
                    'answer' => 'Yes. Exercises can be adjusted in intensity and volume to match all fitness levels.',
                ],
                [
                    'question' => 'Can I lose fat with this plan?',
                    'answer' => 'Yes. The combination of strength training and cardio supports fat loss while maintaining lean muscle.',
                ],
            ],
        ],

        'new-year-reset' => [
            'title' => 'New Year Workout Plan – Your 6-Week Fitness Reset',
            'description' => 'Start the new year strong with a structured 6-week workout plan. Build strength, burn fat and create sustainable habits – home or gym.',
            'h1' => 'New Year Workout Plan – Your 6-Week Fitness Reset',
            'intro' => 'This 6-week fitness reset focuses on structure instead of motivation hacks. Strength training, cardio and recovery are combined into a sustainable system to rebuild consistency and performance.',
            'keywords' => ['new year workout plan', 'fitness reset', 'workout restart', 'new year fitness'],
            'internal_type' => 'new_year_reset',
            'published_at' => '2025-12-24',
            'last_updated_at' => '2025-12-24',
            'why_it_works' => [
                'title' => 'Why the New Year workout reset works',
                'content' => [
                    [
                        'heading' => 'A reset instead of overload',
                        'text' => 'After longer breaks, your body doesn’t need extreme workouts. This 6-week reset focuses on controlled training to rebuild strength, endurance and mobility safely.',
                    ],
                    [
                        'heading' => 'Structure beats motivation',
                        'text' => 'Motivation comes and goes, routines stay. With clearly defined training days and manageable sessions, training becomes a habit rather than a struggle.',
                    ],
                    [
                        'heading' => 'Holistic training approach',
                        'text' => 'The plan combines strength, cardio, core stability and mobility. This improves not only muscle strength but also cardiovascular health, joint function and overall movement quality.',
                    ],
                    [
                        'heading' => 'Progression without pressure',
                        'text' => 'Intensity increases gradually through volume, exercise selection and workload. This prevents plateaus and supports steady progress after time off.',
                    ],
                    [
                        'heading' => 'Optimized for body recomposition',
                        'text' => 'Strength training preserves muscle mass while cardio increases calorie expenditure. Together, they support fat loss and muscle gain when paired with sensible nutrition.',
                    ],
                ],
            ],

            'common_mistakes' => [
                'title' => 'The most common restart mistakes – and how to avoid them',
                'mistakes' => [
                    [
                        'title' => 'Starting too aggressively',
                        'problem' => 'Many people begin the year with 5–6 workouts per week.',
                        'consequence' => 'Overuse, soreness and loss of motivation after 1–2 weeks.',
                        'solution' => 'Start with 3–4 structured sessions per week.',
                        'example' => 'Consistency for 6 weeks beats intensity for 10 days.',
                    ],
                    [
                        'title' => 'Changing everything at once',
                        'problem' => 'Training, diet, sleep and lifestyle all change simultaneously.',
                        'consequence' => 'Mental and physical overload.',
                        'solution' => 'Build training consistency first, then optimize nutrition.',
                        'example' => 'Establish workouts before cutting calories aggressively.',
                    ],
                    [
                        'title' => 'Ignoring recovery',
                        'problem' => 'No rest days after months of inactivity.',
                        'consequence' => 'Fatigue, declining performance and injury risk.',
                        'solution' => 'Treat recovery as part of the program.',
                        'example' => 'At least one mobility or rest-focused day per week.',
                    ],
                    [
                        'title' => 'Unrealistic expectations',
                        'problem' => 'Expecting visible transformation within 1–2 weeks.',
                        'consequence' => 'Frustration and early dropout.',
                        'solution' => 'Measure progress through energy, routine and performance.',
                        'example' => 'Better sleep, more daily movement and improved strength.',
                    ],
                    [
                        'title' => 'No fixed training schedule',
                        'problem' => 'Training only “when there’s time”.',
                        'consequence' => 'Inconsistency and lack of adaptation.',
                        'solution' => 'Schedule fixed training days.',
                        'example' => 'Monday, Wednesday and Friday as non-negotiable sessions.',
                    ],
                ],
                'summary' => 'Most New Year restarts fail due to unrealistic expectations. This plan succeeds by prioritizing structure, patience and sustainable progress.',
            ],

            'workout' => [
                'weeks' => 6,
                'workouts_per_week' => 4,
                'duration_minutes' => 45,
                'level' => 'Beginner to Advanced',
                'equipment' => ['Bodyweight', 'Optional dumbbells', 'Mat'],

                'schedule' => [

                    [
                        'day' => 'Day 1 – Full Body Strength',
                        'focus' => 'Rebuild muscle and boost metabolism',
                        'exercises' => [
                            ['name' => 'Squats', 'sets' => 3, 'reps' => '12–15', 'rest' => '60s', 'notes' => 'Controlled form'],
                            ['name' => 'Push-ups', 'sets' => 3, 'reps' => '8–12', 'rest' => '60s', 'notes' => 'Scale as needed'],
                            ['name' => 'Rows (Dumbbell or Band)', 'sets' => 3, 'reps' => '12–15', 'rest' => '60s', 'notes' => 'Back engaged'],
                            ['name' => 'Plank', 'sets' => 3, 'reps' => '30–45s', 'rest' => '45s', 'notes' => 'Core tension'],
                        ],
                    ],

                    [
                        'day' => 'Day 2 – Cardio & Core',
                        'focus' => 'Fat loss, conditioning and core stability',
                        'exercises' => [
                            ['name' => 'Jumping Jacks', 'sets' => 4, 'reps' => '30s', 'rest' => '30s'],
                            ['name' => 'Mountain Climbers', 'sets' => 4, 'reps' => '30s', 'rest' => '30s'],
                            ['name' => 'Russian Twists', 'sets' => 3, 'reps' => '20', 'rest' => '45s'],
                            ['name' => 'Dead Bug', 'sets' => 3, 'reps' => '10 per side', 'rest' => '45s'],
                        ],
                    ],

                    [
                        'day' => 'Day 3 – Lower Body Focus',
                        'focus' => 'Leg strength, stability and posture',
                        'exercises' => [
                            ['name' => 'Lunges', 'sets' => 3, 'reps' => '10 per leg', 'rest' => '60s'],
                            ['name' => 'Hip Thrusts', 'sets' => 3, 'reps' => '15', 'rest' => '60s'],
                            ['name' => 'Wall Sit', 'sets' => 3, 'reps' => '30–45s', 'rest' => '45s'],
                        ],
                    ],

                    [
                        'day' => 'Day 4 – Mobility & Recovery',
                        'focus' => 'Movement quality, recovery and injury prevention',
                        'exercises' => [
                            ['name' => 'Dynamic Full Body Stretching', 'sets' => 1, 'reps' => '10–15 min'],
                            ['name' => 'Breathing & Relaxation Exercises', 'sets' => 1, 'reps' => '5 min'],
                        ],
                    ],

                ],

                'progression' => 'Weeks 1–2: Build routine and consistency | Weeks 3–4: Increase volume | Weeks 5–6: Improve control and intensity',
                'tips' => [
                    'Consistency matters more than intensity',
                    'Avoid extreme diets – focus on habits',
                    '7–9 hours of sleep support recovery and results',
                    'Track workouts, not just body weight',
                ],
            ],

            'faqs' => [
                [
                    'question' => 'Is this plan suitable after a long break?',
                    'answer' => 'Yes. This plan is specifically designed for restarts. Exercises are scalable and focus on rebuilding routine, strength and confidence.',
                ],
                [
                    'question' => 'Can beginners follow this plan?',
                    'answer' => 'Absolutely. Beginners can reduce reps or rest longer, while advanced users can increase intensity or add light weights.',
                ],
                [
                    'question' => 'Can I lose fat and gain strength at the same time?',
                    'answer' => 'Yes. The combination of strength training, cardio and recovery supports fat loss while maintaining or rebuilding muscle.',
                ],
            ],
        ],

        /* ============================
           Strength
        ============================ */
        'strength' => [
            'title' => 'Free Strength Training Plan – 10 Weeks',
            'description' => 'Free 10-week strength training plan. Build real-world strength with compound lifts, progressive overload and structured periodisation – gym-based.',
            'h1' => 'Strength Training Plan – Get Stronger in 10 Weeks',
            'intro' => 'This 10-week strength training programme focuses on the barbell and dumbbell lifts that matter most. Built around progressive overload and a 4-day upper/lower split, it develops full-body strength for lifters of all levels.',
            'internal_type' => 'strength',
            'published_at' => '2026-03-12',
            'last_updated_at' => '2026-03-12',

            'workout' => [
                'weeks' => 10,
                'workouts_per_week' => 4,
                'duration_minutes' => 55,
                'level' => 'Beginner to Advanced',
                'equipment' => ['Barbell', 'Dumbbells', 'Pull-up Bar', 'Bench'],

                'schedule' => [

                    [
                        'day' => 'Day 1 – Upper Body Strength',
                        'focus' => 'Horizontal & vertical pressing, upper back',
                        'exercises' => [
                            ['name' => 'Barbell Bench Press', 'sets' => 4, 'reps' => '5–6', 'rest' => '120s'],
                            ['name' => 'Barbell Overhead Press', 'sets' => 3, 'reps' => '6–8', 'rest' => '90s'],
                            ['name' => 'Dumbbell Rows', 'sets' => 3, 'reps' => '8–10', 'rest' => '90s'],
                            ['name' => 'Pull-ups or Lat Pulldown', 'sets' => 3, 'reps' => '6–10', 'rest' => '90s'],
                            ['name' => 'Face Pulls', 'sets' => 3, 'reps' => '12–15', 'rest' => '60s'],
                        ],
                    ],

                    [
                        'day' => 'Day 2 – Lower Body Strength',
                        'focus' => 'Squat pattern, posterior chain, core',
                        'exercises' => [
                            ['name' => 'Barbell Back Squat', 'sets' => 4, 'reps' => '5–6', 'rest' => '120s'],
                            ['name' => 'Romanian Deadlift', 'sets' => 3, 'reps' => '8–10', 'rest' => '90s'],
                            ['name' => 'Bulgarian Split Squat', 'sets' => 3, 'reps' => '8–10 per leg', 'rest' => '90s'],
                            ['name' => 'Barbell Hip Thrust', 'sets' => 3, 'reps' => '8–12', 'rest' => '90s'],
                            ['name' => 'Hanging Leg Raise', 'sets' => 3, 'reps' => '10–12', 'rest' => '60s'],
                        ],
                    ],

                    [
                        'day' => 'Day 3 – Upper Body Power',
                        'focus' => 'Pressing strength, pulling volume, shoulders',
                        'exercises' => [
                            ['name' => 'Barbell Overhead Press', 'sets' => 4, 'reps' => '5–6', 'rest' => '120s'],
                            ['name' => 'Incline Dumbbell Press', 'sets' => 3, 'reps' => '8–10', 'rest' => '90s'],
                            ['name' => 'Barbell Bent-Over Row', 'sets' => 4, 'reps' => '6–8', 'rest' => '90s'],
                            ['name' => 'Dumbbell Lateral Raise', 'sets' => 3, 'reps' => '12–15', 'rest' => '60s'],
                            ['name' => 'Barbell Curl', 'sets' => 3, 'reps' => '8–10', 'rest' => '60s'],
                        ],
                    ],

                    [
                        'day' => 'Day 4 – Lower Body Power',
                        'focus' => 'Deadlift pattern, single-leg strength, stability',
                        'exercises' => [
                            ['name' => 'Conventional Deadlift', 'sets' => 4, 'reps' => '4–6', 'rest' => '150s'],
                            ['name' => 'Front Squat', 'sets' => 3, 'reps' => '6–8', 'rest' => '120s'],
                            ['name' => 'Walking Lunges', 'sets' => 3, 'reps' => '10 per leg', 'rest' => '90s'],
                            ['name' => 'Calf Raises', 'sets' => 4, 'reps' => '12–15', 'rest' => '60s'],
                            ['name' => 'Plank', 'sets' => 3, 'reps' => '45–60s', 'rest' => '45s'],
                        ],
                    ],

                ],

                'progression' => 'Weeks 1–3: Learn movement patterns, establish working weights | Weeks 4–6: Add 2.5–5 kg to main lifts every 1–2 weeks | Weeks 7–9: Increase intensity, reduce rep ranges on compounds | Week 10: Deload at 60% intensity to consolidate gains',
                'tips' => [
                    'Consume 1.6–2.2 g protein per kg bodyweight daily to support recovery',
                    'Sleep 7–9 hours — growth hormone peaks during deep sleep',
                    'Track every session: log weight, reps and RPE (rate of perceived exertion)',
                    'Warm up with 2–3 progressive sets before your working weight on compound lifts',
                ],
            ],

            'why_it_works' => [
                'title' => 'Why This Strength Training Plan Works',
                'content' => [
                    [
                        'heading' => 'Progressive Overload Is the Foundation of Strength',
                        'text' => 'Strength gains depend on systematically increasing the demands placed on your muscles. This plan uses a structured loading model — adding weight every 1–2 weeks — which aligns with the principle of specific adaptation to imposed demands (SAID). Without progressive overload, the body has no reason to get stronger.',
                    ],
                    [
                        'heading' => 'Compound Lifts Recruit Maximum Muscle Mass',
                        'text' => 'The plan centres on multi-joint movements like squats, deadlifts, bench press and overhead press. These exercises activate large amounts of muscle tissue simultaneously, producing greater mechanical tension — the primary driver of strength adaptation according to exercise physiology research.',
                    ],
                    [
                        'heading' => 'Upper/Lower Split Optimises Frequency and Recovery',
                        'text' => 'Training each muscle group twice per week provides the optimal stimulus-to-recovery ratio for strength development. Meta-analyses show that hitting a muscle group at least twice weekly produces significantly greater strength gains than once-per-week training, without the recovery cost of full-body daily sessions.',
                    ],
                    [
                        'heading' => 'Longer Rest Periods Maximise Force Production',
                        'text' => 'Rest periods of 90–150 seconds allow near-complete replenishment of phosphocreatine stores, the primary fuel for maximal efforts. Research shows that rest periods of 2–3 minutes between heavy compound sets lead to significantly more strength gains compared to shorter rest intervals.',
                    ],
                    [
                        'heading' => 'Built-In Deload Prevents Overtraining',
                        'text' => 'Week 10 reduces intensity to 60%, allowing the central nervous system and connective tissues to recover fully. Periodisation research consistently shows that planned deloads improve long-term strength outcomes by preventing accumulated fatigue from undermining performance.',
                    ],
                ],
            ],

            'common_mistakes' => [
                'title' => 'The 7 Most Common Strength Training Mistakes — and How to Avoid Them',
                'mistakes' => [
                    [
                        'title' => 'Ego Lifting With Poor Form',
                        'problem' => 'Loading more weight than you can handle with proper technique, especially on squats and deadlifts.',
                        'consequence' => 'Increased injury risk to the lower back, knees and shoulders. Reduced muscle activation because momentum replaces controlled force.',
                        'solution' => 'Use a weight you can control for the full range of motion. If your form breaks down before the last rep, the weight is too heavy.',
                        'example' => 'If your squat form breaks at 100 kg, train at 85–90 kg until depth and control are consistent for all prescribed reps.',
                    ],
                    [
                        'title' => 'Skipping the Warm-Up',
                        'problem' => 'Jumping straight to working sets without progressive warm-up sets.',
                        'consequence' => 'Cold muscles and joints are stiffer and weaker. You lift less weight and increase the risk of strains and joint pain.',
                        'solution' => 'Perform 2–3 warm-up sets at increasing loads before each compound lift, plus 5 minutes of general movement.',
                        'example' => 'Before a 100 kg squat: empty bar × 10, 60 kg × 5, 80 kg × 3, then working sets.',
                    ],
                    [
                        'title' => 'Neglecting Posterior Chain Work',
                        'problem' => 'Focusing on mirror muscles (chest, biceps) while ignoring back, hamstrings and glutes.',
                        'consequence' => 'Muscle imbalances develop, leading to rounded shoulders, lower back pain and reduced overall strength potential.',
                        'solution' => 'Match or exceed your pushing volume with pulling volume. This plan includes rows, deadlifts and face pulls for balance.',
                        'example' => 'For every 4 sets of bench press, perform at least 4 sets of rows or pull-ups.',
                    ],
                    [
                        'title' => 'Changing Programmes Too Often',
                        'problem' => 'Switching to a new programme every 2–3 weeks because progress feels slow.',
                        'consequence' => 'No consistent stimulus for adaptation. Strength gains require 6–10 weeks of consistent, progressive training on the same movements.',
                        'solution' => 'Commit to one programme for the full duration. Track your lifts to see objective progress rather than relying on how it feels.',
                        'example' => 'A 10 kg squat increase over 10 weeks (1 kg/week) is excellent progress — trust the process.',
                    ],
                    [
                        'title' => 'Cutting Rest Periods Short',
                        'problem' => 'Resting only 30–60 seconds between heavy compound sets to "keep the heart rate up".',
                        'consequence' => 'Phosphocreatine stores do not replenish fully, reducing force output on subsequent sets by 10–20%.',
                        'solution' => 'Rest 2–3 minutes between heavy compound lifts. Shorter rests (60s) are fine for accessory and isolation exercises.',
                        'example' => 'After a heavy set of 5 deadlifts: rest 150s, not 60s. Use a timer to stay consistent.',
                    ],
                    [
                        'title' => 'Insufficient Protein Intake',
                        'problem' => 'Eating less than 1.2 g protein per kg bodyweight while training for strength.',
                        'consequence' => 'Muscle repair is compromised, recovery between sessions is slower, and strength gains plateau prematurely.',
                        'solution' => 'Aim for 1.6–2.2 g protein per kg bodyweight daily, spread across 3–4 meals.',
                        'example' => 'At 80 kg bodyweight: 130–175 g protein daily (e.g. 250 g chicken, 200 g Greek yoghurt, 3 eggs, 1 protein shake).',
                    ],
                    [
                        'title' => 'Never Deloading',
                        'problem' => 'Pushing maximum intensity every single week for months without planned recovery weeks.',
                        'consequence' => 'Accumulated fatigue leads to stalled lifts, joint discomfort, poor sleep and eventual burnout or injury.',
                        'solution' => 'Schedule a deload week every 6–8 weeks. Reduce volume and intensity to 50–60% to allow full recovery.',
                        'example' => 'If your working squat is 100 kg × 5, deload week: 60 kg × 5 for 3 sets. You will come back stronger.',
                    ],
                ],
                'summary' => 'Most lifters stall not because of a bad programme, but because of poor execution. Master form, eat enough protein, rest properly and follow the plan — strength will come.',
            ],

            'faqs' => [
                [
                    'question' => 'How much weight should I start with?',
                    'answer' => 'Start with a weight you can control for all prescribed reps with clean form. If unsure, begin at roughly 60–70% of your estimated 1-rep max and increase from there.',
                ],
                [
                    'question' => 'Can I build strength training only 3 days per week?',
                    'answer' => 'Yes, but 4 days allows better volume distribution and recovery. If limited to 3 days, combine one upper and one lower session into a full-body day.',
                ],
                [
                    'question' => 'How long until I see strength improvements?',
                    'answer' => 'Beginners often notice measurable strength gains within 2–3 weeks. Intermediate lifters can expect meaningful progress after 4–6 weeks of consistent training.',
                ],
            ],
        ],

        /* ============================
           Fat Loss
        ============================ */
        'fat-loss' => [
            'title' => 'Free Fat Loss Workout Plan – 8 Weeks',
            'description' => 'Free 8-week fat loss workout plan. Superset-based strength and conditioning to burn fat, preserve muscle and boost metabolism.',
            'h1' => 'Fat Loss Workout Plan – Burn Fat and Keep Your Muscle',
            'intro' => 'This 8-week fat loss programme uses superset-based strength training and metabolic conditioning to maximise calorie burn while preserving lean muscle. Designed for 4 sessions per week at 40 minutes each.',
            'internal_type' => 'fat_loss',
            'published_at' => '2026-03-12',
            'last_updated_at' => '2026-03-12',

            'workout' => [
                'weeks' => 8,
                'workouts_per_week' => 4,
                'duration_minutes' => 40,
                'level' => 'Beginner to Advanced',
                'equipment' => ['Dumbbells', 'Kettlebell (optional)', 'Mat'],

                'schedule' => [

                    [
                        'day' => 'Day 1 – Push & Pull Supersets',
                        'focus' => 'Upper body strength with elevated heart rate',
                        'exercises' => [
                            ['name' => 'Dumbbell Bench Press → Dumbbell Rows (superset)', 'sets' => 4, 'reps' => '10–12 each', 'rest' => '60s'],
                            ['name' => 'Shoulder Press → Face Pulls (superset)', 'sets' => 3, 'reps' => '10–12 each', 'rest' => '60s'],
                            ['name' => 'Push-ups', 'sets' => 3, 'reps' => '12–15', 'rest' => '45s'],
                            ['name' => 'Renegade Rows', 'sets' => 3, 'reps' => '8–10 per side', 'rest' => '60s'],
                            ['name' => 'Plank', 'sets' => 3, 'reps' => '30–45s', 'rest' => '30s'],
                        ],
                    ],

                    [
                        'day' => 'Day 2 – Metabolic HIIT',
                        'focus' => 'Maximum calorie burn and cardiovascular conditioning',
                        'exercises' => [
                            ['name' => 'Kettlebell Swings (or Dumbbell Swings)', 'sets' => 4, 'reps' => '15–20', 'rest' => '45s'],
                            ['name' => 'Burpees', 'sets' => 4, 'reps' => '8–10', 'rest' => '45s'],
                            ['name' => 'Mountain Climbers', 'sets' => 4, 'reps' => '30s', 'rest' => '30s'],
                            ['name' => 'Jump Squats', 'sets' => 3, 'reps' => '12–15', 'rest' => '45s'],
                            ['name' => 'Dead Bug', 'sets' => 3, 'reps' => '10 per side', 'rest' => '30s'],
                        ],
                    ],

                    [
                        'day' => 'Day 3 – Legs & Core Supersets',
                        'focus' => 'Lower body strength with core activation',
                        'exercises' => [
                            ['name' => 'Goblet Squat → Reverse Lunges (superset)', 'sets' => 4, 'reps' => '10–12 each', 'rest' => '60s'],
                            ['name' => 'Romanian Deadlift → Glute Bridges (superset)', 'sets' => 3, 'reps' => '10–12 each', 'rest' => '60s'],
                            ['name' => 'Step-ups', 'sets' => 3, 'reps' => '10 per leg', 'rest' => '60s'],
                            ['name' => 'Russian Twists', 'sets' => 3, 'reps' => '20', 'rest' => '30s'],
                            ['name' => 'Hanging Leg Raise or Lying Leg Raise', 'sets' => 3, 'reps' => '10–12', 'rest' => '45s'],
                        ],
                    ],

                    [
                        'day' => 'Day 4 – Total Body Conditioning',
                        'focus' => 'Full body circuit for endurance and fat burn',
                        'exercises' => [
                            ['name' => 'Dumbbell Thrusters', 'sets' => 4, 'reps' => '10–12', 'rest' => '45s'],
                            ['name' => 'Dumbbell Renegade Row to Push-up', 'sets' => 3, 'reps' => '8–10', 'rest' => '60s'],
                            ['name' => 'Lateral Lunges', 'sets' => 3, 'reps' => '10 per side', 'rest' => '45s'],
                            ['name' => 'High Knees', 'sets' => 3, 'reps' => '30s', 'rest' => '30s'],
                            ['name' => 'Plank to Push-up', 'sets' => 3, 'reps' => '8–10', 'rest' => '45s'],
                        ],
                    ],

                ],

                'progression' => 'Weeks 1–2: Learn superset pacing, moderate intensity | Weeks 3–4: Shorten rest periods by 10–15s | Weeks 5–6: Increase weight on all strength exercises | Weeks 7–8: Add one extra set to compound supersets',
                'tips' => [
                    'Maintain a calorie deficit of 300–500 kcal — aggressive cuts slow metabolism and cost muscle',
                    'Consume 1.8–2.2 g protein per kg bodyweight daily to protect lean mass during a deficit',
                    'Aim for 8,000–10,000 steps daily outside of training to increase NEAT',
                    'Sleep 7–9 hours — poor sleep increases cortisol and hunger hormones, stalling fat loss',
                ],
            ],

            'why_it_works' => [
                'title' => 'Why This Fat Loss Workout Plan Works',
                'content' => [
                    [
                        'heading' => 'Supersets Keep Your Heart Rate Elevated',
                        'text' => 'Pairing opposing muscle groups with minimal rest keeps your heart rate in the fat-burning zone throughout the session. Research shows that superset-based training increases calorie expenditure by 30–40% compared to traditional straight-set formats, without requiring longer sessions.',
                    ],
                    [
                        'heading' => 'Strength Training Preserves Muscle During a Deficit',
                        'text' => 'When you eat fewer calories than you burn, your body can break down muscle for energy. The resistance training in this plan sends a strong signal to preserve lean tissue. Studies consistently show that combining strength training with a calorie deficit results in significantly more fat loss and less muscle loss than dieting alone.',
                    ],
                    [
                        'heading' => 'EPOC Extends Your Calorie Burn for Hours',
                        'text' => 'High-intensity supersets and metabolic conditioning create excess post-exercise oxygen consumption (EPOC). This means your metabolism stays elevated for 12–24 hours after each session, burning an additional 50–150 kcal beyond the workout itself.',
                    ],
                    [
                        'heading' => 'Short Sessions Improve Adherence',
                        'text' => 'At 40 minutes per session, this plan removes the most common barrier to consistency: time. Behavioural research shows that shorter, more frequent workouts produce better long-term adherence than longer, less frequent sessions — and adherence is the single strongest predictor of fat loss success.',
                    ],
                    [
                        'heading' => 'Metabolic Conditioning Improves Insulin Sensitivity',
                        'text' => 'The HIIT and conditioning days improve how your body handles carbohydrates by increasing insulin sensitivity. Better insulin sensitivity means your body is more efficient at using food for energy rather than storing it as fat — a key metabolic advantage for long-term body composition.',
                    ],
                ],
            ],

            'common_mistakes' => [
                'title' => 'The 7 Most Common Fat Loss Mistakes — and How to Avoid Them',
                'mistakes' => [
                    [
                        'title' => 'Relying on Cardio Alone',
                        'problem' => 'Spending 60+ minutes on the treadmill or elliptical without any resistance training.',
                        'consequence' => 'You lose weight, but a significant portion comes from muscle. Metabolic rate drops, making it harder to sustain fat loss and easier to regain weight.',
                        'solution' => 'Prioritise strength-based training at least 3 times per week. Use cardio as a supplement, not the foundation.',
                        'example' => 'Replace 2 of 5 weekly cardio sessions with the superset strength days from this plan.',
                    ],
                    [
                        'title' => 'Cutting Calories Too Aggressively',
                        'problem' => 'Dropping to 1,000–1,200 kcal per day to speed up results.',
                        'consequence' => 'Metabolic adaptation kicks in within 1–2 weeks. Hunger hormones surge, energy crashes, and muscle loss accelerates. Most people binge and regain within a month.',
                        'solution' => 'Use a moderate deficit of 300–500 kcal below maintenance. This supports 0.5–0.7 kg of fat loss per week without metabolic collapse.',
                        'example' => 'If maintenance is 2,400 kcal: eat 1,900–2,100 kcal, not 1,200.',
                    ],
                    [
                        'title' => 'Ignoring Protein Intake',
                        'problem' => 'Eating high-carb, low-protein meals while in a calorie deficit.',
                        'consequence' => 'Muscle breakdown increases, recovery suffers, and hunger is harder to manage. Protein has the highest thermic effect of any macronutrient — skipping it wastes a metabolic advantage.',
                        'solution' => 'Target 1.8–2.2 g protein per kg bodyweight daily. Spread intake across 3–4 meals for optimal muscle protein synthesis.',
                        'example' => 'At 70 kg: aim for 125–155 g protein daily (e.g. 200 g chicken, 150 g Greek yoghurt, 2 eggs, 1 protein shake).',
                    ],
                    [
                        'title' => 'Resting Too Long Between Supersets',
                        'problem' => 'Taking 2–3 minutes between superset pairs, eliminating the metabolic benefit.',
                        'consequence' => 'Heart rate drops, calorie burn decreases by 30–40%, and the session loses its conditioning effect. You get a strength workout but miss the fat loss advantage.',
                        'solution' => 'Rest 30–60 seconds between superset pairs. Use a timer. The discomfort is the point — it drives EPOC.',
                        'example' => 'Complete bench press → rows with no rest between, then rest 60s before the next pair.',
                    ],
                    [
                        'title' => 'Not Tracking Daily Movement',
                        'problem' => 'Training 4 times per week but sitting for 10+ hours on rest days.',
                        'consequence' => 'NEAT (non-exercise activity thermogenesis) can account for 15–30% of total daily energy expenditure. Low step counts on rest days can eliminate the calorie deficit you created through training.',
                        'solution' => 'Track daily steps and aim for 8,000–10,000 on both training and rest days.',
                        'example' => 'A 15-minute walk after each meal adds roughly 3,000 steps and 150 kcal — without feeling like exercise.',
                    ],
                    [
                        'title' => 'Skipping Workouts After a Bad Meal',
                        'problem' => 'Eating a large meal or having a "cheat day" and then skipping the next workout out of guilt.',
                        'consequence' => 'One overeating episode adds maybe 500–1,000 extra kcal. Skipping a workout removes 200–400 kcal of expenditure plus the metabolic benefits. The gap widens.',
                        'solution' => 'Never punish or compensate. A single overeating episode is statistically irrelevant over 8 weeks. Show up for the next session regardless.',
                        'example' => 'Even after a 3,000 kcal dinner: train the next day as planned. One meal does not define an 8-week programme.',
                    ],
                    [
                        'title' => 'Sleeping Less Than 6 Hours',
                        'problem' => 'Chronically under-sleeping while training hard and eating in a deficit.',
                        'consequence' => 'Cortisol rises, testosterone and growth hormone drop, ghrelin (hunger hormone) increases by up to 28%, and leptin (satiety hormone) decreases. Fat loss stalls even with perfect training and nutrition.',
                        'solution' => 'Protect 7–9 hours of sleep. It is not optional during a fat loss phase — it is arguably the most important recovery variable.',
                        'example' => 'With 5.5 hours of sleep, up to 70% of weight lost comes from muscle. With 8 hours, up to 80% comes from fat.',
                    ],
                ],
                'summary' => 'Fat loss fails when people focus only on what happens in the gym. This plan works because it addresses training, nutrition, recovery and daily movement as one system.',
            ],

            'faqs' => [
                [
                    'question' => 'What is the difference between fat loss and weight loss?',
                    'answer' => 'Weight loss includes muscle, water and fat. Fat loss specifically targets body fat while preserving muscle — which this plan achieves through strength-based training combined with a moderate calorie deficit.',
                ],
                [
                    'question' => 'Can I do this plan at home?',
                    'answer' => 'Yes. All you need is a pair of dumbbells and a mat. A kettlebell is optional — dumbbell swings work as a substitute.',
                ],
                [
                    'question' => 'How quickly will I see results?',
                    'answer' => 'Most people notice improved energy and performance within 2 weeks. Visible fat loss typically becomes apparent after 4–6 weeks of consistent training and nutrition.',
                ],
            ],
        ],

        /* ============================
           Abs
        ============================ */
        'abs' => [
            'title' => 'Free Abs Workout Plan – 8 Weeks',
            'description' => 'Free 8-week abs workout plan. Targeted core training with anti-movement, rotation and flexion exercises – suitable for home or gym.',
            'h1' => 'Abs Workout Plan – Build a Strong Core in 8 Weeks',
            'intro' => 'This 8-week abs workout plan develops your entire core musculature systematically. Three focused sessions per week train stability, rotational strength and flexion — for visible abs and functional core strength.',
            'internal_type' => 'abs',
            'published_at' => '2026-03-12',
            'last_updated_at' => '2026-03-12',

            'workout' => [
                'weeks' => 8,
                'workouts_per_week' => 3,
                'duration_minutes' => 35,
                'level' => 'Beginner to Advanced',
                'equipment' => ['Mat', 'Pull-up Bar (optional)', 'Dumbbells (optional)'],

                'schedule' => [

                    [
                        'day' => 'Day 1 – Anti-Movement & Stability',
                        'focus' => 'Isometric core stability, anti-extension, anti-lateral flexion',
                        'exercises' => [
                            ['name' => 'Plank', 'sets' => 3, 'reps' => '30–60s', 'rest' => '45s'],
                            ['name' => 'Side Plank', 'sets' => 3, 'reps' => '25–40s per side', 'rest' => '45s'],
                            ['name' => 'Dead Bug', 'sets' => 3, 'reps' => '10–12 per side', 'rest' => '45s'],
                            ['name' => 'Bird Dog', 'sets' => 3, 'reps' => '10–12 per side', 'rest' => '45s'],
                            ['name' => 'Hollow Body Hold', 'sets' => 3, 'reps' => '20–30s', 'rest' => '45s'],
                        ],
                    ],

                    [
                        'day' => 'Day 2 – Rotation & Dynamics',
                        'focus' => 'Rotational strength, anti-rotation, obliques',
                        'exercises' => [
                            ['name' => 'Pallof Press', 'sets' => 3, 'reps' => '10–12 per side', 'rest' => '45s'],
                            ['name' => 'Russian Twist', 'sets' => 3, 'reps' => '12–16 total', 'rest' => '45s'],
                            ['name' => 'Bicycle Crunches', 'sets' => 3, 'reps' => '12–16 per side', 'rest' => '45s'],
                            ['name' => 'Mountain Climbers', 'sets' => 3, 'reps' => '20–30s', 'rest' => '45s'],
                            ['name' => 'Suitcase Carry', 'sets' => 3, 'reps' => '30–40m per side', 'rest' => '60s'],
                        ],
                    ],

                    [
                        'day' => 'Day 3 – Flexion & Lower Abs',
                        'focus' => 'Upper and lower rectus abdominis, hip flexor control',
                        'exercises' => [
                            ['name' => 'Hanging Leg Raise or Knee Raise', 'sets' => 3, 'reps' => '8–12', 'rest' => '60s'],
                            ['name' => 'Reverse Crunch', 'sets' => 3, 'reps' => '12–15', 'rest' => '45s'],
                            ['name' => 'Ab Wheel Rollout or Plank Walk-Out', 'sets' => 3, 'reps' => '8–12', 'rest' => '60s'],
                            ['name' => 'Toe Touches', 'sets' => 3, 'reps' => '12–15', 'rest' => '45s'],
                            ['name' => 'L-Sit Hold or Tuck Hold', 'sets' => 3, 'reps' => '15–25s', 'rest' => '60s'],
                        ],
                    ],

                ],

                'progression' => 'Weeks 1–2: Learn proper form, use shorter hold times and easier variations | Weeks 3–4: Increase hold times by 10–15s, add reps | Weeks 5–6: Introduce weighted variations (e.g. weighted planks, dumbbell Russian Twist) | Weeks 7–8: Progress to advanced variations (Hanging Leg Raise instead of Knee Raise, Ab Wheel instead of Walk-Out)',
                'tips' => [
                    'Body fat percentage matters most: visible abs typically require below 15% for men and below 22% for women',
                    'Consume 1.6–2.0 g protein per kg bodyweight daily to support muscle growth in a calorie deficit',
                    'Exhale fully on every rep to maximally activate the deep core musculature',
                    'Never train core directly before heavy squats or deadlifts — schedule these sessions on separate days',
                ],
            ],

            'why_it_works' => [
                'title' => 'Why This Abs Workout Plan Works',
                'content' => [
                    [
                        'heading' => 'Three Core Functions, Not Just Crunches',
                        'text' => 'Your core has three primary functions: stabilisation (plank), rotation (Russian twist) and flexion (leg raises). This plan trains all three on separate days. Research shows that multidirectional core training increases trunk strength 29% more than flexion-only programmes.',
                    ],
                    [
                        'heading' => 'Anti-Movement Exercises Build Functional Stability',
                        'text' => 'Exercises like planks, dead bugs and Pallof presses train your core to resist movement — exactly what it needs to do in daily life and sport. Spinal stability research confirms that these anti-movement patterns significantly reduce lower back injury risk.',
                    ],
                    [
                        'heading' => 'Progressive Overload Applies to Abs Too',
                        'text' => 'Abs are skeletal muscles and respond to the same growth principle: progressive overload. This plan systematically progresses from bodyweight through longer holds to weighted and advanced variations — so your core muscles actually grow in size and strength.',
                    ],
                    [
                        'heading' => 'High Frequency Accelerates Core Development',
                        'text' => 'Core muscles recover faster than large muscle groups because they are smaller and designed for endurance work. Three sessions per week with different emphases provide sufficient volume for growth while allowing adequate recovery between sessions.',
                    ],
                    [
                        'heading' => 'Short Sessions Boost Consistency',
                        'text' => '35 minutes per session dramatically lowers the barrier to entry. Adherence studies show that shorter training sessions lead to 40% higher compliance rates than longer programmes — and consistency is the strongest predictor of visible results.',
                    ],
                ],
            ],

            'common_mistakes' => [
                'title' => 'The 7 Most Common Abs Training Mistakes — and How to Avoid Them',
                'mistakes' => [
                    [
                        'title' => 'Only Doing Crunches',
                        'problem' => 'The entire core routine consists of crunches and sit-ups — always the same movement plane.',
                        'consequence' => 'Only the upper rectus abdominis is targeted. Obliques, lower core and deep stabilisers remain underdeveloped.',
                        'solution' => 'Train all core functions: stability (plank), rotation (Russian twist), anti-rotation (Pallof press) and flexion (leg raises).',
                        'example' => 'Instead of 100 crunches: 3 sets of planks + 3 sets of Pallof press + 3 sets of hanging leg raises.',
                    ],
                    [
                        'title' => 'Ignoring Body Fat',
                        'problem' => 'Training abs intensely without addressing nutrition or calorie deficit.',
                        'consequence' => 'Your abs get stronger but remain hidden under a layer of fat. You see no definition despite hard training.',
                        'solution' => 'Combine core training with a moderate calorie deficit (300–500 kcal). No amount of training can out-train a poor diet.',
                        'example' => 'At a maintenance of 2,400 kcal: eat 1,900–2,100 kcal with 1.6–2.0 g protein per kg bodyweight.',
                    ],
                    [
                        'title' => 'Using Hip Flexors Instead of Core',
                        'problem' => 'Lifting legs with hip flexors rather than controlling the movement with the core during leg raises and sit-ups.',
                        'consequence' => 'Lower back pain, overactive hip flexors and minimal ab activation despite high training volume.',
                        'solution' => 'Press your lower back into the floor, move your legs slowly and with control. If you feel it in your lower back, bend your knees more.',
                        'example' => 'On reverse crunches: lift the pelvis off the floor rather than just swinging the legs. 12 controlled reps beat 30 uncontrolled ones.',
                    ],
                    [
                        'title' => 'Too Much Volume, Too Little Intensity',
                        'problem' => 'Doing 50–100 reps per exercise with bodyweight but never progressing the difficulty.',
                        'consequence' => 'You are training endurance, not muscle growth. The abs do not get thicker or more defined, just more fatigue-resistant.',
                        'solution' => 'Keep rep ranges at 8–15 and increase difficulty through added weight or leverage variations.',
                        'example' => 'Instead of 50 crunches: 3 × 12 weighted cable crunches or 3 × 10 ab wheel rollouts.',
                    ],
                    [
                        'title' => 'Training Core Before Heavy Compound Lifts',
                        'problem' => 'Performing intense ab training directly before squats or deadlifts.',
                        'consequence' => 'A pre-fatigued core cannot stabilise the spine under heavy loads. Performance drops and injury risk rises.',
                        'solution' => 'Train core at the end of your session or on separate days. Never directly before heavy lifting.',
                        'example' => 'Monday: squats + leg training. Tuesday: core session. Not the other way around.',
                    ],
                    [
                        'title' => 'Holding Your Breath',
                        'problem' => 'Holding your breath or only breathing shallowly during core exercises.',
                        'consequence' => 'The transversus abdominis — the deepest core layer — is not activated. Dizziness and raised blood pressure are additional risks.',
                        'solution' => 'Exhale deliberately and fully during every effort phase. Forced exhalation activates the deep core automatically.',
                        'example' => 'On crunches: exhale fully as you come up, inhale controlled as you lower.',
                    ],
                    [
                        'title' => 'Neglecting Recovery',
                        'problem' => 'Training abs every single day without rest days because "abs recover fast".',
                        'consequence' => 'Even though core muscles recover faster, they still need 24–48 hours. Daily training leads to overtraining and stagnating results.',
                        'solution' => '3–4 core sessions per week with at least one rest day in between. This plan uses 3 days with alternating focus.',
                        'example' => 'Monday: stability, Wednesday: rotation, Friday: flexion. Tuesday/Thursday/weekend: rest or other training.',
                    ],
                ],
                'summary' => 'Visible abs require the combination of targeted training across all core functions, progressive overload and controlled nutrition. This plan provides the training structure — your diet determines whether they show.',
            ],

            'faqs' => [
                [
                    'question' => 'Can I get a six-pack with this plan?',
                    'answer' => 'This plan builds the muscle, but visibility depends on body fat percentage. Below 15% (men) or 22% (women) the muscles become visible. Combine the plan with a moderate calorie deficit for best results.',
                ],
                [
                    'question' => 'Can I combine this core training with my regular strength training?',
                    'answer' => 'Yes. Schedule core sessions on days without heavy squats or deadlifts, or add them at the end of your regular session. Avoid core training directly before heavy compound lifts.',
                ],
                [
                    'question' => 'Do I need equipment for this plan?',
                    'answer' => 'No. Most exercises use bodyweight and a mat. A pull-up bar for hanging leg raises and dumbbells for weighted variations are optional but recommended from week 5 onwards.',
                ],
            ],
        ],

        /* ============================
           Over 40
        ============================ */
        'over-40' => [
            'title' => 'Free Over-40 Workout Plan – 10 Weeks',
            'description' => 'Free 10-week workout plan for over 40. Joint-friendly strength and mobility training to fight muscle loss, stiffness and bone density decline.',
            'h1' => 'Over-40 Workout Plan – Stay Strong and Mobile',
            'intro' => 'This 10-week workout plan is designed specifically for trainees over 40. Three balanced sessions per week combine strength training, mobility and balance work — joint-friendly, progressive and practical for everyday life.',
            'internal_type' => 'over_40',
            'published_at' => '2026-03-12',
            'last_updated_at' => '2026-03-12',

            'workout' => [
                'weeks' => 10,
                'workouts_per_week' => 3,
                'duration_minutes' => 45,
                'level' => 'Beginner to Advanced',
                'equipment' => ['Dumbbells', 'Mat', 'Chair or Bench'],

                'schedule' => [

                    [
                        'day' => 'Day 1 – Full Body Strength',
                        'focus' => 'Compound movements with controlled tempo, emphasis on posture and stability',
                        'exercises' => [
                            ['name' => 'Goblet Squat', 'sets' => 3, 'reps' => '10–12', 'rest' => '90s'],
                            ['name' => 'Dumbbell Chest Press (Bench or Floor)', 'sets' => 3, 'reps' => '10–12', 'rest' => '90s'],
                            ['name' => 'Single-Arm Dumbbell Row', 'sets' => 3, 'reps' => '10–12 per side', 'rest' => '60s'],
                            ['name' => 'Romanian Deadlift (Dumbbells)', 'sets' => 3, 'reps' => '10–12', 'rest' => '90s'],
                            ['name' => 'Plank', 'sets' => 3, 'reps' => '25–45s', 'rest' => '45s'],
                        ],
                    ],

                    [
                        'day' => 'Day 2 – Mobility & Balance',
                        'focus' => 'Joint mobility, single-leg stability, fall prevention',
                        'exercises' => [
                            ['name' => 'Alternating Lunges', 'sets' => 3, 'reps' => '8–10 per leg', 'rest' => '60s'],
                            ['name' => 'Seated Dumbbell Shoulder Press', 'sets' => 3, 'reps' => '10–12', 'rest' => '90s'],
                            ['name' => 'Bird Dog', 'sets' => 3, 'reps' => '8–10 per side', 'rest' => '45s'],
                            ['name' => 'Step-Ups (Chair or Bench)', 'sets' => 3, 'reps' => '8–10 per leg', 'rest' => '60s'],
                            ['name' => 'Wall Chest Stretch', 'sets' => 3, 'reps' => '30–40s per side', 'rest' => '30s'],
                        ],
                    ],

                    [
                        'day' => 'Day 3 – Strength & Endurance',
                        'focus' => 'Higher volume with moderate weight, metabolic stimulus',
                        'exercises' => [
                            ['name' => 'Sumo Squat (Dumbbell)', 'sets' => 3, 'reps' => '12–15', 'rest' => '60s'],
                            ['name' => 'Push-Ups (adjusted to level)', 'sets' => 3, 'reps' => '8–15', 'rest' => '60s'],
                            ['name' => 'Dumbbell Reverse Fly', 'sets' => 3, 'reps' => '12–15', 'rest' => '60s'],
                            ['name' => 'Weighted Glute Bridge', 'sets' => 3, 'reps' => '12–15', 'rest' => '60s'],
                            ['name' => 'Dead Bug', 'sets' => 3, 'reps' => '10–12 per side', 'rest' => '45s'],
                        ],
                    ],

                ],

                'progression' => 'Weeks 1–3: Focus on movement quality and tempo, use light weights | Weeks 4–6: Increase weight by 1–2 kg, extend hold times | Weeks 7–9: Lower rep ranges (8–10), increase load | Week 10: Deload at 60% intensity, focus on mobility and technique refinement',
                'tips' => [
                    'Consume 1.6–2.0 g protein per kg bodyweight daily — protein needs increase after 40 due to declining muscle protein synthesis',
                    'Warm up 5–10 minutes before every session: mobilise joints, raise heart rate gently',
                    'Use controlled tempo: 2 seconds lifting, 3 seconds lowering — protects joints and maximises muscle tension',
                    'Sleep 7–9 hours — recovery takes measurably longer after 40',
                ],
            ],

            'why_it_works' => [
                'title' => 'Why This Over-40 Workout Plan Works',
                'content' => [
                    [
                        'heading' => 'Strength Training Is the Best Defence Against Muscle Loss',
                        'text' => 'From age 30, the body loses 3–8% of muscle mass per decade without targeted training (sarcopenia). This plan uses the most effective countermeasure: progressive resistance training. Research shows that even trainees over 60 can still build significant muscle mass and strength.',
                    ],
                    [
                        'heading' => 'Joint-Friendly Training Prevents Overuse Injuries',
                        'text' => 'Unlike plans designed for younger trainees, this programme avoids high-impact movements and extreme joint positions. Controlled tempo (2-3-1 cadence) reduces peak load on tendons and ligaments by up to 40%, while muscle activation actually increases.',
                    ],
                    [
                        'heading' => 'Mobility and Balance Work Reduces Fall Risk',
                        'text' => 'From age 40, proprioceptive performance declines measurably. A dedicated mobility day with single-leg exercises, balance training and stretches trains exactly the neuromuscular skills that protect against falls and injuries in daily life.',
                    ],
                    [
                        'heading' => 'Three Sessions Per Week Optimise Recovery',
                        'text' => 'Recovery time increases demonstrably with age. Three sessions with at least 48 hours between them give muscles, tendons and the nervous system enough time for complete recovery — the decisive factor for continuous progress after 40.',
                    ],
                    [
                        'heading' => 'Bone Density Increases Through Resistance Training',
                        'text' => 'Osteoporosis risk rises significantly after 40, especially in women. Strength training creates mechanical stress on bones, stimulating osteoblast activity. Meta-analyses confirm that 2–3 strength sessions per week can significantly slow age-related bone density loss.',
                    ],
                ],
            ],

            'common_mistakes' => [
                'title' => 'The 7 Most Common Over-40 Training Mistakes — and How to Avoid Them',
                'mistakes' => [
                    [
                        'title' => 'Training Like You Are 25',
                        'problem' => 'Using the same exercises, intensity and frequency as 15 years ago, without adjusting for changed recovery and joint health.',
                        'consequence' => 'Chronic joint pain, tendinitis and loss of motivation due to constant discomfort.',
                        'solution' => 'Adapt tempo, volume and exercise selection. Controlled tempo with moderate weights and full range of motion is more effective than heavy maximal attempts.',
                        'example' => 'Instead of heavy barbell squats for 3 reps: goblet squats for 3 × 12 at a controlled 2-3-1 tempo.',
                    ],
                    [
                        'title' => 'Skipping the Warm-Up',
                        'problem' => 'Jumping straight to working sets because time is short.',
                        'consequence' => 'Joints are stiffer, synovial fluid is not distributed, and injury risk increases significantly. After 40, tendons and ligaments are less elastic.',
                        'solution' => 'At least 5–10 minutes of warm-up: joint circles, dynamic stretches, 1–2 light warm-up sets per exercise.',
                        'example' => 'Before goblet squats: 20 bodyweight squats, hip circles, then 1 set at 50% of working weight.',
                    ],
                    [
                        'title' => 'Ignoring Mobility Work',
                        'problem' => 'Only doing strength training with no dedicated mobility or balance work.',
                        'consequence' => 'Increasing stiffness in hips, shoulders and thoracic spine. Everyday movements become restricted and fall risk rises.',
                        'solution' => 'At least one session per week with a mobility and balance focus. This plan integrates that on Day 2.',
                        'example' => 'Bird dogs, step-ups and wall chest stretches on mobility day take only 15 minutes but protect against injuries long-term.',
                    ],
                    [
                        'title' => 'Insufficient Protein Intake',
                        'problem' => 'Eating the same diet as at 30, despite protein needs increasing measurably after 40.',
                        'consequence' => 'The body needs more protein per meal after 40 to trigger muscle protein synthesis (anabolic resistance). Without adjustment, muscle loss accelerates.',
                        'solution' => 'At least 1.6–2.0 g protein per kg bodyweight, spread across 3–4 meals with at least 30 g protein each.',
                        'example' => 'At 80 kg bodyweight: 130–160 g protein daily. Each meal: 200 g chicken or 250 g Greek yoghurt or 3 eggs + protein shake.',
                    ],
                    [
                        'title' => 'Ignoring Pain Signals',
                        'problem' => 'Pushing through joint pain because "it comes with age".',
                        'consequence' => 'Acute pain becomes chronic inflammation. Compensatory movement patterns lead to secondary injuries at other joints.',
                        'solution' => 'Distinguish muscle soreness (24–72h, symmetrical, dull) from joint pain (one-sided, sharp, directly at the joint). Joint pain = modify the exercise or rest.',
                        'example' => 'Knee pain during deep squats: limit depth to 90° or switch to goblet squats with elevated heels.',
                    ],
                    [
                        'title' => 'Only Doing Cardio',
                        'problem' => 'Exclusively running, cycling or swimming with no strength training.',
                        'consequence' => 'Cardio alone does not protect against sarcopenia or osteoporosis. Muscle mass and bone density decline despite high cardiovascular fitness.',
                        'solution' => 'Strength training is more important than cardio after 40. Ideal combination: 3× strength + 2× moderate cardio (walking, cycling).',
                        'example' => 'Instead of 5× jogging: 3× this training plan + 2× 30 min brisk walking. Result: more muscle, stronger bones and still good endurance.',
                    ],
                    [
                        'title' => 'Neglecting Recovery',
                        'problem' => 'Training every day or pushing hard despite poor sleep.',
                        'consequence' => 'After 40, testosterone and growth hormone levels decline. Recovery takes 20–30% longer than at 25. Overtraining leads to performance drops and increased susceptibility to illness.',
                        'solution' => 'At least 48 hours between strength sessions. Prioritise sleep: 7–9 hours. On rest days, do light movement (walking, stretching).',
                        'example' => 'Monday: strength, Tuesday: walk, Wednesday: mobility, Thursday: rest, Friday: strength, Saturday/Sunday: active recovery.',
                    ],
                ],
                'summary' => 'Training after 40 does not require a completely new system — just intelligent adjustments: controlled tempo, mobility work, sufficient protein and respected recovery. This plan delivers exactly that.',
            ],

            'faqs' => [
                [
                    'question' => 'Is strength training still safe after 40?',
                    'answer' => 'Yes. Strength training is considered the most important exercise form after 40 by the WHO and sports medicine for preventing muscle loss, osteoporosis and metabolic disease. The keys are controlled tempo and progressive overload.',
                ],
                [
                    'question' => 'Can I do this plan without a gym?',
                    'answer' => 'Yes. All you need is a pair of dumbbells, a mat and a sturdy chair or bench. Every exercise works at home.',
                ],
                [
                    'question' => 'How is this plan different from a regular training plan?',
                    'answer' => 'Controlled tempo instead of maximal loads, a dedicated mobility day, longer rest periods and a deload in week 10. These adjustments respect the changed recovery capacity and joint situation after 40.',
                ],
            ],
        ],
    ],
];
