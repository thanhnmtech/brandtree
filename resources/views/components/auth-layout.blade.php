<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'AI Cây Thương Hiệu' }}</title>
     <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700&display=swap"
        rel="stylesheet" />
    <!-- Toastify CSS (Local) -->
    <link rel="stylesheet" href="{{ asset('assets/css/toastify.min.css') }}">

    <!-- Vite Assets (Tailwind + App) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="tw-bg-[#f8faf9] tw-font-bevietnam tw-text-[#1a1a1a]">
    <!-- TOP TASKBAR -->
    <div class="tw-fixed tw-top-0 tw-left-0 tw-w-full tw-h-[70px] tw-bg-white tw-shadow-md tw-flex tw-items-center tw-justify-between tw-px-6 md:tw-px-14 tw-z-[999]">
        <div class="tw-flex tw-items-center tw-gap-3">
            <img src="{{ asset('assets/img/ccf8fd54d8a10b4ab49d514622f1efb57099e1a4.svg') }}" class="tw-w-[42px] tw-h-[42px] tw-object-contain" />
            <div class="tw-leading-tight">
                <h1 class="tw-text-base md:tw-text-lg tw-font-bold">AI Cây Thương Hiệu</h1>
                <p class="tw-text-[10px] md:tw-text-xs tw-text-gray-500 tw--mt-1">By VLBC</p>
            </div>
        </div>

        <div class="tw-hidden sm:tw-flex tw-items-center tw-gap-3 md:tw-gap-6">
            <!-- Language Switcher -->
            <x-language-switcher />

            <a href="{{ route('login') }}" class="tw-py-1 tw-px-4 md:tw-px-6 tw-rounded-full tw-font-semibold {{ request()->routeIs('login') ? 'tw-text-[#16a249] tw-border-2 tw-border-[#16a249]' : 'tw-text-black hover:tw-text-[#16a249]' }} tw-text-xs md:tw-text-sm tw-transition">
                {{ __('auth.login') }}
            </a>

            <a href="{{ route('register') }}" class="tw-py-1 tw-px-4 md:tw-px-6 tw-rounded-full tw-font-semibold {{ request()->routeIs('register') ? 'tw-text-[#16a249] tw-border-2 tw-border-[#16a249]' : 'tw-text-black hover:tw-text-[#16a249]' }} tw-text-xs md:tw-text-sm tw-transition">
                {{ __('auth.register') }}
            </a>
        </div>
    </div>

    <!-- MAIN CONTAINER -->
    <div class="tw-bg-gradient-to-br tw-from-[#fffaf1] tw-to-[#f5fff8] lg:tw-bg-none tw-flex tw-flex-col lg:tw-flex-row tw-pt-[70px] tw-min-h-screen tw-bg-white">
        <!-- LEFT SECTION (Form) -->
        <div class="tw-order-2 lg:tw-order-1 tw-w-full lg:tw-w-1/2 tw-flex tw-flex-col tw-px-6 md:tw-px-14 tw-py-0 md:tw-py-2 lg:tw-py-10 tw-shadow-md lg:tw-shadow-md">
            <div class="tw-flex tw-justify-center tw-w-full tw-mt-4 md:tw-mt-6">
                <div class="tw-w-full tw-max-w-[600px] tw-space-y-6">
                    {{ $slot }}
                </div>
            </div>
        </div>

        <!-- RIGHT SECTION (Hero) -->
        <div class="tw-order-1 lg:tw-order-2 tw-w-full lg:tw-w-1/2 tw-bg-gradient-to-br tw-from-[#fffaf1] tw-to-[#f5fff8] tw-px-6 md:tw-px-14 tw-py-2 tw-flex tw-items-start tw-justify-center">
            <div class="tw-max-w-[700px] tw-w-full sm:tw-scale-[1] md:tw-scale-[1] lg:tw-scale-[0.73] xl:tw-scale-[0.77] tw-origin-top">
                <h2 class="tw-text-[30px] md:tw-text-[40px] lg:tw-text-[60px] tw-font-bold tw-leading-[36px] md:tw-leading-[48px] lg:tw-leading-[68px] tw-mt-4 md:tw-mt-4 lg:tw-mt-2">
                    {{ __('auth.hero_heading_1') }} <br />
                    <span class="tw-text-transparent tw-bg-clip-text tw-bg-gradient-to-b tw-from-green-600 tw-to-yellow-500">
                        {{ __('auth.hero_heading_2') }}
                    </span><br />
                    {{ __('auth.hero_heading_3') }}
                </h2>

                <p class="tw-hidden lg:tw-block tw-text-sm md:tw-text-lg tw-text-gray-500 tw-leading-6 md:tw-leading-7 tw-my-4 md:tw-mb-6">
                    {{ $heroDescription ?? __('auth.hero_description') }}
                </p>

                <div class="tw-hidden lg:tw-block tw-rounded-xl tw-overflow-hidden tw-w-full">
                    <img src="{{ asset('assets/img/9b17fdbe0cd6a3609fe3980d667fee9518d98207.png') }}" class="tw-w-full tw-object-contain" />
                </div>

                <div class="tw-hidden lg:tw-block tw-pt-4 md:tw-pt-4 tw-border-t tw-border-gray-300 tw-mt-6 md:tw-mt-6">
                    <h3 class="tw-text-lg md:tw-text-2xl tw-font-semibold">
                        {{ $heroTitle ?? __('auth.hero_title') }}
                    </h3>
                    <p class="tw-text-sm md:tw-text-base tw-text-gray-600 tw-leading-6 md:tw-leading-7 tw-mt-2">
                        {{ $heroText ?? __('auth.hero_text') }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Toastify JS (Local) -->
    <script src="{{ asset('assets/js/toastify.min.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if(session('success'))
                showToast(@json(session('success')), 'success');
            @endif
            @if(session('error'))
                showToast(@json(session('error')), 'error');
            @endif
            @if(session('warning'))
                showToast(@json(session('warning')), 'warning');
            @endif
            @if(session('info'))
                showToast(@json(session('info')), 'info');
            @endif
            @if($errors->any())
                @foreach($errors->all() as $error)
                    showToast(@json($error), 'error');
                @endforeach
            @endif
        });
    </script>
</body>
</html>
