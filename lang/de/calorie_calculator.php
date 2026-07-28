<?php

return [
    'meta' => [
        'title' => 'Kalorienrechner: Täglichen Kalorienbedarf berechnen',
        'description' => 'Berechne deinen täglichen Kalorienbedarf in 30 Sekunden — zum Abnehmen, Zunehmen oder Gewicht halten. Kostenloser Kalorienrechner, wissenschaftlich fundiert.',
    ],

    'schema' => [
        'name' => 'fytrr Kalorienrechner',
    ],

    'faqs' => [
        [
            'question' => 'Wie genau ist der Kalorienrechner?',
            'answer' => 'Unser Kalorienrechner nutzt die Mifflin-St-Jeor-Formel, die in Studien als genaueste Methode zur Berechnung des Grundumsatzes bestätigt wurde. Die Genauigkeit liegt bei ±10% des tatsächlichen Werts. Für noch präzisere Ergebnisse kannst du deinen Kalorienbedarf über 2–3 Wochen mit der fytrr-App tracken.',
        ],
        [
            'question' => 'Wie viele Kalorien brauche ich zum Abnehmen?',
            'answer' => 'Zum Abnehmen benötigst du ein moderates Kaloriendefizit von 300–500 kcal unter deinem Gesamtbedarf. Unser Rechner zeigt dir den exakten Wert. Ein zu großes Defizit (über 1.000 kcal) ist kontraproduktiv, da es Muskelabbau fördert und den Stoffwechsel verlangsamt.',
        ],
        [
            'question' => 'Wie berechne ich meinen Grundumsatz?',
            'answer' => 'Der Grundumsatz (BMR) ist die Energiemenge, die dein Körper im Ruhezustand benötigt. Er wird nach der Mifflin-St-Jeor-Formel berechnet: Für Männer: 10 × Gewicht(kg) + 6,25 × Größe(cm) − 5 × Alter − 5. Für Frauen: 10 × Gewicht(kg) + 6,25 × Größe(cm) − 5 × Alter − 161. Gib deine Daten oben ein, der Rechner berechnet alles automatisch.',
        ],
        [
            'question' => 'Was ist der Unterschied zwischen Grundumsatz und Gesamtumsatz?',
            'answer' => 'Der Grundumsatz (BMR) ist die Energie, die dein Körper in völliger Ruhe verbraucht — für Atmung, Herzschlag und Zellprozesse. Der Gesamtumsatz (TDEE) ist der Grundumsatz multipliziert mit einem Aktivitätsfaktor, der deine tägliche Bewegung und Sport berücksichtigt. Der Gesamtumsatz ist die relevante Zahl für deine Ernährungsplanung.',
        ],
        [
            'question' => 'Soll ich Kalorien tracken oder reicht der Rechner?',
            'answer' => 'Der Rechner liefert einen guten Startwert. Für langfristigen Erfolg empfehlen wir, die ersten 2–3 Wochen die Kalorien zu tracken, um ein Gefühl für Portionsgrößen zu entwickeln. Die fytrr-App erstellt dir automatisch einen personalisierten Ernährungsplan mit den richtigen Kalorienmengen.',
        ],
    ],
];
