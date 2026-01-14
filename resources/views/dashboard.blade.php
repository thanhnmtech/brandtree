<x-app-layout>
    <div
        data-controller="brand-form brand-filter"
        data-brand-form-has-errors-value="{{ $errors->any() ? 'true' : 'false' }}"
        data-brand-form-modal-mode-value="{{ old('_brand_modal_mode') }}"
        data-brand-filter-url-value="{{ route('dashboard.filter') }}">
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
                <div class="tw-mt-[6px] tw-text-[30px] tw-font-bold tw-text-black" data-stat="total">
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
                <div class="tw-mt-[6px] tw-text-[30px] tw-font-bold tw-text-[#DC282E]" data-stat="seedling">
                    {{ $seedlingCount }}
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
                <div class="tw-mt-[6px] tw-text-[30px] tw-font-bold tw-text-[#F59F0A]" data-stat="growing">
                    {{ $growingCount }}
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
                <div class="tw-mt-[6px] tw-text-[30px] tw-font-bold tw-text-[#1AA24C]" data-stat="completed">
                    {{ $completedCount }}
                </div>
                <div class="tw-mt-[10px] tw-text-[14px] tw-text-[#829B99]">
                    Sẵn sàng để khai thác
                </div>
            </div>
        </div>

        <!-- CONTROLS -->
        <div
            class="tw-flex tw-flex-row tw-items-center tw-gap-[12px] tw-mx-[26px] tw-mt-[13px] tw-mb-0 max-[900px]:tw-flex-col max-[900px]:tw-items-stretch tw-justify-end" id="filter_section">
            <!-- Search -->
            <div class="tw-relative tw-w-[680px] tw-max-w-full">
                <input
                    data-brand-filter-target="search"
                    data-action="input->brand-filter#search"
                    value="{{ $search ?? '' }}"
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
                        data-brand-filter-target="status"
                        data-action="change->brand-filter#filter"
                        class="tw-w-full tw-h-full tw-rounded-[7px] tw-pl-[36px] tw-pr-[28px] tw-text-[14px] tw-text-[#1a1a1a] tw-bg-transparent tw-border-none tw-outline-none tw-appearance-none">
                        <option value="">Tất cả trạng thái</option>
                        <option value="seedling" {{ ($status ?? '') === 'seedling' ? 'selected' : '' }}>Cần chăm sóc</option>
                        <option value="growing" {{ ($status ?? '') === 'growing' ? 'selected' : '' }}>Đang tăng trưởng</option>
                        <option value="completed" {{ ($status ?? '') === 'completed' ? 'selected' : '' }}>Đã hoàn thiện</option>
                    </select>
                    <img src="./assets/img/drop-down-vector.svg" alt="dropdown"
                        class="tw-absolute tw-right-[10px] tw-top-1/2 tw--translate-y-1/2 tw-w-[16px] tw-h-[16px] tw-pointer-events-none" />
                </div>

                <!-- Time filter -->
                <div
                    class="tw-relative tw-h-[44px] tw-w-[190px] tw-rounded-[7px] tw-border-[2px] tw-border-[#E2EBE7] tw-bg-white tw-flex tw-items-center">
                    <select name="order_by"
                        data-brand-filter-target="orderBy"
                        data-action="change->brand-filter#filter"
                        class="tw-h-[44px] tw-w-full tw-h-full tw-rounded-[7px] tw-pl-[12px] tw-pr-[12px] tw-text-[14px] tw-text-[#1a1a1a] tw-bg-transparent tw-border-none tw-outline-none tw-appearance-none">
                        <option value="updated_at" {{ ($orderBy ?? 'updated_at') === 'updated_at' ? 'selected' : '' }}>Cập nhật gần nhất</option>
                        <option value="created_at" {{ ($orderBy ?? '') === 'created_at' ? 'selected' : '' }}>Mới nhất</option>
                    </select>
                    <img src="./assets/img/drop-down-vector.svg" alt="dropdown"
                        class="tw-absolute tw-right-[10px] tw-top-1/2 tw--translate-y-1/2 tw-w-[16px] tw-h-[16px] tw-pointer-events-none" />
                </div>
            </div>

            <!-- Button -->
            <div class="tw-relative">
                <button data-action="click->brand-form#openAdd"
                    class="tw-h-[44px] tw-w-[235px] tw-px-4 tw-rounded-[7px] tw-bg-[linear-gradient(180deg,#34b269_0%,#78d29e_100%)] tw-text-white tw-font-light tw-text-sm tw-shadow-sm tw-whitespace-nowrap hover:tw-scale-[1.02] hover:tw-shadow-md tw-transition">
                    <img src="./assets/img/add-vector.svg" alt="add"
                        class="tw-absolute tw-left-[12px] tw-top-1/2 -tw-translate-y-1/2 tw-w-[16px] tw-h-[16px] tw-pointer-events-none" />
                    <div>Thêm thương hiệu</div>
                </button>
            </div>
        </div>
    </section>

    <!-- CARDS -->
    <section class="tw-p-[26px] tw-relative">
        <!-- Loading indicator -->
        <div data-brand-filter-target="loading" class="tw-hidden tw-absolute tw-inset-0 tw-items-center tw-justify-center tw-bg-white/50 tw-z-10" style="display: none;">
            <div class="tw-animate-spin tw-rounded-full tw-h-8 tw-w-8 tw-border-b-2 tw-border-[#269063]"></div>
        </div>

        <div data-brand-filter-target="cardsContainer" class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 lg:tw-grid-cols-3 tw-gap-[20px] tw-transition-opacity">
            @include('partials.brand-cards', ['brands' => $brands])
        </div>
    </section>

    <!-- Add Brand Modal Component -->
    <x-modal-brand-form mode="create" />
    </main>
    </div>
</x-app-layout>
