<?php /** @var string $eyebrow */ ?>
<?php /** @var string[] $titleLines */ ?>
<?php /** @var string $subtitle */ ?>
<svg xmlns="http://www.w3.org/2000/svg" width="1200" height="630" viewBox="0 0 1200 630" fill="none">
    <defs>
        <linearGradient id="bg" x1="0" y1="0" x2="1200" y2="630" gradientUnits="userSpaceOnUse">
            <stop offset="0" stop-color="#0B2E52"/>
            <stop offset="1" stop-color="#08233E"/>
        </linearGradient>
        <radialGradient id="glow" cx="0" cy="0" r="1" gradientTransform="translate(1140 610) rotate(-135) scale(520)" gradientUnits="userSpaceOnUse">
            <stop stop-color="#48D670" stop-opacity="0.22"/>
            <stop offset="1" stop-color="#48D670" stop-opacity="0"/>
        </radialGradient>
    </defs>

    <rect width="1200" height="630" fill="url(#bg)"/>
    <rect width="1200" height="630" fill="url(#glow)"/>
    <rect x="0" y="0" width="10" height="630" fill="#48D670"/>

    <g font-family="'Helvetica Neue', Helvetica, Arial, sans-serif">
        {{-- Wordmark --}}
        <text x="80" y="104" font-size="34" font-weight="700" fill="#FFFFFF" letter-spacing="0.5">fytrr</text>
        <circle cx="168" cy="94" r="6" fill="#48D670"/>

        {{-- Eyebrow --}}
        <text x="80" y="232" font-size="26" font-weight="700" fill="#48D670" letter-spacing="3">{{ $eyebrow }}</text>

        {{-- Title (pre-wrapped into lines) --}}
        <text x="78" font-size="76" font-weight="800" fill="#FFFFFF" letter-spacing="-1">
            @foreach ($titleLines as $i => $line)
                <tspan x="78" y="{{ 316 + $i * 88 }}">{{ $line }}</tspan>
            @endforeach
        </text>

        {{-- Subtitle --}}
        @if ($subtitle !== '')
            <text x="80" y="{{ 316 + (count($titleLines) - 1) * 88 + 62 }}" font-size="30" font-weight="400" fill="#9EC2E6">{{ $subtitle }}</text>
        @endif

        {{-- Footer --}}
        <text x="80" y="576" font-size="24" font-weight="600" fill="#6E8CAE">fytrr.com</text>
    </g>
</svg>
