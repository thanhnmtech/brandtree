<x-app-layout>
    @php
        $rootData = $brand->root_data ?? [];
        $trunkData = $brand->trunk_data ?? [];
        // Chuẩn bị dữ liệu cho controller result-modal
        $initialData = [
            'root1' => $rootData['root1'] ?? '',
            'root2' => $rootData['root2'] ?? '',
            'root3' => $rootData['root3'] ?? '',
            'trunk1' => $trunkData['trunk1'] ?? '',
            'trunk2' => $trunkData['trunk2'] ?? '',
        ];
    @endphp
    <div data-controller="brand-form result-modal" 
        data-brand-form-has-errors-value="{{ $errors->any() ? 'true' : 'false' }}"
        data-brand-form-modal-mode-value="{{ old('_brand_modal_mode') }}"
        data-brand-form-brand-logo-value="{{ $brand->logo_path ? Storage::url($brand->logo_path) : '' }}"
        data-brand-form-brand-name-value="{{ $brand->name }}"
        data-brand-form-delete-url-value="{{ route('brands.destroy', $brand) }}"
        data-brand-form-redirect-url-value="{{ route('dashboard') }}"
        data-result-modal-brand-slug-value="{{ $brand->slug }}"
        data-result-modal-url-value="{{ route('brands.show', $brand->slug) }}"
        data-result-modal-data-value='@json($initialData)'>
        <main class="tw-mt-[36px] tw-flex tw-flex-col tw-gap-10">
            <!-- =================== HERO =================== -->
            <div class="tw-w-full tw-bg-[#F9F8F5] tw-py-6 tw-px-8 tw-flex tw-items-center tw-justify-between">
                <div>
                    <div class="tw-flex tw-items-center tw-gap-2">
                        <h2 class="tw-text-3xl tw-font-bold tw-text-gray-900">
                            {{ $brand->name }}
                        </h2>
                        @if($brand->isAdmin(auth()->user()))
                            <div class="tw-relative" data-controller="dropdown">
                                <i class="ri-more-2-fill tw-text-gray-600 tw-text-2xl tw-cursor-pointer hover:tw-text-gray-800 tw-transition"
                                    data-action="click->dropdown#toggle"></i>

                                <!-- Dropdown Menu -->
                                <div data-dropdown-target="menu"
                                    class="tw-hidden tw-absolute tw-left-0 tw-top-full tw-mt-2 tw-w-64 tw-bg-white tw-rounded-lg tw-shadow-lg tw-border tw-border-gray-200 tw-z-50">
                                    <div class="tw-py-2">
                                        <!-- Menu Items -->
                                        <button data-action="click->brand-form#openEdit click->dropdown#close" type="button"
                                            class="tw-w-full tw-flex tw-items-center tw-gap-3 tw-px-4 tw-py-3 tw-text-gray-700 hover:tw-bg-gray-50 tw-transition">
                                            <i class="ri-edit-line tw-text-lg tw-text-gray-500"></i>
                                            <span class="tw-text-sm tw-font-medium">{{ __('messages.brand_show.update_brand') }}</span>
                                        </button>

                                        <a href="{{ route('brands.subscription.show', $brand) }}"
                                            class="tw-flex tw-items-center tw-gap-3 tw-px-4 tw-py-3 tw-text-gray-700 hover:tw-bg-gray-50 tw-transition">
                                            <i class="ri-flashlight-line tw-text-lg tw-text-gray-500"></i>
                                            <span class="tw-text-sm tw-font-medium">{{ __('messages.brand_show.manage_plan') }}</span>
                                        </a>

                                        <a href="{{ route('brands.members.index', $brand) }}"
                                            class="tw-flex tw-items-center tw-gap-3 tw-px-4 tw-py-3 tw-text-gray-700 hover:tw-bg-gray-50 tw-transition">
                                            <i class="ri-group-line tw-text-lg tw-text-gray-500"></i>
                                            <span class="tw-text-sm tw-font-medium">{{ __('messages.brand_show.manage_members') }}</span>
                                        </a>

                                        <a href="{{ route('brands.payments.index', $brand) }}"
                                            class="tw-flex tw-items-center tw-gap-3 tw-px-4 tw-py-3 tw-text-gray-700 hover:tw-bg-gray-50 tw-transition">
                                            <i class="ri-bank-card-line tw-text-lg tw-text-gray-500"></i>
                                            <span class="tw-text-sm tw-font-medium">{{ __('messages.brand_show.payment_history') }}</span>
                                        </a>

                                        <a href="{{ route('brands.credits.stats', $brand) }}"
                                            class="tw-flex tw-items-center tw-gap-3 tw-px-4 tw-py-3 tw-text-gray-700 hover:tw-bg-gray-50 tw-transition tw-border-t tw-border-gray-100">
                                            <i class="ri-bar-chart-box-line tw-text-lg tw-text-gray-500"></i>
                                            <span class="tw-text-sm tw-font-medium">{{ __('messages.brand_show.energy_stats') }}</span>
                                        </a>

                                        <button data-action="click->brand-form#openDelete click->dropdown#close"
                                            type="button"
                                            class="tw-w-full tw-flex tw-items-center tw-gap-3 tw-px-4 tw-py-3 tw-text-red-600 hover:tw-bg-red-50 tw-transition tw-border-t tw-border-gray-100">
                                            <i class="ri-delete-bin-line tw-text-lg"></i>
                                            <span class="tw-text-sm tw-font-medium">{{ __('messages.brand_show.delete_brand') }}</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endif
                        {{-- <i class="ri-arrow-down-s-line tw-text-gray-500 tw-text-2xl tw-cursor-pointer"
                            id="brandSwitcherBtn"></i> --}}

                    </div>
                    <p class="tw-text-md tw-text-gray-500 tw-mt-1">
                        {{ __('messages.brand_show.founded_year') }}: {{ $brand->founded_year }}
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
                            <circle cx="60" cy="60" r="52" stroke="#1AA24C" stroke-width="8" stroke-linecap="round"
                                fill="none" stroke-dasharray="326" stroke-dashoffset="153" />
                        </svg>

                        <div class="tw-absolute tw-inset-0 tw-flex tw-items-center tw-justify-center">
                            <div class="tw-text-center">
                                <p class="tw-text-3xl tw-font-bold tw-text-[#1AA24C]">53%</p>
                                <p class="tw-text-sm tw-text-gray-500 tw-mt-1">{{ __('messages.brand_show.completed') }}</p>
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
                        <div
                            class="tw-h-9 tw-w-9 tw-rounded-md tw-bg-[#E7F4EF] tw-flex tw-items-center tw-justify-center">
                            <i class="ri-building-2-line tw-text-[#3A8F6E] tw-text-3xl"></i>
                        </div>
                        <div>
                            <p class="tw-text tw-text-gray-500 tw-font-medium">{{ __('messages.brand_show.industry') }}</p>
                            <p class="tw-text-lg tw-text-gray-800 tw-font-semibold tw-mt-1">
                                {{ $brand->industry }}
                            </p>
                        </div>
                    </div>

                    <!-- CARD 3 -->
                    <div
                        class="tw-bg-white tw-rounded-xl tw-border tw-border-[#E4ECE8] tw-p-5 tw-shadow-sm tw-flex tw-items-start tw-gap-3">
                        <div
                            class="tw-h-9 tw-w-9 tw-rounded-md tw-bg-[#F5F3ED] tw-flex tw-items-center tw-justify-center">
                            <i class="ri-group-line tw-text-[#C4A676] tw-text-3xl"></i>
                        </div>
                        <div>
                            <p class="tw-text tw-text-gray-500 tw-font-medium">
                                {{ __('messages.brand_show.target_market') }}
                            </p>
                            <p class="tw-text-lg tw-text-gray-800 tw-font-semibold tw-mt-1">
                                {{ $brand->target_market }}
                            </p>
                        </div>
                    </div>
                    <a href="{{ route('brands.subscription.show', $brand) }}"
                        class="tw-bg-white tw-rounded-xl tw-border tw-border-[#E4ECE8] tw-p-5 tw-shadow-sm tw-flex tw-items-start tw-gap-3">
                        <div
                            class="tw-h-9 tw-w-9 tw-rounded-md tw-bg-[#E7F4EF] tw-flex tw-items-center tw-justify-center">
                            <i class="ri-flashlight-line tw-text-[#3A8F6E] tw-text-3xl"></i>
                        </div>
                        <div>
                            <p class="tw-text tw-text-gray-500 tw-font-medium">
                                {{ __('messages.brand_show.energy') }}
                            </p>
                            <p class="tw-text-lg tw-text-gray-800 tw-font-semibold tw-mt-1">
                                {{ $brand->credits_remaining }} / {{ $brand->total_credits }}
                            </p>
                        </div>
                    </a>

                    <!-- CARD 4 -->
                    <a href="{{ route('brands.members.index', $brand) }}"
                        class="tw-bg-white tw-rounded-xl tw-border tw-border-[#E4ECE8] tw-p-5 tw-shadow-sm tw-flex tw-items-start tw-gap-3">
                        <div
                            class="tw-h-9 tw-w-9 tw-rounded-md tw-bg-[#FFF4E8] tw-flex tw-items-center tw-justify-center">
                            <i class="ri-group-line tw-text-[#E3A65A] tw-text-3xl"></i>
                        </div>
                        <div>
                            <p class="tw-text tw-text-gray-500 tw-font-medium">
                                {{ __('messages.brand_show.members') }}
                            </p>
                            <p class="tw-text-lg tw-text-gray-800 tw-font-semibold tw-mt-1">
                                {{ $brand->members->count() }}
                            </p>
                        </div>
                    </a>
                </div>
            </section>

            <!-- =================== BRAND INFO =================== -->
            @include('brands.partials.brand-info', ['brand' => $brand])

            <!-- =================== PROGRESS HEADER =================== -->
            <section class="tw-px-8">
                {{-- Container cho Progress Header, có thể reload qua AJAX --}}
                <div data-result-modal-target="progressContainer">
                    @include('brands.partials.progress-header', ['brand' => $brand, 'phases' => $phases])
                </div>
            </section>

            <!-- =================== BRAND OVERVIEW =================== -->
            <section class="tw-px-8">
                <div
                    class="tw-w-full tw-bg-[linear-gradient(96deg,#F5FBF7_0.31%,#FCF9F3_50%,#F2F7F0_99.69%)] tw-border tw-border-[#E0EAE6] tw-rounded-xl tw-p-8 tw-shadow-sm tw-flex tw-flex-col md:tw-flex-row tw-gap-10 tw-items-center">
                    <!-- LEFT -->
                    <div class="tw-flex-1 tw-w-full tw-space-y-6">
                        <div>
                            <h2 class="tw-text-2xl tw-font-bold tw-flex tw-items-center tw-gap-2">
                                <i class="ri-bar-chart-line tw-text-[#1AA24C] tw-text-2xl"></i>
                                {{ __('messages.brand_show.brand_overview') }}
                            </h2>
                            <p class="tw-text-sm tw-text-gray-500 tw-mt-1">
                                {{ __('messages.brand_show.brand_overview_desc') }}
                            </p>
                        </div>

                        <div>
                            <div class="tw-flex tw-justify-between tw-text-sm tw-text-gray-700">
                                <span class="tw-flex tw-items-center tw-gap-1">
                                    <img src="{{ asset('assets/img/icon-dinhvi-green.svg') }}" alt="ok"
                                        class="tw-w-[14px] tw-h-[14px]" />
                                    {{ __('messages.brand_show.positioning') }}
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
                                    {{ __('messages.brand_show.identity') }}
                                </span>
                                <span class="tw-text-[#F59F0A] tw-font-semibold">60%</span>
                            </div>
                            <div class="tw-w-full tw-h-2 tw-rounded-full tw-bg-[#E8EDEB] tw-overflow-hidden tw-mt-1">
                                <div class="tw-h-full tw-bg-[#F59F0A]" style="width: 60%"></div>
                            </div>
                        </div>

                        <div
                            class="tw-w-full tw-bg-[linear-gradient(90deg,#E7F2E7_0%,#F8F1E0_100%)] tw-border tw-border-[#E0EAE6] tw-rounded-lg tw-py-5 tw-px-10 tw-shadow-sm tw-flex tw-items-center tw-justify-between">
                            <div>
                                <p class="tw-text-gray-500 tw-text-md">{{ __('messages.brand_show.overall_score') }}</p>
                                <p class="tw-text-3xl tw-font-bold tw-text-[#1AA24C]">
                                    53 <span class="tw-text-gray-400 tw-text-xl">/100</span>
                                </p>
                            </div>
                            <span
                                class="tw-inline-block tw-mt-2 tw-text-sm tw-font-semibold tw-bg-vlbcgreen tw-text-white tw-px-3 tw-py-1 tw-rounded-full">
                                {{ __('messages.brand_show.developing') }}
                            </span>
                        </div>
                    </div>

                    <!-- RIGHT -->
                    <div class="tw-flex tw-items-center tw-justify-center tw-w-full md:tw-w-[280px]">
                        <div class="tw-relative tw-w-[200px] tw-h-[200px]">
                            <svg class="tw-w-full tw-h-full" viewBox="0 0 120 120">
                                <circle cx="60" cy="60" r="52" stroke="#E8EDEB" stroke-width="8" fill="none" />
                            </svg>

                            <svg class="tw-w-full tw-h-full tw-absolute tw-top-0 tw-left-0 tw-rotate-[-90deg]"
                                viewBox="0 0 120 120">
                                <circle cx="60" cy="60" r="52" stroke="#1AA24C" stroke-width="8" stroke-linecap="round"
                                    fill="none" stroke-dasharray="326" stroke-dashoffset="153" />
                            </svg>

                            <div class="tw-absolute tw-inset-0 tw-flex tw-items-center tw-justify-center">
                                <div class="tw-text-center">
                                    <p class="tw-text-3xl tw-font-bold tw-text-[#1AA24C]">53%</p>
                                    <p class="tw-text-sm tw-text-gray-500 tw-mt-1">{{ __('messages.brand_show.completion') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <!-- =================== KẾT QUẢ CHIẾN LƯỢC =================== -->
            <section class="tw-px-8 tw-space-y-6">
                <h2 class="tw-text-2xl tw-font-bold tw-text-gray-800">
                    {{ __('messages.brand_show.strategy_results') }}
                </h2>

                <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-3 tw-gap-5">
                    <!-- CARD 1 — Active -->
                    <div
                        class="tw-bg-white tw-rounded-xl tw-border-2 tw-border-[#1AA24C] tw-shadow-sm tw-p-6 tw-transition">
                        <div class="tw-flex tw-items-start tw-gap-3">
                            <div
                                class="tw-h-10 tw-w-10 tw-rounded-full tw-bg-[#E6F6EC] tw-flex tw-items-center tw-justify-center">
                                <i class="ri-disc-line tw-text-[#1AA24C] tw-text-xl"></i>
                            </div>

                            <div class="tw-flex-1">
                                <h3 class="tw-text-lg tw-font-semibold tw-text-gray-800">
                                    {{ __('messages.brand_show.authentic_foundation') }}
                                </h3>
                                <p class="tw-text-sm tw-text-gray-500 tw-mt-1">
                                    {{ __('messages.brand_show.core_values_defined') }}
                                </p>

                                <div class="tw-flex tw-gap-2 tw-flex-wrap tw-mt-3">
                                    <span
                                        class="tw-bg-[#E6F6EC] tw-text-[#1AA24C] tw-text-xs tw-font-semibold tw-px-3 tw-py-1 tw-rounded-full">
                                        {{ __('messages.brand_show.quality') }}
                                    </span>
                                    <span
                                        class="tw-bg-[#E6F6EC] tw-text-[#1AA24C] tw-text-xs tw-font-semibold tw-px-3 tw-py-1 tw-rounded-full">
                                        {{ __('messages.brand_show.dedication') }}
                                    </span>
                                    <span
                                        class="tw-bg-[#E6F6EC] tw-text-[#1AA24C] tw-text-xs tw-font-semibold tw-px-3 tw-py-1 tw-rounded-full">
                                        {{ __('messages.brand_show.innovation') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- CARD 2 — Locked -->
                    <div
                        class="tw-bg-white tw-rounded-xl tw-border tw-border-[#E4ECE8] tw-shadow-sm tw-p-6 tw-opacity-70">
                        <div class="tw-flex tw-items-start tw-gap-3">
                            <div
                                class="tw-h-10 tw-w-10 tw-rounded-full tw-bg-[#FFF5E6] tw-flex tw-items-center tw-justify-center">
                                <i class="ri-focus-3-line tw-text-[#E3A65A] tw-text-xl"></i>
                            </div>

                            <div class="tw-flex-1">
                                <h3 class="tw-text-lg tw-font-semibold tw-text-gray-500">
                                    {{ __('messages.brand_show.consistent_identity') }}
                                </h3>
                                <p class="tw-text-sm tw-text-gray-400 tw-mt-1">
                                    {{ __('messages.brand_show.trunk_not_completed') }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- CARD 3 — Locked -->
                    <div
                        class="tw-bg-white tw-rounded-xl tw-border tw-border-[#E4ECE8] tw-shadow-sm tw-p-6 tw-opacity-60">
                        <div class="tw-flex tw-items-start tw-gap-3">
                            <div
                                class="tw-h-10 tw-w-10 tw-rounded-full tw-bg-[#EAF5F1] tw-flex tw-items-center tw-justify-center">
                                <i class="ri-heart-pulse-line tw-text-[#87B2A1] tw-text-xl"></i>
                            </div>

                            <div class="tw-flex-1">
                                <h3 class="tw-text-lg tw-font-semibold tw-text-gray-500">
                                    {{ __('messages.brand_show.brand_health') }}
                                </h3>
                                <p class="tw-text-sm tw-text-gray-400 tw-mt-1">
                                    {{ __('messages.brand_show.canopy_not_started') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <!-- =================== BƯỚC TIẾP THEO =================== -->
            <div data-result-modal-target="nextStepContainer">
                @include('brands.partials.next-step', ['brand' => $brand, 'phases' => $phases])
            </div>

            <!-- Edit Brand Modal Component -->
            <x-modal-brand-form :brand="$brand" mode="edit" />

            <!-- Delete Brand Confirmation Modal -->
            <div data-brand-form-target="deleteModal"
                class="tw-hidden tw-fixed tw-inset-0 tw-bg-black tw-bg-opacity-50 tw-z-50 tw-p-4" style="display: none;"
                data-action="click->brand-form#closeOnBackdrop">
                <div class="tw-bg-white tw-rounded-2xl tw-shadow-2xl tw-max-w-md tw-w-full tw-p-6"
                    data-action="click->brand-form#stopPropagation">
                    <!-- Header -->
                    <div class="tw-flex tw-items-start tw-justify-between tw-mb-6">
                        <div class="tw-flex tw-items-center tw-gap-3">
                            <div
                                class="tw-h-12 tw-w-12 tw-rounded-full tw-bg-red-100 tw-flex tw-items-center tw-justify-center">
                                <i class="ri-delete-bin-line tw-text-red-600 tw-text-2xl"></i>
                            </div>
                            <div>
                                <h3 class="tw-text-xl tw-font-bold tw-text-gray-900">Xóa thương hiệu</h3>
                                <p class="tw-text-sm tw-text-gray-500">Hành động này không thể hoàn tác</p>
                            </div>
                        </div>
                        <button data-action="click->brand-form#closeDelete" type="button"
                            class="tw-text-gray-400 hover:tw-text-gray-600 tw-transition">
                            <i class="ri-close-line tw-text-2xl"></i>
                        </button>
                    </div>

                    <!-- Warning Message -->
                    <div class="tw-bg-red-50 tw-border tw-border-red-200 tw-rounded-lg tw-p-4 tw-mb-6">
                        <p class="tw-text-sm tw-text-red-800">
                            <i class="ri-alert-line tw-mr-2"></i>
                            Bạn sắp xóa thương hiệu <strong>{{ $brand->name }}</strong>. Tất cả dữ liệu liên quan sẽ bị
                            xóa vĩnh viễn.
                        </p>
                    </div>

                    <!-- Confirmation Input -->
                    <div class="tw-mb-6">
                        <label class="tw-block tw-text-sm tw-font-medium tw-text-gray-700 tw-mb-2">
                            Để xác nhận, vui lòng nhập tên thương hiệu: <strong
                                class="tw-text-red-600">{{ $brand->name }}</strong>
                        </label>
                        <input data-brand-form-target="deleteConfirmInput"
                            data-action="input->brand-form#updateDeleteConfirmName" type="text"
                            class="tw-w-full tw-px-4 tw-py-3 tw-border tw-border-gray-300 tw-rounded-lg focus:tw-ring-2 focus:tw-ring-red-500 focus:tw-border-transparent tw-transition"
                            placeholder="Nhập tên thương hiệu">
                    </div>

                    <!-- Actions -->
                    <div class="tw-flex tw-gap-3">
                        <button data-action="click->brand-form#closeDelete" type="button"
                            class="tw-flex-1 tw-px-4 tw-py-3 tw-bg-gray-100 tw-text-gray-700 tw-font-medium tw-rounded-lg hover:tw-bg-gray-200 tw-transition disabled:tw-opacity-50">
                            Hủy
                        </button>
                        <button data-action="click->brand-form#submitDeleteBrand" type="button"
                            class="tw-flex-1 tw-px-4 tw-py-3 tw-bg-red-600 tw-text-white tw-font-medium tw-rounded-lg hover:tw-bg-red-700 tw-transition disabled:tw-opacity-50 disabled:tw-cursor-not-allowed tw-flex tw-items-center tw-justify-center tw-gap-2">
                            <span>Xóa thương hiệu</span>
                        </button>
                    </div>
                </div>
            </div>

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