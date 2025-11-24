<x-auth-layout>
    <x-slot name="title">Xác thực Email - AI Cây Thương Hiệu</x-slot>

    <div class="tw-space-y-6">
        <!-- Description -->
        <div class="tw-text-gray-600 tw-text-sm md:tw-text-base">
            Cảm ơn bạn đã đăng ký! Trước khi bắt đầu, vui lòng xác thực địa chỉ email của bạn bằng cách nhấp vào liên kết chúng tôi vừa gửi đến email của bạn. Nếu bạn không nhận được email, chúng tôi sẽ gửi lại cho bạn.
        </div>

        <!-- Success Message -->
        @if (session('status') == 'verification-link-sent')
            <div class="tw-bg-green-50 tw-border tw-border-green-200 tw-text-green-800 tw-px-4 tw-py-3 tw-rounded-lg">
                Một liên kết xác thực mới đã được gửi đến địa chỉ email bạn đã cung cấp khi đăng ký.
            </div>
        @endif

        <!-- Resend Button -->
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button
                type="submit"
                class="tw-w-full tw-h-[44px] md:tw-h-[50px] tw-bg-[linear-gradient(180deg,_#34b269_0%,_#78d29e_100%)] tw-text-white tw-rounded-lg tw-font-semibold tw-text-sm md:tw-text-base tw-transition tw-transform hover:tw-scale-[1.03] hover:tw-shadow-lg"
            >
                Gửi lại Email xác thực
            </button>
        </form>

        <!-- Logout -->
        <div class="tw-text-center">
            <form method="POST" action="{{ route('logout') }}" class="tw-inline">
                @csrf
                <button
                    type="submit"
                    class="tw-text-gray-600 tw-font-medium tw-text-sm md:tw-text-base hover:tw-text-gray-900 hover:tw-underline"
                >
                    Đăng xuất
                </button>
            </form>
        </div>
    </div>
</x-auth-layout>
