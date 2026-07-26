{{-- Embed the Nunito brand font so PDF text renders in every viewer
     (DOMPDF would otherwise fall back to non-embedded Helvetica). --}}
<style type="text/css">
    @font-face {
        font-family: 'Nunito';
        font-style: normal;
        font-weight: normal;
        src: url("{{ public_path('assets/fonts/Nunito-Regular.ttf') }}") format('truetype');
    }
    @font-face {
        font-family: 'Nunito';
        font-style: normal;
        font-weight: bold;
        src: url("{{ public_path('assets/fonts/Nunito-Bold.ttf') }}") format('truetype');
    }
    @font-face {
        font-family: 'Nunito';
        font-style: italic;
        font-weight: normal;
        src: url("{{ public_path('assets/fonts/Nunito-Italic.ttf') }}") format('truetype');
    }
    @font-face {
        font-family: 'Nunito';
        font-style: italic;
        font-weight: bold;
        src: url("{{ public_path('assets/fonts/Nunito-BoldItalic.ttf') }}") format('truetype');
    }
</style>
