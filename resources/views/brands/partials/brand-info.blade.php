@props(['brand'])

@php
    $rootData = $brand->root_data ?? [];
    $trunkData = $brand->trunk_data ?? [];
    
    // Normalize data to ensure keys exist for JS object
    $initialData = [
        'root1' => $rootData['root1'] ?? '',
        'root2' => $rootData['root2'] ?? '',
        'root3' => $rootData['root3'] ?? '',
        'trunk1' => $trunkData['trunk1'] ?? '',
        'trunk2' => $trunkData['trunk2'] ?? '',
    ];
@endphp

<section class="tw-px-8" x-data="{
    openModal: false,
    modalTitle: '',
    modalContent: '',
    currentKey: '',
    brandSlug: '{{ $brand->slug }}',
    isSaving: false,
    saveStatus: '',
    data: @js($initialData),

    openInfo(title, key) {
        this.modalTitle = title;
        this.currentKey = key;
        // Load content from central data state
        this.modalContent = this.data[key] || '';
        this.openModal = true;
        this.saveStatus = '';
    },

    getChatUrl() {
        let agentType = this.currentKey; 
        let agentId = 1;
        
        switch(agentType) {
            case 'root1': agentId = 1; break;
            case 'root2': agentId = 2; break;
            case 'root3': agentId = 3; break;
            case 'trunk1': agentId = 4; break;
            case 'trunk2': agentId = 5; break;
            default: agentId = 1;
        }

        return `/brands/${this.brandSlug}/chat/${agentType}/${agentId}/new`;
    },

    async saveInfo() {
        this.isSaving = true;
        this.saveStatus = '';

        try {
            const response = await fetch(`/brands/${this.brandSlug}/update-section`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                },
                body: JSON.stringify({
                    key: this.currentKey,
                    content: this.modalContent
                })
            });

            const result = await response.json();

            if (result.status === 'success') {
                this.saveStatus = 'Đã lưu thành công';
                
                // Update local state so reopen shows new data
                this.data[this.currentKey] = this.modalContent;
            } else {
                this.saveStatus = 'Lỗi: ' + (result.message || 'Không thể lưu');
            }
        } catch (error) {
            console.error(error);
            this.saveStatus = 'Lỗi kết nối';
        } finally {
            this.isSaving = false;
        }
    }
}">
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
                    <!-- Item 1 -->
                    <button 
                        @click="openInfo('Văn Hóa Dịch Vụ', 'root1')"
                        class="tw-w-full tw-text-left tw-px-4 tw-py-3 tw-bg-[#F9FBF9] tw-border tw-border-[#E8F3EE] tw-rounded-lg hover:tw-bg-[#E6F6EC] hover:tw-border-[#1AA24C] tw-transition tw-group">
                        <div class="tw-flex tw-items-center tw-justify-between">
                            <span class="tw-font-medium tw-text-gray-700 group-hover:tw-text-[#1AA24C]">Văn Hóa Dịch Vụ</span>
                            <i class="ri-arrow-right-s-line tw-text-gray-400 group-hover:tw-text-[#1AA24C]"></i>
                        </div>
                    </button>
                    <!-- Item 2 -->
                    <button 
                        @click="openInfo('Thổ Nhưỡng', 'root2')"
                        class="tw-w-full tw-text-left tw-px-4 tw-py-3 tw-bg-[#F9FBF9] tw-border tw-border-[#E8F3EE] tw-rounded-lg hover:tw-bg-[#E6F6EC] hover:tw-border-[#1AA24C] tw-transition tw-group">
                        <div class="tw-flex tw-items-center tw-justify-between">
                            <span class="tw-font-medium tw-text-gray-700 group-hover:tw-text-[#1AA24C]">Thổ Nhưỡng</span>
                             <i class="ri-arrow-right-s-line tw-text-gray-400 group-hover:tw-text-[#1AA24C]"></i>
                        </div>
                    </button>
                    <!-- Item 3 -->
                    <button 
                        @click="openInfo('Giá Trị Giải Pháp', 'root3')"
                        class="tw-w-full tw-text-left tw-px-4 tw-py-3 tw-bg-[#F9FBF9] tw-border tw-border-[#E8F3EE] tw-rounded-lg hover:tw-bg-[#E6F6EC] hover:tw-border-[#1AA24C] tw-transition tw-group">
                        <div class="tw-flex tw-items-center tw-justify-between">
                            <span class="tw-font-medium tw-text-gray-700 group-hover:tw-text-[#1AA24C]">Giá Trị Giải Pháp</span>
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
                    <!-- Item 1 -->
                    <button 
                        @click="openInfo('Định Vị Thương Hiệu', 'trunk1')"
                        class="tw-w-full tw-text-left tw-px-4 tw-py-3 tw-bg-[#F9FBF9] tw-border tw-border-[#E8F3EE] tw-rounded-lg hover:tw-bg-[#E6F6EC] hover:tw-border-[#489A6D] tw-transition tw-group">
                         <div class="tw-flex tw-items-center tw-justify-between">
                            <span class="tw-font-medium tw-text-gray-700 group-hover:tw-text-[#489A6D]">Định Vị Thương Hiệu</span>
                             <i class="ri-arrow-right-s-line tw-text-gray-400 group-hover:tw-text-[#489A6D]"></i>
                        </div>
                    </button>
                     <!-- Item 2 -->
                    <button 
                        @click="openInfo('Nhận Diện Ngôn Ngữ', 'trunk2')"
                        class="tw-w-full tw-text-left tw-px-4 tw-py-3 tw-bg-[#F9FBF9] tw-border tw-border-[#E8F3EE] tw-rounded-lg hover:tw-bg-[#E6F6EC] hover:tw-border-[#489A6D] tw-transition tw-group">
                         <div class="tw-flex tw-items-center tw-justify-between">
                            <span class="tw-font-medium tw-text-gray-700 group-hover:tw-text-[#489A6D]">Nhận Diện Ngôn Ngữ</span>
                             <i class="ri-arrow-right-s-line tw-text-gray-400 group-hover:tw-text-[#489A6D]"></i>
                        </div>
                    </button>
                 </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div x-show="openModal" style="display: none;"
        class="tw-fixed tw-inset-0 tw-z-50 tw-flex tw-items-center tw-justify-center tw-bg-black/50 tw-backdrop-blur-sm"
        x-transition:enter="tw-transition tw-ease-out tw-duration-300"
        x-transition:enter-start="tw-opacity-0"
        x-transition:enter-end="tw-opacity-100"
        x-transition:leave="tw-transition tw-ease-in tw-duration-200"
        x-transition:leave-start="tw-opacity-100"
        x-transition:leave-end="tw-opacity-0">

        <div class="tw-bg-white tw-rounded-xl tw-shadow-xl tw-w-[90%] md:tw-w-[800px] tw-max-h-[90vh] tw-flex tw-flex-col"
            @click.away="openModal = false">
            
            <!-- Modal Header -->
            <div class="tw-px-6 tw-py-4 tw-border-b tw-border-gray-100 tw-flex tw-items-center tw-justify-between">
                <div class="tw-flex tw-items-center tw-gap-4">
                    <h3 class="tw-text-xl tw-font-bold tw-text-gray-800" x-text="modalTitle"></h3>
                    
                    <a :href="getChatUrl()" 
                       class="tw-inline-flex tw-items-center tw-gap-1 tw-bg-[#1AA24C] tw-text-white tw-text-xs tw-font-medium tw-px-3 tw-py-1.5 tw-rounded-full hover:tw-bg-[#15803d] tw-transition">
                       <i class="ri-chat-smile-3-line"></i>
                       Chat ngay với trợ lý AI
                    </a>
                </div>

                <button @click="openModal = false" class="tw-text-gray-400 hover:tw-text-gray-600">
                    <i class="ri-close-line tw-text-2xl"></i>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="tw-p-6 tw-flex-1 tw-overflow-y-auto">
                <textarea class="tw-w-full tw-h-[400px] tw-border tw-border-gray-200 tw-rounded-lg tw-p-4 tw-text-gray-700 focus:tw-outline-none focus:tw-ring-2 focus:tw-ring-[#1AA24C] tw-resize-none"
                    x-model="modalContent"
                    placeholder="Chưa có thông tin..."></textarea>
                
                <!-- Footer Action -->
                <div class="tw-mt-4 tw-flex tw-items-center tw-gap-3">
                    <button 
                        @click="saveInfo()"
                        :disabled="isSaving"
                        class="tw-bg-[#1AA24C] tw-text-white tw-px-6 tw-py-2 tw-rounded-lg tw-font-medium hover:tw-bg-[#15803d] tw-transition disabled:tw-opacity-50 disabled:tw-cursor-not-allowed tw-flex tw-items-center tw-gap-2">
                            <span x-show="isSaving" class="tw-animate-spin"><i class="ri-loader-4-line"></i></span>
                            <span>Lưu</span>
                    </button>
                    
                    <!-- Status Message -->
                    <span 
                        x-show="saveStatus" 
                        x-text="saveStatus"
                        class="tw-text-sm tw-font-medium"
                        :class="saveStatus.includes('Lỗi') ? 'tw-text-red-600' : 'tw-text-[#1AA24C]'">
                    </span>
                </div>
            </div>
        </div>
    </div>
</section>