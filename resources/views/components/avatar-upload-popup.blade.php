{{--
    Avatar Upload Popup Component
    Popup cho phép người dùng preview, upload và xóa avatar.
    Sử dụng Stimulus controller: avatar_upload_controller.js
    
    Usage: <x-avatar-upload-popup />
--}}

<div data-controller="avatar-upload"
    data-avatar-upload-current-avatar-value="{{ auth()->user()->avatar ? Storage::url(auth()->user()->avatar) : '' }}"
    data-avatar-upload-delete-url-value="{{ route('profile.avatar.delete') }}"
    data-avatar-upload-csrf-token-value="{{ csrf_token() }}">
    
    {{-- Popup overlay --}}
    <div data-avatar-upload-target="popup"
        data-action="click->avatar-upload#closeOnOverlay"
        class="tw-fixed tw-inset-0 tw-bg-black/50 tw-flex tw-items-center tw-justify-center tw-z-[10000] tw-hidden">
        
        <div class="tw-bg-white tw-rounded-2xl tw-w-[400px] tw-max-w-[90%] tw-overflow-y-auto tw-p-6 tw-relative tw-border tw-border-gray-300"
            data-action="click->avatar-upload#stopPropagation">
            <!-- Close Button -->
            <button type="button" data-action="click->avatar-upload#close"
                class="tw-absolute tw-top-3 tw-right-3 tw-text-gray-600 hover:tw-text-black tw-text-xl">
                <i class="ri-close-line"></i>
            </button>

            <h2 class="tw-text-lg tw-font-bold tw-text-[#1AA24C] tw-flex tw-items-center tw-gap-2 tw-mb-6">
                <i class="ri-user-settings-line tw-text-2xl"></i>
                Ảnh đại diện
            </h2>

            {{-- Avatar preview circle - click để chọn file --}}
            <form id="avatarUploadForm" action="{{ route('profile.avatar.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PATCH')
                
                <div class="tw-flex tw-flex-col tw-items-center tw-gap-4">
                    {{-- Ô tròn preview avatar --}}
                    <div class="tw-relative tw-cursor-pointer tw-group tw-w-[80%]" 
                        data-action="click->avatar-upload#openFilePicker">
                        <div class="tw-relative tw-w-full tw-aspect-square tw-rounded-full tw-border-2 tw-border-dashed tw-cursor-pointer tw-transition tw-flex tw-items-center tw-justify-center tw-overflow-hidden
                                {{ $errors->has('avatar') ? 'tw-border-red-500 tw-bg-red-50' : 'tw-border-gray-300 tw-bg-gray-50 hover:tw-bg-gray-100' }}">
                            @if(auth()->user()->avatar)
                                <img data-avatar-upload-target="previewImg" 
                                    src="{{ Storage::url(auth()->user()->avatar) }}" 
                                    class="tw-w-full tw-h-full tw-object-cover" alt="Preview">
                            @else
                                <div data-avatar-upload-target="placeholder" class="tw-text-center tw-text-gray-400">
                                    <i class="ri-camera-line tw-text-3xl"></i>
                                    <p class="tw-text-sm tw-mt-1">Chọn ảnh</p>
                                </div>
                                <img data-avatar-upload-target="previewImg" 
                                    src="" class="tw-w-full tw-h-full tw-object-cover tw-hidden" alt="Preview">
                            @endif
                        </div>
                    </div>

                    {{-- Hidden file input --}}
                    <input type="file" 
                        data-avatar-upload-target="fileInput" 
                        data-action="change->avatar-upload#preview"
                        name="avatar" accept="image/*" class="tw-hidden">
                </div>

                {{-- Buttons --}}
                <div class="tw-flex tw-items-center tw-justify-between tw-mt-6 tw-pt-4 tw-border-t tw-border-gray-200">
                    {{-- Nút xóa avatar --}}
                    <button type="button" 
                        data-avatar-upload-target="deleteBtn"
                        data-action="avatar-upload#delete"
                        class="tw-text-red-500 hover:tw-text-red-600 tw-text-sm tw-font-medium tw-flex tw-items-center tw-gap-1 tw-transition-colors"
                        @if(!auth()->user()->avatar) disabled style="opacity: 0.5; cursor: not-allowed;" @endif>
                        <i class="ri-delete-bin-line"></i> Xóa ảnh
                    </button>

                    {{-- Nút xác nhận --}}
                    <div class="tw-flex tw-gap-2">
                        <button type="button" data-action="avatar-upload#close" 
                            class="tw-px-4 tw-py-2 tw-text-sm tw-text-gray-600 hover:tw-text-gray-800 tw-transition-colors">
                            Hủy
                        </button>
                        <button type="submit" 
                            data-avatar-upload-target="confirmBtn"
                            class="tw-px-4 tw-py-2 tw-bg-vlbcgreen tw-text-white tw-rounded-lg tw-text-sm tw-font-medium hover:tw-bg-vlbcgreen/90 tw-transition-colors disabled:tw-opacity-50 disabled:tw-cursor-not-allowed"
                            disabled>
                            Xác nhận
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Script để expose hàm openAvatarPreviewPopup() cho navigation --}}
<script>
    function openAvatarPreviewPopup() {
        const controller = document.querySelector('[data-controller="avatar-upload"]');
        if (controller) {
            const application = window.Stimulus || Stimulus;
            const avatarController = application.getControllerForElementAndIdentifier(controller, 'avatar-upload');
            if (avatarController) {
                avatarController.open();
            }
        }
    }
</script>
