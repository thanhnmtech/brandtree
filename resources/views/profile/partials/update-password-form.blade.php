<section>
    <header class="tw-mb-6">
        <h2 class="tw-text-xl tw-font-bold tw-text-gray-900 tw-flex tw-items-center tw-gap-2">
            <i class="ri-lock-password-line tw-text-2xl tw-text-[#1AA24C]"></i>
            {{ Auth::user()->google_id ? 'Đặt mật khẩu' : 'Cập nhật mật khẩu' }}
        </h2>
        <p class="tw-mt-2 tw-text-sm tw-text-gray-600">
            @if(Auth::user()->google_id)
                Bạn đã đăng ký qua Google. Đặt mật khẩu để có thể đăng nhập bằng email và mật khẩu.
            @else
                Đảm bảo tài khoản của bạn sử dụng mật khẩu dài và ngẫu nhiên để giữ an toàn.
            @endif
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="tw-space-y-5">
        @csrf
        @method('put')

        @if(!Auth::user()->google_id)
        <!-- Mật khẩu hiện tại -->
        <div>
            <label for="update_password_current_password" class="tw-block tw-text-sm tw-font-medium tw-text-gray-700 tw-mb-2">
                Mật khẩu hiện tại <span class="tw-text-red-500">*</span>
            </label>
            <input
                id="update_password_current_password"
                name="current_password"
                type="password"
                autocomplete="current-password"
                class="tw-w-full tw-p-3 tw-border tw-rounded-lg focus:tw-border-[#1AA24C] focus:tw-ring-1 focus:tw-ring-[#1AA24C] tw-outline-none tw-transition {{ $errors->updatePassword->has('current_password') ? 'tw-border-red-500' : 'tw-border-gray-300' }}"
                placeholder="Nhập mật khẩu hiện tại"
            />
            @error('current_password', 'updatePassword')
                <p class="tw-text-red-600 tw-text-sm tw-mt-1">{{ $message }}</p>
            @enderror
        </div>
        @endif

        <!-- Mật khẩu mới -->
        <div>
            <label for="update_password_password" class="tw-block tw-text-sm tw-font-medium tw-text-gray-700 tw-mb-2">
                {{ Auth::user()->google_id ? 'Mật khẩu' : 'Mật khẩu mới' }} <span class="tw-text-red-500">*</span>
            </label>
            <input
                id="update_password_password"
                name="password"
                type="password"
                autocomplete="new-password"
                class="tw-w-full tw-p-3 tw-border tw-rounded-lg focus:tw-border-[#1AA24C] focus:tw-ring-1 focus:tw-ring-[#1AA24C] tw-outline-none tw-transition {{ $errors->updatePassword->has('password') ? 'tw-border-red-500' : 'tw-border-gray-300' }}"
                placeholder="Nhập mật khẩu mới (tối thiểu 8 ký tự)"
            />
            @error('password', 'updatePassword')
                <p class="tw-text-red-600 tw-text-sm tw-mt-1">{{ $message }}</p>
            @enderror
            <p class="tw-text-xs tw-text-gray-500 tw-mt-1">
                <i class="ri-information-line"></i>
                Mật khẩu phải có ít nhất 8 ký tự
            </p>
        </div>

        <!-- Xác nhận mật khẩu -->
        <div>
            <label for="update_password_password_confirmation" class="tw-block tw-text-sm tw-font-medium tw-text-gray-700 tw-mb-2">
                {{ Auth::user()->google_id ? 'Xác nhận mật khẩu' : 'Xác nhận mật khẩu mới' }} <span class="tw-text-red-500">*</span>
            </label>
            <input
                id="update_password_password_confirmation"
                name="password_confirmation"
                type="password"
                autocomplete="new-password"
                class="tw-w-full tw-p-3 tw-border tw-rounded-lg focus:tw-border-[#1AA24C] focus:tw-ring-1 focus:tw-ring-[#1AA24C] tw-outline-none tw-transition {{ $errors->updatePassword->has('password_confirmation') ? 'tw-border-red-500' : 'tw-border-gray-300' }}"
                placeholder="Nhập lại mật khẩu mới"
            />
            @error('password_confirmation', 'updatePassword')
                <p class="tw-text-red-600 tw-text-sm tw-mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Submit Button -->
        <div class="tw-flex tw-items-center tw-gap-4 tw-pt-2">
            <button
                type="submit"
                class="tw-bg-[#1AA24C] tw-text-white tw-font-semibold tw-py-3 tw-px-6 tw-rounded-lg hover:tw-bg-[#148a3f] tw-transition tw-flex tw-items-center tw-gap-2">
                <i class="ri-save-line"></i>
                <span>{{ Auth::user()->google_id ? 'Đặt mật khẩu' : 'Lưu thay đổi' }}</span>
            </button>

            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 3000)"
                    class="tw-text-sm tw-text-green-600 tw-font-medium tw-flex tw-items-center tw-gap-1">
                    <i class="ri-checkbox-circle-fill"></i>
                    Đã lưu thành công!
                </p>
            @endif
        </div>
    </form>
</section>
