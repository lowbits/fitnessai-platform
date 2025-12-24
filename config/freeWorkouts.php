<?php

return [
    'default_author' => [
        'de'=> [
            'name' => 'Tobias Lobitz',
            'title' => 'Gründer & Software-Entwickler',
            'bio' => 'Entwickelt seit 2018 Fitness-Software. Eigene Trainings-Erfahrung: 15 Jahre Krafttraining.',
            'image' => '/assets/authors/tobias.jpeg',
        ],
        'en' => [
            'name' => 'Tobias Lobitz',
            'title' => 'Founder & Software Developer',
            'bio' => 'Developing fitness software since 2018. Personal training experience: 15 years of strength training.',
            'image' => '/assets/authors/tobias.jpeg',
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
            'title' => 'Kostenloser Trainingsplan zum Abnehmen – 8 Wochen | fitnessAI.me',
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
            'published_at' => '2025-12-24',
            'last_updated_at' => '2025-12-24',

            'why_it_works' => [
                'title' => 'Warum dieser Muskelaufbau-Trainingsplan funktioniert',
                'content' => [
                    [
                        'heading' => 'Progressive Überlastung treibt Muskelwachstum',
                        'text' => 'Muskelwachstum entsteht nur, wenn der Körper regelmäßig mit steigenden Belastungen konfrontiert wird. Dieser Plan basiert auf progressiver Überlastung – also dem gezielten Steigern von Gewicht, Wiederholungen oder Trainingsvolumen. Dieses Prinzip gilt als der wichtigste Faktor für Hypertrophie und ist wissenschaftlich eindeutig belegt.'
                    ],
                    [
                        'heading' => 'Optimales Trainingsvolumen und Frequenz',
                        'text' => 'Jede Muskelgruppe wird etwa zwei Mal pro Woche trainiert. Studien zeigen, dass diese Frequenz das beste Verhältnis aus Trainingsreiz und Regeneration bietet und deutlich effektiver ist als einmalige, sehr hohe Belastungen.'
                    ],
                    [
                        'heading' => 'Grundübungen maximieren den Wachstumsreiz',
                        'text' => 'Der Trainingsplan setzt bewusst auf komplexe Mehrgelenksübungen wie Kniebeugen, Bankdrücken, Rudern und Klimmzüge. Diese aktivieren große Muskelgruppen, erzeugen hohe mechanische Spannung und fördern einen starken Wachstumsreiz.'
                    ],
                    [
                        'heading' => 'Geplante Regeneration verhindert Übertraining',
                        'text' => 'Muskeln wachsen nicht im Training, sondern in der Erholung. Durch sinnvolle Splits, Pausentage und kontrollierte Trainingsfrequenz erhält dein Körper ausreichend Zeit zur Regeneration – ein entscheidender Faktor für langfristigen Muskelaufbau.'
                    ],
                    [
                        'heading' => 'Training und Ernährung sind aufeinander abgestimmt',
                        'text' => 'Der Plan ist darauf ausgelegt, mit einem moderaten Kalorienüberschuss und ausreichender Proteinzufuhr kombiniert zu werden. So stehen deinem Körper alle notwendigen Bausteine zur Verfügung, um Muskulatur effektiv aufzubauen.'
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
                        'example' => 'Woche 1: Bankdrücken 3×8 mit 60 kg → Woche 2: 3×9 oder 62,5 kg.'
                    ],
                    [
                        'title' => 'Zu geringe Kalorienzufuhr',
                        'problem' => 'Muskelaufbau bei Erhaltungsbedarf oder sogar Kaloriendefizit.',
                        'consequence' => 'Dem Körper fehlt Energie für Regeneration und Muskelaufbau.',
                        'solution' => 'Halte einen moderaten Kalorienüberschuss von 300–500 kcal pro Tag.',
                        'example' => 'Bei Erhaltungsbedarf von 2.400 kcal: Ziel sind 2.700–2.900 kcal.'
                    ],
                    [
                        'title' => 'Zu wenig Protein',
                        'problem' => 'Unregelmäßige oder zu niedrige Proteinzufuhr.',
                        'consequence' => 'Muskelregeneration verlangsamt sich, Muskelaufbau bleibt hinter dem Potenzial zurück.',
                        'solution' => 'Ziel: 2,0–2,2 g Protein pro kg Körpergewicht täglich, gleichmäßig verteilt.',
                        'example' => 'Bei 80 kg Körpergewicht: etwa 160–175 g Protein pro Tag.'
                    ],
                    [
                        'title' => 'Schlechte Übungsausführung',
                        'problem' => 'Zu hohe Gewichte auf Kosten der Technik.',
                        'consequence' => 'Zielmuskulatur wird schlechter belastet, Verletzungsrisiko steigt.',
                        'solution' => 'Saubere Technik und voller Bewegungsumfang haben immer Vorrang vor Gewicht.',
                        'example' => 'Gewicht reduzieren, wenn Schwung oder verkürzte Bewegung nötig wird.'
                    ],
                    [
                        'title' => 'Zu viel Trainingsvolumen, zu wenig Erholung',
                        'problem' => 'Täglich hartes Training ohne ausreichende Pausen.',
                        'consequence' => 'Überlastung, Leistungsabfall, stagnierender Muskelaufbau.',
                        'solution' => 'Halte dich an strukturierte Trainingspläne und respektiere Ruhetage.',
                        'example' => '4 fokussierte Trainingstage sind effektiver als 6 schlecht regenerierte.'
                    ],
                    [
                        'title' => 'Schlaf wird unterschätzt',
                        'problem' => 'Weniger als 6–7 Stunden Schlaf bei intensivem Training.',
                        'consequence' => 'Schlechtere Regeneration, niedrigere Testosteronwerte, geringerer Muskelaufbau.',
                        'solution' => '7–9 Stunden Schlaf pro Nacht unterstützen Hormone und Muskelwachstum.',
                        'example' => 'Studien zeigen deutlich reduzierte Muskelproteinsynthese bei Schlafmangel.'
                    ],
                    [
                        'title' => 'Ständiger Trainingsplan-Wechsel',
                        'problem' => 'Alle paar Wochen ein neues Trainingsprogramm beginnen.',
                        'consequence' => 'Keine messbare Progression, keine Anpassung, kein konstanter Muskelaufbau.',
                        'solution' => 'Bleibe mindestens 8–12 Wochen bei einem strukturierten Plan.',
                        'example' => 'Erst nach Abschluss des 12-Wochen-Zyklus bewerten und anpassen.'
                    ],
                ],
                'summary' => 'Muskelaufbau scheitert selten an fehlenden Übungen, sondern fast immer an fehlender Struktur, Regeneration und Konsequenz. Vermeidest du diese Fehler, wird Fortschritt planbar.'
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
            'published_at' => '2025-12-24',
            'last_updated_at' => '2025-12-24',

            'why_it_works' => [
                'title' => 'Warum dieser Trainingsplan für Anfänger funktioniert',
                'content' => [
                    [
                        'heading' => 'Sicherer Einstieg ohne Überforderung',
                        'text' => 'Der Plan ist speziell für Einsteiger konzipiert. Die Übungen sind technisch einfach, gut skalierbar und belasten Gelenke sowie das Nervensystem nicht übermäßig. So kann sich dein Körper schrittweise an Bewegung und Krafttraining gewöhnen.'
                    ],
                    [
                        'heading' => 'Grundbewegungen statt komplizierter Übungen',
                        'text' => 'Statt isolierter oder komplexer Übungen setzt der Plan auf grundlegende Bewegungsmuster wie Kniebeugen, Drücken, Ziehen und Stabilisation. Diese bilden die Basis für jedes weitere Training und verbessern Kraft, Koordination und Körpergefühl.'
                    ],
                    [
                        'heading' => 'Optimale Trainingsfrequenz für Anpassung',
                        'text' => 'Mit 2–3 Trainingseinheiten pro Woche erhält dein Körper genügend Reize, ohne überfordert zu werden. Studien zeigen, dass diese Frequenz für Anfänger ideal ist, um Fortschritte zu erzielen und gleichzeitig ausreichend zu regenerieren.'
                    ],
                    [
                        'heading' => 'Progression ohne Leistungsdruck',
                        'text' => 'Der Plan steigert sich behutsam über Wiederholungen, Dauer oder Übungsvarianten – nicht über hohe Gewichte. So entsteht Fortschritt ohne Stress oder Verletzungsrisiko.'
                    ],
                    [
                        'heading' => 'Fokus auf Gewohnheiten statt Perfektion',
                        'text' => 'Langfristiger Trainingserfolg entsteht durch Regelmäßigkeit. Dieser Plan hilft dir, eine feste Trainingsroutine aufzubauen – ein entscheidender Faktor für nachhaltige Fitness.'
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
                        'example' => 'Lieber 3 kurze Einheiten pro Woche als 5 überambitionierte.'
                    ],
                    [
                        'title' => 'Falsche oder unsaubere Technik',
                        'problem' => 'Übungen werden ohne Körperkontrolle oder mit Schwung ausgeführt.',
                        'consequence' => 'Geringerer Trainingseffekt und erhöhtes Verletzungsrisiko.',
                        'solution' => 'Führe jede Bewegung kontrolliert und sauber aus – Qualität vor Quantität.',
                        'example' => 'Lieber 10 saubere Kniebeugen als 20 unsaubere.'
                    ],
                    [
                        'title' => 'Zu seltenes Training',
                        'problem' => 'Große Pausen zwischen den Einheiten verhindern Anpassung.',
                        'consequence' => 'Der Körper beginnt jedes Mal wieder bei Null.',
                        'solution' => 'Plane feste Trainingstage pro Woche ein.',
                        'example' => 'Montag, Mittwoch, Freitag als feste Termine.'
                    ],
                    [
                        'title' => 'Zu wenig Erholung',
                        'problem' => 'Kein Schlaf oder Training an aufeinanderfolgenden Tagen ohne Pause.',
                        'consequence' => 'Müdigkeit, Leistungsabfall und fehlende Motivation.',
                        'solution' => 'Mindestens ein Ruhetag zwischen den Einheiten einhalten.',
                        'example' => 'Training jeden zweiten Tag.'
                    ],
                    [
                        'title' => 'Ungeduld bei Ergebnissen',
                        'problem' => 'Erwartung sichtbarer Veränderungen nach wenigen Tagen.',
                        'consequence' => 'Frust und vorzeitiger Abbruch.',
                        'solution' => 'Konzentriere dich auf Energie, Beweglichkeit und Routine – sichtbare Ergebnisse folgen.',
                        'example' => 'Erste Verbesserungen nach 2–3 Wochen sind völlig normal.'
                    ],
                ],
                'summary' => 'Als Anfänger zählt nicht Perfektion, sondern Kontinuität. Wer diese typischen Fehler vermeidet, legt die beste Grundlage für langfristige Fitness.'
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
            'published_at' => '2025-12-24',
            'last_updated_at' => '2025-12-24',

            'why_it_works' => [
                'title' => 'Warum dieser Trainingsplan für Zuhause funktioniert',
                'content' => [
                    [
                        'heading' => 'Körpergewichtstraining ist hochwirksam',
                        'text' => 'Dieser Plan nutzt dein eigenes Körpergewicht als Widerstand. Studien zeigen, dass Bodyweight-Training Kraft, Muskelspannung und Ausdauer effektiv verbessert – vorausgesetzt, Intensität und Ausführung stimmen.'
                    ],
                    [
                        'heading' => 'Ganzkörperbelastung ohne Geräte',
                        'text' => 'Durch Mehrgelenksübungen wie Liegestütze, Squats und Ausfallschritte werden mehrere Muskelgruppen gleichzeitig trainiert. Das erhöht den Kalorienverbrauch und spart Zeit – ideal für Home Workouts.'
                    ],
                    [
                        'heading' => 'Progression ohne zusätzliche Gewichte',
                        'text' => 'Der Plan steigert sich über Wiederholungen, Tempo, Pausenlänge und Übungsvarianten. So erzielst du Fortschritt, auch ohne Hanteln oder Maschinen.'
                    ],
                    [
                        'heading' => 'Konstante Trainingsfrequenz ohne Hürden',
                        'text' => 'Da du weder Anfahrt noch Equipment brauchst, fällt die größte Trainingshürde weg. Das erhöht die Wahrscheinlichkeit, dass du regelmäßig trainierst – der wichtigste Faktor für Ergebnisse.'
                    ],
                    [
                        'heading' => 'Gelenkschonend und alltagstauglich',
                        'text' => 'Alle Übungen lassen sich an dein Fitnesslevel anpassen. Dadurch ist der Plan sowohl für Anfänger als auch für Fortgeschrittene geeignet – ohne unnötiges Verletzungsrisiko.'
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
                        'example' => 'Langsame Liegestütze mit Körperspannung statt schnelles „Abspulen“.'
                    ],
                    [
                        'title' => 'Fehlende Progression',
                        'problem' => 'Wochenlang dieselben Übungen mit gleicher Intensität.',
                        'consequence' => 'Der Körper passt sich an, Fortschritt stagniert.',
                        'solution' => 'Steigere Wiederholungen, Tempo oder wähle schwierigere Varianten.',
                        'example' => 'Von normalen Squats zu Squats mit Pause unten.'
                    ],
                    [
                        'title' => 'Ablenkung während des Trainings',
                        'problem' => 'Training nebenbei mit Handy, Fernsehen oder Unterbrechungen.',
                        'consequence' => 'Geringerer Trainingseffekt und längere Einheiten.',
                        'solution' => 'Plane feste, ungestörte Trainingszeiten.',
                        'example' => '30 Minuten Fokus-Training statt 60 Minuten mit Ablenkung.'
                    ],
                    [
                        'title' => 'Unsaubere Technik',
                        'problem' => 'Bewegungen werden schnell und ohne Kontrolle ausgeführt.',
                        'consequence' => 'Erhöhtes Verletzungsrisiko und geringere Wirkung.',
                        'solution' => 'Saubere Technik hat Priorität – auch ohne Spiegel.',
                        'example' => 'Plank mit Körperspannung statt durchhängendem Rücken.'
                    ],
                    [
                        'title' => 'Zu wenig Regeneration',
                        'problem' => 'Tägliches Training ohne Pausen.',
                        'consequence' => 'Erschöpfung, Leistungsabfall, Motivationsverlust.',
                        'solution' => 'Mindestens 1–2 Resttage pro Woche einplanen.',
                        'example' => '4 Trainingstage + 3 aktive Erholungstage.'
                    ],
                ],
                'summary' => 'Zuhause erfolgreich zu trainieren hängt nicht von Geräten ab, sondern von Struktur, Intensität und Konsequenz. Wer diese Fehler vermeidet, erzielt auch ohne Fitnessstudio starke Ergebnisse.'
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
            'published_at' => '2025-12-24',
            'last_updated_at' => '2025-12-24',

            'why_it_works' => [
                'title' => 'Warum dieser Trainingsplan für Frauen funktioniert',
                'content' => [
                    [
                        'heading' => 'Krafttraining formt statt aufzublähen',
                        'text' => 'Frauen haben deutlich niedrigere Testosteronwerte als Männer. Krafttraining führt daher nicht zu „massigen“ Muskeln, sondern zu Straffung, Definition und einer verbesserten Körperform. Genau darauf ist dieser Plan ausgelegt.'
                    ],
                    [
                        'heading' => 'Gezielte Übungsauswahl für typische Zielzonen',
                        'text' => 'Der Trainingsplan setzt Schwerpunkte auf Beine, Gesäß, Core und Oberkörper. Diese Muskelgruppen beeinflussen Haltung, Figur und Kraft im Alltag besonders stark.'
                    ],
                    [
                        'heading' => 'Kombination aus Kraft und Cardio',
                        'text' => 'Krafttraining erhöht den Grundumsatz und formt den Körper, Cardio unterstützt die Fettverbrennung. Die Kombination sorgt für sichtbare Ergebnisse ohne extremes Training.'
                    ],
                    [
                        'heading' => 'Hormonfreundliche Trainingsstruktur',
                        'text' => 'Moderate Intensität, ausreichende Pausen und sinnvolle Trainingsfrequenz unterstützen einen stabilen Hormonhaushalt – besonders wichtig für langfristige Ergebnisse und Wohlbefinden.'
                    ],
                    [
                        'heading' => 'Stärkung von Selbstvertrauen und Körpergefühl',
                        'text' => 'Regelmäßiges Krafttraining verbessert nicht nur die körperliche Leistungsfähigkeit, sondern auch das Selbstbewusstsein. Viele Frauen berichten von mehr Energie, besserer Haltung und höherem Körpervertrauen.'
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
                        'example' => '2–3 Krafteinheiten pro Woche statt ausschließlich Cardio.'
                    ],
                    [
                        'title' => 'Zu viel Cardio, zu wenig Kraft',
                        'problem' => 'Stundenlanges Ausdauertraining ohne Krafttraining.',
                        'consequence' => 'Muskelabbau, stagnierender Stoffwechsel und wenig Körperform.',
                        'solution' => 'Krafttraining priorisieren, Cardio ergänzend einsetzen.',
                        'example' => '3 Kraft-Einheiten + 1–2 lockere Cardio-Sessions.'
                    ],
                    [
                        'title' => 'Zu geringe Trainingsintensität',
                        'problem' => 'Sehr leichte Gewichte oder kaum muskuläre Ermüdung.',
                        'consequence' => 'Der Trainingsreiz reicht nicht aus, um Veränderungen auszulösen.',
                        'solution' => 'Übungen sollten fordern, aber technisch sauber bleiben.',
                        'example' => 'Letzte 2 Wiederholungen sollten anstrengend sein.'
                    ],
                    [
                        'title' => 'Vernachlässigung des Oberkörpers',
                        'problem' => 'Fokus nur auf Beine und Po.',
                        'consequence' => 'Haltungsprobleme, Schulter- und Nackenschmerzen.',
                        'solution' => 'Oberkörpertraining gezielt integrieren.',
                        'example' => 'Rudern, Schulterdrücken und Planks regelmäßig einbauen.'
                    ],
                    [
                        'title' => 'Zu wenig Regeneration',
                        'problem' => 'Training trotz Erschöpfung oder Schlafmangel.',
                        'consequence' => 'Leistungsabfall, hormonelle Dysbalance, Demotivation.',
                        'solution' => 'Ausreichend Schlaf und Ruhetage einplanen.',
                        'example' => 'Mindestens 1–2 trainingsfreie Tage pro Woche.'
                    ],
                ],
                'summary' => 'Erfolgreiches Training für Frauen bedeutet nicht „mehr Cardio“, sondern smartere Kombination aus Kraft, Bewegung und Regeneration.'
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
            'published_at' => '2025-12-24',
            'last_updated_at' => '2025-12-24',
            'why_it_works' => [
                'title' => 'Warum der Neujahrs-Trainingsplan funktioniert',
                'content' => [
                    [
                        'heading' => 'Reset statt Überforderung',
                        'text' => 'Nach längeren Pausen braucht der Körper keinen Extremplan, sondern Struktur. Dieser 6-Wochen-Reset setzt bewusst auf kontrollierte Belastung, um Kraft, Ausdauer und Beweglichkeit wieder aufzubauen – ohne Verletzungsrisiko.'
                    ],
                    [
                        'heading' => 'Feste Struktur schlägt Motivation',
                        'text' => 'Motivation schwankt, Routinen bleiben. Mit klar definierten Trainingstagen und überschaubarem Umfang wird Training zu einem festen Bestandteil deines Alltags – unabhängig von Tagesform.'
                    ],
                    [
                        'heading' => 'Ganzheitlicher Ansatz',
                        'text' => 'Der Plan kombiniert Krafttraining, Cardio, Core-Stabilität und Mobility. So werden nicht nur Muskeln aufgebaut, sondern auch Herz-Kreislauf-System, Gelenke und Beweglichkeit verbessert.'
                    ],
                    [
                        'heading' => 'Progression ohne Leistungsdruck',
                        'text' => 'Die Intensität steigt schrittweise über Volumen, Übungsauswahl und Belastung. Das verhindert Plateaus und sorgt für messbare Fortschritte – auch nach längeren Trainingspausen.'
                    ],
                    [
                        'heading' => 'Ideal für Körperkomposition',
                        'text' => 'Krafttraining erhält Muskulatur, Cardio erhöht den Kalorienverbrauch. In Kombination mit moderater Ernährung unterstützt der Plan gleichzeitig Fettabbau und Muskelaufbau.'
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
                        'example' => 'Lieber konstant 4 Wochen trainieren als nach 10 Tagen abbrechen.'
                    ],
                    [
                        'title' => 'Alles auf einmal ändern',
                        'problem' => 'Training, Diät, Schlaf, Alltag – alles gleichzeitig.',
                        'consequence' => 'Überforderung und schnelle Erschöpfung.',
                        'solution' => 'Fokus zuerst auf Training, danach Ernährung optimieren.',
                        'example' => 'Erst Trainingsroutine etablieren, dann Kalorien feinjustieren.'
                    ],
                    [
                        'title' => 'Fehlende Regeneration',
                        'problem' => 'Kein Ruhetag trotz Trainingspause in den letzten Monaten.',
                        'consequence' => 'Leistungsabfall, Verletzungsrisiko, Müdigkeit.',
                        'solution' => 'Regeneration als festen Bestandteil des Plans sehen.',
                        'example' => 'Mindestens 1 Mobility- oder Resttag pro Woche.'
                    ],
                    [
                        'title' => 'Unrealistische Erwartungen',
                        'problem' => 'Erwartung sichtbarer Transformation nach 1–2 Wochen.',
                        'consequence' => 'Frust und Abbruch.',
                        'solution' => 'Fortschritt an Energie, Routine und Leistungsfähigkeit messen.',
                        'example' => 'Besser schlafen, stärker fühlen, mehr Bewegung im Alltag.'
                    ],
                    [
                        'title' => 'Kein klarer Trainingsrhythmus',
                        'problem' => 'Training „wenn Zeit ist“.',
                        'consequence' => 'Unregelmäßigkeit und fehlende Anpassung.',
                        'solution' => 'Feste Trainingstage einplanen.',
                        'example' => 'Montag, Mittwoch, Freitag als feste Termine.'
                    ],
                ],
                'summary' => 'Ein erfolgreicher Neustart scheitert selten am Willen, sondern an falschen Erwartungen. Dieser Plan setzt auf Struktur, Geduld und nachhaltigen Fortschritt.'
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
                        'text' => 'This plan uses a proven approach: strength training preserves muscle mass during a calorie deficit, while HIIT sessions maximize calorie burn. Studies show that this combination can be up to 40% more effective for fat loss than cardio-only training.'
                    ],
                    [
                        'heading' => 'The Afterburn Effect (EPOC) Works in Your Favor',
                        'text' => 'Intense strength training and HIIT intervals increase post-exercise oxygen consumption (EPOC). This means your body continues to burn additional calories for up to 48 hours after training — even at rest. This so-called "afterburn effect" can increase total calorie expenditure by 6–15%.'
                    ],
                    [
                        'heading' => 'Preserving Muscle Prevents the Yo-Yo Effect',
                        'text' => 'Unlike diet-only approaches, this plan maintains muscle mass. This is crucial: each kilogram of muscle burns roughly 13 kcal per day at rest. Losing muscle through extreme dieting lowers your metabolic rate and significantly increases the risk of weight regain.'
                    ],
                    [
                        'heading' => 'Progressive Structure Prevents Plateaus',
                        'text' => 'The 8-week structure with systematic progression (more reps, shorter rest periods, increased intensity) continuously challenges your body. This helps prevent the common plateau many people experience after 3–4 weeks with less structured plans.'
                    ],
                    [
                        'heading' => 'Scientifically Supported Training Frequency',
                        'text' => 'Three training sessions per week provide the optimal balance between stimulus and recovery. Research from the American College of Sports Medicine shows that this frequency supports sustainable fat loss of 0.5–1 kg per week without overloading the body or risking muscle loss.'
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
                        'example' => 'If your maintenance level is 2,000 kcal: eat 1,500–1,700 kcal instead of 1,000.'
                    ],
                    [
                        'title' => 'Too Little Protein Intake',
                        'problem' => 'Protein is underestimated and replaced by excessive carbohydrates.',
                        'consequence' => 'Muscle mass decreases, hunger increases, and metabolic rate drops. You lose weight — but mainly muscle instead of fat.',
                        'solution' => 'Aim for 1.6–2.0 g of protein per kg of body weight daily. Prioritize protein-rich foods at every meal.',
                        'example' => 'At 75 kg body weight: 120–150 g protein daily (e.g. 200 g chicken, 200 g low-fat quark, 3 eggs, 1 protein shake).'
                    ],
                    [
                        'title' => 'Cardio Without Strength Training',
                        'problem' => 'Only jogging or cycling, with no strength training.',
                        'consequence' => 'Calories are burned, but muscles are not preserved. The result is “skinny fat”: low body weight but high body fat percentage and weak musculature.',
                        'solution' => 'Prioritize strength training (at least twice per week) and use cardio as a supplement. This plan is built exactly that way.',
                        'example' => 'Instead of running 5 times per week: 3 sessions of this plan (strength + HIIT) plus 2 light walks.'
                    ],
                    [
                        'title' => 'Inconsistent Training',
                        'problem' => 'Monday: highly motivated. Thursday: no motivation. Next week: starting over again.',
                        'consequence' => 'No physical adaptation, no muscle development, no progress. Fat loss requires consistency over several weeks.',
                        'solution' => 'Schedule fixed training days (e.g. Mon/Wed/Fri). Even a 20-minute session is better than skipping. Use habit stacking: train immediately after work.',
                        'example' => 'Instead of “when I have time”: block “Monday 6:00 PM – Training” in your calendar like an important meeting.'
                    ],
                    [
                        'title' => 'Lack of Sleep',
                        'problem' => 'Only 5–6 hours of sleep per night while training hard and dieting.',
                        'consequence' => 'Cortisol (stress hormone) increases, testosterone decreases, and hunger hormones become imbalanced (higher ghrelin = more hunger). Fat loss stalls.',
                        'solution' => 'Aim for 7–9 hours of sleep per night. Studies show that adequate sleep can improve fat loss by up to 55% with identical training.',
                        'example' => 'With 6 hours of sleep: ~60% of weight loss comes from muscle. With 8 hours: ~80% comes from fat.'
                    ],
                    [
                        'title' => 'Excessively Long Rest Periods',
                        'problem' => 'Scrolling on your phone or chatting — turning 60 seconds of rest into 3–5 minutes.',
                        'consequence' => 'Calorie burn drops significantly, training effectiveness (especially EPOC) decreases, and a 45-minute workout turns into 90 minutes.',
                        'solution' => 'Use a timer and stick to prescribed rest periods (60s for strength, 30–45s for HIIT). This is a major difference-maker.',
                        'example' => 'Use your smartphone timer or gym clock — start the timer after every set.'
                    ],
                    [
                        'title' => 'Lack of Daily Movement',
                        'problem' => 'Training 3 times per week, but sitting for 12 hours per day (office, car, couch).',
                        'consequence' => 'NEAT (Non-Exercise Activity Thermogenesis) remains minimal. The effect of “3 workouts per week” is lost with fewer than 3,000 daily steps.',
                        'solution' => 'Aim for 8,000–10,000 steps per day. That adds an extra 200–400 kcal burn without additional workouts.',
                        'example' => '15-minute walk during lunch, walking while on calls, stairs instead of elevators, parking 500 m farther away.'
                    ],
                ],
                'summary' => 'Most people fail not because of training, but because of these hidden mistakes. Avoid them, and your success becomes highly predictable.'
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
            'published_at' => '2025-12-24',
            'last_updated_at' => '2025-12-24',
            'why_it_works' => [
                'title' => 'Why This Muscle Building Plan Works',
                'content' => [
                    [
                        'heading' => 'Progressive Overload Drives Muscle Growth',
                        'text' => 'Muscle growth only happens when your body is exposed to increasing demands over time. This plan is built around progressive overload — gradually increasing weights, reps, or training volume — which is the most proven driver of hypertrophy according to decades of strength training research.'
                    ],
                    [
                        'heading' => 'Optimal Training Volume and Frequency',
                        'text' => 'Training each muscle group 2 times per week provides the optimal balance between stimulus and recovery. Scientific reviews show that moderate-to-high weekly volume distributed across multiple sessions leads to significantly more muscle growth than single, high-volume workouts.'
                    ],
                    [
                        'heading' => 'Compound Exercises Maximize Hormonal Response',
                        'text' => 'The plan prioritizes compound movements like squats, presses, rows, and pull-ups. These exercises recruit large amounts of muscle mass, increase mechanical tension, and stimulate anabolic hormones — all key factors for efficient muscle growth.'
                    ],
                    [
                        'heading' => 'Sufficient Recovery Is Built In',
                        'text' => 'Muscles grow during recovery, not during training. Rest days, intelligent splits, and controlled training frequency allow your nervous system and muscles to recover fully, reducing injury risk and supporting consistent long-term progress.'
                    ],
                    [
                        'heading' => 'Nutrition and Training Are Aligned',
                        'text' => 'The plan is designed to work alongside a moderate calorie surplus and adequate protein intake. This alignment ensures your body has the necessary building blocks to repair and grow muscle tissue efficiently after each training session.'
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
                        'example' => 'Week 1: Bench Press 3×8 at 60 kg → Week 2: 3×9 or 62.5 kg.'
                    ],
                    [
                        'title' => 'Too Little Food Intake',
                        'problem' => 'Trying to build muscle while eating at maintenance or in a calorie deficit.',
                        'consequence' => 'The body lacks energy and building material, leading to slow or nonexistent muscle gains.',
                        'solution' => 'Maintain a calorie surplus of 300–500 kcal per day to support muscle protein synthesis.',
                        'example' => 'If maintenance is 2,400 kcal, aim for 2,700–2,900 kcal daily.'
                    ],
                    [
                        'title' => 'Insufficient Protein Intake',
                        'problem' => 'Protein intake is inconsistent or too low to support muscle repair.',
                        'consequence' => 'Recovery is impaired and muscle protein synthesis remains suboptimal.',
                        'solution' => 'Consume 2.0–2.2 g of protein per kg of body weight daily, spread evenly across meals.',
                        'example' => 'At 80 kg body weight: 160–175 g protein per day.'
                    ],
                    [
                        'title' => 'Poor Exercise Technique',
                        'problem' => 'Using excessive weight at the cost of proper form.',
                        'consequence' => 'Target muscles are under-stimulated while injury risk increases significantly.',
                        'solution' => 'Prioritize controlled execution and full range of motion before increasing weight.',
                        'example' => 'Lower the weight if range of motion shortens or momentum takes over.'
                    ],
                    [
                        'title' => 'Too Much Volume, Not Enough Recovery',
                        'problem' => 'Training every muscle group hard every day without adequate rest.',
                        'consequence' => 'Chronic fatigue, stalled progress, joint pain, and increased injury risk.',
                        'solution' => 'Follow a structured split and respect rest days. More is not always better.',
                        'example' => '4 focused training days outperform 6 poorly recovered sessions.'
                    ],
                    [
                        'title' => 'Ignoring Sleep Quality',
                        'problem' => 'Sleeping less than 6–7 hours per night while training intensely.',
                        'consequence' => 'Reduced testosterone, impaired recovery, slower muscle growth.',
                        'solution' => 'Aim for 7–9 hours of quality sleep per night to maximize hormonal recovery.',
                        'example' => 'Studies show up to 30% lower muscle protein synthesis with sleep deprivation.'
                    ],
                    [
                        'title' => 'Constant Program Hopping',
                        'problem' => 'Switching training programs every 2–3 weeks.',
                        'consequence' => 'No measurable progression, no adaptation, no reliable muscle growth.',
                        'solution' => 'Stick to one structured plan for at least 8–12 weeks before making changes.',
                        'example' => 'Finish the full 12-week cycle before evaluating results.'
                    ],
                ],
                'summary' => 'Muscle building fails not because of bad exercises, but because of poor execution, recovery, and consistency. Avoid these mistakes and your progress becomes predictable.'
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
            'published_at' => '2025-12-24',
            'last_updated_at' => '2025-12-24',

            'why_it_works' => [
                'title' => 'Why this beginner workout plan works',
                'content' => [
                    [
                        'heading' => 'Safe and sustainable entry into training',
                        'text' => 'This plan is designed specifically for beginners. Exercises are simple, scalable and joint-friendly, allowing your body to adapt gradually to regular training without overload.'
                    ],
                    [
                        'heading' => 'Focus on fundamental movement patterns',
                        'text' => 'Instead of complex exercises, the plan emphasizes basic movements like squatting, pushing, pulling and core stability. These movements build a solid foundation for all future training.'
                    ],
                    [
                        'heading' => 'Optimal training frequency for beginners',
                        'text' => 'With 2–3 workouts per week, your body receives enough stimulus to improve while still having sufficient time to recover. This frequency is considered ideal for beginners by sports science research.'
                    ],
                    [
                        'heading' => 'Progression without pressure',
                        'text' => 'Progress is achieved through small increases in repetitions, duration or exercise difficulty—not heavy weights. This ensures steady improvement without unnecessary stress.'
                    ],
                    [
                        'heading' => 'Building habits, not chasing perfection',
                        'text' => 'Consistency is the key to long-term success. This plan helps you establish a regular training routine, which is far more important than short-term intensity.'
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
                        'example' => '3 short, consistent workouts beat 5 overly intense sessions.'
                    ],
                    [
                        'title' => 'Poor exercise technique',
                        'problem' => 'Exercises are performed with momentum instead of control.',
                        'consequence' => 'Reduced results and higher injury risk.',
                        'solution' => 'Focus on controlled, clean movement execution.',
                        'example' => '10 quality reps are better than 20 sloppy ones.'
                    ],
                    [
                        'title' => 'Training too infrequently',
                        'problem' => 'Long gaps between workouts prevent adaptation.',
                        'consequence' => 'Your body keeps restarting from scratch.',
                        'solution' => 'Schedule fixed workout days each week.',
                        'example' => 'Monday, Wednesday and Friday as non-negotiable training days.'
                    ],
                    [
                        'title' => 'Neglecting recovery',
                        'problem' => 'Too little sleep or training on consecutive days without rest.',
                        'consequence' => 'Fatigue, loss of motivation and stalled progress.',
                        'solution' => 'Allow at least one rest day between workouts.',
                        'example' => 'Training every other day works best for beginners.'
                    ],
                    [
                        'title' => 'Expecting instant results',
                        'problem' => 'Beginners expect visible changes within days.',
                        'consequence' => 'Disappointment and early dropout.',
                        'solution' => 'Focus on energy levels, mobility and consistency first.',
                        'example' => 'Noticeable improvements usually appear after 2–3 weeks.'
                    ],
                ],
                'summary' => 'Beginners don’t fail because of lack of effort, but because of unrealistic expectations. Avoid these mistakes and your progress will follow naturally.'
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
            'published_at' => '2025-12-24',
            'last_updated_at' => '2025-12-24',

            'why_it_works' => [
                'title' => 'Why this home workout plan works',
                'content' => [
                    [
                        'heading' => 'Bodyweight training is highly effective',
                        'text' => 'This plan uses your own body weight as resistance. Research shows that bodyweight training can significantly improve strength, muscle tone and endurance when performed with proper intensity.'
                    ],
                    [
                        'heading' => 'Full-body stimulus without equipment',
                        'text' => 'Compound movements like push-ups, squats and lunges activate multiple muscle groups at once. This increases calorie burn and efficiency—perfect for home workouts.'
                    ],
                    [
                        'heading' => 'Progression without weights',
                        'text' => 'Progress is achieved through more repetitions, controlled tempo, shorter rest periods and harder exercise variations—no equipment required.'
                    ],
                    [
                        'heading' => 'Lower barriers, higher consistency',
                        'text' => 'Without commuting or equipment setup, training becomes easier to integrate into daily life. This significantly increases long-term consistency.'
                    ],
                    [
                        'heading' => 'Joint-friendly and scalable',
                        'text' => 'All exercises can be adapted to your fitness level, making the plan suitable for beginners and advanced trainees alike while minimizing injury risk.'
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
                        'example' => 'Slow, controlled push-ups instead of rushing through reps.'
                    ],
                    [
                        'title' => 'No progression over time',
                        'problem' => 'Repeating the same exercises with the same intensity for weeks.',
                        'consequence' => 'The body adapts and progress stalls.',
                        'solution' => 'Increase reps, slow down tempo or choose more challenging variations.',
                        'example' => 'Adding pauses at the bottom of squats.'
                    ],
                    [
                        'title' => 'Distractions during workouts',
                        'problem' => 'Training while checking your phone or watching TV.',
                        'consequence' => 'Reduced effectiveness and longer workout times.',
                        'solution' => 'Schedule focused, distraction-free sessions.',
                        'example' => '30 minutes of focused training instead of 60 minutes of interruptions.'
                    ],
                    [
                        'title' => 'Poor movement quality',
                        'problem' => 'Exercises are performed without control or proper alignment.',
                        'consequence' => 'Higher injury risk and reduced results.',
                        'solution' => 'Prioritize clean, controlled movement.',
                        'example' => 'A solid plank with core tension instead of a sagging lower back.'
                    ],
                    [
                        'title' => 'Insufficient recovery',
                        'problem' => 'Training every day without rest.',
                        'consequence' => 'Fatigue, declining performance and loss of motivation.',
                        'solution' => 'Plan at least 1–2 rest days per week.',
                        'example' => '4 workout days combined with active recovery.'
                    ],
                ],
                'summary' => 'Effective home training is not about equipment—it’s about structure, intensity and consistency. Avoid these mistakes and you can achieve excellent results at home.'
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
            'published_at' => '2025-12-24',
            'last_updated_at' => '2025-12-24',

            'why_it_works' => [
                'title' => 'Why this workout plan for women works',
                'content' => [
                    [
                        'heading' => 'Strength training shapes instead of bulks',
                        'text' => 'Women have significantly lower testosterone levels than men. As a result, strength training leads to toning and definition—not bulky muscles. This plan is designed specifically with that in mind.'
                    ],
                    [
                        'heading' => 'Targeted exercises for key areas',
                        'text' => 'The program focuses on legs, glutes, core and upper body—areas that strongly influence posture, body shape and everyday strength.'
                    ],
                    [
                        'heading' => 'Balanced combination of strength and cardio',
                        'text' => 'Strength training increases resting metabolism and shapes the body, while cardio supports fat loss. Together, they deliver visible and sustainable results.'
                    ],
                    [
                        'heading' => 'Hormone-friendly training structure',
                        'text' => 'Moderate intensity, adequate recovery and a sensible training frequency help support hormonal balance—crucial for long-term success and overall well-being.'
                    ],
                    [
                        'heading' => 'Improved confidence and body awareness',
                        'text' => 'Regular strength training not only improves physical performance but also boosts confidence. Many women experience higher energy levels, better posture and stronger body awareness.'
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
                        'example' => '2–3 strength workouts per week instead of cardio-only routines.'
                    ],
                    [
                        'title' => 'Too much cardio, not enough strength',
                        'problem' => 'Excessive cardio with little or no resistance training.',
                        'consequence' => 'Muscle loss, slower metabolism and limited body shaping.',
                        'solution' => 'Prioritize strength training and use cardio as a supplement.',
                        'example' => '3 strength sessions plus 1–2 light cardio workouts.'
                    ],
                    [
                        'title' => 'Training with too little intensity',
                        'problem' => 'Using very light weights or stopping far from muscle fatigue.',
                        'consequence' => 'Insufficient stimulus for physical change.',
                        'solution' => 'Exercises should feel challenging while maintaining good form.',
                        'example' => 'The last 2 reps should feel demanding.'
                    ],
                    [
                        'title' => 'Neglecting upper body training',
                        'problem' => 'Only focusing on legs and glutes.',
                        'consequence' => 'Poor posture and increased risk of neck and shoulder discomfort.',
                        'solution' => 'Include upper body exercises regularly.',
                        'example' => 'Rows, shoulder presses and planks in weekly workouts.'
                    ],
                    [
                        'title' => 'Insufficient recovery',
                        'problem' => 'Training despite fatigue or lack of sleep.',
                        'consequence' => 'Declining performance, hormonal imbalance and loss of motivation.',
                        'solution' => 'Plan adequate sleep and rest days.',
                        'example' => 'At least 1–2 non-training days per week.'
                    ],
                ],
                'summary' => 'Effective training for women is not about endless cardio—it’s about a smart balance of strength, movement and recovery.'
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
            'published_at' => '2025-12-24',
            'last_updated_at' => '2025-12-24',
            'why_it_works' => [
                'title' => 'Why the New Year workout reset works',
                'content' => [
                    [
                        'heading' => 'A reset instead of overload',
                        'text' => 'After longer breaks, your body doesn’t need extreme workouts. This 6-week reset focuses on controlled training to rebuild strength, endurance and mobility safely.'
                    ],
                    [
                        'heading' => 'Structure beats motivation',
                        'text' => 'Motivation comes and goes, routines stay. With clearly defined training days and manageable sessions, training becomes a habit rather than a struggle.'
                    ],
                    [
                        'heading' => 'Holistic training approach',
                        'text' => 'The plan combines strength, cardio, core stability and mobility. This improves not only muscle strength but also cardiovascular health, joint function and overall movement quality.'
                    ],
                    [
                        'heading' => 'Progression without pressure',
                        'text' => 'Intensity increases gradually through volume, exercise selection and workload. This prevents plateaus and supports steady progress after time off.'
                    ],
                    [
                        'heading' => 'Optimized for body recomposition',
                        'text' => 'Strength training preserves muscle mass while cardio increases calorie expenditure. Together, they support fat loss and muscle gain when paired with sensible nutrition.'
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
                        'example' => 'Consistency for 6 weeks beats intensity for 10 days.'
                    ],
                    [
                        'title' => 'Changing everything at once',
                        'problem' => 'Training, diet, sleep and lifestyle all change simultaneously.',
                        'consequence' => 'Mental and physical overload.',
                        'solution' => 'Build training consistency first, then optimize nutrition.',
                        'example' => 'Establish workouts before cutting calories aggressively.'
                    ],
                    [
                        'title' => 'Ignoring recovery',
                        'problem' => 'No rest days after months of inactivity.',
                        'consequence' => 'Fatigue, declining performance and injury risk.',
                        'solution' => 'Treat recovery as part of the program.',
                        'example' => 'At least one mobility or rest-focused day per week.'
                    ],
                    [
                        'title' => 'Unrealistic expectations',
                        'problem' => 'Expecting visible transformation within 1–2 weeks.',
                        'consequence' => 'Frustration and early dropout.',
                        'solution' => 'Measure progress through energy, routine and performance.',
                        'example' => 'Better sleep, more daily movement and improved strength.'
                    ],
                    [
                        'title' => 'No fixed training schedule',
                        'problem' => 'Training only “when there’s time”.',
                        'consequence' => 'Inconsistency and lack of adaptation.',
                        'solution' => 'Schedule fixed training days.',
                        'example' => 'Monday, Wednesday and Friday as non-negotiable sessions.'
                    ],
                ],
                'summary' => 'Most New Year restarts fail due to unrealistic expectations. This plan succeeds by prioritizing structure, patience and sustainable progress.'
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

