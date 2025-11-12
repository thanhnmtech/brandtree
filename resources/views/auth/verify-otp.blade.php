<x-guest-layout>
    <form method="POST" action="{{ route('otp.verify.submit') }}">
        @csrf

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('success')" />

        <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
            {{ __('Please enter the 6-digit OTP code sent to your email address.') }}
        </div>

        <!-- OTP -->
        <div>
            <x-input-label for="otp" :value="__('OTP Code')" />
            <x-text-input id="otp" class="block mt-1 w-full" type="text" name="otp" :value="old('otp')" required autofocus maxlength="6" pattern="[0-9]{6}" />
            <x-input-error :messages="$errors->get('otp')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between mt-4">
            <form method="POST" action="{{ route('otp.resend') }}">
                @csrf
                <x-secondary-button type="submit">
                    {{ __('Resend OTP') }}
                </x-secondary-button>
            </form>

            <x-primary-button>
                {{ __('Verify') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>

