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
        $user = User::with('profile')->findOrFail($request->query('user'));

        $isMobile = (bool) preg_match('/Mobile|Android|iPhone|iPad|iPod/i', $request->userAgent() ?? '');

        $appStoreUrl = config('app.app_store.ios.url');

        $setPasswordUrl = null;
        if (! $user->password) {
            $token = $user->getPasswordResetToken();
            $setPasswordUrl = URL::signedRoute('set-password', [
                'token' => $token,
                'email' => $user->email,
            ]);
        }

        $appStoreQrCode = null;
        $setPasswordQrCode = null;

        if (! $isMobile) {
            $qrCodeService = app(QrCodeService::class);
            $appStoreQrCode = $qrCodeService->generate($appStoreUrl);

            if ($setPasswordUrl) {
                $setPasswordQrCode = $qrCodeService->generate($setPasswordUrl);
            }
        }

        return Inertia::render('DownloadApp', [
            'userName' => $user->name,
            'bodyGoal' => $user->profile?->body_goal?->value,
            'setPasswordUrl' => $setPasswordUrl,
            'appStoreUrl' => $appStoreUrl,
            'isMobile' => $isMobile,
            'appStoreQrCode' => $appStoreQrCode,
            'setPasswordQrCode' => $setPasswordQrCode,
            'utmSource' => $request->query('utm_source'),
            'utmMedium' => $request->query('utm_medium'),
            'utmCampaign' => $request->query('utm_campaign'),
        ]);
    }
}
