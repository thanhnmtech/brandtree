<x-app-layout>
    <style>
        [x-cloak] { display: none !important; }
    </style>
    <div x-data="{
        editBrandModal: {{ $errors->any() && old('_brand_modal_mode') === 'edit' ? 'true' : 'false' }},
        deleteBrandModal: false,
        deleteConfirmName: '',
        logoPreview: '{{ $brand->logo_path ? Storage::url($brand->logo_path) : null }}',
        isSubmitting: false,
        formErrors: {},

        openEditBrand() {
            this.editBrandModal = true;
            this.formErrors = {};
            this.$nextTick(() => this.$refs.brandNameInput?.focus());
        },

        closeEditBrand() {
            this.editBrandModal = false;
            this.resetLogoPreview();
            this.formErrors = {};
        },

        openDeleteBrand() {
            this.deleteBrandModal = true;
            this.deleteConfirmName = '';
            this.$nextTick(() => this.$refs.deleteConfirmInput?.focus());
        },

        closeDeleteBrand() {
            this.deleteBrandModal = false;
            this.deleteConfirmName = '';
        },

        async submitDeleteBrand() {
            if (this.deleteConfirmName !== '{{ $brand->name }}') {
                alert('Tên thương hiệu không chính xác!');
                return;
            }

            this.isSubmitting = true;

            try {
                const response = await fetch('{{ route('brands.destroy', $brand) }}', {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    }
                });

                const data = await response.json();

                if (response.ok) {
                    this.closeDeleteBrand();

                    if (window.showNotification) {
                        window.showNotification(data.message, 'success');
                    }

                    setTimeout(() => {
                        window.location.href = data.redirect || '{{ route('dashboard') }}';
                    }, 500);
                } else {
                    alert(data.message || 'Có lỗi xảy ra. Vui lòng thử lại.');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Có lỗi xảy ra. Vui lòng thử lại.');
            } finally {
                this.isSubmitting = false;
            }
        },

        async submitBrandForm(event) {
            this.isSubmitting = true;
            this.formErrors = {};

            const form = event.target;
            const formData = new FormData(form);

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    }
                });

                const data = await response.json();

                if (response.ok) {
                    // Success
                    this.closeEditBrand();

                    // Show success notification
                    if (window.showNotification) {
                        window.showNotification(data.message, 'success');
                    }

                    // Redirect to updated brand URL (slug might have changed)
                    if (data.redirect) {
                        window.location.href = data.redirect;
                    } else {
                        window.location.reload();
                    }
                } else {
                    // Validation errors
                    if (data.errors) {
                        this.formErrors = data.errors;
                    } else {
                        this.formErrors.general = data.message || 'Có lỗi xảy ra. Vui lòng thử lại.';
                    }
                }
            } catch (error) {
                console.error('Error:', error);
                this.formErrors.general = 'Có lỗi xảy ra. Vui lòng thử lại.';
            } finally {
                this.isSubmitting = false;
            }
        },

        previewLogo(event) {
            const file = event.target.files[0];
            if (!file) return;

            // Check file size (2MB)
            if (file.size > 2 * 1024 * 1024) {
                alert('Logo không được lớn hơn 2MB');
                event.target.value = '';
                return;
            }

            const reader = new FileReader();
            reader.onload = (e) => {
                this.logoPreview = e.target.result;
            };
            reader.readAsDataURL(file);
        },

        resetLogoPreview() {
            this.logoPreview = '{{ $brand->logo_path ? Storage::url($brand->logo_path) : null }}';
            const input = this.$refs.logoInput;
            if (input) input.value = '';
        }
    }">
    <main class="tw-mt-[36px] tw-flex tw-flex-col tw-gap-10">
    <!-- =================== HERO =================== -->
    <div class="tw-w-full tw-bg-[#F9F8F5] tw-py-6 tw-px-8 tw-flex tw-items-center tw-justify-between">
        <div>
            <div class="tw-flex tw-items-center tw-gap-2">
                <h2 class="tw-text-3xl tw-font-bold tw-text-gray-900">
                    {{ $brand->name }}
                </h2>
                @if($brand->isAdmin(auth()->user()))
                    <div class="tw-relative" x-data="{ open: false }" @click.away="open = false">
                        <i class="ri-more-2-fill tw-text-gray-600 tw-text-2xl tw-cursor-pointer hover:tw-text-gray-800 tw-transition" @click="open = !open"></i>

                        <!-- Dropdown Menu -->
                        <x-modal-transition type="dropdown"
                             x-show="open"
                             class="tw-absolute tw-left-0 tw-top-full tw-mt-2 tw-w-64 tw-bg-white tw-rounded-lg tw-shadow-lg tw-border tw-border-gray-200 tw-z-50"
                             style="display: none;">
                            <div class="tw-py-2">
                                <!-- Menu Items -->
                                <button @click="openEditBrand()" type="button" class="tw-w-full tw-flex tw-items-center tw-gap-3 tw-px-4 tw-py-3 tw-text-gray-700 hover:tw-bg-gray-50 tw-transition">
                                    <i class="ri-edit-line tw-text-lg tw-text-gray-500"></i>
                                    <span class="tw-text-sm tw-font-medium">Cập nhật thương hiệu</span>
                                </button>

                                <a href="{{ route('brands.subscription.show', $brand) }}" class="tw-flex tw-items-center tw-gap-3 tw-px-4 tw-py-3 tw-text-gray-700 hover:tw-bg-gray-50 tw-transition">
                                    <i class="ri-flashlight-line tw-text-lg tw-text-gray-500"></i>
                                    <span class="tw-text-sm tw-font-medium">Quản lý gói</span>
                                </a>

                                <a href="{{ route('brands.members.index', $brand) }}" class="tw-flex tw-items-center tw-gap-3 tw-px-4 tw-py-3 tw-text-gray-700 hover:tw-bg-gray-50 tw-transition">
                                    <i class="ri-group-line tw-text-lg tw-text-gray-500"></i>
                                    <span class="tw-text-sm tw-font-medium">Quản lý thành viên</span>
                                </a>

                                <a href="{{ route('brands.payments.index', $brand) }}" class="tw-flex tw-items-center tw-gap-3 tw-px-4 tw-py-3 tw-text-gray-700 hover:tw-bg-gray-50 tw-transition">
                                    <i class="ri-bank-card-line tw-text-lg tw-text-gray-500"></i>
                                    <span class="tw-text-sm tw-font-medium">Lịch sử thanh toán</span>
                                </a>

                                <a href="{{ route('brands.credits.stats', $brand) }}" class="tw-flex tw-items-center tw-gap-3 tw-px-4 tw-py-3 tw-text-gray-700 hover:tw-bg-gray-50 tw-transition tw-border-t tw-border-gray-100">
                                    <i class="ri-bar-chart-box-line tw-text-lg tw-text-gray-500"></i>
                                    <span class="tw-text-sm tw-font-medium">Thống kê năng lượng</span>
                                </a>

                                <button @click="openDeleteBrand(); open = false" type="button" class="tw-w-full tw-flex tw-items-center tw-gap-3 tw-px-4 tw-py-3 tw-text-red-600 hover:tw-bg-red-50 tw-transition tw-border-t tw-border-gray-100">
                                    <i class="ri-delete-bin-line tw-text-lg"></i>
                                    <span class="tw-text-sm tw-font-medium">Xóa thương hiệu</span>
                                </button>
                            </div>
                        </x-modal-transition>
                    </div>
                @endif
                {{-- <i class="ri-arrow-down-s-line tw-text-gray-500 tw-text-2xl tw-cursor-pointer" id="brandSwitcherBtn"></i> --}}

            </div>
            <p class="tw-text-md tw-text-gray-500 tw-mt-1">
                Năm thành lập: {{ $brand->founded_year }}
            </p>
            <p class="tw-text-md tw-text-gray-500 tw-mt-1">
                {{ $brand->description }}
            </p>
        </div>

        <div class="tw-flex tw-items-center">
            <div class="tw-relative tw-w-[124px] tw-h-[124px]">
                <svg class="tw-w-full tw-h-full" viewBox="0 0 120 120">
                    <circle cx="60" cy="60" r="52" stroke="#E8EDEB" stroke-width="8" fill="none" />
                </svg>

                <svg class="tw-w-full tw-h-full tw-absolute tw-top-0 tw-left-0 tw-rotate-[-90deg]"
                    viewBox="0 0 120 120">
                    <circle cx="60" cy="60" r="52" stroke="#1AA24C" stroke-width="8"
                        stroke-linecap="round" fill="none" stroke-dasharray="326" stroke-dashoffset="153" />
                </svg>

                <div class="tw-absolute tw-inset-0 tw-flex tw-items-center tw-justify-center">
                    <div class="tw-text-center">
                        <p class="tw-text-3xl tw-font-bold tw-text-[#1AA24C]">53%</p>
                        <p class="tw-text-sm tw-text-gray-500 tw-mt-1">Hoàn thành</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- =================== SUMMARY CARDS =================== -->
    <section class="tw-px-8">
        <!-- Inserted from brand_details_cards.html -->
        <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-4 tw-gap-4">
            <!-- CARD 1 -->
            <div
                class="tw-bg-white tw-rounded-xl tw-border tw-border-[#E4ECE8] tw-p-5 tw-shadow-sm tw-flex tw-items-start tw-gap-3">
                <div class="tw-h-9 tw-w-9 tw-rounded-md tw-bg-[#E7F4EF] tw-flex tw-items-center tw-justify-center">
                    <i class="ri-building-2-line tw-text-[#3A8F6E] tw-text-3xl"></i>
                </div>
                <div>
                    <p class="tw-text tw-text-gray-500 tw-font-medium">Ngành nghề</p>
                    <p class="tw-text-lg tw-text-gray-800 tw-font-semibold tw-mt-1">
                        {{ $brand->industry }}
                    </p>
                </div>
            </div>

            <!-- CARD 3 -->
            <div
                class="tw-bg-white tw-rounded-xl tw-border tw-border-[#E4ECE8] tw-p-5 tw-shadow-sm tw-flex tw-items-start tw-gap-3">
                <div class="tw-h-9 tw-w-9 tw-rounded-md tw-bg-[#F5F3ED] tw-flex tw-items-center tw-justify-center">
                    <i class="ri-group-line tw-text-[#C4A676] tw-text-3xl"></i>
                </div>
                <div>
                    <p class="tw-text tw-text-gray-500 tw-font-medium">
                        Thị trường mục tiêu
                    </p>
                    <p class="tw-text-lg tw-text-gray-800 tw-font-semibold tw-mt-1">
                        {{ $brand->target_market }}
                    </p>
                </div>
            </div>
            <a href="{{ route('brands.subscription.show',$brand) }}"
                class="tw-bg-white tw-rounded-xl tw-border tw-border-[#E4ECE8] tw-p-5 tw-shadow-sm tw-flex tw-items-start tw-gap-3">
                <div class="tw-h-9 tw-w-9 tw-rounded-md tw-bg-[#E7F4EF] tw-flex tw-items-center tw-justify-center">
                    <i class="ri-flashlight-line tw-text-[#3A8F6E] tw-text-3xl"></i>
                </div>
                <div>
                    <p class="tw-text tw-text-gray-500 tw-font-medium">
                        Năng lượng
                    </p>
                    <p class="tw-text-lg tw-text-gray-800 tw-font-semibold tw-mt-1">
                        {{ $brand->credits_remaining }} / {{ $brand->total_credits }}
                    </p>
                </div>
            </a>

            <!-- CARD 4 -->
            <a href="{{ route('brands.members.index',$brand) }}"
                class="tw-bg-white tw-rounded-xl tw-border tw-border-[#E4ECE8] tw-p-5 tw-shadow-sm tw-flex tw-items-start tw-gap-3">
                <div class="tw-h-9 tw-w-9 tw-rounded-md tw-bg-[#FFF4E8] tw-flex tw-items-center tw-justify-center">
                    <i class="ri-group-line tw-text-[#E3A65A] tw-text-3xl"></i>
                </div>
                <div>
                    <p class="tw-text tw-text-gray-500 tw-font-medium">
                        Thành viên
                    </p>
                    <p class="tw-text-lg tw-text-gray-800 tw-font-semibold tw-mt-1">
                    {{ $brand->members->count() }}
                    </p>
                </div>
            </a>
        </div>
    </section>

    <!-- =================== PROGRESS HEADER =================== -->
    <section class="tw-px-8">
        <!-- Inserted from progress_header.html -->
        <div
            class="tw-w-full tw-bg-[linear-gradient(180deg,rgba(207,240,255,0.5)_0%,rgba(255,255,247,0.5)_100%)] tw-from-[#F4FAF7] tw-to-[#EEF6F2] tw-rounded-xl tw-border tw-border-[#E0EAE6] tw-p-8 shadow-[0_18px_40px_-8px_rgba(0,0,0,0.15)]">
            <div class="tw-text-center tw-mb-8">
                <h2
                    class="tw-text-2xl tw-font-bold tw-bg-[linear-gradient(90deg,#33B45E_0%,#ABB354_100%)] tw-bg-clip-text tw-text-transparent">
                    Hành Trình Thương Hiệu
                </h2>
                <p class="tw-text-gray-600 tw-text-sm tw-mt-1">
                    Theo dõi tiến độ phát triển thương hiệu
                </p>
            </div>

            <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-3 tw-gap-6 tw-mt-6">
                <!-- CARD 1 -->
                <div
                    class="tw-bg-white tw-rounded-xl tw-border tw-border-[#DDE7E2] tw-p-6 tw-shadow-sm tw-relative tw-overflow-hidden">
                    <div class="tw-h-2 tw-w-full tw-bg-[#1AA24C] tw-absolute tw-top-0"></div>

                    <div class="tw-text-center tw-flex tw-flex-col tw-items-center">
                        <div
                            class="tw-h-14 tw-w-14 tw-rounded-full tw-bg-[#E6F4EC] tw-flex tw-items-center tw-justify-center tw-text-[#1AA24C] tw-text-2xl tw-mb-3">
                            <i class="ri-seedling-fill"></i>
                        </div>

                        <p
                            class="tw-bg-[#F2F8F4] tw-text-[#1AA24C] tw-text-xs tw-font-bold tw-px-3 tw-py-1 tw-rounded-full">
                            Giai đoạn 1
                        </p>

                        <h3 class="tw-text-lg tw-font-semibold">Gốc Cây</h3>
                        <p class="tw-text-gray-500 tw-text-sm">Nền tảng Thương Hiệu</p>

                        <div class="tw-mt-4 tw-w-full">
                            <div class="tw-text-xs tw-font-medium tw-text-gray-700 tw-mb-1">
                                Tiến độ
                            </div>
                            <div class="tw-w-full tw-h-2 tw-bg-[#E8EDEB] tw-rounded-full tw-overflow-hidden">
                                <div class="tw-h-full tw-bg-[#1AA24C]" style="width: 100%"></div>
                            </div>

                            <div class="tw-text-right tw-text-xs tw-text-gray-700 tw-mt-1">
                                100%
                            </div>
                        </div>

                        <div class="tw-mt-5">
                            <span
                                class="tw-text-xs tw-font-semibold tw-bg-[#E6F6EC] tw-text-[#1AA24C] tw-px-3 tw-py-1 tw-rounded-full">
                                ✔ Hoàn thành
                            </span>
                        </div>
                    </div>
                </div>

                <!-- CARD 2 -->
                <div
                    class="tw-bg-white tw-rounded-xl tw-border tw-border-[#DDE7E2] tw-p-6 tw-shadow-sm tw-relative tw-overflow-hidden">
                    <div class="tw-h-2 tw-w-full tw-bg-[#96C7B0] tw-absolute tw-top-0"></div>

                    <div class="tw-text-center tw-flex tw-flex-col tw-items-center">
                        <div
                            class="tw-h-14 tw-w-14 tw-rounded-full tw-bg-[#E8F2EE] tw-flex tw-items-center tw-justify-center tw-text-[#489A6D] tw-text-2xl tw-mb-3">
                            <i class="ri-tree-line"></i>
                        </div>

                        <p
                            class="tw-bg-[#F0F5F2] tw-text-[#489A6D] tw-text-xs tw-font-bold tw-px-3 tw-py-1 tw-rounded-full">
                            Giai đoạn 2
                        </p>

                        <h3 class="tw-text-lg tw-font-semibold">Thân Cây</h3>
                        <p class="tw-text-gray-500 tw-text-sm">Bản sắc Thương Hiệu</p>

                        <div class="tw-mt-4 tw-w-full">
                            <div class="tw-text-xs tw-font-medium tw-text-gray-700 tw-mb-1">
                                Tiến độ
                            </div>
                            <div class="tw-w-full tw-h-2 tw-bg-[#E8EDEB] tw-rounded-full tw-overflow-hidden">
                                <div class="tw-h-full tw-bg-[#1AA24C]" style="width: 60%"></div>
                            </div>

                            <div class="tw-text-right tw-text-xs tw-text-gray-700 tw-mt-1">
                                60%
                            </div>
                        </div>

                        <div class="tw-mt-5">
                            <span
                                class="tw-text-xs tw-font-semibold tw-bg-[#E9F3EF] tw-text-[#489A6D] tw-px-3 tw-py-1 tw-rounded-full">
                                ⏳ Đang thực hiện
                            </span>
                        </div>
                    </div>
                </div>

                <!-- CARD 3 -->
                <div class="tw-bg-white tw-rounded-xl tw-border tw-border-[#E2EAE5] tw-p-6 tw-shadow-sm tw-opacity-60">
                    <div class="tw-text-center tw-flex tw-flex-col tw-items-center">
                        <div
                            class="tw-h-14 tw-w-14 tw-rounded-full tw-bg-[#F3F5F4] tw-flex tw-items-center tw-justify-center tw-text-[#A0B5AA] tw-text-2xl tw-mb-3">
                            <i class="ri-sparkling-fill"></i>
                        </div>

                        <p
                            class="tw-bg-[#F3F5F4] tw-text-[#A0B5AA] tw-text-xs tw-font-bold tw-px-3 tw-py-1 tw-rounded-full">
                            Giai đoạn 3
                        </p>

                        <h3 class="tw-text-lg tw-font-semibold tw-text-gray-500">
                            Tán Cây
                        </h3>
                        <p class="tw-text-gray-400 tw-text-sm">Phát triển & Lan tỏa</p>

                        <div class="tw-mt-5">
                            <span
                                class="tw-text-xs tw-font-semibold tw-bg-[#EDEFEF] tw-text-[#A0B5AA] tw-px-3 tw-py-1 tw-rounded-full">
                                Chưa mở khóa
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- =================== BRAND OVERVIEW =================== -->
    <section class="tw-px-8">
        <div
            class="tw-w-full tw-bg-[linear-gradient(96deg,#F5FBF7_0.31%,#FCF9F3_50%,#F2F7F0_99.69%)] tw-border tw-border-[#E0EAE6] tw-rounded-xl tw-p-8 tw-shadow-sm tw-flex tw-flex-col lg:tw-flex-row tw-gap-10 tw-items-center">
            <!-- LEFT -->
            <div class="tw-flex-1 tw-space-y-6">
                <div>
                    <h2 class="tw-text-2xl tw-font-bold tw-flex tw-items-center tw-gap-2">
                        <i class="ri-bar-chart-line tw-text-[#1AA24C] tw-text-2xl"></i>
                        Tổng Quan Thương Hiệu
                    </h2>
                    <p class="tw-text-sm tw-text-gray-500 tw-mt-1">
                        Phân tích hiệu suất và tiến độ xây dựng thương hiệu.
                    </p>
                </div>

                <div>
                    <div class="tw-flex tw-justify-between tw-text-sm tw-text-gray-700">
                        <span class="tw-flex tw-items-center tw-gap-1">
                            <img src="{{ asset('assets/img/icon-dinhvi-green.svg') }}" alt="ok"
                                class="tw-w-[14px] tw-h-[14px]" />
                            Định vị
                        </span>
                        <span class="tw-text-[#1AA24C] tw-font-semibold">100%</span>
                    </div>
                    <div class="tw-w-full tw-h-2 tw-rounded-full tw-bg-[#E8EDEB] tw-overflow-hidden tw-mt-1">
                        <div class="tw-h-full tw-bg-[#1AA24C]" style="width: 100%"></div>
                    </div>
                </div>

                <div>
                    <div class="tw-flex tw-justify-between tw-text-sm tw-text-gray-700">
                        <span class="tw-flex tw-items-center tw-gap-1">
                            <i class="ri-brush-fill tw-text-[#F59F0A]"></i>
                            Bản sắc
                        </span>
                        <span class="tw-text-[#F59F0A] tw-font-semibold">60%</span>
                    </div>
                    <div class="tw-w-full tw-h-2 tw-rounded-full tw-bg-[#E8EDEB] tw-overflow-hidden tw-mt-1">
                        <div class="tw-h-full tw-bg-[#F59F0A]" style="width: 60%"></div>
                    </div>
                </div>

                <div
                    class="tw-w-full tw-bg-[linear-gradient(90deg,#E7F2E7_0%,#F8F1E0_100%)] tw-border tw-border-[#E0EAE6] tw-rounded-lg tw-py-5 tw-px-10 tw-w-[260px] tw-shadow-sm tw-flex tw-items-center tw-justify-between">
                    <div>
                        <p class="tw-text-gray-500 tw-text-md">Điểm tổng thể</p>
                        <p class="tw-text-3xl tw-font-bold tw-text-[#1AA24C]">
                            53 <span class="tw-text-gray-400 tw-text-xl">/100</span>
                        </p>
                    </div>
                    <span
                        class="tw-inline-block tw-mt-2 tw-text-sm tw-font-semibold tw-bg-vlbcgreen tw-text-white tw-px-3 tw-py-1 tw-rounded-full">
                        Đang phát triển
                    </span>
                </div>
            </div>

            <!-- RIGHT -->
            <div class="tw-flex tw-items-center tw-justify-center tw-w-full lg:tw-w-[320px]">
                <div class="tw-relative tw-w-[200px] tw-h-[200px]">
                    <svg class="tw-w-full tw-h-full" viewBox="0 0 120 120">
                        <circle cx="60" cy="60" r="52" stroke="#E8EDEB" stroke-width="8"
                            fill="none" />
                    </svg>

                    <svg class="tw-w-full tw-h-full tw-absolute tw-top-0 tw-left-0 tw-rotate-[-90deg]"
                        viewBox="0 0 120 120">
                        <circle cx="60" cy="60" r="52" stroke="#1AA24C" stroke-width="8"
                            stroke-linecap="round" fill="none" stroke-dasharray="326" stroke-dashoffset="153" />
                    </svg>

                    <div class="tw-absolute tw-inset-0 tw-flex tw-items-center tw-justify-center">
                        <div class="tw-text-center">
                            <p class="tw-text-3xl tw-font-bold tw-text-[#1AA24C]">53%</p>
                            <p class="tw-text-sm tw-text-gray-500 tw-mt-1">Hoàn thiện</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- =================== KẾT QUẢ CHIẾN LƯỢC =================== -->
    <section class="tw-px-8 tw-space-y-6">
        <h2 class="tw-text-2xl tw-font-bold tw-text-gray-800">
            Kết Quả Chiến Lược
        </h2>

        <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-3 tw-gap-5">
            <!-- CARD 1 — Active -->
            <div class="tw-bg-white tw-rounded-xl tw-border-2 tw-border-[#1AA24C] tw-shadow-sm tw-p-6 tw-transition">
                <div class="tw-flex tw-items-start tw-gap-3">
                    <div
                        class="tw-h-10 tw-w-10 tw-rounded-full tw-bg-[#E6F6EC] tw-flex tw-items-center tw-justify-center">
                        <i class="ri-disc-line tw-text-[#1AA24C] tw-text-xl"></i>
                    </div>

                    <div class="tw-flex-1">
                        <h3 class="tw-text-lg tw-font-semibold tw-text-gray-800">
                            Nền Tảng Chân Thực
                        </h3>
                        <p class="tw-text-sm tw-text-gray-500 tw-mt-1">
                            Giá trị cốt lõi đã được xác định
                        </p>

                        <div class="tw-flex tw-gap-2 tw-flex-wrap tw-mt-3">
                            <span
                                class="tw-bg-[#E6F6EC] tw-text-[#1AA24C] tw-text-xs tw-font-semibold tw-px-3 tw-py-1 tw-rounded-full">
                                Chất lượng
                            </span>
                            <span
                                class="tw-bg-[#E6F6EC] tw-text-[#1AA24C] tw-text-xs tw-font-semibold tw-px-3 tw-py-1 tw-rounded-full">
                                Tận tâm
                            </span>
                            <span
                                class="tw-bg-[#E6F6EC] tw-text-[#1AA24C] tw-text-xs tw-font-semibold tw-px-3 tw-py-1 tw-rounded-full">
                                Đổi mới
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CARD 2 — Locked (Thân Cây chưa xong) -->
            <div class="tw-bg-white tw-rounded-xl tw-border tw-border-[#E4ECE8] tw-shadow-sm tw-p-6 tw-opacity-70">
                <div class="tw-flex tw-items-start tw-gap-3">
                    <div
                        class="tw-h-10 tw-w-10 tw-rounded-full tw-bg-[#FFF5E6] tw-flex tw-items-center tw-justify-center">
                        <i class="ri-focus-3-line tw-text-[#E3A65A] tw-text-xl"></i>
                    </div>

                    <div class="tw-flex-1">
                        <h3 class="tw-text-lg tw-font-semibold tw-text-gray-500">
                            Bản Sắc Nhất Quán
                        </h3>
                        <p class="tw-text-sm tw-text-gray-400 tw-mt-1">
                            Chưa hoàn thành giai đoạn Thân Cây
                        </p>
                    </div>
                </div>
            </div>

            <!-- CARD 3 — Locked (Tán Cây chưa bắt đầu) -->
            <div class="tw-bg-white tw-rounded-xl tw-border tw-border-[#E4ECE8] tw-shadow-sm tw-p-6 tw-opacity-60">
                <div class="tw-flex tw-items-start tw-gap-3">
                    <div
                        class="tw-h-10 tw-w-10 tw-rounded-full tw-bg-[#EAF5F1] tw-flex tw-items-center tw-justify-center">
                        <i class="ri-heart-pulse-line tw-text-[#87B2A1] tw-text-xl"></i>
                    </div>

                    <div class="tw-flex-1">
                        <h3 class="tw-text-lg tw-font-semibold tw-text-gray-500">
                            Sức Khỏe Thương Hiệu
                        </h3>
                        <p class="tw-text-sm tw-text-gray-400 tw-mt-1">
                            Chưa bắt đầu giai đoạn Tán Cây
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- =================== BƯỚC TIẾP THEO =================== -->
    <section class="tw-px-8">
        <div
            class="tw-w-full tw-bg-[linear-gradient(273deg,#FDFAF4_0.32%,#F5FBF7_99.68%)] tw-border tw-border-[#E0EAE6] tw-rounded-xl tw-p-8 tw-shadow-sm">
            <!-- Header -->
            <div class="tw-flex tw-items-center tw-gap-3">
                <div
                    class="tw-h-12 tw-w-12 tw-rounded-full tw-bg-[linear-gradient(324deg,#69CB92_15.2%,#41B873_81.83%)] tw-flex tw-items-center tw-justify-center">
                    <img src="{{ asset('assets/img/icon-star-white.svg') }}" alt="ok" class="tw-w-[24px] tw-h-[24px]" />
                </div>

                <div>
                    <h2 class="tw-text-2xl tw-font-bold tw-text-gray-800">
                        Bước Tiếp Theo
                    </h2>
                    <p class="tw-text-sm tw-text-gray-500 tw-mt-1">
                        Tuyệt vời! Giờ là lúc tạo bản sắc độc đáo.
                    </p>
                </div>
            </div>

            <!-- Description Box -->
            <div
                class="tw-bg-white tw-border tw-border-[#E0EAE6] tw-rounded-lg tw-p-4 tw-mt-4 tw-text-gray-700 tw-text-sm">
                Hãy tiếp tục hoàn thiện Bản sắc Thương hiệu của bạn. Tạo tuyên bố
                định vị độc đáo và thiết kế hệ thống nhận diện thị giác nhất quán.
            </div>

            <!-- Action Button -->
            <div class="tw-mt-5">
                <button
                    class="tw-w-full tw-bg-gradient-to-r tw-from-[#34B26A] tw-to-[#78D29E] tw-text-white tw-font-medium tw-text-base tw-py-1 tw-rounded-lg tw-flex tw-items-center tw-justify-center tw-gap-4 tw-shadow-sm hover:tw-scale-[1.01] tw-transition">
                    Bắt đầu ngay
                    <i class="ri-arrow-right-line tw-text-lg"></i>
                </button>
            </div>

            <!-- Hint -->
            <div class="tw-text-xs tw-text-gray-500 tw-flex tw-items-center tw-justify-center tw-gap-1 tw-mt-4">
                <i class="ri-sparkling-2-line tw-text-[#F4C56A]"></i>
                Mẹo: Bạn có thể yêu cầu AI hỗ trợ bất kỳ lúc nào trong quá trình xây
                dựng thương hiệu
            </div>
        </div>
    </section>

    <!-- Edit Brand Modal Component -->
    <x-modal-brand-form :brand="$brand" mode="edit" />

    <!-- Delete Brand Confirmation Modal -->
    <x-modal-transition
        x-show="deleteBrandModal"
        @click.away="closeDeleteBrand()"
        x-cloak>
        <div class="tw-fixed tw-inset-0 tw-bg-black tw-bg-opacity-50 tw-flex tw-items-center tw-justify-center tw-z-50 tw-p-4">
            <div class="tw-bg-white tw-rounded-2xl tw-shadow-2xl tw-max-w-md tw-w-full tw-p-6" @click.stop>
                <!-- Header -->
                <div class="tw-flex tw-items-start tw-justify-between tw-mb-6">
                    <div class="tw-flex tw-items-center tw-gap-3">
                        <div class="tw-h-12 tw-w-12 tw-rounded-full tw-bg-red-100 tw-flex tw-items-center tw-justify-center">
                            <i class="ri-delete-bin-line tw-text-red-600 tw-text-2xl"></i>
                        </div>
                        <div>
                            <h3 class="tw-text-xl tw-font-bold tw-text-gray-900">Xóa thương hiệu</h3>
                            <p class="tw-text-sm tw-text-gray-500">Hành động này không thể hoàn tác</p>
                        </div>
                    </div>
                    <button
                        @click="closeDeleteBrand()"
                        type="button"
                        class="tw-text-gray-400 hover:tw-text-gray-600 tw-transition">
                        <i class="ri-close-line tw-text-2xl"></i>
                    </button>
                </div>

                <!-- Warning Message -->
                <div class="tw-bg-red-50 tw-border tw-border-red-200 tw-rounded-lg tw-p-4 tw-mb-6">
                    <p class="tw-text-sm tw-text-red-800">
                        <i class="ri-alert-line tw-mr-2"></i>
                        Bạn sắp xóa thương hiệu <strong>{{ $brand->name }}</strong>. Tất cả dữ liệu liên quan sẽ bị xóa vĩnh viễn.
                    </p>
                </div>

                <!-- Confirmation Input -->
                <div class="tw-mb-6">
                    <label class="tw-block tw-text-sm tw-font-medium tw-text-gray-700 tw-mb-2">
                        Để xác nhận, vui lòng nhập tên thương hiệu: <strong class="tw-text-red-600">{{ $brand->name }}</strong>
                    </label>
                    <input
                        x-ref="deleteConfirmInput"
                        x-model="deleteConfirmName"
                        type="text"
                        class="tw-w-full tw-px-4 tw-py-3 tw-border tw-border-gray-300 tw-rounded-lg focus:tw-ring-2 focus:tw-ring-red-500 focus:tw-border-transparent tw-transition"
                        placeholder="Nhập tên thương hiệu"
                        @keydown.enter="submitDeleteBrand()">
                </div>

                <!-- Actions -->
                <div class="tw-flex tw-gap-3">
                    <button
                        @click="closeDeleteBrand()"
                        type="button"
                        :disabled="isSubmitting"
                        class="tw-flex-1 tw-px-4 tw-py-3 tw-bg-gray-100 tw-text-gray-700 tw-font-medium tw-rounded-lg hover:tw-bg-gray-200 tw-transition disabled:tw-opacity-50">
                        Hủy
                    </button>
                    <button
                        @click="submitDeleteBrand()"
                        type="button"
                        :disabled="isSubmitting || deleteConfirmName !== '{{ $brand->name }}'"
                        class="tw-flex-1 tw-px-4 tw-py-3 tw-bg-red-600 tw-text-white tw-font-medium tw-rounded-lg hover:tw-bg-red-700 tw-transition disabled:tw-opacity-50 disabled:tw-cursor-not-allowed tw-flex tw-items-center tw-justify-center tw-gap-2">
                        <span x-show="isSubmitting">
                            <i class="ri-loader-4-line tw-animate-spin"></i>
                        </span>
                        <span x-text="isSubmitting ? 'Đang xóa...' : 'Xóa thương hiệu'"></span>
                    </button>
                </div>
            </div>
        </div>
    </x-modal-transition>

    <!-- =================== AI AGENTS CHUYÊN BIỆT =================== -->
    <section class="tw-px-8">
        <h2 class="tw-text-2xl tw-font-bold tw-text-gray-800 tw-mb-4">
            AI Agents Chuyên Biệt
        </h2>

        <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-4 tw-gap-5">
            <!-- Agent 1 — Active -->
            <div
                class="tw-bg-white tw-border tw-border-[#E4ECE8] tw-rounded-xl tw-shadow-sm tw-p-6 tw-flex tw-flex-col tw-justify-between">
                <div class="tw-flex tw-flex-col tw-items-start tw-gap-3">
                    <div
                        class="tw-h-10 tw-w-10 tw-rounded-full tw-bg-[#E6F6EC] tw-flex tw-items-center tw-justify-center">
                        <i class="ri-line-chart-line tw-text-[#1AA24C] tw-text-xl"></i>
                    </div>

                    <div>
                        <h3 class="tw-text-lg tw-font-semibold tw-text-gray-800">
                            Nhà Phân Tích
                        </h3>
                        <p class="tw-text-sm tw-text-gray-500 tw-mt-1">
                            Phân tích thị trường và đối thủ cạnh tranh
                        </p>
                    </div>
                </div>

                <button
                    class="tw-w-full tw-mt-5 tw-bg-[#F3F7F5] tw-border tw-border-[#D2E5DC] tw-text-gray-700 tw-font-medium tw-text-sm tw-py-1 tw-rounded-md hover:tw-bg-[#E6F3ED] tw-transition tw-flex tw-items-center tw-justify-center tw-gap-1">
                    <i class="ri-add-line"></i> Tương tác
                </button>
            </div>

            <!-- Agent 2 — Active -->
            <div
                class="tw-bg-white tw-border tw-border-[#E4ECE8] tw-rounded-xl tw-shadow-sm tw-p-6 tw-flex tw-flex-col tw-justify-between">
                <div class="tw-flex tw-flex-col tw-items-start tw-gap-3">
                    <div
                        class="tw-h-10 tw-w-10 tw-rounded-full tw-bg-[#FFF3DB] tw-flex tw-items-center tw-justify-center">
                        <i class="ri-lightbulb-flash-line tw-text-[#E3A74B] tw-text-xl"></i>
                    </div>

                    <div>
                        <h3 class="tw-text-lg tw-font-semibold tw-text-gray-800">
                            Nhà Chiến Lược
                        </h3>
                        <p class="tw-text-sm tw-text-gray-500 tw-mt-1">
                            Xây dựng chiến lược định vị thương hiệu
                        </p>
                    </div>
                </div>

                <button
                    class="tw-w-full tw-mt-5 tw-bg-[#F3F7F5] tw-border tw-border-[#D2E5DC] tw-text-gray-700 tw-font-medium tw-text-sm tw-py-1 tw-rounded-lg hover:tw-bg-[#E6F3ED] tw-transition tw-flex tw-items-center tw-justify-center tw-gap-1">
                    <i class="ri-add-line"></i> Tương tác
                </button>
            </div>

            <!-- Agent 3 — Locked -->
            <div
                class="tw-bg-white tw-border tw-border-[#E4ECE8] tw-rounded-xl tw-shadow-sm tw-p-6 tw-opacity-50 tw-flex tw-flex-col tw-justify-between">
                <div class="tw-flex tw-flex-col tw-items-start tw-gap-3">
                    <div
                        class="tw-h-10 tw-w-10 tw-rounded-full tw-bg-[#F1F1F1] tw-flex tw-items-center tw-justify-center">
                        <i class="ri-brush-line tw-text-gray-400 tw-text-xl"></i>
                    </div>

                    <div>
                        <h3 class="tw-text-lg tw-font-semibold tw-text-gray-500">
                            Nhà Sáng Tạo
                        </h3>
                        <p class="tw-text-sm tw-text-gray-400 tw-mt-1">
                            Tạo nội dung và thiết kế sáng tạo
                        </p>
                    </div>
                </div>

                <button
                    class="tw-w-full tw-mt-5 tw-bg-[#F7F7F7] tw-border tw-border-[#E5E5E5] tw-text-gray-400 tw-font-medium tw-text-sm tw-py-1 tw-rounded-lg tw-flex tw-items-center tw-justify-center">
                    Mở khóa sau
                </button>
            </div>

            <!-- Agent 4 — Locked -->
            <div
                class="tw-bg-white tw-border tw-border-[#E4ECE8] tw-rounded-xl tw-shadow-sm tw-p-6 tw-opacity-50 tw-flex tw-flex-col tw-justify-between">
                <div class="tw-flex tw-flex-col tw-items-start tw-gap-3">
                    <div
                        class="tw-h-10 tw-w-10 tw-rounded-full tw-bg-[#F1F1F1] tw-flex tw-items-center tw-justify-center">
                        <i class="ri-group-line tw-text-gray-400 tw-text-xl"></i>
                    </div>

                    <div>
                        <h3 class="tw-text-lg tw-font-semibold tw-text-gray-500">
                            Nhà Cộng Đồng
                        </h3>
                        <p class="tw-text-sm tw-text-gray-400 tw-mt-1">
                            Quản lý và phát triển cộng đồng
                        </p>
                    </div>
                </div>

                <button
                    class="tw-w-full tw-mt-5 tw-bg-[#F7F7F7] tw-border tw-border-[#E5E5E5] tw-text-gray-400 tw-font-medium tw-text-sm tw-py-1 tw-rounded-lg tw-flex tw-items-center tw-justify-center">
                    Mở khóa sau
                </button>
            </div>
        </div>
    </section>
</main>
    </div>
</x-app-layout>
