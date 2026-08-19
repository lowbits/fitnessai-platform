@php
    use App\Helpers\LocalizationHelper;
    use App\Helpers\StructuredData;

    $alternateUrls = LocalizationHelper::getAlternateUrls();
@endphp
    <!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" @class(['dark' => ($appearance ?? 'system') == 'dark'])>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Dark mode script --}}
    <script>
        (function() {
            const appearance = '{{ $appearance ?? "system" }}';
            if (appearance === 'system') {
                const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                if (prefersDark) {
                    document.documentElement.classList.add('dark');
                }
            }
        })();
    </script>

    {{-- Inline styles --}}
    <style>
        html { background-color: oklch(1 0 0); }
        html.dark { background-color: oklch(0.145 0 0); }
    </style>

    {{-- Favicons --}}
    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="/favicon-96x96.png">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <meta name="apple-mobile-web-app-title" content="fytrr">
    <link rel="manifest" href="/site.webmanifest">

    {{-- Static meta that doesn't change per page --}}
    <meta name="theme-color" content="{{ __('meta.theme_color') }}">
    <meta name="robots" content="index, follow, max-image-preview:large">
    <meta property="og:locale" content="{{ LaravelLocalization::getCurrentLocaleRegional() }}">

    @foreach(LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
        @if($localeCode !== LaravelLocalization::getCurrentLocale() && isset($alternateUrls[$localeCode]))
            <meta property="og:locale:alternate" content="{{ $properties['regional'] }}">
        @endif
    @endforeach

    {{-- Hreflang tags --}}
    @foreach($alternateUrls as $localeCode => $url)
        <link rel="alternate"
              hreflang="{{ $localeCode }}"
              href="{{ $url }}" />
    @endforeach

    @if(isset($alternateUrls[config('app.fallback_locale')]))
        <link rel="alternate"
              hreflang="x-default"
              href="{{ $alternateUrls[config('app.fallback_locale')] }}" />
    @endif

    {{-- schema.org structured data (JSON-LD) --}}
    @foreach(StructuredData::forRequest(app()->getLocale(), request()->path(), $page['props'] ?? null) as $schema)
    <script type="application/ld+json">
        {!! json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>
    @endforeach

    <!-- Privacy-friendly analytics by Plausible -->
    <link rel="preconnect" href="https://plausible.io" crossorigin>
    <script async src="https://plausible.io/js/pa-5NooHzKTVU8I9YKYq0POF.js"></script>
    <script>
        window.plausible=window.plausible||function(){(plausible.q=plausible.q||[]).push(arguments)},plausible.init=plausible.init||function(i){plausible.o=i||{}};
        plausible.init()
    </script>


    {{-- Inertia will inject page-specific meta tags here --}}
    @vite(['resources/js/app.ts', "resources/js/pages/{$page['component']}.vue"])
    @inertiaHead
</head>
<body class="font-sans antialiased bg-dark-surfaces-900">
@inertia
</body>
</html>
