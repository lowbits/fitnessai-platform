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

        return Inertia::render('DownloadApp', [
            'userName' => $user?->name,
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
}
