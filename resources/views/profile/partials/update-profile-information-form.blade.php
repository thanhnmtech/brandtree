<section>
    <header class="tw-mb-6">
        <h2 class="tw-text-xl tw-font-bold tw-text-gray-900 tw-flex tw-items-center tw-gap-2">
            <i class="ri-user-line tw-text-2xl tw-text-[#1AA24C]"></i>
            Thông tin cá nhân
        </h2>
        <p class="tw-mt-2 tw-text-sm tw-text-gray-600">
            Cập nhật thông tin hồ sơ và địa chỉ email của bạn.
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="tw-space-y-5">
        @csrf
        @method('patch')

        <!-- Họ tên -->
        <div>
            <label for="name" class="tw-block tw-text-sm tw-font-medium tw-text-gray-700 tw-mb-2">
                Họ tên <span class="tw-text-red-500">*</span>
            </label>
            <input
                id="name"
                name="name"
                type="text"
                value="{{ old('name', $user->name) }}"
                required
                autofocus
                autocomplete="name"
                class="tw-w-full tw-p-3 tw-border tw-rounded-lg focus:tw-border-[#1AA24C] focus:tw-ring-1 focus:tw-ring-[#1AA24C] tw-outline-none tw-transition {{ $errors->has('name') ? 'tw-border-red-500' : 'tw-border-gray-300' }}"
                placeholder="Nhập họ tên của bạn"
            />
            @error('name')
                <p class="tw-text-red-600 tw-text-sm tw-mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Email -->
        <div>
            <label for="phone" class="tw-block tw-text-sm tw-font-medium tw-text-gray-700 tw-mb-2">
                Số điện thoại <span class="tw-text-red-500">*</span>
            </label>
            <input
                id="phone"
                name="phone"
                type="text"
                value="{{ old('phone', $user->phone) }}"
                required
                autocomplete="tel"
                class="tw-w-full tw-p-3 tw-border tw-rounded-lg focus:tw-border-[#1AA24C] focus:tw-ring-1 focus:tw-ring-[#1AA24C] tw-outline-none tw-transition {{ $errors->has('phone') ? 'tw-border-red-500' : 'tw-border-gray-300' }}"
                placeholder="Nhập số điện thoại của bạn"
            />
            @error('phone')
                <p class="tw-text-red-600 tw-text-sm tw-mt-1">{{ $message }}</p>
            @enderror

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="tw-mt-3 tw-p-3 tw-bg-yellow-50 tw-border tw-border-yellow-200 tw-rounded-lg">
                    <div class="tw-flex tw-items-start tw-gap-2">
                        <i class="ri-alert-line tw-text-yellow-600 tw-text-lg tw-mt-0.5"></i>
                        <div class="tw-flex-1">
                            <p class="tw-text-sm tw-text-gray-800">
                                Địa chỉ email của bạn chưa được xác thực.
                                <button
                                    form="send-verification"
                                    type="submit"
                                    class="tw-underline tw-text-sm tw-text-[#1AA24C] hover:tw-text-[#148a3f] tw-font-medium">
                                    Gửi lại email xác thực
                                </button>
                            </p>

                            @if (session('status') === 'verification-link-sent')
                                <p class="tw-mt-2 tw-text-sm tw-text-green-600 tw-font-medium">
                                    ✓ Đã gửi email xác thực mới đến hộp thư của bạn.
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Submit Button -->
        <div class="tw-flex tw-items-center tw-gap-4 tw-pt-2">
            <button
                type="submit"
                class="tw-bg-[#1AA24C] tw-text-white tw-font-semibold tw-py-3 tw-px-6 tw-rounded-lg hover:tw-bg-[#148a3f] tw-transition tw-flex tw-items-center tw-gap-2">
                <i class="ri-save-line"></i>
                <span>Lưu thay đổi</span>
            </button>

            @if (session('status') === 'profile-updated')
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
