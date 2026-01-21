<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700&display=swap"
        rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="{{ asset('assets/js/tailwind-config.js') }}"></script>
    <!-- Vite Assets (Tailwind + App) -->
    {{-- @vite(['resources/css/app.css', 'resources/js/app.js']) --}}
    @vite(['resources/js/app.js'])

    <link rel="stylesheet" href="{{ asset('assets/css/toastify.min.css') }}">
    @livewireStyles
</head>

<body class="tw-font-bevietnam tw-bg-[#FFFFFF] tw-text-[#1a1a1a] tw-min-h-screen tw-overflow-x-hidden">
    @include('layouts.navigation')
    {{ $slot }}
    @include('layouts.footer')
    <!-- Toastify JS (Local) -->
    <script src="{{ asset('assets/js/toastify.min.js') }}"></script>
    <script>
        // Show flash messages on page load
        document.addEventListener('DOMContentLoaded', function () {
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
    @livewireScripts
</body>

</html>