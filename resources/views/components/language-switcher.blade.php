@php
    $currentLocale = app()->getLocale();
@endphp

<div class="tw-relative" id="language-switcher">
    <!-- Current Language Button -->
    <button
        type="button"
        id="language-btn"
        class="tw-w-9 tw-h-9 tw-rounded-full tw-overflow-hidden tw-border tw-border-gray-200 tw-cursor-pointer hover:tw-opacity-80 tw-transition-all tw-flex tw-items-center tw-justify-center"
        title="{{ $currentLocale === 'vi' ? 'Tiếng Việt' : 'English' }}" style="border:3px solid #e9e9e9"
    >
        @if($currentLocale === 'vi')
            {{-- Cờ Việt Nam --}}
            <svg class="tw-w-9 tw-h-9" viewBox="0 0 512 512">
                <circle fill="#DA251D" cx="256" cy="256" r="256"/>
                <path fill="#FFFF00" d="M256 133.6l27.2 83.7h88l-71.2 51.7 27.2 83.8-71.2-51.8-71.2 51.8 27.2-83.8-71.2-51.7h88z"/>
            </svg>
        @else
            {{-- Cờ Mỹ --}}
            <svg class="tw-w-9 tw-h-9" viewBox="0 0 512 512">
                <defs>
                    <clipPath id="circleClip1">
                        <circle cx="256" cy="256" r="256"/>
                    </clipPath>
                </defs>
                <g clip-path="url(#circleClip1)">
                    <rect fill="#bf0a30" y="0" width="512" height="512"/>
                    <rect fill="#fff" y="39" width="512" height="39"/>
                    <rect fill="#fff" y="118" width="512" height="39"/>
                    <rect fill="#fff" y="197" width="512" height="39"/>
                    <rect fill="#fff" y="276" width="512" height="39"/>
                    <rect fill="#fff" y="354" width="512" height="39"/>
                    <rect fill="#fff" y="433" width="512" height="39"/>
                    <rect fill="#002868" y="0" width="205" height="276"/>
                    <g fill="#fff">
                        <polygon points="17,10 20,19 29,19 22,25 24,34 17,28 10,34 12,25 5,19 14,19"/>
                        <polygon points="51,10 54,19 63,19 56,25 58,34 51,28 44,34 46,25 39,19 48,19"/>
                        <polygon points="85,10 88,19 97,19 90,25 92,34 85,28 78,34 80,25 73,19 82,19"/>
                        <polygon points="119,10 122,19 131,19 124,25 126,34 119,28 112,34 114,25 107,19 116,19"/>
                        <polygon points="153,10 156,19 165,19 158,25 160,34 153,28 146,34 148,25 141,19 150,19"/>
                        <polygon points="187,10 190,19 199,19 192,25 194,34 187,28 180,34 182,25 175,19 184,19"/>
                        <polygon points="34,30 37,39 46,39 39,45 41,54 34,48 27,54 29,45 22,39 31,39"/>
                        <polygon points="68,30 71,39 80,39 73,45 75,54 68,48 61,54 63,45 56,39 65,39"/>
                        <polygon points="102,30 105,39 114,39 107,45 109,54 102,48 95,54 97,45 90,39 99,39"/>
                        <polygon points="136,30 139,39 148,39 141,45 143,54 136,48 129,54 131,45 124,39 133,39"/>
                        <polygon points="170,30 173,39 182,39 175,45 177,54 170,48 163,54 165,45 158,39 167,39"/>
                    </g>
                </g>
            </svg>
        @endif
    </button>

    <!-- Dropdown -->
    <div
        id="language-dropdown"
        class="tw-absolute tw-right-0 tw-mt-2 tw-bg-white tw-rounded-lg tw-shadow-lg tw-border tw-border-gray-200 tw-py-1 tw-z-50 tw-hidden"
    >
        @if($currentLocale === 'vi')
            <a
                href="{{ LaravelLocalization::getLocalizedURL('en', null, [], true) }}"
                class="tw-flex tw-items-center tw-gap-3 tw-px-4 tw-py-2 tw-text-sm tw-text-gray-700 hover:tw-bg-gray-100 tw-transition tw-whitespace-nowrap"
            >
                {{-- Cờ Mỹ --}}
                <svg class="tw-w-5 tw-h-5 tw-flex-shrink-0 tw-rounded-full" viewBox="0 0 512 512">
                    <defs>
                        <clipPath id="circleClip2">
                            <circle cx="256" cy="256" r="256"/>
                        </clipPath>
                    </defs>
                    <g clip-path="url(#circleClip2)">
                        <rect fill="#bf0a30" y="0" width="512" height="512"/>
                        <rect fill="#fff" y="39" width="512" height="39"/>
                        <rect fill="#fff" y="118" width="512" height="39"/>
                        <rect fill="#fff" y="197" width="512" height="39"/>
                        <rect fill="#fff" y="276" width="512" height="39"/>
                        <rect fill="#fff" y="354" width="512" height="39"/>
                        <rect fill="#fff" y="433" width="512" height="39"/>
                        <rect fill="#002868" y="0" width="205" height="276"/>
                        <g fill="#fff">
                            <polygon points="17,10 20,19 29,19 22,25 24,34 17,28 10,34 12,25 5,19 14,19"/>
                            <polygon points="51,10 54,19 63,19 56,25 58,34 51,28 44,34 46,25 39,19 48,19"/>
                            <polygon points="85,10 88,19 97,19 90,25 92,34 85,28 78,34 80,25 73,19 82,19"/>
                            <polygon points="119,10 122,19 131,19 124,25 126,34 119,28 112,34 114,25 107,19 116,19"/>
                            <polygon points="153,10 156,19 165,19 158,25 160,34 153,28 146,34 148,25 141,19 150,19"/>
                            <polygon points="187,10 190,19 199,19 192,25 194,34 187,28 180,34 182,25 175,19 184,19"/>
                            <polygon points="34,30 37,39 46,39 39,45 41,54 34,48 27,54 29,45 22,39 31,39"/>
                            <polygon points="68,30 71,39 80,39 73,45 75,54 68,48 61,54 63,45 56,39 65,39"/>
                            <polygon points="102,30 105,39 114,39 107,45 109,54 102,48 95,54 97,45 90,39 99,39"/>
                            <polygon points="136,30 139,39 148,39 141,45 143,54 136,48 129,54 131,45 124,39 133,39"/>
                            <polygon points="170,30 173,39 182,39 175,45 177,54 170,48 163,54 165,45 158,39 167,39"/>
                        </g>
                    </g>
                </svg>
                <span>English</span>
            </a>
        @else
            <a
                href="{{ LaravelLocalization::getLocalizedURL('vi', null, [], true) }}"
                class="tw-flex tw-items-center tw-gap-3 tw-px-4 tw-py-2 tw-text-sm tw-text-gray-700 hover:tw-bg-gray-100 tw-transition tw-whitespace-nowrap"
            >
                {{-- Cờ Việt Nam --}}
                <svg class="tw-w-5 tw-h-5 tw-flex-shrink-0 tw-rounded-full" viewBox="0 0 512 512">
                    <circle fill="#DA251D" cx="256" cy="256" r="256"/>
                    <path fill="#FFFF00" d="M256 133.6l27.2 83.7h88l-71.2 51.7 27.2 83.8-71.2-51.8-71.2 51.8 27.2-83.8-71.2-51.7h88z"/>
                </svg>
                <span>Tiếng Việt</span>
            </a>
        @endif
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const btn = document.getElementById('language-btn');
    const dropdown = document.getElementById('language-dropdown');

    if (btn && dropdown) {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            dropdown.classList.toggle('tw-hidden');
        });

        document.addEventListener('click', function(e) {
            if (!dropdown.contains(e.target) && !btn.contains(e.target)) {
                dropdown.classList.add('tw-hidden');
            }
        });
    }
});
</script>
