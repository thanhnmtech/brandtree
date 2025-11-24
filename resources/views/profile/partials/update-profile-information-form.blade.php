<section>
    <header>
        <h2 class="tw-text-xl tw-font-semibold tw-text-gray-800">
            Thông tin cá nhân
        </h2>

        <p class="tw-mt-2 tw-text-sm tw-text-gray-600">
            Cập nhật thông tin hồ sơ và địa chỉ email của bạn.
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="tw-mt-6 tw-space-y-6">
        @csrf
        @method('patch')

        <div>
            <label for="name" class="tw-block tw-text-sm tw-font-medium tw-text-gray-700 tw-mb-2">
                Họ tên
            </label>
            <input
                id="name"
                name="name"
                type="text"
                value="{{ old('name', $user->name) }}"
                required
                autofocus
                autocomplete="name"
                class="tw-w-full tw-h-[44px] md:tw-h-[50px] tw-border tw-border-gray-300 tw-rounded-full tw-px-4 md:tw-px-5 tw-text-gray-600 tw-text-sm md:tw-text-base focus:tw-border-green-600 focus:tw-shadow-[0_0_0_3px_rgba(22,162,73,0.15)] focus:tw-outline-none tw-transition"
            />
            @error('name')
                <p class="tw-text-red-600 tw-text-sm tw-mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="email" class="tw-block tw-text-sm tw-font-medium tw-text-gray-700 tw-mb-2">
                Email
            </label>
            <input
                id="email"
                name="email"
                type="email"
                value="{{ old('email', $user->email) }}"
                required
                autocomplete="username"
                class="tw-w-full tw-h-[44px] md:tw-h-[50px] tw-border tw-border-gray-300 tw-rounded-full tw-px-4 md:tw-px-5 tw-text-gray-600 tw-text-sm md:tw-text-base focus:tw-border-green-600 focus:tw-shadow-[0_0_0_3px_rgba(22,162,73,0.15)] focus:tw-outline-none tw-transition"
            />
            @error('email')
                <p class="tw-text-red-600 tw-text-sm tw-mt-1">{{ $message }}</p>
            @enderror

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="tw-mt-3">
                    <p class="tw-text-sm tw-text-gray-800">
                        Địa chỉ email của bạn chưa được xác thực.

                        <button
                            form="send-verification"
                            type="submit"
                            class="tw-underline tw-text-sm tw-text-[#16a249] hover:tw-text-[#34b269] tw-ml-1">
                            Nhấn vào đây để gửi lại email xác thực.
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="tw-mt-2 tw-font-medium tw-text-sm tw-text-green-600">
                            Một liên kết xác thực mới đã được gửi đến địa chỉ email của bạn.
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="tw-flex tw-items-center tw-gap-4">
            <button
                type="submit"
                class="tw-h-[44px] md:tw-h-[50px] tw-px-6 md:tw-px-8 tw-bg-[linear-gradient(180deg,_#34b269_0%,_#78d29e_100%)] tw-text-white tw-rounded-lg tw-font-semibold tw-text-sm md:tw-text-base tw-transition tw-transform hover:tw-scale-[1.03] hover:tw-shadow-lg">
                Lưu thay đổi
            </button>

            @if (session('status') === 'profile-updated')
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
