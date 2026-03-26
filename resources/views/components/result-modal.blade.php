@props(['brand', 'type'])  {{-- type có thể là 'data' hoặc 'brief' để xác định lưu vào cột dữ liệu raw hay sau tóm tăts --}}

{{-- 
    Result Modal Component (Dumb View)
    Chỉ chứa HTML structure của modal.
    Logic điều khiển nằm ở Controller cấp trang (ví dụ thẻ <main>).
--}}

<div data-result-modal-target="modal"
     class="tw-hidden tw-fixed tw-inset-0 tw-z-50 tw-flex tw-items-center tw-justify-center tw-bg-black/50 tw-backdrop-blur-sm"
     data-action="click->result-modal#closeOnBackdrop">
    
    {{-- Modal Content --}}
    <div class="tw-bg-white tw-rounded-xl tw-shadow-xl tw-w-[90%] md:tw-w-[800px] tw-min-h-[70vh] tw-max-h-[80vh] tw-flex tw-flex-col tw-overflow-hidden">

        {{-- Modal Header --}}
        <div class="tw-px-6 tw-py-4 tw-border-b tw-border-gray-100 tw-flex tw-items-center tw-justify-between">
            <div class="tw-flex tw-items-center tw-gap-4">
                <h3 data-result-modal-target="title" 
                    class="tw-text-xl tw-font-bold tw-text-gray-800">
                </h3>

                <a data-result-modal-target="chatLink"
                    href="#"
                    class="tw-inline-flex tw-items-center tw-gap-1 tw-bg-[#1AA24C] tw-text-white tw-text-xs tw-font-medium tw-px-3 tw-py-1.5 tw-rounded-full hover:tw-bg-[#15803d] tw-transition">
                    <i class="ri-chat-smile-3-line"></i>
                    Chat ngay với trợ lý AI
                </a>
            </div>

            <button data-action="result-modal#close" 
                class="tw-text-gray-400 hover:tw-text-gray-600 tw-transition">
                <i class="ri-close-line tw-text-2xl"></i>
            </button>
        </div>

        {{-- Modal Body --}}
        <div class="tw-p-6 tw-flex tw-flex-col tw-flex-1 tw-overflow-hidden">

            {{-- Toggle tabs: Xem / Sửa --}}
            <div class="tw-flex tw-items-center tw-gap-1 tw-mb-3 tw-border-b tw-border-gray-200">
                {{-- Tab Xem (mặc định active) --}}
                <button
                    data-result-modal-target="viewTab"
                    data-action="result-modal#switchToView"
                    class="tw-px-4 tw-py-2 tw-text-sm tw-font-medium tw-border-b-2 tw-border-[#1AA24C] tw-text-[#1AA24C] tw-transition-colors">
                    <i class="ri-eye-line tw-mr-1"></i>Xem
                </button>
                {{-- Tab Sửa --}}
                <button
                    data-result-modal-target="editTab"
                    data-action="result-modal#switchToEdit"
                    class="tw-px-4 tw-py-2 tw-text-sm tw-font-medium tw-border-b-2 tw-border-transparent tw-text-gray-500 hover:tw-text-gray-700 tw-transition-colors">
                    <i class="ri-edit-line tw-mr-1"></i>Sửa
                </button>
            </div>

            {{-- Chế độ Xem: hiển thị markdown đã render --}}
            <div data-result-modal-target="preview"
                class="tw-flex-1 tw-w-full tw-border tw-border-gray-200 tw-rounded-lg tw-p-4 tw-text-gray-700 tw-overflow-y-auto">
                <div class="chat-markdown-content" data-result-modal-target="previewContent">
                    {{-- Nội dung markdown được render bởi JS --}}
                </div>
            </div>

            {{-- Chế độ Sửa: textarea raw text (ẩn mặc định) --}}
            <textarea
                data-result-modal-target="content"
                class="tw-hidden tw-flex-1 tw-w-full tw-border tw-border-gray-200 tw-rounded-lg tw-p-4 tw-text-gray-700 focus:tw-outline-none focus:tw-ring-2 focus:tw-ring-[#1AA24C] tw-resize-none"
                spellcheck="false"
                placeholder="Chưa có kết quả phân tích..."></textarea>

            {{-- Footer Actions --}}
            <div class="tw-mt-4 tw-flex tw-items-center tw-gap-3">
                <button
                    data-result-modal-target="saveBtn"
                    data-action="result-modal#save"
                    data-result-modal-type-value="data" {{-- hardcode với type = data để lưu vào cột root/trunk_data  --}}
                    class="tw-bg-[#1AA24C] tw-text-white tw-px-6 tw-py-2 tw-rounded-lg tw-font-medium hover:tw-bg-[#15803d] tw-transition disabled:tw-opacity-50 disabled:tw-cursor-not-allowed tw-flex tw-items-center tw-gap-2">
                    <span>Lưu</span>
                </button>

                {{-- Status Message --}}
                <span
                    data-result-modal-target="status"
                    class="tw-hidden tw-text-sm tw-font-medium">
                </span>
            </div>
        </div>
    </div>
</div>
