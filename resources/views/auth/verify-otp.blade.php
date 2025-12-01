<x-auth-layout>
    <x-slot name="title">Xác thực Email - AI Cây Thương Hiệu</x-slot>

    <form method="POST" action="{{ route('otp.verify.submit') }}" class="tw-space-y-6">
        @csrf

        <!-- Description -->
        <div class="tw-text-gray-600 tw-text-sm md:tw-text-base">
            Vui lòng nhập mã OTP gồm 6 chữ số đã được gửi đến địa chỉ email của bạn để xác thực tài khoản.
        </div>

        <!-- OTP -->
        <div class="tw-space-y-2">
            <label for="otp" class="tw-text-base md:tw-text-lg tw-font-medium">Mã OTP</label>
            <input
                type="text"
                id="otp"
                name="otp"
                value="{{ old('otp') }}"
                maxlength="6"
                pattern="[0-9]{6}"
                class="tw-w-full tw-h-[44px] md:tw-h-[50px] tw-border tw-border-gray-300 tw-rounded-full tw-px-4 md:tw-px-5 tw-text-gray-600 tw-text-sm md:tw-text-lg tw-text-center tw-tracking-widest focus:tw-border-green-600 focus:tw-shadow-[0_0_0_3px_rgba(22,162,73,0.15)] tw-transition"
                placeholder="000000"
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
            Xác thực
        </button>

        <!-- Resend OTP -->
        <div class="tw-text-center">
            <form method="POST" action="{{ route('otp.resend') }}" class="tw-inline">
                @csrf
                <button
                    type="submit"
                    class="tw-text-[#16a249] tw-font-semibold tw-text-sm md:tw-text-base hover:tw-underline"
                >
                    Gửi lại mã OTP
                </button>
            </form>
        </div>
    </form>
</x-auth-layout>
