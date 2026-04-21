@php
    use App\Helpers\LocalizationHelper;
    use Illuminate\Support\Str;

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
    <link rel="icon" href="/favicon.ico" sizes="16x16">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="manifest" href="/site.webmanifest">

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
              href="{{ $alternateUrls[$localeCode] ?? LaravelLocalization::getLocalizedURL($localeCode, null, [], true) }}" />
    @endforeach

    <link rel="alternate"
          hreflang="x-default"
          href="{{ $alternateUrls[config('app.fallback_locale')] ?? LaravelLocalization::getLocalizedURL(config('app.fallback_locale'), null, [], true) }}" />

    {{-- BreadcrumbList schema --}}
    @php
        $baseUrl = config('app.url');
        $locale = app()->getLocale();
        $path = request()->path();
        $segments = array_values(array_filter(explode('/', $path)));

        // Remove locale prefix
        if (!empty($segments) && in_array($segments[0], ['de', 'en'])) {
            array_shift($segments);
        }

        $breadcrumbs = [['name' => 'Home', 'url' => "{$baseUrl}/{$locale}"]];
        $currentUrl = "{$baseUrl}/{$locale}";

        foreach ($segments as $i => $segment) {
            $currentUrl .= "/{$segment}";
            $name = ucfirst(str_replace(['-', '_'], ' ', $segment));
            $breadcrumbs[] = ['name' => $name, 'url' => $currentUrl];
        }

        // Override last breadcrumb name with page title from Inertia props if available
        if (isset($page['props']['meta']['title']) && count($breadcrumbs) > 1) {
            $title = $page['props']['meta']['title'];
            // Strip site name suffix if present
            $breadcrumbs[count($breadcrumbs) - 1]['name'] = Str::before($title, ' |');
        } elseif (isset($page['props']['article']['h1'])) {
            $breadcrumbs[count($breadcrumbs) - 1]['name'] = $page['props']['article']['h1'];
        }
    @endphp
    @if(count($breadcrumbs) > 1)
    <script type="application/ld+json">
        {!! json_encode([
            "\x40context" => 'https://schema.org',
            "\x40type" => 'BreadcrumbList',
            'itemListElement' => collect($breadcrumbs)->map(fn ($crumb, $i) => [
                "\x40type" => 'ListItem',
                'position' => $i + 1,
                'name' => $crumb['name'],
                'item' => $crumb['url'],
            ])->all(),
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>
    @endif

    {{-- Standalone Organization schema --}}
    <script type="application/ld+json">
        {!! json_encode([
            "\x40context" => 'https://schema.org',
            "\x40type" => 'Organization',
            'name' => 'fytrr',
            'url' => config('app.url'),
            'logo' => config('app.url') . '/apple-touch-icon.png',
            'foundingDate' => '2024',
            'founder' => [
                "\x40type" => 'Person',
                'name' => 'Tobias Lobitz',
            ],
            'description' => __('meta.description'),
            'contactPoint' => [
                "\x40type" => 'ContactPoint',
                'email' => 'hello@fytrr.com',
                'contactType' => 'customer support',
            ],
            'sameAs' => [
                'https://instagram.com/getfytrr',
                'https://apps.apple.com/app/fytrr-ki-personal-trainer/id6757151695',
            ],
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>

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
