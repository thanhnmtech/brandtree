<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>
     <link
      href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700&display=swap"
      rel="stylesheet"
    />
     <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="{{ asset('assets/js/tailwind-config.js') }}"></script>
    <!-- Vite Assets (Tailwind + App) -->
    {{-- @vite(['resources/css/app.css', 'resources/js/app.js']) --}}
    @vite(['resources/js/app.js'])

    <link rel="stylesheet" href="{{ asset('assets/css/toastify.min.css') }}">
</head>

<body class="tw-font-bevietnam tw-bg-[#FFFFFF] tw-text-[#1a1a1a] tw-min-h-screen tw-overflow-x-hidden">
    @include('layouts.navigation')
     {{ $slot }}
    @include('layouts.footer')
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
        // Toast notification helper
        function showToast(message, type = 'success') {
            const config = {
                success: {
                    background: 'linear-gradient(to right, #00b09b, #96c93d)',
                    icon: '✓ '
                },
                error: {
                    background: 'linear-gradient(to right, #ff5f6d, #ffc371)',
                    icon: '✕ '
                },
                warning: {
                    background: 'linear-gradient(to right, #f093fb, #f5576c)',
                    icon: '⚠ '
                },
                info: {
                    background: 'linear-gradient(to right, #4facfe, #00f2fe)',
                    icon: 'ℹ '
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

        // Alias for showToast to support different naming conventions
        window.showNotification = showToast;
        window.showToast = showToast;

        // Show flash messages on page load
        document.addEventListener('DOMContentLoaded', function() {
            @if (session('success'))
                showToast(@json(session('success')), 'success');
            @endif

            @if (session('error'))
                showToast(@json(session('error')), 'error');
            @endif

            @if (session('warning'))
                showToast(@json(session('warning')), 'warning');
            @endif

            @if (session('info'))
                showToast(@json(session('info')), 'info');
            @endif

            @if ($errors->any())
                @foreach ($errors->all() as $error)
                    showToast(@json($error), 'error');
                @endforeach
            @endif
        });

        function toggleAccountPopup() {
            const popup = document.getElementById("accountPopup");
            if (!popup) return;

            const isHidden = popup.classList.contains("tw-hidden");

            if (isHidden) {
                popup.classList.remove("tw-hidden");
            } else {
                popup.classList.add("tw-hidden");
            }
        }
    </script>

    @stack('scripts')
</body>

</html>
