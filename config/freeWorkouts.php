<?php

return [
    'index_labels' => [
        'de' => [
            'heading' => 'Kostenlose Trainingspläne',
            'intro' => 'Entdecke unsere wissenschaftlich fundierten Trainingspläne für jedes Ziel. Kostenlos, personalisiert und sofort verfügbar.',
            'viewPlan' => 'Plan ansehen',
            'ctaHeading' => 'Bereit für dein Training?',
            'ctaText' => 'Wähle den passenden Plan für dein Ziel und starte noch heute mit deinem kostenlosen, wissenschaftlich fundierten Trainingsprogramm.',
        ],
        'en' => [
            'heading' => 'Free Workout Plans',
            'intro' => 'Discover our science-based workout plans for every goal. Free, personalized and available immediately.',
            'viewPlan' => 'View Plan',
            'ctaHeading' => 'Ready for Your Training?',
            'ctaText' => 'Choose the right plan for your goal and start today with your free, science-based training program.',
        ],
    ],

    'de' => [
        /* ============================
           Abnehmen
        ============================ */
        'abnehmen' => [
            'title' => 'Kostenloser Trainingsplan zum Abnehmen – 8 Wochen | fitnessAI.me',
            'description' => 'Kostenloser 8-Wochen-Trainingsplan zum Abnehmen. Kraft- & Cardio-Training für nachhaltigen Fettabbau – Zuhause & Gym.',
            'h1' => 'Trainingsplan zum Abnehmen – nachhaltig Fett verlieren',
            'intro' => 'Dieser strukturierte 8-Wochen-Trainingsplan kombiniert Krafttraining und Cardio, um Fett abzubauen, Muskeln zu erhalten und den Stoffwechsel nachhaltig zu steigern.',
            'internal_type' => 'weight_loss',
            'why_it_works' => [
                'title' => 'Warum dieser Trainingsplan beim Abnehmen funktioniert',
                'content' => [
                    [
                        'heading' => 'Kombination aus Kraft und Cardio maximiert Fettabbau',
                        'text' => 'Dieser Plan nutzt eine bewährte Kombination: Krafttraining erhält deine Muskelmasse während des Kaloriendefizits, während HIIT-Einheiten den Kalorienverbrauch maximieren. Studien zeigen, dass diese Kombination bis zu 40% effektiver ist als reines Cardio-Training beim Fettabbau.'
                    ],
                    [
                        'heading' => 'Der Nachbrenneffekt (EPOC) arbeitet für dich',
                        'text' => 'Durch intensives Krafttraining und HIIT-Intervalle erzeugst du einen erhöhten Sauerstoffverbrauch nach dem Training (EPOC). Dein Körper verbrennt dadurch bis zu 48 Stunden nach dem Training zusätzliche Kalorien – selbst im Ruhezustand. Dieser "Nachbrenneffekt" kann den Gesamtkalorienverbrauch um 6-15% erhöhen.'
                    ],
                    [
                        'heading' => 'Muskelerhalt schützt vor Jojo-Effekt',
                        'text' => 'Im Gegensatz zu reinen Diäten ohne Training erhält dieser Plan deine Muskelmasse. Das ist entscheidend: Jedes Kilogramm Muskelmasse verbrennt täglich etwa 13 kcal im Ruhezustand. Verlierst du Muskeln durch falsche Diäten, sinkt dein Grundumsatz – der Jojo-Effekt ist vorprogrammiert.'
                    ],
                    [
                        'heading' => 'Progressive Steigerung verhindert Plateaus',
                        'text' => 'Der 8-Wochen-Aufbau mit systematischer Progression (mehr Wiederholungen, kürzere Pausen, höhere Intensität) sorgt dafür, dass dein Körper sich kontinuierlich anpassen muss. So vermeidest du das typische Plateau nach 3-4 Wochen, wo viele andere Pläne stagnieren.'
                    ],
                    [
                        'heading' => 'Wissenschaftlich fundierte Frequenz',
                        'text' => '3 Trainingseinheiten pro Woche bieten das optimale Verhältnis zwischen Belastung und Regeneration. Studien der American College of Sports Medicine zeigen: Diese Frequenz ermöglicht nachhaltigen Fettabbau von 0,5-1 kg pro Woche, ohne den Körper zu überlasten oder Muskelabbau zu riskieren.'
                    ]
                ]
            ],

            'common_mistakes' => [
                'title' => 'Die 7 häufigsten Fehler beim Abnehmen – und wie du sie vermeidest',
                'mistakes' => [
                    [
                        'title' => 'Zu großes Kaloriendefizit',
                        'problem' => 'Viele setzen auf extreme Diäten mit 800-1000 kcal täglich und erwarten schnelle Erfolge.',
                        'consequence' => 'Dein Körper schaltet in den "Hungermodus", Stoffwechsel verlangsamt sich, Muskelmasse wird abgebaut, Energie fehlt für Training.',
                        'solution' => 'Halte ein moderates Defizit von 300-500 kcal. Das bedeutet: 0,5-0,7 kg Gewichtsverlust pro Woche – nachhaltig und ohne Muskelabbau.',
                        'example' => 'Bei einem Grundumsatz von 2000 kcal: Esse 1500-1700 kcal statt 1000 kcal.'
                    ],
                    [
                        'title' => 'Zu wenig Protein',
                        'problem' => 'Protein wird unterschätzt und durch zu viele Kohlenhydrate ersetzt.',
                        'consequence' => 'Muskelmasse geht verloren, Hunger-Attacken nehmen zu, Stoffwechsel sinkt. Du verlierst zwar Gewicht, aber das falsche (Muskeln statt Fett).',
                        'solution' => 'Ziel: 1,6-2,0g Protein pro kg Körpergewicht täglich. Priorisiere proteinreiche Lebensmittel bei jeder Mahlzeit.',
                        'example' => 'Bei 75 kg Körpergewicht: 120-150g Protein täglich (z.B. 200g Hähnchen, 200g Magerquark, 3 Eier, 1 Shake).'
                    ],
                    [
                        'title' => 'Cardio ohne Krafttraining',
                        'problem' => 'Ausschließlich Joggen oder Radfahren, kein Krafttraining.',
                        'consequence' => 'Zwar Kalorienverbrauch, aber kein Muskelschutz. Ergebnis: "Skinny Fat" – niedriges Gewicht, aber hoher Körperfettanteil und schwache Muskulatur.',
                        'solution' => 'Priorisiere Krafttraining (mindestens 2x/Woche), nutze Cardio als Ergänzung. Unser Plan macht genau das.',
                        'example' => 'Statt 5x Joggen: 3x dieser Trainingsplan (Kraft + HIIT) + 2x lockeres Spazieren.'
                    ],
                    [
                        'title' => 'Inkonsistentes Training',
                        'problem' => 'Montag: Hochmotiviert. Donnerstag: Keine Lust. Nächste Woche: Wieder bei Null.',
                        'consequence' => 'Keine Anpassung des Körpers, kein Muskelaufbau, kein Fortschritt. Fettabbau braucht Kontinuität über Wochen.',
                        'solution' => 'Setze feste Trainingstage (z.B. Mo/Mi/Fr). Auch ein 20-Minuten-Training ist besser als ausfallen lassen. Nutze Habit-Stacking: Training direkt nach Feierabend.',
                        'example' => 'Statt "wenn ich Zeit habe": Kalendereintrag "Mo 18:00 Training" wie ein wichtiger Termin.'
                    ],
                    [
                        'title' => 'Zu wenig Schlaf',
                        'problem' => 'Nur 5-6 Stunden Schlaf pro Nacht, aber hartes Training und Diät.',
                        'consequence' => 'Cortisol-Spiegel steigt (Stresshormon), Testosteron sinkt, Hunger-Hormone geraten durcheinander (mehr Ghrelin = mehr Hunger). Fettabbau stoppt.',
                        'solution' => '7-9 Stunden Schlaf pro Nacht. Studien zeigen: Guter Schlaf kann Fettabbau um bis zu 55% steigern bei gleichem Training.',
                        'example' => 'Bei 6h Schlaf: 60% des Gewichtsverlusts ist Muskelmasse. Bei 8h Schlaf: 80% des Verlusts ist Fett.'
                    ],
                    [
                        'title' => 'Zu lange Trainingspausen zwischen Sätzen',
                        'problem' => 'Am Handy scrollen oder quatschen – aus 60s Pause werden 3-5 Minuten.',
                        'consequence' => 'Kalorienverbrauch sinkt drastisch, Trainingseffekt (besonders EPOC) reduziert sich, 45-Minuten-Training wird zu 90 Minuten.',
                        'solution' => 'Timer nutzen! Halte dich strikt an die Pausenzeiten (60s Kraft, 30-45s HIIT). Das macht den Unterschied zwischen durchschnittlich und effektiv.',
                        'example' => 'Smartphone-Timer oder Gym-Uhr: Nach jeder Übung Timer starten.'
                    ],
                    [
                        'title' => 'Keine Alltagsbewegung',
                        'problem' => 'Training 3x/Woche, aber sonst 12 Stunden täglich sitzen (Büro, Auto, Couch).',
                        'consequence' => 'NEAT (Non-Exercise Activity Thermogenesis) ist minimal. Der "3x Training" Effekt verpufft bei <3.000 Schritten täglich.',
                        'solution' => 'Ziel: 8.000-10.000 Schritte täglich. Das sind zusätzlich 200-400 kcal Verbrauch – ohne extra Training.',
                        'example' => 'Mittagspause: 15 Min Spaziergang. Telefonate: Im Gehen. Treppe statt Aufzug. Parken: 500m weiter weg.'
                    ]
                ],
                'summary' => 'Die meisten scheitern nicht am Training, sondern an diesen versteckten Fehlern. Vermeide sie und dein Erfolg ist fast garantiert.'
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
                    'answer' => '3–4 Trainingseinheiten pro Woche sind ideal, um Fett abzubauen und gleichzeitig ausreichend zu regenerieren.'
                ],
                [
                    'question' => 'Ist Krafttraining sinnvoll zum Abnehmen?',
                    'answer' => 'Ja. Krafttraining erhält Muskelmasse, erhöht den Grundumsatz und verbessert langfristig den Fettabbau.'
                ],
                [
                    'question' => 'Wann sehe ich erste Ergebnisse?',
                    'answer' => 'Viele spüren bereits nach 2–3 Wochen mehr Energie. Sichtbare Veränderungen zeigen sich meist nach 4–6 Wochen.'
                ],
            ],
        ],

        /* ============================
           MUSKELAUFBAU
        ============================ */
        'muskelaufbau' => [
            'title' => 'Trainingsplan Muskelaufbau – 12 Wochen | fitnessAI.me',
            'description' => 'Kostenloser 12-Wochen-Trainingsplan für Muskelaufbau mit progressiver Belastungssteigerung. Ideal für Anfänger und Fortgeschrittene.',
            'h1' => 'Muskelaufbau Trainingsplan – systematisch stärker werden',
            'intro' => 'Dieser 12-Wochen-Trainingsplan basiert auf progressiver Überlastung und kombiniert Muskelwachstum, Kraftsteigerung und ausreichende Regeneration.',
            'internal_type' => 'muscle_gain',

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
                    'answer' => '4 Trainingseinheiten pro Woche sind ideal, um ausreichend Volumen zu erreichen und gleichzeitig gut zu regenerieren.'
                ],
                [
                    'question' => 'Wie viel Protein brauche ich für Muskelaufbau?',
                    'answer' => 'Empfohlen sind etwa 2,0–2,2 Gramm Protein pro Kilogramm Körpergewicht täglich.'
                ],
                [
                    'question' => 'Wann sehe ich Muskelwachstum?',
                    'answer' => 'Kraftsteigerungen treten oft nach 2–3 Wochen auf, sichtbare Muskelzuwächse meist nach 6–8 Wochen.'
                ],
            ],
        ],

        /* ============================
           Anfänger
        ============================ */
        'anfaenger' => [
            'title' => 'Trainingsplan für Anfänger – 6 Wochen | fitnessAI.me',
            'description' => 'Der perfekte Einstieg ins Training. Sicher, strukturiert und nachhaltig – ideal für Anfänger ohne Vorerfahrung.',
            'h1' => 'Trainingsplan für Anfänger – sicher & effektiv starten',
            'intro' => 'Dieser 6-Wochen-Anfängerplan hilft dir, grundlegende Bewegungen zu erlernen, Kraft aufzubauen und eine stabile Trainingsroutine zu entwickeln.',
            'internal_type' => 'beginner',

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
                    'answer' => '2–3 Trainingseinheiten pro Woche sind ideal, um Fortschritte zu machen und gleichzeitig ausreichend zu regenerieren.'
                ],
                [
                    'question' => 'Brauche ich ein Fitnessstudio?',
                    'answer' => 'Nein. Dieser Plan ist so aufgebaut, dass du komplett ohne Geräte oder im Home-Workout starten kannst.'
                ],
                [
                    'question' => 'Wann sehe ich erste Fortschritte?',
                    'answer' => 'Viele Anfänger spüren bereits nach 1–2 Wochen mehr Energie, bessere Beweglichkeit und steigende Kraft.'
                ],
            ],
        ],

        /* ============================
           Zuhause
        ============================ */
        'zuhause' => [
            'title' => 'Trainingsplan für Zuhause – Ohne Geräte effektiv trainieren | fitnessAI.me',
            'description' => 'Effektives Home Workout ohne Geräte. Bodyweight Training für jedes Fitnesslevel – 8 Wochen strukturiert & kostenlos.',
            'h1' => 'Trainingsplan für Zuhause: Effektiv ohne Geräte',
            'intro' => 'Dieser 8-Wochen-Trainingsplan zeigt dir, wie du mit reinem Körpergewicht zuhause Kraft, Ausdauer und Muskulatur aufbaust – ganz ohne Fitnessstudio.',
            'internal_type' => 'home',

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
                    'answer' => 'Ja. Durch progressive Überlastung (mehr Wiederholungen, langsameres Tempo, schwierigere Varianten) ist effektiver Muskelaufbau mit Bodyweight möglich.'
                ],
                [
                    'question' => 'Wie lange dauert ein Home Workout?',
                    'answer' => 'Die Einheiten dauern ca. 35–45 Minuten und sind ideal in den Alltag integrierbar.'
                ],
            ],
        ],

        /* ============================
           Frauen
        ============================ */
        'frauen' => [
            'title' => 'Trainingsplan für Frauen – Gezielt & Effektiv | fitnessAI.me',
            'description' => 'Strukturierter Kraft- & Cardio-Trainingsplan speziell für Frauen. Fokus auf Straffung, Definition & Fettabbau – 8 Wochen.',
            'h1' => 'Trainingsplan für Frauen: Straff & Stark',
            'intro' => 'Dieser 8-Wochen-Trainingsplan kombiniert Krafttraining und Cardio gezielt für Straffung, Definition und einen starken, femininen Körper.',
            'internal_type' => 'women',

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
                    'answer' => 'Nein. Frauen haben deutlich weniger Testosteron. Krafttraining strafft, definiert und formt den Körper, ohne „bulky“ zu machen.'
                ],
                [
                    'question' => 'Wie schnell sehe ich Ergebnisse?',
                    'answer' => 'Mehr Energie und Kraft meist nach 2–3 Wochen, sichtbare Straffung nach etwa 4–6 Wochen.'
                ],
            ],
        ],


        /* ============================
           NEUJAHR
        ============================ */
        'neujahrs-trainingsplan' => [
            'title' => 'Neujahrs Trainingsplan – 6 Wochen Fitness Reset | fitnessAI.me',
            'description' => 'Der ideale Trainingsplan für deinen Neustart ins neue Jahr. ✓ 6 Wochen Reset ✓ Kraft, Cardio & Mobility ✓ Nachhaltig & sicher',
            'h1' => 'Neujahrs Trainingsplan: Dein 6-Wochen Fitness Reset',
            'intro' => 'Dieser 6-Wochen Fitness Reset hilft dir, nach Pausen oder einem Neustart wieder in eine nachhaltige Trainingsroutine zu finden. Fokus auf Kraft, Ausdauer, Beweglichkeit und langfristigen Fortschritt.',
            'internal_type' => 'new_year_reset',

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
                    'answer' => 'Ja. Der Plan ist bewusst moderat aufgebaut und ideal für Neustarts nach Pausen oder längerer Inaktivität.'
                ],
                [
                    'question' => 'Kann ich damit gleichzeitig Fett verlieren?',
                    'answer' => 'Ja. Durch die Kombination aus Krafttraining, Cardio und Bewegung im Alltag unterstützt der Plan Fettabbau und Muskelaufbau.'
                ],
                [
                    'question' => 'Muss ich alle vier Tage trainieren?',
                    'answer' => 'Nein. Drei Einheiten reichen aus. Der vierte Tag ist optional und fokussiert auf Regeneration.'
                ],
            ],
        ],
    ],

    'en' => [
        'weight-loss' => [
            'title' => 'Free Weight Loss Workout Plan – 8 Weeks | fitnessAI.me',
            'description' => 'Free 8-week weight loss workout plan. Structured strength and cardio training for sustainable fat loss – suitable for home or gym.',
            'h1' => 'Weight Loss Workout Plan – Lose Fat Sustainably',
            'intro' => 'This structured 8-week workout plan combines strength training and cardio to reduce body fat, preserve muscle mass, and improve metabolic health.',
            'internal_type' => 'weight_loss',

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

            'faqs' => [
                [
                    'question' => 'How often should I train to lose weight?',
                    'answer' => 'Training 3–4 times per week is ideal to promote fat loss while allowing proper recovery.'
                ],
                [
                    'question' => 'Is strength training important for weight loss?',
                    'answer' => 'Yes. Strength training preserves lean muscle mass and helps maintain a higher metabolic rate.'
                ],
                [
                    'question' => 'When can I expect results?',
                    'answer' => 'Improved energy and performance often appear after 2–3 weeks. Visible fat loss typically follows after 4–6 weeks.'
                ],
            ],
        ],

        'muscle-gain' => [
            'title' => 'Free Muscle Building Workout Plan – 12 Weeks | fitnessAI.me',
            'description' => 'Free 12-week muscle building workout plan. Structured strength training with progressive overload for sustainable muscle growth.',
            'h1' => 'Muscle Building Workout Plan – Build Strength & Muscle',
            'intro' => 'This 12-week muscle building program is designed to systematically increase strength and muscle mass through progressive overload and structured training.',
            'keywords' => ['muscle building workout plan', 'hypertrophy training', 'strength training program', 'build muscle'],
            'internal_type' => 'muscle_gain',

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
                    'answer' => 'Training 3–5 times per week is ideal, depending on recovery and overall training volume.'
                ],
                [
                    'question' => 'How much protein do I need?',
                    'answer' => 'Around 2.0–2.2 grams of protein per kilogram of body weight per day supports optimal muscle growth.'
                ],
                [
                    'question' => 'When will I see muscle gains?',
                    'answer' => 'Strength improvements often appear within 2–3 weeks. Visible muscle growth usually follows after 6–8 weeks.'
                ],
            ],
        ],

        'beginner' => [
            'title' => 'Beginner Workout Plan – 6 Weeks | fitnessAI.me',
            'description' => 'The ideal beginner workout plan. Safe, structured and easy to follow – perfect for starting fitness training at home or in the gym.',
            'h1' => 'Beginner Workout Plan – Start Training Safely',
            'intro' => 'This 6-week beginner workout plan helps you learn fundamental movements, build basic strength and establish a sustainable training routine.',
            'keywords' => ['beginner workout plan', 'fitness for beginners', 'starter workout plan', 'beginner fitness'],
            'internal_type' => 'beginner',

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
                    'answer' => 'Training 2–3 times per week is ideal for beginners to adapt safely and recover properly.'
                ],
                [
                    'question' => 'Do I need a gym to start?',
                    'answer' => 'No. This beginner plan is designed to be effective at home using bodyweight exercises.'
                ],
                [
                    'question' => 'When will I notice progress?',
                    'answer' => 'Most beginners feel stronger and more confident within the first 1–2 weeks. Visible changes usually follow after a few weeks.'
                ],
            ],
        ],

        'home' => [
            'title' => 'Home Workout Plan – Train Effectively Without Equipment | fitnessAI.me',
            'description' => 'Effective 8-week home workout plan with no equipment required. Structured bodyweight training for all fitness levels.',
            'h1' => 'Home Workout Plan – Train Effectively Without a Gym',
            'intro' => 'This 8-week home workout plan uses bodyweight exercises only. It helps you build strength, improve endurance and stay consistent without needing a gym.',
            'keywords' => ['home workout plan', 'bodyweight training', 'no equipment workout', 'home fitness'],
            'internal_type' => 'home',

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
                    'answer' => 'Yes. Bodyweight training is highly effective for building strength, endurance and muscle when exercises are structured properly.'
                ],
                [
                    'question' => 'Is this suitable for beginners?',
                    'answer' => 'Yes. All exercises can be scaled to match your fitness level, making this plan suitable for beginners and advanced users.'
                ],
                [
                    'question' => 'How long are the workouts?',
                    'answer' => 'Each workout takes approximately 35–45 minutes, including warm-up and short recovery periods.'
                ],
            ],
        ],

        'women' => [
            'title' => 'Workout Plan for Women – Targeted & Effective | fitnessAI.me',
            'description' => 'Structured 8-week workout plan for women. Strength and cardio training for toning, definition and overall fitness.',
            'h1' => 'Workout Plan for Women – Strength, Tone & Balance',
            'intro' => 'This 8-week workout plan is designed specifically for women and combines strength training and cardio to improve muscle tone, definition and overall fitness.',
            'keywords' => ['workout plan for women', 'women fitness training', 'female workout plan', 'toning workout'],
            'internal_type' => 'women',

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
                    'answer' => 'No. Women typically have much lower testosterone levels. Strength training tones and shapes the body without excessive muscle mass.'
                ],
                [
                    'question' => 'Is this plan suitable for beginners?',
                    'answer' => 'Yes. Exercises can be adjusted in intensity and volume to match all fitness levels.'
                ],
                [
                    'question' => 'Can I lose fat with this plan?',
                    'answer' => 'Yes. The combination of strength training and cardio supports fat loss while maintaining lean muscle.'
                ],
            ],
        ],

        'new-year-reset' => [
            'title' => 'New Year Workout Plan – Your 6-Week Fitness Reset | fitnessAI.me',
            'description' => 'Start the new year strong with a structured 6-week workout plan. Build strength, burn fat and create sustainable habits – home or gym.',
            'h1' => 'New Year Workout Plan – Your 6-Week Fitness Reset',
            'intro' => 'This 6-week fitness reset focuses on structure instead of motivation hacks. Strength training, cardio and recovery are combined into a sustainable system to rebuild consistency and performance.',
            'keywords' => ['new year workout plan', 'fitness reset', 'workout restart', 'new year fitness'],
            'internal_type' => 'new_year_reset',

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
                    'answer' => 'Yes. This plan is specifically designed for restarts. Exercises are scalable and focus on rebuilding routine, strength and confidence.'
                ],
                [
                    'question' => 'Can beginners follow this plan?',
                    'answer' => 'Absolutely. Beginners can reduce reps or rest longer, while advanced users can increase intensity or add light weights.'
                ],
                [
                    'question' => 'Can I lose fat and gain strength at the same time?',
                    'answer' => 'Yes. The combination of strength training, cardio and recovery supports fat loss while maintaining or rebuilding muscle.'
                ],
            ],
        ],
    ],
];

