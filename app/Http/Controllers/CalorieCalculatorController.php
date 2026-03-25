<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;

class CalorieCalculatorController extends Controller
{
    public function __invoke(): Response
    {
        $locale = app()->getLocale();
        $baseUrl = config('app.url');

        $altLocale = $locale === 'de' ? 'en' : 'de';
        $altPath = trans('routes.free_tools_calorie_calculator', [], $altLocale);
        $currentPath = trans('routes.free_tools_calorie_calculator', [], $locale);

        $meta = [
            'title' => $locale === 'de'
                ? 'Kalorienrechner: Täglichen Kalorienbedarf berechnen'
                : 'Calorie Calculator: Calculate Your Daily Calorie Needs',
            'description' => $locale === 'de'
                ? 'Berechne deinen täglichen Kalorienbedarf in 30 Sekunden. Kostenloser Kalorienrechner für Abnehmen, Zunehmen oder Gewicht halten.'
                : 'Calculate your daily calorie needs in 30 seconds. Free calorie calculator for weight loss, muscle gain or maintenance.',
            'canonical' => "{$baseUrl}/{$locale}/{$currentPath}",
        ];

        $alternateUrls = [
            $altLocale => "{$baseUrl}/{$altLocale}/{$altPath}",
        ];

        $schema = $this->buildSchema($locale, $meta);

        return Inertia::render('CalorieCalculator', [
            'meta' => $meta,
            'alternateUrls' => $alternateUrls,
            'schema' => $schema,
        ]);
    }

    private function buildSchema(string $locale, array $meta): array
    {
        $baseUrl = config('app.url');
        $currentPath = trans('routes.free_tools_calorie_calculator', [], $locale);

        $webApp = [
            '@context' => 'https://schema.org',
            '@type' => 'WebApplication',
            'name' => $locale === 'de' ? 'Fytrr Kalorienrechner' : 'Fytrr Calorie Calculator',
            'url' => "{$baseUrl}/{$locale}/{$currentPath}",
            'applicationCategory' => 'HealthApplication',
            'operatingSystem' => 'All',
            'offers' => [
                '@type' => 'Offer',
                'price' => '0',
                'priceCurrency' => 'EUR',
            ],
            'description' => $meta['description'],
            'inLanguage' => $locale,
        ];

        $faqs = $this->getFaqs($locale);
        $faqSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => array_map(fn (array $faq) => [
                '@type' => 'Question',
                'name' => $faq['question'],
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => $faq['answer'],
                ],
            ], $faqs),
        ];

        return [$webApp, $faqSchema];
    }

    private function getFaqs(string $locale): array
    {
        if ($locale === 'de') {
            return [
                [
                    'question' => 'Wie genau ist der Kalorienrechner?',
                    'answer' => 'Unser Kalorienrechner nutzt die Mifflin-St-Jeor-Formel, die in Studien als genaueste Methode zur Berechnung des Grundumsatzes bestätigt wurde. Die Genauigkeit liegt bei ±10% des tatsächlichen Werts. Für noch präzisere Ergebnisse kannst du deinen Kalorienbedarf über 2–3 Wochen mit der Fytrr-App tracken.',
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
                    'answer' => 'Der Rechner liefert einen guten Startwert. Für langfristigen Erfolg empfehlen wir, die ersten 2–3 Wochen die Kalorien zu tracken, um ein Gefühl für Portionsgrößen zu entwickeln. Die Fytrr-App erstellt dir automatisch einen personalisierten Ernährungsplan mit den richtigen Kalorienmengen.',
                ],
            ];
        }

        return [
            [
                'question' => 'How accurate is this calorie calculator?',
                'answer' => 'Our calculator uses the Mifflin-St Jeor equation, confirmed by studies as the most accurate method for estimating basal metabolic rate. Accuracy is within ±10% of actual values. For even more precise results, you can track your calorie intake for 2–3 weeks with the Fytrr app.',
            ],
            [
                'question' => 'How many calories do I need to lose weight?',
                'answer' => 'For weight loss, you need a moderate calorie deficit of 300–500 kcal below your total daily expenditure. Our calculator shows you the exact number. A deficit larger than 1,000 kcal is counterproductive as it promotes muscle loss and slows your metabolism.',
            ],
            [
                'question' => 'How do I calculate my basal metabolic rate?',
                'answer' => 'Your basal metabolic rate (BMR) is the energy your body needs at complete rest. It is calculated using the Mifflin-St Jeor equation: For men: 10 × weight(kg) + 6.25 × height(cm) − 5 × age − 5. For women: 10 × weight(kg) + 6.25 × height(cm) − 5 × age − 161. Enter your data above and the calculator does the rest.',
            ],
            [
                'question' => 'What is the difference between BMR and TDEE?',
                'answer' => 'BMR (Basal Metabolic Rate) is the energy your body uses at complete rest — for breathing, heart function and cell processes. TDEE (Total Daily Energy Expenditure) is your BMR multiplied by an activity factor that accounts for daily movement and exercise. TDEE is the number that matters for your nutrition planning.',
            ],
            [
                'question' => 'Should I track calories or is the calculator enough?',
                'answer' => 'The calculator provides a solid starting point. For long-term success, we recommend tracking calories for the first 2–3 weeks to develop a sense of portion sizes. The Fytrr app automatically creates a personalised nutrition plan with the right calorie amounts.',
            ],
        ];
    }
}
