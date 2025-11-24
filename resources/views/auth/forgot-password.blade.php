<x-auth-layout>
    <x-slot name="title">Quên mật khẩu - AI Cây Thương Hiệu</x-slot>

    <form method="POST" action="{{ route('password.email') }}" class="tw-space-y-6">
        @csrf

        <!-- Session Status -->
        @if (session('status'))
            <div class="tw-bg-green-50 tw-border tw-border-green-200 tw-text-green-800 tw-px-4 tw-py-3 tw-rounded-lg">
                {{ session('status') }}
            </div>
        @endif

        <!-- Description -->
        <div class="tw-text-gray-600 tw-text-sm md:tw-text-base">
            Quên mật khẩu? Không sao. Chỉ cần cho chúng tôi biết địa chỉ email của bạn và chúng tôi sẽ gửi cho bạn liên kết đặt lại mật khẩu.
        </div>

        <!-- Email -->
        <div class="tw-space-y-2">
            <label for="email" class="tw-text-base md:tw-text-lg tw-font-medium">Địa chỉ email</label>
            <input
                type="email"
                id="email"
                name="email"
                value="{{ old('email') }}"
                class="tw-w-full tw-h-[44px] md:tw-h-[50px] tw-border tw-border-gray-300 tw-rounded-full tw-px-4 md:tw-px-5 tw-text-gray-600 tw-text-sm md:tw-text-lg focus:tw-border-green-600 focus:tw-shadow-[0_0_0_3px_rgba(22,162,73,0.15)] tw-transition"
                placeholder="Email@gmail.com"
                required
                autofocus
            />
            @error('email')
                <p class="tw-text-red-600 tw-text-sm tw-mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Submit -->
        <button
            type="submit"
            class="tw-w-full tw-h-[44px] md:tw-h-[50px] tw-bg-[linear-gradient(180deg,_#34b269_0%,_#78d29e_100%)] tw-text-white tw-rounded-lg tw-font-semibold tw-text-sm md:tw-text-base tw-transition tw-transform hover:tw-scale-[1.03] hover:tw-shadow-lg"
        >
            Gửi liên kết đặt lại mật khẩu
        </button>

        <!-- Back to Login -->
        <p class="tw-text-center tw-font-medium tw-text-sm md:tw-text-base tw-mt-4">
            <a href="{{ route('login') }}" class="tw-text-[#16a249] tw-font-semibold hover:tw-underline">← Quay lại đăng nhập</a>
        </p>
    </form>
</x-auth-layout>
