<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'AI Cây Thương Hiệu' }}</title>

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
                    <img src="{{ asset('assets/img/9b17fdbe0cd6a3609fe3980d667fee9518d98207.png') }}" class="tw-w-full tw-object-contain" />
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

    <!-- Form Submit Loading Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Get all forms in the page
            const forms = document.querySelectorAll('form');

            forms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    // Find submit button in this form
                    const submitBtn = form.querySelector('button[type="submit"]');

                    if (submitBtn && !submitBtn.disabled) {
                        // Store original text
                        const originalText = submitBtn.innerHTML;

                        // Disable button
                        submitBtn.disabled = true;

                        // Add loading state
                        submitBtn.innerHTML = `
                            <span class="tw-flex tw-items-center tw-justify-center tw-gap-2">
                                <svg class="tw-animate-spin tw-h-5 tw-w-5 tw-text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="tw-opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="tw-opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span>Đang xử lý...</span>
                            </span>
                        `;

                        // Change button style to indicate loading
                        submitBtn.style.opacity = '0.7';
                        submitBtn.style.cursor = 'not-allowed';

                        // If form validation fails, re-enable button
                        setTimeout(() => {
                            if (!form.checkValidity()) {
                                submitBtn.disabled = false;
                                submitBtn.innerHTML = originalText;
                                submitBtn.style.opacity = '1';
                                submitBtn.style.cursor = 'pointer';
                            }
                        }, 100);
                    }
                });
            });
        });
    </script>

    <!-- Toastify JS (Local) -->
    <script src="{{ asset('assets/js/toastify.min.js') }}"></script>
    <script>
        function showToast(message, type = 'success') {
            const config = {
                success: { background: 'linear-gradient(to right, #00b09b, #96c93d)', icon: '✓' },
                error: { background: 'linear-gradient(to right, #ff5f6d, #ffc371)', icon: '✕' },
                warning: { background: 'linear-gradient(to right, #f093fb, #f5576c)', icon: '⚠' },
                info: { background: 'linear-gradient(to right, #4facfe, #00f2fe)', icon: 'ℹ' }
            };
            const settings = config[type] || config.info;
            Toastify({
                text: settings.icon + '  ' + message,
                duration: 4000,
                gravity: 'top',
                position: 'right',
                stopOnFocus: true,
                style: { background: settings.background }
            }).showToast();
        }

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
