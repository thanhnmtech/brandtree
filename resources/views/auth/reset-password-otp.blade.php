<x-auth-layout>
    <x-slot name="title">Đặt lại mật khẩu - AI Cây Thương Hiệu</x-slot>

    <form method="POST" action="{{ route('password.update') }}" class="tw-space-y-6">
        @csrf

        <!-- Session Status -->
        @if (session('success'))
            <div class="tw-bg-green-50 tw-border tw-border-green-200 tw-text-green-800 tw-px-4 tw-py-3 tw-rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        <!-- Description -->
        <div class="tw-text-gray-600 tw-text-sm md:tw-text-base">
            Vui lòng nhập mật khẩu mới của bạn.
        </div>

        <!-- Password -->
        <div class="tw-space-y-2">
            <label for="password" class="tw-text-base md:tw-text-lg tw-font-medium">Mật khẩu mới</label>
            <input
                type="password"
                id="password"
                name="password"
                class="tw-w-full tw-h-[44px] md:tw-h-[50px] tw-border tw-border-gray-300 tw-rounded-full tw-px-4 md:tw-px-5 tw-text-gray-600 tw-text-sm md:tw-text-lg focus:tw-border-green-600 focus:tw-shadow-[0_0_0_3px_rgba(22,162,73,0.15)] tw-transition"
                placeholder="Nhập mật khẩu mới"
                required
                autocomplete="new-password"
            />
            @error('password')
                <p class="tw-text-red-600 tw-text-sm tw-mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Confirm Password -->
        <div class="tw-space-y-2">
            <label for="password_confirmation" class="tw-text-base md:tw-text-lg tw-font-medium">Xác nhận mật khẩu</label>
            <input
                type="password"
                id="password_confirmation"
                name="password_confirmation"
                class="tw-w-full tw-h-[44px] md:tw-h-[50px] tw-border tw-border-gray-300 tw-rounded-full tw-px-4 md:tw-px-5 tw-text-gray-600 tw-text-sm md:tw-text-lg focus:tw-border-green-600 focus:tw-shadow-[0_0_0_3px_rgba(22,162,73,0.15)] tw-transition"
                placeholder="Nhập lại mật khẩu mới"
                required
                autocomplete="new-password"
            />
            @error('password_confirmation')
                <p class="tw-text-red-600 tw-text-sm tw-mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Submit -->
        <button
            type="submit"
            class="tw-w-full tw-h-[44px] md:tw-h-[50px] tw-bg-[linear-gradient(180deg,_#34b269_0%,_#78d29e_100%)] tw-text-white tw-rounded-lg tw-font-semibold tw-text-sm md:tw-text-base tw-transition tw-transform hover:tw-scale-[1.03] hover:tw-shadow-lg"
        >
            Đặt lại mật khẩu
        </button>
    </form>
</x-auth-layout>
