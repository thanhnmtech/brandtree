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

    <form method="post" action="{{ route('profile.update') }}" class="tw-space-y-5" enctype="multipart/form-data">
        @csrf
        @method('patch')

        <!-- Avatar Upload -->
        <div x-data="{ photoName: null, photoPreview: null }" class="tw-col-span-6 sm:tw-col-span-4">
            <!-- Profile Photo File Input -->
            <input type="file" id="photo" class="tw-hidden"
                        name="avatar"
                        x-ref="photo"
                        x-on:change="
                                photoName = $refs.photo.files[0].name;
                                const reader = new FileReader();
                                reader.onload = (e) => {
                                    photoPreview = e.target.result;
                                };
                                reader.readAsDataURL($refs.photo.files[0]);
                        " />

            <div class="tw-items-center tw-gap-6">
                <label class="tw-block tw-text-sm tw-font-medium tw-text-gray-700 tw-mb-2">
                    Ảnh đại diện
                </label>
                
                <div class="tw-flex tw-gap-6">
                    <!-- Select File Area (Left) -->
                    <div class="tw-flex-1 tw-mt-1 tw-flex tw-justify-center tw-px-6 tw-pt-5 tw-pb-6 tw-border-2 tw-border-gray-300 tw-border-dashed tw-rounded-lg tw-cursor-pointer hover:tw-bg-gray-50 tw-transition" x-on:click.prevent="$refs.photo.click()">
                        <div class="tw-space-y-1 tw-text-center">
                            <i class="ri-upload-cloud-2-line tw-text-4xl tw-text-gray-400"></i>
                            <div class="tw-flex tw-text-sm tw-text-gray-600">
                                <span class="tw-relative tw-cursor-pointer tw-bg-white tw-rounded-md tw-font-medium tw-text-[#1AA24C] hover:tw-text-[#148a3f]">
                                    Tải ảnh lên
                                </span>
                                <p class="tw-pl-1">hoặc kéo thả</p>
                            </div>
                            <p class="tw-text-xs tw-text-gray-500">
                                PNG, JPG, GIF tối đa 2MB
                            </p>
                        </div>
                    </div>

                    <!-- Preview Area (Right) -->
                    <div class="tw-flex tw-flex-col tw-items-center tw-justify-center tw-gap-2">
                        <div class="tw-mt-2" x-show="! photoPreview">
                            @if (auth()->user()->avatar)
                                <img src="{{ Storage::url(auth()->user()->avatar) }}" alt="{{ auth()->user()->name }}" class="tw-rounded-full tw-h-32 tw-w-32 tw-object-cover tw-border-2 tw-border-gray-200">
                            @else
                                <div class="tw-rounded-full tw-h-32 tw-w-32 tw-bg-gray-100 tw-flex tw-items-center tw-justify-center tw-text-gray-400 tw-border-2 tw-border-gray-200">
                                    <i class="ri-user-line tw-text-4xl"></i>
                                </div>
                            @endif
                        </div>
                        <!-- New Profile Photo Preview -->
                        <div class="tw-mt-2" x-show="photoPreview" style="display: none;">
                            <span class="tw-block tw-rounded-full tw-w-32 tw-h-32 tw-bg-cover tw-bg-no-repeat tw-bg-center tw-border-2 tw-border-[#1AA24C]"
                                x-bind:style="'background-image: url(\'' + photoPreview + '\');'">
                            </span>
                        </div>
                        
                        <button type="button" class="tw-text-sm tw-text-gray-500 hover:tw-text-red-500" x-show="photoPreview" x-on:click="photoPreview = null; photoName = null; $refs.photo.value = null">
                            Xóa ảnh
                        </button>
                        
                    </div>
                </div>
            </div>
            <x-input-error class="tw-mt-2" :messages="$errors->get('avatar')" />
        </div>

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
