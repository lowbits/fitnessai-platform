<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"  @class(['dark' => ($appearance ?? 'system') == 'dark'])>
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

        <title>{{ __('meta.title') }}</title>
        <meta name="title" content="{{ __('meta.title') }}">
        <meta name="description" content="{{ __('meta.description') }}">

        <!-- Canonical URL -->
        <link rel="canonical" href="{{ url()->current() }}">

        <!-- Theme Color -->
        <meta name="theme-color" content="{{ __('meta.theme_color') }}">

        <!-- Robots -->
        <meta name="robots" content="index, follow, max-image-preview:large">

        <!-- Open Graph / Facebook -->
        <meta property="og:type" content="{{ __('meta.og.type') }}">
        <meta property="og:url" content="{{ url()->current() }}">
        <meta property="og:title" content="{{ __('meta.title_short') }}">
        <meta property="og:description" content="{{ __('meta.description_social') }}">
        <meta property="og:image" content="{{ asset('/fitness-plan.png') }}">
        <meta property="og:image:width" content="1200">
        <meta property="og:image:height" content="630">
        <meta property="og:site_name" content="{{ __('meta.og.site_name') }}">

        <!-- Twitter -->
        <meta name="twitter:card" content="{{ __('meta.twitter.card') }}">
        <meta name="twitter:site" content="{{ __('meta.twitter.site') }}">
        <meta name="twitter:url" content="{{ url()->current() }}">
        <meta name="twitter:title" content="{{ __('meta.title') }}">
        <meta name="twitter:description" content="{{ __('meta.description_social') }}">
        <meta name="twitter:image" content="{{ asset('/fitness-plan.png') }}">
        <!-- Open Graph Locale -->
        <meta property="og:locale" content="{{ LaravelLocalization::getCurrentLocaleRegional() }}">
        @foreach(LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
            @if($localeCode !== LaravelLocalization::getCurrentLocale())
                <meta property="og:locale:alternate" content="{{ $properties['regional'] }}">
            @endif
        @endforeach

        @foreach(LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
            <link rel="alternate"
                  hreflang="{{ $localeCode }}"
                  href="{{ LaravelLocalization::getLocalizedURL($localeCode, null, [], true) }}" />
        @endforeach

        <link rel="alternate"
              hreflang="x-default"
              href="{{ LaravelLocalization::getLocalizedURL(config('app.fallback_locale'), null, [], true) }}" />


        @vite(['resources/js/app.ts', "resources/js/pages/{$page['component']}.vue"])
        @inertiaHead
    </head>
    <body class="font-sans antialiased bg-dark-surfaces-900">
        @inertia
    </body>
</html>
