<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Toastify CSS (Local) -->
    <link rel="stylesheet" href="{{ asset('css/toastify.min.css') }}">

    <!-- Vite Assets (Tailwind + App) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="tw-bg-gradient-to-br tw-from-[#fffaf1] tw-to-[#f5fff8] tw-min-h-screen">
    @include('layouts.navigation')

    <!-- Page Content -->
    <main class="tw-py-8 md:tw-py-12">
        <div class="tw-max-w-7xl tw-mx-auto tw-px-4 sm:tw-px-6 lg:tw-px-8">
            <!-- Page Heading -->
            @isset($header)
                <div class="tw-mb-6 md:tw-mb-8">
                    {{ $header }}
                </div>
            @endisset

            {{ $slot }}
        </div>
    </main>

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
    <script src="{{ asset('js/toastify.min.js') }}"></script>
    <script>
        // Toast notification helper
        function showToast(message, type = 'success') {
            const config = {
                success: {
                    background: 'linear-gradient(to right, #00b09b, #96c93d)',
                    icon: '✓'
                },
                error: {
                    background: 'linear-gradient(to right, #ff5f6d, #ffc371)',
                    icon: '✕'
                },
                warning: {
                    background: 'linear-gradient(to right, #f093fb, #f5576c)',
                    icon: '⚠'
                },
                info: {
                    background: 'linear-gradient(to right, #4facfe, #00f2fe)',
                    icon: 'ℹ'
                }
            };

            const settings = config[type] || config.info;

            Toastify({
                text: settings.icon + '  ' + message,
                duration: 4000,
                gravity: 'top',
                position: 'right',
                stopOnFocus: true,
                style: {
                    background: settings.background,
                },
                onClick: function() {}
            }).showToast();
        }

        // Show flash messages on page load
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

    @stack('scripts')
</body>
</html>
