<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'AI Cây Thương Hiệu' }}</title>

    <!-- Tailwind CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Tailwind Config -->
    <script>
        tailwind.config = {
            prefix: "tw-",
            theme: {
                extend: {
                    fontFamily: {
                        bevietnam: ["Be Vietnam Pro", "sans-serif"],
                    },
                },
            },
        };
    </script>

    <!-- Google Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700&display=swap" rel="stylesheet" />
</head>

<body class="tw-bg-[#f8faf9] tw-font-bevietnam tw-text-[#1a1a1a]">
    <!-- TOP TASKBAR -->
    <div class="tw-fixed tw-top-0 tw-left-0 tw-w-full tw-h-[70px] tw-bg-white tw-shadow-md tw-flex tw-items-center tw-justify-between tw-px-6 md:tw-px-14 tw-z-[999]">
        <div class="tw-flex tw-items-center tw-gap-3">
            <img src="{{ asset('html/assets/img/ccf8fd54d8a10b4ab49d514622f1efb57099e1a4.svg') }}" class="tw-w-[42px] tw-h-[42px] tw-object-contain" />
            <div class="tw-leading-tight">
                <h1 class="tw-text-base md:tw-text-lg tw-font-bold">AI Cây Thương Hiệu</h1>
                <p class="tw-text-[10px] md:tw-text-xs tw-text-gray-500 tw--mt-1">By VLBC</p>
            </div>
        </div>

        <div class="tw-hidden sm:tw-block tw-flex tw-items-center tw-gap-3 md:tw-gap-6">
            <a href="{{ route('login') }}" class="tw-py-1 tw-px-4 md:tw-px-6 tw-rounded-full tw-font-semibold {{ request()->routeIs('login') ? 'tw-text-[#16a249] tw-border-2 tw-border-[#16a249]' : 'tw-text-black hover:tw-text-[#16a249]' }} tw-text-xs md:tw-text-sm tw-transition">
                Đăng nhập
            </a>

            <a href="{{ route('register') }}" class="tw-py-1 tw-px-4 md:tw-px-6 tw-rounded-full tw-font-semibold {{ request()->routeIs('register') ? 'tw-text-[#16a249] tw-border-2 tw-border-[#16a249]' : 'tw-text-black hover:tw-text-[#16a249]' }} tw-text-xs md:tw-text-sm tw-transition">
                Đăng ký
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
                    Sẵn sàng nuôi dưỡng <br />
                    <span class="tw-text-transparent tw-bg-clip-text tw-bg-gradient-to-b tw-from-green-600 tw-to-yellow-500">
                        Cây Thương Hiệu
                    </span><br />
                    của bạn?
                </h2>

                <p class="tw-hidden lg:tw-block tw-text-sm md:tw-text-lg tw-text-gray-500 tw-leading-6 md:tw-leading-7 tw-my-4 md:tw-mb-6">
                    {{ $heroDescription ?? 'Bắt đầu từ hành trình xây dựng thương hiệu bền vững với sự hỗ trợ của AI.' }}
                </p>

                <div class="tw-hidden lg:tw-block tw-rounded-xl tw-overflow-hidden tw-w-full">
                    <img src="{{ asset('html/assets/img/9b17fdbe0cd6a3609fe3980d667fee9518d98207.png') }}" class="tw-w-full tw-object-contain" />
                </div>

                <div class="tw-hidden lg:tw-block tw-pt-4 md:tw-pt-4 tw-border-t tw-border-gray-300 tw-mt-6 md:tw-mt-6">
                    <h3 class="tw-text-lg md:tw-text-2xl tw-font-semibold">
                        {{ $heroTitle ?? 'Nền Tảng Chiến Lược' }}
                    </h3>
                    <p class="tw-text-sm md:tw-text-base tw-text-gray-600 tw-leading-6 md:tw-leading-7 tw-mt-2">
                        {{ $heroText ?? 'Mỗi thương hiệu vĩ đại bắt đầu từ một hạt mầm vững chắc. Cùng xây dựng nền tảng chiến lược rõ ràng cho sự phát triển bền vững.' }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

