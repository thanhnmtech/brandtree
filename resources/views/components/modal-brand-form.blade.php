@props(['brand' => null, 'mode' => 'create'])

@php
    $isEdit = $mode === 'edit' && $brand;
    $modalState = $isEdit ? 'editBrandModal' : 'addBrandModal';
    $title = $isEdit ? 'Cập nhật thương hiệu' : 'Thêm thương hiệu mới';
    $icon = $isEdit ? 'ri-edit-line' : 'ri-add-circle-line';
    $submitText = $isEdit ? 'Cập nhật' : 'Thêm ngay';
    $submitIcon = $isEdit ? 'ri-save-line' : '';
    $action = $isEdit ? route('brands.update', $brand) : route('brands.store');
@endphp

<!-- ================= BRAND FORM POPUP ================= -->
<x-modal-transition type="overlay"
    x-show="{{ $modalState }}"
    x-cloak
    class="tw-fixed tw-inset-0 tw-bg-black/40 tw-backdrop-blur-sm tw-flex tw-items-center tw-justify-center tw-z-[9999]"
    @click.self="close{{ $isEdit ? 'Edit' : 'Add' }}Brand()">
    <x-modal-transition type="modal"
        class="tw-bg-white tw-rounded-2xl tw-w-[600px] tw-max-w-[90%] tw-overflow-y-auto tw-p-6 tw-relative tw-border tw-border-gray-300"
        @click.stop>
        <!-- Close Button -->
        <button type="button" @click="close{{ $isEdit ? 'Edit' : 'Add' }}Brand()"
            class="tw-absolute tw-top-3 tw-right-3 tw-text-gray-600 hover:tw-text-black tw-text-xl">
            <i class="ri-close-line"></i>
        </button>

        <h2 class="tw-text-lg tw-font-bold tw-text-[#1AA24C] tw-flex tw-items-center tw-gap-2">
            <i class="{{ $icon }} tw-text-2xl"></i>
            {{ $title }}
        </h2>

        <form x-ref="brandForm" action="{{ $action }}" method="POST" enctype="multipart/form-data" class="tw-mt-4 tw-space-y-4">
            @csrf
            @if($isEdit)
                @method('PUT')
            @endif

            <!-- Hidden field để track modal mode khi có validation error -->
            <input type="hidden" name="_brand_modal_mode" value="{{ $mode }}">

            <!-- Tên thương hiệu -->
            <div>
                <label for="brand_name" class="tw-text-sm tw-font-medium">Tên thương hiệu <span class="tw-text-red-500">*</span></label>
                <input type="text" id="brand_name" name="name" required
                    value="{{ old('name', $brand->name ?? '') }}" x-ref="brandNameInput"
                    class="tw-w-full tw-p-2 tw-border tw-rounded-lg tw-mt-1 focus:tw-border-[#1AA24C] focus:tw-ring-1 focus:tw-ring-[#1AA24C] tw-outline-none {{ $errors->has('name') ? 'tw-border-red-500' : 'tw-border-gray-300' }}"
                    placeholder="Nhập tên thương hiệu">
                @error('name')
                    <p class="tw-text-red-500 tw-text-sm tw-mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Ngành nghề -->
            <div>
                <label for="brand_industry" class="tw-text-sm tw-font-medium">
                    Ngành nghề
                    @if(!$isEdit)<span class="tw-text-red-500">*</span>@endif
                </label>
                <input type="text" id="brand_industry" name="industry" {{ !$isEdit ? 'required' : '' }}
                    value="{{ old('industry', $brand->industry ?? '') }}"
                    class="tw-w-full tw-p-2 tw-border tw-rounded-lg tw-mt-1 focus:tw-border-[#1AA24C] focus:tw-ring-1 focus:tw-ring-[#1AA24C] tw-outline-none {{ $errors->has('industry') ? 'tw-border-red-500' : 'tw-border-gray-300' }}"
                    placeholder="Nhập ngành nghề">
                @error('industry')
                    <p class="tw-text-red-500 tw-text-sm tw-mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Thị trường mục tiêu -->
            <div>
                <label for="brand_target_market" class="tw-text-sm tw-font-medium">
                    Thị trường mục tiêu
                    @if(!$isEdit)<span class="tw-text-red-500">*</span>@endif
                </label>
                <input type="text" id="brand_target_market" name="target_market" {{ !$isEdit ? 'required' : '' }}
                    value="{{ old('target_market', $brand->target_market ?? '') }}"
                    class="tw-w-full tw-p-2 tw-border tw-rounded-lg tw-mt-1 focus:tw-border-[#1AA24C] focus:tw-ring-1 focus:tw-ring-[#1AA24C] tw-outline-none {{ $errors->has('target_market') ? 'tw-border-red-500' : 'tw-border-gray-300' }}"
                    placeholder="Nhập thị trường mục tiêu">
                @error('target_market')
                    <p class="tw-text-red-500 tw-text-sm tw-mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Năm thành lập -->
            <div>
                <label for="brand_founded_year" class="tw-text-sm tw-font-medium">
                    Năm thành lập
                    @if(!$isEdit)<span class="tw-text-red-500">*</span>@endif
                </label>
                <input type="number" id="brand_founded_year" name="founded_year" {{ !$isEdit ? 'required' : '' }}
                    min="1900" max="{{ date('Y') }}" value="{{ old('founded_year', $brand->founded_year ?? '') }}"
                    class="tw-w-full tw-p-2 tw-border tw-rounded-lg tw-mt-1 focus:tw-border-[#1AA24C] focus:tw-ring-1 focus:tw-ring-[#1AA24C] tw-outline-none {{ $errors->has('founded_year') ? 'tw-border-red-500' : 'tw-border-gray-300' }}"
                    placeholder="Nhập năm thành lập">
                @error('founded_year')
                    <p class="tw-text-red-500 tw-text-sm tw-mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Mô tả -->
            <div>
                <label for="brand_description" class="tw-text-sm tw-font-medium">
                    Mô tả
                    @if(!$isEdit)<span class="tw-text-red-500">*</span>@endif
                </label>
                <textarea id="brand_description" name="description" {{ !$isEdit ? 'required' : '' }} rows="3"
                    class="tw-w-full tw-p-2 tw-border tw-rounded-lg tw-mt-1 focus:tw-border-[#1AA24C] focus:tw-ring-1 focus:tw-ring-[#1AA24C] tw-outline-none tw-resize-none {{ $errors->has('description') ? 'tw-border-red-500' : 'tw-border-gray-300' }}"
                    placeholder="Nhập mô tả">{{ old('description', $brand->description ?? '') }}</textarea>
                @error('description')
                    <p class="tw-text-red-500 tw-text-sm tw-mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Logo -->
            <div>
                <label class="tw-text-sm tw-font-medium">
                    Logo thương hiệu
                    @if(!$isEdit)<span class="tw-text-red-500">*</span>@endif
                </label>
                <div class="tw-mt-1">
                    <div class="tw-relative tw-w-full tw-h-32 tw-border-2 tw-border-dashed tw-rounded-lg tw-cursor-pointer tw-bg-gray-50 hover:tw-bg-gray-100 tw-transition {{ $errors->has('logo') ? 'tw-border-red-500' : 'tw-border-gray-300' }}" @click="$refs.logoInput.click()">
                        <!-- Preview khi đã chọn file -->
                        <div x-show="logoPreview" x-cloak class="tw-absolute tw-inset-0 tw-flex tw-items-center tw-justify-center">
                            <img :src="logoPreview" class="tw-max-h-28 tw-max-w-full tw-object-contain" alt="Preview">
                            <button type="button" @click.stop="{{ $isEdit ? 'logoPreview = null; $refs.logoInput.value = \'\'' : 'clearLogoPreview()' }}"
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
                    <input type="file" name="logo" accept="image/*" {{ !$isEdit ? 'required' : '' }} class="tw-hidden" x-ref="logoInput" @change="previewLogo($event)">
                    @error('logo')
                        <p class="tw-text-red-500 tw-text-sm tw-mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Submit Button -->
            <button type="submit"
                class="tw-w-full tw-bg-[#1AA24C] tw-text-white tw-font-semibold tw-py-2.5 tw-rounded-lg tw-mt-2 hover:tw-bg-[#148a3f] tw-transition tw-flex tw-items-center tw-justify-center tw-gap-2">
                @if($submitIcon)
                    <i class="{{ $submitIcon }}"></i>
                @endif
                {{ $submitText }}
            </button>
        </form>
    </x-modal-transition>
</x-modal-transition>
