<div x-show="openCreateModal" style="display: none;" x-data="{
        agentName: '',
        hasKnowledge: true,
        instruction: '',
        isCreating: false,
        brandSlug: '{{ request()->route('brand')->slug ?? '' }}',

        async createAgent() {
            if (!this.agentName.trim()) {
                alert('Vui lòng nhập tên Agent');
                return;
            }

            this.isCreating = true;

            try {
                const response = await fetch(`/brands/${this.brandSlug}/agents`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                    },
                    body: JSON.stringify({
                        name: this.agentName,
                        has_knowledge: this.hasKnowledge,
                        instruction: this.instruction
                    })
                });

                const result = await response.json();

                if (result.status === 'success') {
                    // Reload page to show new agent
                    window.location.reload();
                } else {
                    alert(result.message || 'Có lỗi xảy ra');
                }
            } catch (error) {
                console.error(error);
                alert('Lỗi kết nối');
            } finally {
                this.isCreating = false;
            }
        }
    }"
    class="tw-fixed tw-inset-0 tw-z-[9999] tw-flex tw-items-center tw-justify-center tw-bg-black/50 tw-backdrop-blur-sm"
    x-transition:enter="tw-transition tw-ease-out tw-duration-300" x-transition:enter-start="tw-opacity-0"
    x-transition:enter-end="tw-opacity-100" x-transition:leave="tw-transition tw-ease-in tw-duration-200"
    x-transition:leave-start="tw-opacity-100" x-transition:leave-end="tw-opacity-0">

    <div class="tw-bg-white tw-rounded-2xl tw-shadow-xl tw-w-[90%] md:tw-w-[700px] tw-max-h-[90vh] tw-overflow-y-auto tw-relative"
        @click.away="openCreateModal = false">

        <!-- Close Button -->
        <button @click="openCreateModal = false"
            class="tw-absolute tw-top-4 tw-right-4 tw-text-gray-400 hover:tw-text-gray-600">
            <i class="ri-close-line tw-text-2xl"></i>
        </button>

        <!-- Header -->
        <div class="tw-px-8 tw-pt-8 tw-pb-2">
            <h2 class="tw-text-2xl tw-font-bold tw-text-gray-800">Tạo Agent mới</h2>
            <p class="tw-text-sm tw-text-gray-500 tw-mt-1">Tạo một AI Agent chuyên biệt cho nhu cầu riêng của bạn</p>
        </div>

        <!-- Body -->
        <div class="tw-px-8 tw-py-6 tw-space-y-6">
            <!-- Tên Agent -->
            <div>
                <label class="tw-block tw-text-sm tw-font-bold tw-text-gray-800 tw-mb-2">Tên Agent*</label>
                <input type="text" x-model="agentName" placeholder="Ví dụ: Agent viết bài cho sản phẩm Chuột gaming"
                    class="tw-w-full tw-px-4 tw-py-3 tw-border tw-border-green-400 tw-rounded-full focus:tw-outline-none focus:tw-ring-2 focus:tw-ring-green-500 tw-text-gray-700 placeholder:tw-text-gray-400">
            </div>

            <!-- Học kiến thức -->
            <div class="tw-flex tw-items-start tw-gap-3">
                <div class="tw-flex-shrink-0 tw-mt-0.5">
                    <input type="checkbox" x-model="hasKnowledge"
                        class="tw-w-5 tw-h-5 tw-text-green-600 tw-rounded focus:tw-ring-green-500 tw-border-gray-300">
                </div>
                <div>
                    <h3 class="tw-text-sm tw-font-bold tw-text-gray-800 text-green-600">Học kiến thức Gốc và Thân Cây
                    </h3>
                    <p class="tw-text-sm tw-text-gray-500 tw-mt-0.5 tw-leading-relaxed">
                        Agent sẽ học về giá trị cốt lõi, chân dung khách hàng, định vị thương hiệu và bộ quy chuẩn
                        thương hiệu của bạn
                    </p>
                </div>
            </div>

            <!-- Hướng dẫn riêng -->
            <div>
                <label class="tw-block tw-text-sm tw-font-bold tw-text-gray-800 tw-mb-2">Hướng dẫn Riêng
                    (Injection)</label>
                <textarea x-model="instruction" rows="4"
                    placeholder="Nhập các hướng dẫn, kịch bản mẫu hoặc yêu cầu đặc biệt cho Agent này..."
                    class="tw-w-full tw-px-4 tw-py-3 tw-border tw-border-gray-200 tw-rounded-xl focus:tw-outline-none focus:tw-ring-2 focus:tw-ring-green-500 tw-text-gray-700 placeholder:tw-text-gray-400 tw-resize-none"></textarea>
                <p class="tw-text-sm tw-text-gray-500 tw-mt-2">
                    Ví dụ: "Viết theo phong cách trẻ trung, sử dụng emoji, tập trung vào lợi ích sản phẩm"
                </p>
            </div>

            <!-- Tài liệu tham khảo -->
            <div>
                <label class="tw-block tw-text-sm tw-font-bold tw-text-gray-800 tw-mb-2">Tài liệu tham khảo</label>
                <div
                    class="tw-border-2 tw-border-dashed tw-border-gray-200 tw-rounded-xl tw-p-8 tw-text-center hover:tw-bg-gray-50 tw-transition tw-cursor-pointer">
                    <div class="tw-flex tw-flex-col tw-items-center tw-justify-center tw-gap-2">
                        <i class="ri-upload-cloud-2-line tw-text-2xl tw-text-gray-400"></i>
                        <span class="tw-font-bold tw-text-gray-800">Tải lên tài liệu</span>
                        <span class="tw-text-sm tw-text-gray-400">PDF, DOC, DOCX, TXT (tối đa 10MB)</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="tw-px-8 tw-py-6 tw-bg-white tw-flex tw-justify-end tw-gap-3">
            <button @click="openCreateModal = false"
                class="tw-px-6 tw-py-2.5 tw-border tw-border-gray-300 tw-rounded-lg tw-text-gray-700 tw-font-semibold hover:tw-bg-gray-50 tw-transition">
                Hủy
            </button>
            <button @click="createAgent()" :disabled="isCreating"
                class="tw-px-6 tw-py-2.5 tw-bg-[#1AA24C] tw-text-white tw-rounded-lg tw-font-semibold hover:tw-bg-[#15803d] tw-transition disabled:tw-opacity-50 disabled:tw-cursor-not-allowed tw-flex tw-items-center tw-gap-2">
                <span x-show="isCreating" class="tw-animate-spin"><i class="ri-loader-4-line"></i></span>
                <span>Tạo Agent</span>
            </button>
        </div>
    </div>
</div>