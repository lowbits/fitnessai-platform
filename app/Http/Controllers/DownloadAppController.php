<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\QrCodeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Inertia\Inertia;
use Inertia\Response;

class DownloadAppController extends Controller
{
    public function __invoke(Request $request, string $_locale): Response
    {
        $user = null;
        if ($request->query('user')) {
            abort_unless($request->hasValidSignature(), 403);
            $user = User::with('profile')->findOrFail($request->query('user'));
        }

        $isMobile = (bool) preg_match('/Mobile|Android|iPhone|iPad|iPod/i', $request->userAgent() ?? '');

        $locale = app()->getLocale();
        $appStoreUrl = config('app.app_store.ios.url');

        $setPasswordUrl = null;
        $setPasswordDeepLink = null;
        if ($user && ! $user->password) {
            $token = $user->getPasswordResetToken();
            $setPasswordUrl = URL::signedRoute('set-password', [
                'token' => $token,
                'email' => $user->email,
            ]);
            $setPasswordDeepLink = 'fytrr://set-password?'.http_build_query([
                'token' => $token,
                'email' => $user->email,
            ]);
        }

        $isAccountReady = $user && $user->password && $user->hasVerifiedEmail();

        $appStoreQrCode = null;
        $setPasswordQrCode = null;
        $openAppQrCode = null;

        if (! $isMobile) {
            $qrCodeService = app(QrCodeService::class);
            $appStoreQrCode = $qrCodeService->generate($appStoreUrl);

            if ($setPasswordUrl) {
                $setPasswordQrCode = $qrCodeService->generate($setPasswordUrl);
            }

            if ($isAccountReady) {
                $openAppQrCode = $qrCodeService->generate('fytrr://');
            }
        }

        $reviews = $this->appReviews($locale);

        $screenshots = array_map(
            fn (string $slug) => config('app.url')."/assets/images/app/{$slug}-{$locale}.webp",
            ['fytrr-ki-fitness-app', 'fytrr-kalorien-tracker', 'fytrr-trainingsplan', 'fytrr-fortschritt'],
        );

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'MobileApplication',
            'name' => 'fytrr — KI Personal Trainer',
            'operatingSystem' => 'iOS',
            'applicationCategory' => 'HealthApplication',
            'applicationSubCategory' => 'Fitness',
            'url' => $appStoreUrl,
            'downloadUrl' => $appStoreUrl,
            'image' => config('app.url').'/assets/images/og/fytrr-app.webp',
            'screenshot' => $screenshots,
            'featureList' => [
                'AI workout plan with progressive overload',
                'AI meal plan with recipes and shopping list',
                'Calorie and macro tracking with photo logging',
                'Meal and exercise swaps',
                'Progress tracking with Apple Health sync',
                'Mona AI coach',
            ],
            'offers' => [
                '@type' => 'Offer',
                'price' => '0',
                'priceCurrency' => 'EUR',
                'description' => 'Free 7-day trial, then 3.99 EUR/month',
            ],
            'author' => [
                '@type' => 'Organization',
                'name' => 'fytrr',
                'url' => config('app.url'),
            ],
            'aggregateRating' => [
                '@type' => 'AggregateRating',
                'ratingValue' => '5',
                'reviewCount' => 11,
                'bestRating' => '5',
                'worstRating' => '1',
            ],
            'review' => array_map(fn (array $review) => [
                '@type' => 'Review',
                'author' => ['@type' => 'Person', 'name' => $review['author']],
                'datePublished' => $review['date'],
                'name' => $review['title'],
                'reviewBody' => $review['body'],
                'reviewRating' => ['@type' => 'Rating', 'ratingValue' => '5', 'bestRating' => '5'],
            ], $reviews),
        ];

        return Inertia::render('DownloadApp', [
            'userName' => $user?->name,
            'schema' => $schema,
            'reviews' => $reviews,
            'bodyGoal' => $user?->profile?->body_goal?->value,
            'setPasswordUrl' => $setPasswordUrl,
            'setPasswordDeepLink' => $setPasswordDeepLink,
            'appStoreUrl' => $appStoreUrl,
            'isMobile' => $isMobile,
            'appStoreQrCode' => $appStoreQrCode,
            'setPasswordQrCode' => $setPasswordQrCode,
            'openAppQrCode' => $openAppQrCode,
            'utmSource' => $request->query('utm_source'),
            'utmMedium' => $request->query('utm_medium'),
            'utmCampaign' => $request->query('utm_campaign'),
            'isAccountReady' => $isAccountReady,
        ]);
    }

    /**
     * Real 5-star App Store reviews, localized for display and Review schema.
     *
     * @return array<int, array{title: string, body: string, author: string, date: string}>
     */
    private function appReviews(string $locale): array
    {
        if ($locale === 'de') {
            return [
                [
                    'title' => 'Motivation pur',
                    'body' => 'Ich war nie sehr motiviert, aber ich bin jetzt schon seit über einem Monat dabei und schon fast süchtig nach dem Training!',
                    'author' => 'Jazzilalala',
                    'date' => '2026-01-30',
                ],
                [
                    'title' => 'Die beste App zum Zunehmen oder Abnehmen',
                    'body' => 'Ich hatte immer Probleme mit dem Zunehmen, mit fytrr habe ich es endlich geschafft. Man bekommt jeden Tag einen klaren Ernährungsplan und Trainingsplan.',
                    'author' => 'olele-dbld',
                    'date' => '2026-03-28',
                ],
                [
                    'title' => 'Tolle App',
                    'body' => 'Seit etwa 30 Tagen teste ich diese App und bin sehr zufrieden. Sie hat mir geholfen, Gewicht zu verlieren und meine Fitness zu verbessern.',
                    'author' => 'Benedikt Kuhlmann',
                    'date' => '2026-02-05',
                ],
            ];
        }

        return [
            [
                'title' => 'Pure motivation',
                'body' => 'I was never very motivated, but I have been at it for over a month now and I am almost addicted to training!',
                'author' => 'Jazzilalala',
                'date' => '2026-01-30',
            ],
            [
                'title' => 'The best app for gaining or losing weight',
                'body' => 'I always struggled with gaining weight; with fytrr I finally managed it. You get a clear nutrition plan and workout plan every day.',
                'author' => 'olele-dbld',
                'date' => '2026-03-28',
            ],
            [
                'title' => 'Great app',
                'body' => 'I have been testing this app for about 30 days and I am very happy. It has helped me lose weight and improve my fitness.',
                'author' => 'Benedikt Kuhlmann',
                'date' => '2026-02-05',
            ],
        ];
    }
}
