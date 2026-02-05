@props(['brand'])

{{-- 
    Section Thông tin thương hiệu
    Controller result-modal đã được khai báo ở trang cha (show.blade.php)
    để bao gồm cả nextStepContainer trong scope
--}}
<section class="tw-px-8 tw-hidden">
    <!-- Main Box -->
    <div class="tw-w-full tw-bg-white tw-border tw-border-[#E0EAE6] tw-rounded-xl tw-p-8 tw-shadow-sm">
        <h2 class="tw-text-2xl tw-font-bold tw-text-gray-800 tw-mb-6 tw-border-b tw-pb-4">
            Thông tin thương hiệu
        </h2>

        <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-gap-8">
            <!-- Col 1: Gốc Thương Hiệu -->
            <div>
                <h3 class="tw-text-lg tw-font-bold tw-text-[#1AA24C] tw-mb-4 tw-flex tw-items-center tw-gap-2">
                    <i class="ri-seedling-fill"></i> Gốc Thương Hiệu
                </h3>
                <div class="tw-space-y-3">
                    <!-- Item 1: Văn Hóa Dịch Vụ -->
                    <button type="button"
                        data-action="result-modal#open"
                        data-result-modal-title-param="Văn Hóa Dịch Vụ"
                        data-result-modal-key-param="root1"
                        class="tw-w-full tw-text-left tw-px-4 tw-py-3 tw-bg-[#F9FBF9] tw-border tw-border-[#E8F3EE] tw-rounded-lg hover:tw-bg-[#E6F6EC] hover:tw-border-[#1AA24C] tw-transition tw-group">
                        <div class="tw-flex tw-items-center tw-justify-between">
                            <span class="tw-font-medium tw-text-gray-700 group-hover:tw-text-[#1AA24C]">Văn Hóa Dịch
                                Vụ</span>
                            <i class="ri-arrow-right-s-line tw-text-gray-400 group-hover:tw-text-[#1AA24C]"></i>
                        </div>
                    </button>

                    <!-- Item 2: Thổ Nhưỡng -->
                    <button type="button"
                        data-action="result-modal#open"
                        data-result-modal-title-param="Thổ Nhưỡng"
                        data-result-modal-key-param="root2"
                        class="tw-w-full tw-text-left tw-px-4 tw-py-3 tw-bg-[#F9FBF9] tw-border tw-border-[#E8F3EE] tw-rounded-lg hover:tw-bg-[#E6F6EC] hover:tw-border-[#1AA24C] tw-transition tw-group">
                        <div class="tw-flex tw-items-center tw-justify-between">
                            <span class="tw-font-medium tw-text-gray-700 group-hover:tw-text-[#1AA24C]">Thổ
                                Nhưỡng</span>
                            <i class="ri-arrow-right-s-line tw-text-gray-400 group-hover:tw-text-[#1AA24C]"></i>
                        </div>
                    </button>

                    <!-- Item 3: Giá Trị Giải Pháp -->
                    <button type="button"
                        data-action="result-modal#open"
                        data-result-modal-title-param="Giá Trị Giải Pháp"
                        data-result-modal-key-param="root3"
                        class="tw-w-full tw-text-left tw-px-4 tw-py-3 tw-bg-[#F9FBF9] tw-border tw-border-[#E8F3EE] tw-rounded-lg hover:tw-bg-[#E6F6EC] hover:tw-border-[#1AA24C] tw-transition tw-group">
                        <div class="tw-flex tw-items-center tw-justify-between">
                            <span class="tw-font-medium tw-text-gray-700 group-hover:tw-text-[#1AA24C]">Giá Trị Giải
                                Pháp</span>
                            <i class="ri-arrow-right-s-line tw-text-gray-400 group-hover:tw-text-[#1AA24C]"></i>
                        </div>
                    </button>
                </div>
            </div>

            <!-- Col 2: Thân Thương Hiệu -->
            <div>
                <h3 class="tw-text-lg tw-font-bold tw-text-[#489A6D] tw-mb-4 tw-flex tw-items-center tw-gap-2">
                    <i class="ri-tree-line"></i> Thân Thương Hiệu
                </h3>
                <div class="tw-space-y-3">
                    <!-- Item 1: Định Vị Thương Hiệu -->
                    <button type="button"
                        data-action="result-modal#open"
                        data-result-modal-title-param="Định Vị Thương Hiệu"
                        data-result-modal-key-param="trunk1"
                        class="tw-w-full tw-text-left tw-px-4 tw-py-3 tw-bg-[#F9FBF9] tw-border tw-border-[#E8F3EE] tw-rounded-lg hover:tw-bg-[#E6F6EC] hover:tw-border-[#489A6D] tw-transition tw-group">
                        <div class="tw-flex tw-items-center tw-justify-between">
                            <span class="tw-font-medium tw-text-gray-700 group-hover:tw-text-[#489A6D]">Định Vị Thương
                                Hiệu</span>
                            <i class="ri-arrow-right-s-line tw-text-gray-400 group-hover:tw-text-[#489A6D]"></i>
                        </div>
                    </button>

                    <!-- Item 2: Nhận Diện Ngôn Ngữ -->
                    <button type="button"
                        data-action="result-modal#open"
                        data-result-modal-title-param="Nhận Diện Ngôn Ngữ"
                        data-result-modal-key-param="trunk2"
                        class="tw-w-full tw-text-left tw-px-4 tw-py-3 tw-bg-[#F9FBF9] tw-border tw-border-[#E8F3EE] tw-rounded-lg hover:tw-bg-[#E6F6EC] hover:tw-border-[#489A6D] tw-transition tw-group">
                        <div class="tw-flex tw-items-center tw-justify-between">
                            <span class="tw-font-medium tw-text-gray-700 group-hover:tw-text-[#489A6D]">Nhận Diện Ngôn
                                Ngữ</span>
                            <i class="ri-arrow-right-s-line tw-text-gray-400 group-hover:tw-text-[#489A6D]"></i>
                        </div>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Result Modal Component --}}
    <x-result-modal :brand="$brand" />
</section>