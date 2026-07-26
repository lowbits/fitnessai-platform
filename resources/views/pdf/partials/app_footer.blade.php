@php
    use App\Support\AppDownloadQr;

    // Personalized signed link for taps; short clean URL for the QR (scannability).
    $downloadUrl = AppDownloadQr::url($user ?? null);
    $qrDataUri = AppDownloadQr::dataUri(AppDownloadQr::scanUrl(), 5);

    $locale = app()->getLocale();
    $badgeFile = $locale === 'de' ? 'App_Store_Badge_DE.png' : 'App_Store_Badge_EN.png';
    $badgePath = public_path('assets/badges/'.$badgeFile);
@endphp

<style type="text/css">
    .pdf-footer {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        height: 56px;
        background: #ffffff;
        border-top: 1px solid #E3EAF2;
    }

    .pdf-footer__accent {
        height: 3px;
        background: #48D670;
    }

    .pdf-footer__inner {
        width: 100%;
        height: 53px;
        border-collapse: collapse;
    }

    .pdf-footer__inner td {
        vertical-align: middle;
    }

    .pdf-footer__text {
        padding: 0 20px 0 36px;
    }

    .pdf-footer__title {
        display: block;
        font-size: 11px;
        font-weight: bold;
        color: #08233E;
        line-height: 1.3;
    }

    .pdf-footer__subtitle {
        display: block;
        font-size: 9px;
        color: #647488;
        line-height: 1.3;
        margin-top: 2px;
    }

    .pdf-footer__link {
        display: block;
        font-size: 9px;
        color: #2BB673;
        font-weight: bold;
        margin-top: 3px;
        text-decoration: none;
    }

    .pdf-footer__badge-cell {
        text-align: right;
        width: 108px;
        padding: 0 16px 0 0;
    }

    .pdf-footer__qr-cell {
        text-align: center;
        width: 46px;
        padding: 0 36px 0 0;
    }

    .pdf-footer__qr {
        border: 1px solid #E3EAF2;
        border-radius: 6px;
        padding: 2px;
        background: #ffffff;
    }
</style>

<div class="pdf-footer">
    <div class="pdf-footer__accent"></div>
    <table class="pdf-footer__inner">
        <tr>
            <td class="pdf-footer__text">
                <span class="pdf-footer__title">{{ __('pdf.footer.title') }}</span>
                <span class="pdf-footer__subtitle">{{ __('pdf.footer.subtitle') }}</span>
                <a href="{{ $downloadUrl }}" class="pdf-footer__link">{{ __('pdf.footer.download') }} &rarr; {{ AppDownloadQr::displayUrl() }}</a>
            </td>
            <td class="pdf-footer__badge-cell">
                <a href="{{ $downloadUrl }}"><img src="{{ $badgePath }}" alt="App Store" width="96" height="32"></a>
            </td>
            <td class="pdf-footer__qr-cell">
                <a href="{{ $downloadUrl }}"><img class="pdf-footer__qr" src="{{ $qrDataUri }}" alt="{{ __('pdf.footer.scan') }}" width="46" height="46"></a>
            </td>
        </tr>
    </table>
</div>
