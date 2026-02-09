@props(['brand'])

{{-- 
    Modal Brand Info với chức năng Inline Edit
    Hiển thị thông tin phân tích thương hiệu từ root_data và trunk_data
    Sử dụng contenteditable để chỉnh sửa trực tiếp trên nội dung
--}}

<div data-brand-info-target="modal_brand_info"
     class="tw-hidden tw-fixed tw-inset-0 tw-z-50 tw-flex tw-items-center tw-justify-center tw-bg-black/50 tw-backdrop-blur-sm tw-mt-[36px]"
     data-action="click->brand-info#closeOnBackdrop">
    
    {{-- Modal Content --}}
    <div class="tw-bg-white tw-rounded-xl tw-shadow-xl tw-w-[90%] md:tw-w-[800px] tw-max-h-[80vh] tw-flex tw-flex-col tw-overflow-hidden">

        {{-- Modal Header --}}
        <div class="tw-px-6 tw-py-4 tw-border-b tw-border-gray-100 tw-flex tw-items-center tw-justify-between tw-bg-gradient-to-r tw-from-[#F5FBF7] tw-to-[#FCF9F3] tw-rounded-t-xl">
            <div class="tw-flex tw-items-center tw-gap-3">
                <div class="tw-h-10 tw-w-10 tw-rounded-full tw-bg-[#E6F6EC] tw-flex tw-items-center tw-justify-center">
                    <i class="ri-information-line tw-text-[#1AA24C] tw-text-xl"></i>
                </div>
                <div>
                    <h3 class="tw-text-xl tw-font-bold tw-text-gray-800">
                        {{ __('messages.brand_show.brand_info') }}
                    </h3>
                    <p class="tw-text-sm tw-text-gray-500">{{ $brand->name }}</p>
                </div>
            </div>

            <button data-action="brand-info#close" 
                class="tw-text-gray-400 hover:tw-text-gray-600 tw-transition">
                <i class="ri-close-line tw-text-2xl"></i>
            </button>
        </div>

        {{-- Modal Body - Scrollable --}}
        <div class="tw-p-6 tw-flex-1 tw-overflow-y-auto">
            <div class="tw-space-y-6">

                {{-- Root1: Văn hoá Dịch vụ --}}
                <div class="tw-bg-[#F9FBF9] tw-border tw-border-[#E8F3EE] tw-rounded-lg tw-p-4">
                    <div class="tw-flex tw-items-center tw-justify-between tw-mb-2">
                        <h5 class="tw-font-semibold tw-text-gray-700 tw-flex tw-items-center tw-gap-2">
                            {{ __('messages.brand_show.culture_design_canvas') }}
                        </h5>
                        {{-- Nút Edit/Save/Cancel --}}
                        <div class="tw-flex tw-items-center tw-gap-2">
                            <button data-brand-info-target="root1EditBtn"
                                    data-action="click->brand-info#toggleEdit"
                                    data-field="root1"
                                    class="tw-flex tw-items-center tw-gap-1 tw-text-gray-500 hover:tw-text-[#1AA24C] tw-transition tw-px-2 tw-py-1 tw-rounded hover:tw-bg-[#E6F6EC]"
                                    title="Chỉnh sửa">
                                <i class="ri-edit-line tw-text-base"></i>
                                <span class="tw-text-xs tw-font-medium">Sửa</span>
                            </button>
                            <button data-brand-info-target="root1SaveBtn"
                                    data-action="click->brand-info#saveEdit"
                                    data-field="root1"
                                    class="tw-hidden tw-flex tw-items-center tw-gap-1 tw-text-[#1AA24C] tw-bg-[#E6F6EC] tw-transition tw-px-2 tw-py-1 tw-rounded hover:tw-bg-[#d0eedc] tw-text-xs tw-font-medium"
                                    title="Lưu">
                                <i class="ri-check-line tw-text-base"></i>
                                <span>Lưu</span>
                            </button>
                            <button data-brand-info-target="root1CancelBtn"
                                    data-action="click->brand-info#cancelEdit"
                                    data-field="root1"
                                    class="tw-hidden tw-flex tw-items-center tw-gap-1 tw-bg-gray-200 tw-text-gray-600 tw-px-3 tw-py-1 tw-rounded tw-text-xs tw-font-medium hover:tw-bg-gray-300 tw-transition"
                                    title="Hủy">
                                <i class="ri-close-line tw-text-base"></i>
                                <span>Hủy</span>
                            </button>
                        </div>
                    </div>
                    {{-- Nội dung - có thể chỉnh sửa trực tiếp --}}
                    <div data-brand-info-target="root1Content" 
                         data-field="root1"
                         class="tw-text-sm tw-text-gray-600 tw-whitespace-pre-wrap tw-min-h-[60px] tw-outline-none">
                        <span class="tw-text-gray-400 tw-italic">{{ __('messages.brand_show.no_data') }}</span>
                    </div>
                </div>

                {{-- Root2: Thổ nhưỡng --}}
                <div class="tw-bg-[#F9FBF9] tw-border tw-border-[#E8F3EE] tw-rounded-lg tw-p-4">
                    <div class="tw-flex tw-items-center tw-justify-between tw-mb-2">
                        <h5 class="tw-font-semibold tw-text-gray-700 tw-flex tw-items-center tw-gap-2">
                            {{ __('messages.brand_show.market_opportunity_analysis') }}
                        </h5>
                        <div class="tw-flex tw-items-center tw-gap-2">
                            <button data-brand-info-target="root2EditBtn"
                                    data-action="click->brand-info#toggleEdit"
                                    data-field="root2"
                                    class="tw-flex tw-items-center tw-gap-1 tw-text-gray-500 hover:tw-text-[#1AA24C] tw-transition tw-px-2 tw-py-1 tw-rounded hover:tw-bg-[#E6F6EC]"
                                    title="Chỉnh sửa">
                                <i class="ri-edit-line tw-text-base"></i>
                                <span class="tw-text-xs tw-font-medium">Sửa</span>
                            </button>
                            <button data-brand-info-target="root2SaveBtn"
                                    data-action="click->brand-info#saveEdit"
                                    data-field="root2"
                                    class="tw-hidden tw-flex tw-items-center tw-gap-1 tw-text-[#1AA24C] tw-bg-[#E6F6EC] tw-transition tw-px-2 tw-py-1 tw-rounded hover:tw-bg-[#d0eedc] tw-text-xs tw-font-medium"
                                    title="Lưu">
                                <i class="ri-check-line tw-text-base"></i>
                                <span>Lưu</span>
                            </button>
                            <button data-brand-info-target="root2CancelBtn"
                                    data-action="click->brand-info#cancelEdit"
                                    data-field="root2"
                                    class="tw-hidden tw-flex tw-items-center tw-gap-1 tw-bg-gray-200 tw-text-gray-600 tw-px-3 tw-py-1 tw-rounded tw-text-xs tw-font-medium hover:tw-bg-gray-300 tw-transition"
                                    title="Hủy">
                                <i class="ri-close-line tw-text-base"></i>
                                <span>Hủy</span>
                            </button>
                        </div>
                    </div>
                    <div data-brand-info-target="root2Content" 
                         data-field="root2"
                         class="tw-text-sm tw-text-gray-600 tw-whitespace-pre-wrap tw-min-h-[60px] tw-outline-none">
                        <span class="tw-text-gray-400 tw-italic">{{ __('messages.brand_show.no_data') }}</span>
                    </div>
                </div>

                {{-- Root3: Giá trị Giải pháp --}}
                <div class="tw-bg-[#F9FBF9] tw-border tw-border-[#E8F3EE] tw-rounded-lg tw-p-4">
                    <div class="tw-flex tw-items-center tw-justify-between tw-mb-2">
                        <h5 class="tw-font-semibold tw-text-gray-700 tw-flex tw-items-center tw-gap-2">
                            {{ __('messages.brand_show.value_proposition_canvas') }}
                        </h5>
                        <div class="tw-flex tw-items-center tw-gap-2">
                            <button data-brand-info-target="root3EditBtn"
                                    data-action="click->brand-info#toggleEdit"
                                    data-field="root3"
                                    class="tw-flex tw-items-center tw-gap-1 tw-text-gray-500 hover:tw-text-[#1AA24C] tw-transition tw-px-2 tw-py-1 tw-rounded hover:tw-bg-[#E6F6EC]"
                                    title="Chỉnh sửa">
                                <i class="ri-edit-line tw-text-base"></i>
                                <span class="tw-text-xs tw-font-medium">Sửa</span>
                            </button>
                            <button data-brand-info-target="root3SaveBtn"
                                    data-action="click->brand-info#saveEdit"
                                    data-field="root3"
                                    class="tw-hidden tw-flex tw-items-center tw-gap-1 tw-text-[#1AA24C] tw-bg-[#E6F6EC] tw-transition tw-px-2 tw-py-1 tw-rounded hover:tw-bg-[#d0eedc] tw-text-xs tw-font-medium"
                                    title="Lưu">
                                <i class="ri-check-line tw-text-base"></i>
                                <span>Lưu</span>
                            </button>
                            <button data-brand-info-target="root3CancelBtn"
                                    data-action="click->brand-info#cancelEdit"
                                    data-field="root3"
                                    class="tw-hidden tw-flex tw-items-center tw-gap-1 tw-bg-gray-200 tw-text-gray-600 tw-px-3 tw-py-1 tw-rounded tw-text-xs tw-font-medium hover:tw-bg-gray-300 tw-transition"
                                    title="Hủy">
                                <i class="ri-close-line tw-text-base"></i>
                                <span>Hủy</span>
                            </button>
                        </div>
                    </div>
                    <div data-brand-info-target="root3Content" 
                         data-field="root3"
                         class="tw-text-sm tw-text-gray-600 tw-whitespace-pre-wrap tw-min-h-[60px] tw-outline-none">
                        <span class="tw-text-gray-400 tw-italic">{{ __('messages.brand_show.no_data') }}</span>
                    </div>
                </div>

                {{-- Trunk1: Định vị Thương hiệu --}}
                <div class="tw-bg-[#F9FBF9] tw-border tw-border-[#E8F3EE] tw-rounded-lg tw-p-4">
                    <div class="tw-flex tw-items-center tw-justify-between tw-mb-2">
                        <h5 class="tw-font-semibold tw-text-gray-700 tw-flex tw-items-center tw-gap-2">
                            {{ __('messages.brand_show.brand_components_canvas') }}
                        </h5>
                        <div class="tw-flex tw-items-center tw-gap-2">
                            <button data-brand-info-target="trunk1EditBtn"
                                    data-action="click->brand-info#toggleEdit"
                                    data-field="trunk1"
                                    class="tw-flex tw-items-center tw-gap-1 tw-text-gray-500 hover:tw-text-[#1AA24C] tw-transition tw-px-2 tw-py-1 tw-rounded hover:tw-bg-[#E6F6EC]"
                                    title="Chỉnh sửa">
                                <i class="ri-edit-line tw-text-base"></i>
                                <span class="tw-text-xs tw-font-medium">Sửa</span>
                            </button>
                            <button data-brand-info-target="trunk1SaveBtn"
                                    data-action="click->brand-info#saveEdit"
                                    data-field="trunk1"
                                    class="tw-hidden tw-flex tw-items-center tw-gap-1 tw-text-[#1AA24C] tw-bg-[#E6F6EC] tw-transition tw-px-2 tw-py-1 tw-rounded hover:tw-bg-[#d0eedc] tw-text-xs tw-font-medium"
                                    title="Lưu">
                                <i class="ri-check-line tw-text-base"></i>
                                <span>Lưu</span>
                            </button>
                            <button data-brand-info-target="trunk1CancelBtn"
                                    data-action="click->brand-info#cancelEdit"
                                    data-field="trunk1"
                                    class="tw-hidden tw-flex tw-items-center tw-gap-1 tw-bg-gray-200 tw-text-gray-600 tw-px-3 tw-py-1 tw-rounded tw-text-xs tw-font-medium hover:tw-bg-gray-300 tw-transition"
                                    title="Hủy">
                                <i class="ri-close-line tw-text-base"></i>
                                <span>Hủy</span>
                            </button>
                        </div>
                    </div>
                    <div data-brand-info-target="trunk1Content" 
                         data-field="trunk1"
                         class="tw-text-sm tw-text-gray-600 tw-whitespace-pre-wrap tw-min-h-[60px] tw-outline-none">
                        <span class="tw-text-gray-400 tw-italic">{{ __('messages.brand_show.no_data') }}</span>
                    </div>
                </div>

                {{-- Trunk2: Nhận diện Ngôn ngữ --}}
                <div class="tw-bg-[#F9FBF9] tw-border tw-border-[#E8F3EE] tw-rounded-lg tw-p-4">
                    <div class="tw-flex tw-items-center tw-justify-between tw-mb-2">
                        <h5 class="tw-font-semibold tw-text-gray-700 tw-flex tw-items-center tw-gap-2">
                            {{ __('messages.brand_show.brand_verbal_identity') }}
                        </h5>
                        <div class="tw-flex tw-items-center tw-gap-2">
                            <button data-brand-info-target="trunk2EditBtn"
                                    data-action="click->brand-info#toggleEdit"
                                    data-field="trunk2"
                                    class="tw-flex tw-items-center tw-gap-1 tw-text-gray-500 hover:tw-text-[#1AA24C] tw-transition tw-px-2 tw-py-1 tw-rounded hover:tw-bg-[#E6F6EC]"
                                    title="Chỉnh sửa">
                                <i class="ri-edit-line tw-text-base"></i>
                                <span class="tw-text-xs tw-font-medium">Sửa</span>
                            </button>
                            <button data-brand-info-target="trunk2SaveBtn"
                                    data-action="click->brand-info#saveEdit"
                                    data-field="trunk2"
                                    class="tw-hidden tw-flex tw-items-center tw-gap-1 tw-text-[#1AA24C] tw-bg-[#E6F6EC] tw-transition tw-px-2 tw-py-1 tw-rounded hover:tw-bg-[#d0eedc] tw-text-xs tw-font-medium"
                                    title="Lưu">
                                <i class="ri-check-line tw-text-base"></i>
                                <span>Lưu</span>
                            </button>
                            <button data-brand-info-target="trunk2CancelBtn"
                                    data-action="click->brand-info#cancelEdit"
                                    data-field="trunk2"
                                    class="tw-hidden tw-flex tw-items-center tw-gap-1 tw-bg-gray-200 tw-text-gray-600 tw-px-3 tw-py-1 tw-rounded tw-text-xs tw-font-medium hover:tw-bg-gray-300 tw-transition"
                                    title="Hủy">
                                <i class="ri-close-line tw-text-base"></i>
                                <span>Hủy</span>
                            </button>
                        </div>
                    </div>
                    <div data-brand-info-target="trunk2Content" 
                         data-field="trunk2"
                         class="tw-text-sm tw-text-gray-600 tw-whitespace-pre-wrap tw-min-h-[60px] tw-outline-none">
                        <span class="tw-text-gray-400 tw-italic">{{ __('messages.brand_show.no_data') }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Modal Footer --}}
        <div class="tw-px-6 tw-py-4 tw-border-t tw-border-gray-100 tw-flex tw-justify-end">
            <button data-action="brand-info#close"
                class="tw-px-6 tw-py-2 tw-bg-gray-100 tw-text-gray-700 tw-font-medium tw-rounded-lg hover:tw-bg-gray-200 tw-transition">
                {{ __('messages.brand_show.close') }}
            </button>
        </div>
    </div>
</div>
