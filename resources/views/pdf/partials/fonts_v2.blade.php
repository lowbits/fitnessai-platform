{{-- Brand fonts embedded for DOMPDF: Nunito for body text, Space Grotesk for
     display. DOMPDF needs TTF and only renders embedded faces. --}}
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
        font-family: 'Space Grotesk';
        font-style: normal;
        font-weight: normal;
        src: url("{{ public_path('assets/fonts/SpaceGrotesk-Regular.ttf') }}") format('truetype');
    }
    @font-face {
        font-family: 'Space Grotesk';
        font-style: normal;
        font-weight: 500;
        src: url("{{ public_path('assets/fonts/SpaceGrotesk-Medium.ttf') }}") format('truetype');
    }
    @font-face {
        font-family: 'Space Grotesk';
        font-style: normal;
        font-weight: bold;
        src: url("{{ public_path('assets/fonts/SpaceGrotesk-Bold.ttf') }}") format('truetype');
    }
</style>
