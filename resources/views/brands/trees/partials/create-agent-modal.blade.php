<div x-show="openCreateModal" style="display: none;" x-data="{
        agentName: '',
        hasKnowledge: true,
        instruction: '',
        isCreating: false,
        brandSlug: '{{ request()->route('brand')->slug ?? '' }}',

        // === Quản lý file upload ===
        selectedFiles: [],         // Danh sách file đã chọn (chưa upload)
        uploadProgress: '',        // Thông báo tiến trình upload
        maxFileSize: 10 * 1024 * 1024, // 10MB tính bằng bytes
        maxFiles: 20,
        // Các MIME type được phép
        allowedTypes: [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'text/plain'
        ],
        // Extension tương ứng (dùng cho hiển thị)
        allowedExtensions: ['pdf', 'doc', 'docx', 'txt'],

        // Lấy icon cho file dựa theo extension
        getFileIcon(filename) {
            const ext = filename.split('.').pop().toLowerCase();
            const icons = {
                'pdf': 'ri-file-pdf-2-line tw-text-red-500',
                'doc': 'ri-file-word-2-line tw-text-blue-500',
                'docx': 'ri-file-word-2-line tw-text-blue-500',
                'txt': 'ri-file-text-line tw-text-gray-500'
            };
            return icons[ext] || 'ri-file-line tw-text-gray-400';
        },

        // Format kích thước file
        formatSize(bytes) {
            if (bytes < 1024) return bytes + ' B';
            if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB';
            return (bytes / 1048576).toFixed(1) + ' MB';
        },

        // Xử lý khi chọn file
        handleFileSelect(event) {
            const files = Array.from(event.target.files);
            
            for (const file of files) {
                // Kiểm tra đã đạt giới hạn chưa
                if (this.selectedFiles.length >= this.maxFiles) {
                    alert(`Tối đa ${this.maxFiles} file`);
                    break;
                }

                // Kiểm tra trùng tên
                if (this.selectedFiles.some(f => f.name === file.name)) {
                    alert(`File '${file.name}' đã được chọn`);
                    continue;
                }

                // Kiểm tra kích thước
                if (file.size > this.maxFileSize) {
                    alert(`File '${file.name}' vượt quá 10MB`);
                    continue;
                }

                // Kiểm tra extension
                const ext = file.name.split('.').pop().toLowerCase();
                if (!this.allowedExtensions.includes(ext)) {
                    alert(`File '${file.name}' không được hỗ trợ. Chỉ chấp nhận: PDF, DOC, DOCX, TXT`);
                    continue;
                }

                // Thêm vào danh sách
                this.selectedFiles.push(file);
            }

            // Reset input để có thể chọn lại cùng file
            event.target.value = '';
        },

        // Xóa file khỏi danh sách đã chọn
        removeFile(index) {
            this.selectedFiles.splice(index, 1);
        },

        // Upload tất cả file cho agent (sau khi tạo agent xong)
        async uploadFilesForAgent(agentId) {
            const csrfToken = document.querySelector('meta[name=csrf-token]').content;
            let uploadedCount = 0;

            for (let i = 0; i < this.selectedFiles.length; i++) {
                const file = this.selectedFiles[i];
                this.uploadProgress = `Đang upload file ${i + 1}/${this.selectedFiles.length}: ${file.name}`;

                try {
                    const formData = new FormData();
                    formData.append('file', file);

                    const response = await fetch(`/brands/${this.brandSlug}/agents/${agentId}/files`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: formData
                    });

                    const result = await response.json();
                    if (result.success) {
                        uploadedCount++;
                    } else {
                        console.error(`Upload failed for ${file.name}:`, result.error);
                    }
                } catch (error) {
                    console.error(`Upload error for ${file.name}:`, error);
                }
            }

            return uploadedCount;
        },

        // Tạo agent + upload file
        async createAgent() {
            if (!this.agentName.trim()) {
                alert('Vui lòng nhập tên Agent');
                return;
            }

            this.isCreating = true;
            this.uploadProgress = '';

            try {
                // Bước 1: Tạo agent trước
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
                    // Bước 2: Upload file (nếu có)
                    if (this.selectedFiles.length > 0 && result.agent_id) {
                        this.uploadProgress = 'Đang chuẩn bị upload file...';
                        const uploadedCount = await this.uploadFilesForAgent(result.agent_id);
                        console.log(`Uploaded ${uploadedCount}/${this.selectedFiles.length} files for agent ${result.agent_id}`);
                    }

                    // Bước 3: Reload trang
                    window.location.reload();
                } else {
                    alert(result.message || 'Có lỗi xảy ra');
                }
            } catch (error) {
                console.error(error);
                alert('Lỗi kết nối');
            } finally {
                this.isCreating = false;
                this.uploadProgress = '';
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

                <!-- Input file ẩn -->
                <input type="file" x-ref="fileInput" accept=".pdf,.doc,.docx,.txt" multiple class="tw-hidden"
                    @change="handleFileSelect($event)">

                <!-- Vùng kéo thả / click chọn file -->
                <div @click="$refs.fileInput.click()"
                    @dragover.prevent="$event.currentTarget.classList.add('tw-border-green-400', 'tw-bg-green-50')"
                    @dragleave.prevent="$event.currentTarget.classList.remove('tw-border-green-400', 'tw-bg-green-50')"
                    @drop.prevent="
                        $event.currentTarget.classList.remove('tw-border-green-400', 'tw-bg-green-50');
                        const dt = new DataTransfer();
                        for (const f of $event.dataTransfer.files) dt.items.add(f);
                        $refs.fileInput.files = dt.files;
                        $refs.fileInput.dispatchEvent(new Event('change'));
                    "
                    class="tw-border-2 tw-border-dashed tw-border-gray-200 tw-rounded-xl tw-p-6 tw-text-center hover:tw-bg-gray-50 tw-transition tw-cursor-pointer">
                    <div class="tw-flex tw-flex-col tw-items-center tw-justify-center tw-gap-2">
                        <i class="ri-upload-cloud-2-line tw-text-2xl tw-text-gray-400"></i>
                        <span class="tw-font-bold tw-text-gray-800">Kéo thả hoặc nhấn để chọn tài liệu</span>
                        <span class="tw-text-sm tw-text-gray-400">PDF, DOC, DOCX, TXT (tối đa 10MB/file, tối đa 20
                            file)</span>
                    </div>
                </div>

                <!-- Danh sách file đã chọn -->
                <div x-show="selectedFiles.length > 0" class="tw-mt-3 tw-space-y-2">
                    <template x-for="(file, index) in selectedFiles" :key="index">
                        <div
                            class="tw-flex tw-items-center tw-justify-between tw-px-3 tw-py-2 tw-bg-gray-50 tw-rounded-lg tw-border tw-border-gray-100">
                            <div class="tw-flex tw-items-center tw-gap-2 tw-min-w-0">
                                <!-- Icon theo loại file -->
                                <i :class="getFileIcon(file.name)" class="tw-text-lg tw-flex-shrink-0"></i>
                                <!-- Tên file -->
                                <span x-text="file.name" class="tw-text-sm tw-text-gray-700 tw-truncate"></span>
                                <!-- Kích thước -->
                                <span x-text="formatSize(file.size)"
                                    class="tw-text-xs tw-text-gray-400 tw-flex-shrink-0"></span>
                            </div>
                            <!-- Nút xóa -->
                            <button @click.stop="removeFile(index)"
                                class="tw-text-gray-400 hover:tw-text-red-500 tw-transition tw-flex-shrink-0 tw-ml-2"
                                title="Xóa file">
                                <i class="ri-close-circle-line tw-text-lg"></i>
                            </button>
                        </div>
                    </template>
                    <!-- Tổng số file -->
                    <p class="tw-text-xs tw-text-gray-400 tw-mt-1">
                        <span x-text="selectedFiles.length"></span> file đã chọn
                    </p>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="tw-px-8 tw-py-6 tw-bg-white tw-flex tw-items-center tw-justify-between tw-gap-3">
            <!-- Thông báo upload progress -->
            <div class="tw-flex-1">
                <p x-show="uploadProgress" x-text="uploadProgress"
                    class="tw-text-sm tw-text-blue-600 tw-flex tw-items-center tw-gap-1">
                </p>
            </div>
            <div class="tw-flex tw-gap-3">
                <button @click="openCreateModal = false"
                    class="tw-px-6 tw-py-2.5 tw-border tw-border-gray-300 tw-rounded-lg tw-text-gray-700 tw-font-semibold hover:tw-bg-gray-50 tw-transition">
                    Hủy
                </button>
                <button @click="createAgent()" :disabled="isCreating"
                    class="tw-px-6 tw-py-2.5 tw-bg-[#1AA24C] tw-text-white tw-rounded-lg tw-font-semibold hover:tw-bg-[#15803d] tw-transition disabled:tw-opacity-50 disabled:tw-cursor-not-allowed tw-flex tw-items-center tw-gap-2">
                    <span x-show="isCreating" class="tw-animate-spin"><i class="ri-loader-4-line"></i></span>
                    <span
                        x-text="isCreating ? (uploadProgress ? 'Đang upload...' : 'Đang tạo...') : 'Tạo Agent'"></span>
                </button>
            </div>
        </div>
    </div>
</div>