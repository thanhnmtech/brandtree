<section>
    <header>
        <h2 class="tw-text-xl tw-font-semibold tw-text-gray-800">
            Cập nhật mật khẩu
        </h2>

        <p class="tw-mt-2 tw-text-sm tw-text-gray-600">
            Đảm bảo tài khoản của bạn sử dụng mật khẩu dài và ngẫu nhiên để giữ an toàn.
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="tw-mt-6 tw-space-y-6">
        @csrf
        @method('put')

        <div>
            <label for="update_password_current_password" class="tw-block tw-text-sm tw-font-medium tw-text-gray-700 tw-mb-2">
                Mật khẩu hiện tại
            </label>
            <input
                id="update_password_current_password"
                name="current_password"
                type="password"
                autocomplete="current-password"
                class="tw-w-full tw-h-[44px] md:tw-h-[50px] tw-border tw-border-gray-300 tw-rounded-full tw-px-4 md:tw-px-5 tw-text-gray-600 tw-text-sm md:tw-text-base focus:tw-border-green-600 focus:tw-shadow-[0_0_0_3px_rgba(22,162,73,0.15)] focus:tw-outline-none tw-transition"
            />
            @error('current_password', 'updatePassword')
                <p class="tw-text-red-600 tw-text-sm tw-mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="update_password_password" class="tw-block tw-text-sm tw-font-medium tw-text-gray-700 tw-mb-2">
                Mật khẩu mới
            </label>
            <input
                id="update_password_password"
                name="password"
                type="password"
                autocomplete="new-password"
                class="tw-w-full tw-h-[44px] md:tw-h-[50px] tw-border tw-border-gray-300 tw-rounded-full tw-px-4 md:tw-px-5 tw-text-gray-600 tw-text-sm md:tw-text-base focus:tw-border-green-600 focus:tw-shadow-[0_0_0_3px_rgba(22,162,73,0.15)] focus:tw-outline-none tw-transition"
            />
            @error('password', 'updatePassword')
                <p class="tw-text-red-600 tw-text-sm tw-mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="update_password_password_confirmation" class="tw-block tw-text-sm tw-font-medium tw-text-gray-700 tw-mb-2">
                Xác nhận mật khẩu mới
            </label>
            <input
                id="update_password_password_confirmation"
                name="password_confirmation"
                type="password"
                autocomplete="new-password"
                class="tw-w-full tw-h-[44px] md:tw-h-[50px] tw-border tw-border-gray-300 tw-rounded-full tw-px-4 md:tw-px-5 tw-text-gray-600 tw-text-sm md:tw-text-base focus:tw-border-green-600 focus:tw-shadow-[0_0_0_3px_rgba(22,162,73,0.15)] focus:tw-outline-none tw-transition"
            />
            @error('password_confirmation', 'updatePassword')
                <p class="tw-text-red-600 tw-text-sm tw-mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="tw-flex tw-items-center tw-gap-4">
            <button
                type="submit"
                class="tw-h-[44px] md:tw-h-[50px] tw-px-6 md:tw-px-8 tw-bg-[linear-gradient(180deg,_#34b269_0%,_#78d29e_100%)] tw-text-white tw-rounded-lg tw-font-semibold tw-text-sm md:tw-text-base tw-transition tw-transform hover:tw-scale-[1.03] hover:tw-shadow-lg">
                Lưu thay đổi
            </button>

            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="tw-text-sm tw-text-green-600 tw-font-medium"
                >Đã lưu!</p>
            @endif
        </div>
    </form>
</section>
