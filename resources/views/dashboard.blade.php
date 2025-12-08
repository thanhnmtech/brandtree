<x-app-layout>
    <style>
        [x-cloak] { display: none !important; }
    </style>
    <div x-data="{
        addBrandModal: false,
        logoPreview: null,

        openAddBrand() {
            this.addBrandModal = true;
            this.$nextTick(() => this.$refs.brandNameInput.focus());
        },

        closeAddBrand() {
            this.addBrandModal = false;
            this.resetForm();
        },

        resetForm() {
            this.$refs.brandForm.reset();
            this.clearLogoPreview();
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

        clearLogoPreview() {
            this.logoPreview = null;
            const input = this.$refs.logoInput;
            if (input) input.value = '';
        }
    }">
    <main class="tw-flex tw-flex-col md:tw-rounded-[10px] md:tw-bg-[#F3F7F5] md:tw-mt-[36px] md:tw-mx-[71px] md:tw-mb-10">
    <section class="tw-flex tw-flex-col tw-pt-[13px]">
        <!-- SUMMARY GRID -->
        <div class="tw-flex tw-flex-col tw-gap-[15px] tw-mx-[26px] tw-my-[13px] md:tw-flex-row">
            <!-- Card 1 -->
            <div
                class="tw-bg-white tw-rounded-[7px] tw-border-[2px] tw-border-[#E0EAE6] tw-shadow-[0_4px_4px_rgba(0,0,0,0.05)] tw-p-[20px] tw-flex tw-flex-col tw-justify-between tw-flex-1">
                <div class="tw-flex tw-flex-row tw-justify-between tw-text-black tw-text-[18px] tw-font-semibold">
                    <span>Thương hiệu hoạt động</span>
                    <img src="./assets/img/icon-than-gray.svg" alt="total" class="tw-w-[24px] tw-h-[24px]" />
                </div>
                <div class="tw-mt-[6px] tw-text-[30px] tw-font-bold tw-text-black">
                    {{ $activeBrands }}/{{ $totalBrands }}
                </div>
                <div class="tw-mt-[10px] tw-text-[14px] tw-text-[#829B99]">
                    Tổng số thương hiệu bạn đang quản lý
                </div>
            </div>

            <!-- Card 2 -->
            <div
                class="tw-bg-white tw-rounded-[7px] tw-border-[2px] tw-border-[#E0EAE6] tw-shadow-[0_4px_4px_rgba(0,0,0,0.05)] tw-p-[20px] tw-flex tw-flex-col tw-justify-between tw-flex-1">
                <div class="tw-flex tw-flex-row tw-justify-between tw-text-black tw-text-[18px] tw-font-semibold">
                    <span>Cần chăm sóc</span>
                    <img src="./assets/img/icon-warning-red.svg" alt="warning" class="tw-w-[24px] tw-h-[24px]" />
                </div>
                <div class="tw-mt-[6px] tw-text-[30px] tw-font-bold tw-text-[#DC282E]">
                    {{ $totalBrands }}
                </div>
                <div class="tw-mt-[10px] tw-text-[14px] tw-text-[#829B99]">
                    Các thương hiệu đang chờ bạn quay lại
                </div>
            </div>

            <!-- Card 3 -->
            <div
                class="tw-bg-white tw-rounded-[7px] tw-border-[2px] tw-border-[#E0EAE6] tw-shadow-[0_4px_4px_rgba(0,0,0,0.05)] tw-p-[20px] tw-flex tw-flex-col tw-justify-between tw-flex-1">
                <div class="tw-flex tw-flex-row tw-justify-between tw-text-black tw-text-[18px] tw-font-semibold">
                    <span>Đang tăng trưởng</span>
                    <img src="./assets/img/icon-o'clock-orange.svg" alt="grow" class="tw-w-[24px] tw-h-[24px]" />
                </div>
                <div class="tw-mt-[6px] tw-text-[30px] tw-font-bold tw-text-[#F59F0A]">
                    {{ $totalBrands }}
                </div>
                <div class="tw-mt-[10px] tw-text-[14px] tw-text-[#829B99]">
                    Các thương hiệu đang chờ phát triển
                </div>
            </div>

            <!-- Card 4 -->
            <div
                class="tw-bg-white tw-rounded-[7px] tw-border-[2px] tw-border-[#E0EAE6] tw-shadow-[0_4px_4px_rgba(0,0,0,0.05)] tw-p-[20px] tw-flex tw-flex-col tw-justify-between tw-flex-1">
                <div class="tw-flex tw-flex-row tw-justify-between tw-text-black tw-text-[18px] tw-font-semibold">
                    <span>Đã hoàn thiện</span>
                    <img src="./assets/img/icon-success-green.svg" alt="ok" class="tw-w-[24px] tw-h-[24px]" />
                </div>
                <div class="tw-mt-[6px] tw-text-[30px] tw-font-bold tw-text-[#1AA24C]">
                    {{ $totalBrands }}
                </div>
                <div class="tw-mt-[10px] tw-text-[14px] tw-text-[#829B99]">
                    Sẵn sàng để khai thác
                </div>
            </div>
        </div>

        <!-- CONTROLS -->
        <div
            class="tw-flex tw-flex-row tw-items-center tw-gap-[12px] tw-mx-[26px] tw-mt-[13px] tw-mb-0 max-[900px]:tw-flex-col max-[900px]:tw-items-stretch tw-justify-end">
            <!-- Search -->
            <div class="tw-relative tw-w-[680px] tw-max-w-full">
                <input
                    class="tw-w-full tw-border-[2px] tw-border-[#E2EBE7] tw-rounded-[7px] tw-text-[14px] tw-font-normal tw-text-black tw-py-[8px] tw-pl-[36px] tw-pr-[8px] tw-transition"
                    placeholder="Tìm kiếm thương hiệu..." />
                <img src="./assets/img/look-up-vector.svg" alt="look-up"
                    class="tw-absolute tw-left-[10px] tw-top-1/2 tw--translate-y-1/2 tw-w-[16px] tw-h-[16px] tw-pointer-events-none" />
            </div>

            <!-- Filters -->
            <div
                class="tw-flex tw-flex-row tw-gap-[15px] max-[900px]:tw-w-full max-[900px]:tw-justify-between max-[640px]:tw-flex-col">
                <!-- Status filter -->
                <div
                    class="tw-relative tw-h-[44px]  tw-w-[220px] tw-rounded-[7px] tw-border-[2px] tw-border-[#E2EBE7] tw-bg-white tw-flex tw-items-center">
                    <img src="./assets/img/filter-vector.svg" alt="filter"
                        class="tw-absolute tw-left-[10px] tw-top-1/2 tw--translate-y-1/2 tw-w-[16px] tw-h-[16px] tw-pointer-events-none" />
                    <select name="status"
                        class="tw-w-full tw-h-full tw-rounded-[7px] tw-pl-[36px] tw-pr-[28px] tw-text-[14px] tw-text-[#1a1a1a] tw-bg-transparent tw-border-none tw-outline-none tw-appearance-none">
                        <option>Tất cả trạng thái</option>
                        <option>Cần chăm sóc</option>
                        <option>Đang tăng trưởng</option>
                        <option>Đã hoàn thiện</option>
                    </select>
                    <img src="./assets/img/drop-down-vector.svg" alt="dropdown"
                        class="tw-absolute tw-right-[10px] tw-top-1/2 tw--translate-y-1/2 tw-w-[16px] tw-h-[16px] tw-pointer-events-none" />
                </div>

                <!-- Time filter -->
                <div
                    class="tw-relative tw-h-[44px] tw-w-[190px] tw-rounded-[7px] tw-border-[2px] tw-border-[#E2EBE7] tw-bg-white tw-flex tw-items-center">
                    <select name="order_by"
                        class="tw-h-[44px] tw-w-full tw-h-full tw-rounded-[7px] tw-pl-[12px] tw-pr-[12px] tw-text-[14px] tw-text-[#1a1a1a] tw-bg-transparent tw-border-none tw-outline-none tw-appearance-none">
                        <option>Cập nhật gần nhất</option>
                        <option>Mới nhất</option>
                    </select>
                    <img src="./assets/img/drop-down-vector.svg" alt="dropdown"
                        class="tw-absolute tw-right-[10px] tw-top-1/2 tw--translate-y-1/2 tw-w-[16px] tw-h-[16px] tw-pointer-events-none" />
                </div>
            </div>

            <!-- Button -->
            <div class="tw-relative">
                <button @click="openAddBrand()"
                    class="tw-h-[44px] tw-w-[235px] tw-px-4 tw-rounded-[7px] tw-bg-[linear-gradient(180deg,#34b269_0%,#78d29e_100%)] tw-text-white tw-font-light tw-text-sm tw-shadow-sm tw-whitespace-nowrap hover:tw-scale-[1.02] hover:tw-shadow-md tw-transition">
                    <img src="./assets/img/add-vector.svg" alt="add"
                        class="tw-absolute tw-left-[12px] tw-top-1/2 -tw-translate-y-1/2 tw-w-[16px] tw-h-[16px] tw-pointer-events-none" />
                    <div>Thêm thương hiệu</div>
                </button>
            </div>
        </div>
    </section>

    <!-- CARDS -->
    <section class="tw-p-[26px]">
        <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 lg:tw-grid-cols-3 tw-gap-[20px]">
            @foreach ($brands as $brand)
                <brand-card badge="NeedsAttention" avatar="E" title="Eco Garden" update="2 giờ trước" root="85"
                    stem="20"
                    description="Cây cần hoàn thiện phân tích SWOT."><!-- Brand card widget: matches Figma node 226:78 -->
                    <article
                        class="tw-h-full brand-card tw-bg-white tw-rounded-[7px] tw-border-2 tw-border-[#E2EBE7] tw-shadow-[0_4px_4px_rgba(0,0,0,0.05)] tw-p-[20px] tw-flex tw-flex-col tw-relative tw-transition hover:tw-shadow-[0_8px_12px_rgba(0,0,0,0.1)] hover:tw-border-vlbcgreen">
                        <span
                            class="badge tw-absolute tw-top-[15px] tw-right-[15px] tw-bg-[#FAF4EB] tw-text-[#F59F0A] tw-inline-block tw-py-[6px] tw-px-[12px] tw-rounded-[20px] tw-text-[12px] tw-font-semibold"
                            aria-hidden="true" style="background: rgb(250, 244, 235); color: rgb(245, 159, 10);">Cần chú
                            ý</span>

                        <!-- Header: avatar + title -->
                        <div class="tw-flex tw-items-center tw-gap-[12px]">
                             <img src="{{ Storage::url($brand->logo_path) }}" alt="{{ $brand->name }}" class="tw-h-20 tw-w-20 tw-object-contain tw-rounded-full tw-bg-white tw-p-2">
                            <div class="tw-flex tw-flex-col">
                                <div class="title tw-text-[20px] tw-font-semibold tw-text-black">
                                    {{ $brand->name }}
                                </div>
                                <div class="update-time tw-text-[14px] tw-text-[#829B99]">Cập nhật:
                                    {{ $brand->updated_at->diffForHumans() }}</div>
                            </div>
                        </div>

                        <!-- Progress header + small stats -->
                        <div class="tw-flex tw-items-center tw-justify-between tw-mt-[12px] tw-gap-4">
                            <div>
                                <div class="tw-text-[16px] tw-font-semibold tw-text-black">
                                    Tiến độ phát triển
                                </div>
                                <div class="tw-flex tw-gap-4 tw-mt-1">
                                    <div class="root-text tw-text-[14px] tw-text-[#829B99]">Gốc
                                        {{ $process_root = $brand->getProcessRoot() }}</div>
                                    <div class="stem-text tw-text-[14px] tw-text-[#829B99]">Thân
                                        {{ $process_trunk = $brand->getProcessTrunk() }}</div>
                                </div>
                            </div>

                            <!-- Vertical progress indicators (right) -->
                            <div class="tw-w-[110px] tw-relative tw-flex tw-flex-col tw-items-end tw-gap-3">
                                <div class="tw-w-full tw-h-[6px] tw-bg-[#e8eeeb] tw-rounded-full">
                                    <div class="progress-root tw-h-full tw-bg-[#269063] tw-rounded-l-full"
                                        style="width: {{ $process_root }};"></div>
                                </div>
                                <div class="tw-w-full tw-h-[6px] tw-bg-[#e8eeeb] tw-rounded-full">
                                    <div class="progress-stem tw-h-full tw-bg-[#269063] tw-rounded-l-full"
                                        style="width: {{ $process_trunk }};"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="tw-text-[16px] tw-font-semibold tw-text-black tw-mt-[12px]">
                            Bước tiếp theo
                        </div>
                        <p class="description tw-text-[14px] tw-text-[#829B99] tw-leading-[1.5] tw-mt-1 tw-flex-grow tw-overflow-hidden tw-line-clamp-3"
                            style="display: -webkit-box;-webkit-line-clamp: 3;-webkit-box-orient: vertical;">
                            {{ $brand->getNextProcess() }}
                        </p>

                        <!-- Footer area: action button -->
                        <div class="tw-mt-[14px]">
                            <a href="{{ route('brands.show', $brand) }}"
                                class="tw-w-full tw-bg-[#F3F7F5] tw-border-2 tw-border-[#E2EBE7] tw-py-[10px] tw-px-[16px] tw-rounded-[7px] tw-text-[16px] tw-font-medium tw-text-black tw-flex tw-items-center tw-justify-center tw-gap-2 hover:tw-bg-[#E8EEE9] hover:tw-border-vlbcgreen">
                                <span>Quản lý thương hiệu</span>
                                <img class="tw-w-[16px] tw-h-[16px]"
                                    src="./assets/img/4048a4b29522dad1ba63995de703d70091dcb319.svg" alt="arrow" />
                            </a>
                        </div>
                    </article>
                </brand-card>
            @endforeach
        </div>
    </section>
    <!-- ================= ADD BRAND POPUP ================= -->
    <div x-show="addBrandModal" x-cloak
        class="tw-fixed tw-inset-0 tw-bg-black/40 tw-backdrop-blur-sm tw-flex tw-items-center tw-justify-center tw-z-[9999]"
        x-transition:enter="tw-transition tw-ease-out tw-duration-200"
        x-transition:enter-start="tw-opacity-0"
        x-transition:enter-end="tw-opacity-100"
        x-transition:leave="tw-transition tw-ease-in tw-duration-150"
        x-transition:leave-start="tw-opacity-100"
        x-transition:leave-end="tw-opacity-0">
        <div
            class="tw-bg-white tw-rounded-2xl tw-w-[600px] tw-max-w-[90%] tw-overflow-y-auto tw-p-6 tw-relative tw-border tw-border-gray-300"
            x-transition:enter="tw-transition tw-ease-out tw-duration-200 tw-transform"
            x-transition:enter-start="tw-opacity-0 tw-scale-95"
            x-transition:enter-end="tw-opacity-100 tw-scale-100"
            x-transition:leave="tw-transition tw-ease-in tw-duration-150 tw-transform"
            x-transition:leave-start="tw-opacity-100 tw-scale-100"
            x-transition:leave-end="tw-opacity-0 tw-scale-95">
            <!-- Close Button -->
            <button type="button" @click="closeAddBrand()"
                class="tw-absolute tw-top-3 tw-right-3 tw-text-gray-600 hover:tw-text-black tw-text-xl">
                <i class="ri-close-line"></i>
            </button>

            <h2 class="tw-text-lg tw-font-bold tw-text-[#1AA24C] tw-flex tw-items-center tw-gap-2">
                <i class="ri-add-circle-line tw-text-2xl"></i>
                Thêm thương hiệu mới
            </h2>

            <form x-ref="brandForm" action="{{ route('brands.store') }}" method="POST" enctype="multipart/form-data" class="tw-mt-4 tw-space-y-4">
                @csrf
                <div>
                    <label for="brand_name" class="tw-text-sm tw-font-medium">Tên thương hiệu <span class="tw-text-red-500">*</span></label>
                    <input type="text" id="brand_name" name="name" required value="{{ old('name') }}" x-ref="brandNameInput"
                        class="tw-w-full tw-p-2 tw-border tw-rounded-lg tw-mt-1 focus:tw-border-[#1AA24C] focus:tw-ring-1 focus:tw-ring-[#1AA24C] tw-outline-none {{ $errors->has('name') ? 'tw-border-red-500' : 'tw-border-gray-300' }}"
                        placeholder="Nhập tên thương hiệu">
                    @error('name')
                        <p class="tw-text-red-500 tw-text-sm tw-mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="brand_industry" class="tw-text-sm tw-font-medium">Ngành nghề <span class="tw-text-red-500">*</span></label>
                    <input type="text" id="brand_industry" name="industry" required value="{{ old('industry') }}"
                        class="tw-w-full tw-p-2 tw-border tw-rounded-lg tw-mt-1 focus:tw-border-[#1AA24C] focus:tw-ring-1 focus:tw-ring-[#1AA24C] tw-outline-none {{ $errors->has('industry') ? 'tw-border-red-500' : 'tw-border-gray-300' }}"
                        placeholder="Nhập ngành nghề">
                    @error('industry')
                        <p class="tw-text-red-500 tw-text-sm tw-mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="brand_target_market" class="tw-text-sm tw-font-medium">Thị trường mục tiêu <span class="tw-text-red-500">*</span></label>
                    <input type="text" id="brand_target_market" name="target_market" required value="{{ old('target_market') }}"
                        class="tw-w-full tw-p-2 tw-border tw-rounded-lg tw-mt-1 focus:tw-border-[#1AA24C] focus:tw-ring-1 focus:tw-ring-[#1AA24C] tw-outline-none {{ $errors->has('target_market') ? 'tw-border-red-500' : 'tw-border-gray-300' }}"
                        placeholder="Nhập thị trường mục tiêu">
                    @error('target_market')
                        <p class="tw-text-red-500 tw-text-sm tw-mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="brand_founded_year" class="tw-text-sm tw-font-medium">Năm thành lập <span class="tw-text-red-500">*</span></label>
                    <input type="number" id="brand_founded_year" name="founded_year" required min="1900" max="{{ date('Y') }}" value="{{ old('founded_year') }}"
                        class="tw-w-full tw-p-2 tw-border tw-rounded-lg tw-mt-1 focus:tw-border-[#1AA24C] focus:tw-ring-1 focus:tw-ring-[#1AA24C] tw-outline-none {{ $errors->has('founded_year') ? 'tw-border-red-500' : 'tw-border-gray-300' }}"
                        placeholder="Nhập năm thành lập">
                    @error('founded_year')
                        <p class="tw-text-red-500 tw-text-sm tw-mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="brand_description" class="tw-text-sm tw-font-medium">Mô tả <span class="tw-text-red-500">*</span></label>
                    <textarea id="brand_description" name="description" required rows="3"
                        class="tw-w-full tw-p-2 tw-border tw-rounded-lg tw-mt-1 focus:tw-border-[#1AA24C] focus:tw-ring-1 focus:tw-ring-[#1AA24C] tw-outline-none tw-resize-none {{ $errors->has('description') ? 'tw-border-red-500' : 'tw-border-gray-300' }}"
                        placeholder="Nhập mô tả">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="tw-text-red-500 tw-text-sm tw-mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="tw-text-sm tw-font-medium">Logo thương hiệu <span class="tw-text-red-500">*</span></label>
                    <div class="tw-mt-1">
                        <div class="tw-relative tw-w-full tw-h-32 tw-border-2 tw-border-dashed tw-rounded-lg tw-cursor-pointer tw-bg-gray-50 hover:tw-bg-gray-100 tw-transition {{ $errors->has('logo') ? 'tw-border-red-500' : 'tw-border-gray-300' }}" @click="$refs.logoInput.click()">
                            <!-- Preview khi đã chọn file -->
                            <div x-show="logoPreview" x-cloak class="tw-absolute tw-inset-0 tw-flex tw-items-center tw-justify-center">
                                <img :src="logoPreview" class="tw-max-h-28 tw-max-w-full tw-object-contain" alt="Preview">
                                <button type="button" @click.stop="clearLogoPreview()"
                                    class="tw-absolute tw-top-1 tw-right-1 tw-bg-red-500 tw-text-white tw-rounded-full tw-w-6 tw-h-6 tw-flex tw-items-center tw-justify-center tw-text-xs hover:tw-bg-red-600">
                                    <i class="ri-close-line"></i>
                                </button>
                            </div>
                            <!-- Placeholder khi chưa chọn file -->
                            <div x-show="!logoPreview" class="tw-flex tw-flex-col tw-items-center tw-justify-center tw-h-full">
                                <i class="ri-upload-cloud-2-line tw-text-3xl tw-text-gray-400 tw-mb-2"></i>
                                <p class="tw-text-sm tw-text-gray-500">Nhấn để chọn hoặc kéo thả</p>
                                <p class="tw-text-xs tw-text-gray-400">PNG, JPG, GIF (tối đa 2MB)</p>
                            </div>
                        </div>
                        <input type="file" name="logo" accept="image/*" required class="tw-hidden" x-ref="logoInput" @change="previewLogo($event)">
                        @error('logo')
                            <p class="tw-text-red-500 tw-text-sm tw-mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <button type="submit"
                    class="tw-w-full tw-bg-[#1AA24C] tw-text-white tw-font-semibold tw-py-2.5 tw-rounded-lg tw-mt-2 hover:tw-bg-[#148a3f] tw-transition tw-flex tw-items-center tw-justify-center tw-gap-2">
                    Thêm ngay
                </button>
            </form>
        </div>
    </div>
    </main>

    @if ($errors->any())
    <script>
        // Auto-open modal khi có validation errors
        setTimeout(() => {
            const alpineElement = document.querySelector('[x-data]');
            if (alpineElement && alpineElement.__x) {
                alpineElement.__x.$data.addBrandModal = true;
            }
        }, 100);
    </script>
    @endif
    </div>
</x-app-layout>
