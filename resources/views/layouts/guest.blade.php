<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Toastify CSS (Local) -->
        <link rel="stylesheet" href="{{ asset('assets/css/toastify.min.css') }}">

        <!-- Vite Assets (Tailwind + App) -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="tw-font-sans tw-text-gray-900 tw-antialiased">
        <div class="tw-min-h-screen tw-flex tw-flex-col sm:tw-justify-center tw-items-center tw-pt-6 sm:tw-pt-0 tw-bg-gray-100 dark:tw-bg-gray-900">
            <div>
                <a href="/">
                    <x-application-logo class="tw-w-20 tw-h-20 tw-fill-current tw-text-gray-500" />
                </a>
            </div>

            <div class="tw-w-full sm:tw-max-w-md tw-mt-6 tw-px-6 tw-py-4 tw-bg-white dark:tw-bg-gray-800 tw-shadow-md tw-overflow-hidden sm:tw-rounded-lg">
                {{ $slot }}
            </div>
        </div>

        <!-- Toastify JS (Local) -->
        <script src="{{ asset('assets/js/toastify.min.js') }}"></script>
        <script>
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
                    }
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
