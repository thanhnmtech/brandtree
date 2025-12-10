<x-auth-layout>
    <x-slot name="title">Đăng nhập - AI Cây Thương Hiệu</x-slot>

    <form method="POST" action="{{ route('login') }}" class="tw-space-y-6">
        @csrf
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
        </div>

        <!-- Password -->
        <div class="tw-space-y-2">
            <label for="password" class="tw-text-base md:tw-text-lg tw-font-medium">Mật khẩu</label>
            <input
                type="password"
                id="password"
                name="password"
                class="tw-w-full tw-h-[44px] md:tw-h-[50px] tw-border tw-border-gray-300 tw-rounded-full tw-px-4 md:tw-px-5 tw-text-gray-600 tw-text-sm md:tw-text-lg focus:tw-border-green-600 focus:tw-shadow-[0_0_0_3px_rgba(22,162,73,0.15)] tw-transition"
                placeholder="Nhập mật khẩu"
                required
            />

        </div>

        <!-- Options -->
        <div class="tw-flex tw-flex-row tw-flex-wrap tw-gap-3 md:tw-gap-0 tw-justify-between tw-items-start md:tw-items-center">
            <label class="tw-flex tw-items-center tw-gap-2 tw-text-sm md:tw-text-base tw-font-medium">
                <input
                    type="checkbox"
                    name="remember"
                    class="tw-w-4 tw-h-4 md:tw-w-5 md:tw-h-5 tw-accent-green-600"
                />
                <span>Ghi nhớ đăng nhập</span>
            </label>

        </div>

        <!-- Submit -->
        <button
            type="submit"
            class="tw-w-full tw-h-[44px] md:tw-h-[50px] tw-bg-[linear-gradient(180deg,_#34b269_0%,_#78d29e_100%)] tw-text-white tw-rounded-lg tw-font-semibold tw-text-sm md:tw-text-base tw-transition tw-transform hover:tw-scale-[1.03] hover:tw-shadow-lg"
        >
            Đăng nhập
        </button>

        <!-- Divider -->
        <div class="tw-flex tw-items-center tw-gap-3 tw-my-4 md:tw-my-6">
            <div class="tw-flex-1 tw-h-px tw-bg-gray-300"></div>
            <span class="tw-text-gray-500 tw-text-[11px] md:tw-text-sm tw-whitespace-nowrap">Hoặc đăng nhập với Google</span>
            <div class="tw-flex-1 tw-h-px tw-bg-gray-300"></div>
        </div>

        <!-- Google -->
        <a
            href="{{ route('auth.google') }}"
            class="tw-w-full tw-h-[44px] md:tw-h-[54px] tw-bg-[#f3f7f5] tw-rounded-lg tw-flex tw-items-center tw-justify-center tw-gap-3 tw-font-semibold tw-text-sm md:tw-text-base tw-transition hover:tw-scale-[1.03] hover:tw-shadow-md hover:tw-bg-[#e8f4f0]"
        >
            <svg class="tw-w-5 tw-h-5" viewBox="0 0 24 24" fill="none">
                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"></path>
                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"></path>
                <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"></path>
                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"></path>
            </svg>
            <span>Tiếp tục với Google</span>
        </a>

        <p class="tw-text-center tw-font-medium tw-text-sm md:tw-text-base tw-mt-4">
            Bạn chưa có tài khoản?
            <a href="{{ route('register') }}" class="tw-text-[#16a249] tw-font-semibold hover:tw-underline">Đăng ký</a>
        </p>
    </form>
</x-auth-layout>
