<?php /** @var string $eyebrow */ ?>
<?php /** @var string[] $titleLines */ ?>
<?php /** @var string $subtitle */ ?>
<svg xmlns="http://www.w3.org/2000/svg" width="1200" height="630" viewBox="0 0 1200 630" fill="none">
    <defs>
        <linearGradient id="bg" x1="0" y1="0" x2="1200" y2="630" gradientUnits="userSpaceOnUse">
            <stop offset="0" stop-color="#0C0F0E"/>
            <stop offset="1" stop-color="#070908"/>
        </linearGradient>
        <radialGradient id="glow" cx="0" cy="0" r="1" gradientTransform="translate(960 300) scale(430)" gradientUnits="userSpaceOnUse">
            <stop stop-color="#3EE07F" stop-opacity="0.30"/>
            <stop offset="1" stop-color="#3EE07F" stop-opacity="0"/>
        </radialGradient>
        <g id="mark">
            <g transform="translate(0, 5.264)">
                <path d="M84.7821 12.7348C88.0261 15.0919 88.7457 19.6325 86.3885 22.8766L77.7388 34.7819C68.1931 29.2048 55.7863 31.6667 49.1504 40.8009C42.5141 49.9349 44.0065 62.4945 52.2605 69.8496L33.0418 96.3023C29.5063 101.168 22.6953 102.247 17.8291 98.7118L9.01801 92.31C-0.714399 85.2391 -2.87184 71.6173 4.1991 61.8846L42.6088 9.01826C49.6797 -0.714215 63.3016 -2.87171 73.0342 4.19935L84.7821 12.7348Z" fill="#3EE07F"/>
                <path d="M111.47 32.2299C121.203 39.3008 123.36 52.9227 116.289 62.6553L86.415 103.774C81.7009 110.262 72.6196 111.7 66.1314 106.986L48.5092 94.1829C45.2652 91.826 44.546 87.2852 46.9028 84.0411L55.4824 72.2322C65.0699 78.0579 77.6836 75.6451 84.3954 66.4073C91.1068 57.1691 89.504 44.4273 81.001 37.1092L89.5803 25.3007C91.9375 22.0566 96.478 21.3374 99.7221 23.6944L111.47 32.2299Z" fill="#3EE07F"/>
                <path d="M56.1036 68.2893C64.2138 74.1818 75.5653 72.384 81.4581 64.2734C87.3505 56.1632 85.5524 44.8114 77.4421 38.9189C69.3319 33.0265 57.9801 34.8244 52.0877 42.9348C46.1953 51.0451 47.993 62.3969 56.1036 68.2893Z" fill="#EAF7EF"/>
            </g>
        </g>
    </defs>

    <rect width="1200" height="630" fill="url(#bg)"/>
    <rect width="1200" height="630" fill="url(#glow)"/>

    {{-- Hero brand mark, right --}}
    <use href="#mark" transform="translate(792 132) scale(2.9)"/>

    {{-- Wordmark: mark + name + dot --}}
    <use href="#mark" transform="translate(80 62) scale(0.4)"/>
    <text x="135" y="99" font-family="'Space Grotesk'" font-size="38" font-weight="700" fill="#FFFFFF" letter-spacing="0.3">fytrr</text>
    <circle cx="232" cy="96" r="5" fill="#3EE07F"/>

    {{-- Eyebrow --}}
    <text x="82" y="232" font-family="'Space Grotesk'" font-size="25" font-weight="700" fill="#3EE07F" letter-spacing="3">{{ $eyebrow }}</text>

    {{-- Title --}}
    <text font-family="'Space Grotesk'" font-size="68" font-weight="700" fill="#FFFFFF" letter-spacing="-1.5">
        @foreach ($titleLines as $i => $line)
            <tspan x="80" y="{{ 308 + $i * 78 }}">{{ $line }}</tspan>
        @endforeach
    </text>

    {{-- Subtitle --}}
    @if ($subtitle !== '')
        <text x="82" y="{{ 308 + (count($titleLines) - 1) * 78 + 56 }}" font-family="Nunito" font-size="27" font-weight="500" fill="#A7B0AD">{{ $subtitle }}</text>
    @endif

    {{-- Footer --}}
    <text x="82" y="590" font-family="Nunito" font-size="22" font-weight="700" fill="#5B6763">fytrr.com</text>
</svg>
