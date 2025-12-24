@php use App\Helpers\LocalizationHelper; @endphp
    <!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" @class(['dark' => ($appearance ?? 'system') == 'dark'])>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Inline script to detect system dark mode preference and apply it immediately --}}
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

    {{-- Inline style to set the HTML background color based on our theme in app.css --}}
    <style>
        html {
            background-color: oklch(1 0 0);
        }

        html.dark {
            background-color: oklch(0.145 0 0);
        }
    </style>

    <title inertia>{{ config('app.name', 'Laravel') }}</title>

    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet" />

    {{-- Theme Color --}}
    <meta name="theme-color" content="{{ __('meta.theme_color') }}">

    {{-- Robots --}}
    <meta name="robots" content="index, follow, max-image-preview:large">

    {{-- Open Graph Locale --}}
    <meta property="og:locale" content="{{ LaravelLocalization::getCurrentLocaleRegional() }}">
    @foreach(LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
        @if($localeCode !== LaravelLocalization::getCurrentLocale())
            <meta property="og:locale:alternate" content="{{ $properties['regional'] }}">
        @endif
    @endforeach


    @if(isset($page['props']['alternateUrls']) && !empty($page['props']['alternateUrls']))
        {{-- Use manually generated alternates from controller --}}
        @foreach($page['props']['alternateUrls'] as $localeCode => $url)
            <link rel="alternate"
                  hreflang="{{ $localeCode }}"
                  href="{{ $url }}" />
        @endforeach

        {{-- Add x-default (typically English) --}}
        @if(isset($page['props']['alternateUrls']['en']))
            <link rel="alternate"
                  hreflang="x-default"
                  href="{{ $page['props']['alternateUrls']['en'] }}" />
        @endif
    @else
        {{-- Fallback: Automatic generation for routes without parameters --}}
        @foreach(LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
            <link rel="alternate"
                  hreflang="{{ $localeCode }}"
                  href="{{ LaravelLocalization::getLocalizedURL(locale: $localeCode, forceDefaultLocation: true) }}" />
        @endforeach
    @endif

    {{-- Page-specific meta tags from Inertia pages --}}
    @inertiaHead

    @vite(['resources/js/app.ts', "resources/js/pages/{$page['component']}.vue"])
</head>
<body class="font-sans antialiased bg-dark-surfaces-900">
@inertia
</body>
</html>
