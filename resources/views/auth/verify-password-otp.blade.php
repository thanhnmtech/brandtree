<x-auth-layout>
    <x-slot name="title">{{ __('auth.verify_password_otp_title') }}</x-slot>

    <form method="POST" action="{{ route('password.verify-otp.submit') }}" class="tw-space-y-6">
        @csrf

        <!-- Description -->
        <div class="tw-text-gray-600 tw-text-sm md:tw-text-base">
            {{ __('auth.verify_password_otp_description') }}
        </div>

        <!-- OTP -->
        <div class="tw-space-y-2">
            <label for="otp" class="tw-text-base md:tw-text-lg tw-font-medium">{{ __('auth.otp_label') }}</label>
            <input
                type="text"
                id="otp"
                name="otp"
                value="{{ old('otp') }}"
                maxlength="6"
                pattern="[0-9]{6}"
                class="tw-w-full tw-h-[44px] md:tw-h-[50px] tw-border tw-border-gray-300 tw-rounded-full tw-px-4 md:tw-px-5 tw-text-gray-600 tw-text-sm md:tw-text-lg tw-text-center tw-tracking-widest focus:tw-border-green-600 focus:tw-shadow-[0_0_0_3px_rgba(22,162,73,0.15)] tw-transition"
                placeholder="{{ __('auth.otp_placeholder') }}"
                required
                autofocus
            />
            @error('otp')
                <p class="tw-text-red-600 tw-text-sm tw-mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Submit -->
        <button
            type="submit"
            class="tw-w-full tw-h-[44px] md:tw-h-[50px] tw-bg-[linear-gradient(180deg,_#34b269_0%,_#78d29e_100%)] tw-text-white tw-rounded-lg tw-font-semibold tw-text-sm md:tw-text-base tw-transition tw-transform hover:tw-scale-[1.03] hover:tw-shadow-lg"
        >
            {{ __('auth.verify_otp_button') }}
        </button>

        <!-- Resend OTP -->
        <div class="tw-text-center">
            <form method="POST" action="{{ route('password.resend-otp') }}" class="tw-inline">
                @csrf
                <button
                    type="submit"
                    class="tw-text-[#16a249] tw-font-semibold tw-text-sm md:tw-text-base hover:tw-underline"
                >
                    {{ __('auth.resend_otp') }}
                </button>
            </form>
        </div>
    </form>
</x-auth-layout>
