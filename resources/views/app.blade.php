@php use App\Helpers\LocalizationHelper; @endphp
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
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet" />

    {{-- Static meta that doesn't change per page --}}
    <meta name="theme-color" content="{{ __('meta.theme_color') }}">
    <meta name="robots" content="index, follow, max-image-preview:large">
    <meta property="og:locale" content="{{ LaravelLocalization::getCurrentLocaleRegional() }}">

    @foreach(LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
        @if($localeCode !== LaravelLocalization::getCurrentLocale())
            <meta property="og:locale:alternate" content="{{ $properties['regional'] }}">
        @endif
    @endforeach

    {{-- Hreflang tags --}}
    @foreach(LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
        <link rel="alternate"
              hreflang="{{ $localeCode }}"
              href="{{ LaravelLocalization::getLocalizedURL($localeCode, null, [], true) }}" />
    @endforeach

    <link rel="alternate"
          hreflang="x-default"
          href="{{ LaravelLocalization::getLocalizedURL(config('app.fallback_locale'), null, [], true) }}" />

    {{-- Inertia will inject page-specific meta tags here --}}
    @vite(['resources/js/app.ts', "resources/js/pages/{$page['component']}.vue"])
    @inertiaHead
</head>
<body class="font-sans antialiased bg-dark-surfaces-900">
@inertia
</body>
</html>
