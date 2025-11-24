<x-auth-layout>
    <x-slot name="title">Xác nhận mật khẩu - AI Cây Thương Hiệu</x-slot>

    <form method="POST" action="{{ route('password.confirm') }}" class="tw-space-y-6">
        @csrf

        <!-- Description -->
        <div class="tw-text-gray-600 tw-text-sm md:tw-text-base">
            Đây là khu vực bảo mật của ứng dụng. Vui lòng xác nhận mật khẩu của bạn trước khi tiếp tục.
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
                autocomplete="current-password"
            />
            @error('password')
                <p class="tw-text-red-600 tw-text-sm tw-mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Submit -->
        <button
            type="submit"
            class="tw-w-full tw-h-[44px] md:tw-h-[50px] tw-bg-[linear-gradient(180deg,_#34b269_0%,_#78d29e_100%)] tw-text-white tw-rounded-lg tw-font-semibold tw-text-sm md:tw-text-base tw-transition tw-transform hover:tw-scale-[1.03] hover:tw-shadow-lg"
        >
            Xác nhận
        </button>
    </form>
</x-auth-layout>
