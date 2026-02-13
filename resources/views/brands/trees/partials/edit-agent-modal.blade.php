<div x-show="openEditModal" style="display: none;" x-data="{
        agentId: null,
        agentName: '',
        hasKnowledge: true,
        instruction: '',
        isSaving: false,
        brandSlug: '{{ request()->route('brand')->slug ?? '' }}',

        // === Quản lý file upload ===
        existingFiles: [],         // File đã upload trên server
        newFiles: [],              // File mới chưa upload
        deletedFileIds: [],        // ID file cần xóa
        isLoadingFiles: false,     // Đang load danh sách file
        uploadProgress: '',        // Thông báo tiến trình
        maxFileSize: 10 * 1024 * 1024, // 10MB
        maxFiles: 20,
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

        // Lấy label status
        getStatusLabel(status) {
            const labels = {
                'pending': 'Đang chờ...',
                'processing': 'Đang xử lý...',
                'completed': 'Hoàn tất',
                'failed': 'Lỗi'
            };
            return labels[status] || status;
        },

        // Lấy CSS class cho status badge
        getStatusClass(status) {
            const classes = {
                'pending': 'tw-text-yellow-600 tw-bg-yellow-50',
                'processing': 'tw-text-blue-600 tw-bg-blue-50',
                'completed': 'tw-text-green-600 tw-bg-green-50',
                'failed': 'tw-text-red-600 tw-bg-red-50'
            };
            return classes[status] || 'tw-text-gray-600 tw-bg-gray-50';
        },

        // Mở modal edit và load file đã upload
        async openEdit(id, name, hasKnowledge, instruction) {
            this.agentId = id;
            this.agentName = name;
            this.hasKnowledge = hasKnowledge;
            this.instruction = instruction || '';
            this.existingFiles = [];
            this.newFiles = [];
            this.deletedFileIds = [];
            this.uploadProgress = '';
            this.openEditModal = true;

            // Load danh sách file đã upload
            await this.loadExistingFiles();
        },

        // Fetch danh sách file đã upload cho agent
        async loadExistingFiles() {
            this.isLoadingFiles = true;
            try {
                const response = await fetch(`/api/files/agent/${this.agentId}`);
                const data = await response.json();
                this.existingFiles = data.files || [];
            } catch (error) {
                console.error('Lỗi load files:', error);
            } finally {
                this.isLoadingFiles = false;
            }
        },

        // Xử lý khi chọn file mới
        handleFileSelect(event) {
            const files = Array.from(event.target.files);
            const totalFiles = this.existingFiles.length - this.deletedFileIds.length + this.newFiles.length;

            for (const file of files) {
                if (totalFiles + this.newFiles.length >= this.maxFiles) {
                    alert(`Tối đa ${this.maxFiles} file`);
                    break;
                }

                // Kiểm tra trùng tên
                const isDuplicate = this.newFiles.some(f => f.name === file.name) 
                    || this.existingFiles.some(f => f.filename === file.name);
                if (isDuplicate) {
                    alert(`File '${file.name}' đã tồn tại`);
                    continue;
                }

                if (file.size > this.maxFileSize) {
                    alert(`File '${file.name}' vượt quá 10MB`);
                    continue;
                }

                const ext = file.name.split('.').pop().toLowerCase();
                if (!this.allowedExtensions.includes(ext)) {
                    alert(`File '${file.name}' không được hỗ trợ. Chỉ chấp nhận: PDF, DOC, DOCX, TXT`);
                    continue;
                }

                this.newFiles.push(file);
            }

            event.target.value = '';
        },

        // Xóa file mới (chưa upload)
        removeNewFile(index) {
            this.newFiles.splice(index, 1);
        },

        // Đánh dấu xóa file đã upload
        markFileForDeletion(fileId) {
            if (!this.deletedFileIds.includes(fileId)) {
                this.deletedFileIds.push(fileId);
            }
        },

        // Hoàn tác xóa file
        unmarkFileForDeletion(fileId) {
            this.deletedFileIds = this.deletedFileIds.filter(id => id !== fileId);
        },

        // Kiểm tra file có bị đánh dấu xóa không
        isMarkedForDeletion(fileId) {
            return this.deletedFileIds.includes(fileId);
        },

        // Lưu agent + upload file mới + xóa file đã đánh dấu
        async saveAgent() {
            if (!this.agentName.trim()) {
                alert('Vui lòng nhập tên Agent');
                return;
            }

            this.isSaving = true;
            this.uploadProgress = '';

            try {
                // Bước 1: Cập nhật thông tin agent
                const response = await fetch(`/brands/${this.brandSlug}/agents/${this.agentId}`, {
                    method: 'PUT',
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
                    const csrfToken = document.querySelector('meta[name=csrf-token]').content;

                    // Bước 2: Xóa file đã đánh dấu
                    for (const fileId of this.deletedFileIds) {
                        this.uploadProgress = `Đang xóa file...`;
                        try {
                            await fetch(`/api/files/${fileId}`, {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': csrfToken
                                }
                            });
                        } catch (error) {
                            console.error(`Lỗi xóa file ${fileId}:`, error);
                        }
                    }

                    // Bước 3: Upload file mới
                    for (let i = 0; i < this.newFiles.length; i++) {
                        const file = this.newFiles[i];
                        this.uploadProgress = `Đang upload file ${i + 1}/${this.newFiles.length}: ${file.name}`;

                        try {
                            const formData = new FormData();
                            formData.append('file', file);

                            await fetch(`/brands/${this.brandSlug}/agents/${this.agentId}/files`, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': csrfToken
                                },
                                body: formData
                            });
                        } catch (error) {
                            console.error(`Upload error for ${file.name}:`, error);
                        }
                    }

                    // Bước 4: Reload trang
                    window.location.reload();
                } else {
                    alert(result.message || 'Có lỗi xảy ra');
                }
            } catch (error) {
                console.error(error);
                alert('Lỗi kết nối');
            } finally {
                this.isSaving = false;
                this.uploadProgress = '';
            }
        }
    }"
    @open-edit-modal.window="openEdit($event.detail.id, $event.detail.name, $event.detail.hasKnowledge, $event.detail.instruction)"
    class="tw-fixed tw-inset-0 tw-z-[9999] tw-flex tw-items-center tw-justify-center tw-bg-black/50 tw-backdrop-blur-sm"
    x-transition:enter="tw-transition tw-ease-out tw-duration-300" x-transition:enter-start="tw-opacity-0"
    x-transition:enter-end="tw-opacity-100" x-transition:leave="tw-transition tw-ease-in tw-duration-200"
    x-transition:leave-start="tw-opacity-100" x-transition:leave-end="tw-opacity-0">

    <div class="tw-bg-white tw-rounded-2xl tw-shadow-xl tw-w-[90%] md:tw-w-[700px] tw-max-h-[90vh] tw-overflow-y-auto tw-relative"
        @click.away="openEditModal = false">

        <!-- Close Button -->
        <button @click="openEditModal = false"
            class="tw-absolute tw-top-4 tw-right-4 tw-text-gray-400 hover:tw-text-gray-600">
            <i class="ri-close-line tw-text-2xl"></i>
        </button>

        <!-- Header -->
        <div class="tw-px-8 tw-pt-8 tw-pb-2">
            <h2 class="tw-text-2xl tw-font-bold tw-text-gray-800">Chỉnh sửa Agent</h2>
            <p class="tw-text-sm tw-text-gray-500 tw-mt-1">Cập nhật thông tin cho Agent của bạn</p>
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

                <!-- Danh sách file đã upload (existing) -->
                <div x-show="isLoadingFiles" class="tw-text-center tw-py-4">
                    <i class="ri-loader-4-line tw-animate-spin tw-text-xl tw-text-gray-400"></i>
                    <span class="tw-text-sm tw-text-gray-400 tw-ml-2">Đang tải danh sách file...</span>
                </div>

                <div x-show="existingFiles.length > 0 && !isLoadingFiles" class="tw-space-y-2 tw-mb-3">
                    <p class="tw-text-xs tw-font-semibold tw-text-gray-500 tw-uppercase tw-tracking-wider">File đã
                        upload</p>
                    <template x-for="file in existingFiles" :key="file.id">
                        <div class="tw-flex tw-items-center tw-justify-between tw-px-3 tw-py-2 tw-rounded-lg tw-border tw-transition"
                            :class="isMarkedForDeletion(file.id) 
                                ? 'tw-bg-red-50 tw-border-red-200 tw-opacity-60' 
                                : 'tw-bg-gray-50 tw-border-gray-100'">
                            <div class="tw-flex tw-items-center tw-gap-2 tw-min-w-0">
                                <i :class="getFileIcon(file.filename)" class="tw-text-lg tw-flex-shrink-0"></i>
                                <span x-text="file.filename" class="tw-text-sm tw-text-gray-700 tw-truncate"
                                    :class="isMarkedForDeletion(file.id) ? 'tw-line-through' : ''"></span>
                                <span x-text="formatSize(file.file_size)"
                                    class="tw-text-xs tw-text-gray-400 tw-flex-shrink-0"></span>
                                <!-- Status badge -->
                                <span x-text="getStatusLabel(file.status)" :class="getStatusClass(file.status)"
                                    class="tw-text-xs tw-px-2 tw-py-0.5 tw-rounded-full tw-flex-shrink-0"></span>
                            </div>
                            <!-- Nút xóa / hoàn tác -->
                            <button x-show="!isMarkedForDeletion(file.id)" @click.stop="markFileForDeletion(file.id)"
                                class="tw-text-gray-400 hover:tw-text-red-500 tw-transition tw-flex-shrink-0 tw-ml-2"
                                title="Xóa file">
                                <i class="ri-close-circle-line tw-text-lg"></i>
                            </button>
                            <button x-show="isMarkedForDeletion(file.id)" @click.stop="unmarkFileForDeletion(file.id)"
                                class="tw-text-red-400 hover:tw-text-green-500 tw-transition tw-flex-shrink-0 tw-ml-2"
                                title="Hoàn tác xóa">
                                <i class="ri-arrow-go-back-line tw-text-lg"></i>
                            </button>
                        </div>
                    </template>
                </div>

                <!-- Danh sách file mới (chưa upload) -->
                <div x-show="newFiles.length > 0" class="tw-space-y-2 tw-mb-3">
                    <p class="tw-text-xs tw-font-semibold tw-text-green-600 tw-uppercase tw-tracking-wider">File mới</p>
                    <template x-for="(file, index) in newFiles" :key="'new-' + index">
                        <div
                            class="tw-flex tw-items-center tw-justify-between tw-px-3 tw-py-2 tw-bg-green-50 tw-rounded-lg tw-border tw-border-green-100">
                            <div class="tw-flex tw-items-center tw-gap-2 tw-min-w-0">
                                <i :class="getFileIcon(file.name)" class="tw-text-lg tw-flex-shrink-0"></i>
                                <span x-text="file.name" class="tw-text-sm tw-text-gray-700 tw-truncate"></span>
                                <span x-text="formatSize(file.size)"
                                    class="tw-text-xs tw-text-gray-400 tw-flex-shrink-0"></span>
                                <span
                                    class="tw-text-xs tw-px-2 tw-py-0.5 tw-rounded-full tw-bg-green-100 tw-text-green-600 tw-flex-shrink-0">Mới</span>
                            </div>
                            <button @click.stop="removeNewFile(index)"
                                class="tw-text-gray-400 hover:tw-text-red-500 tw-transition tw-flex-shrink-0 tw-ml-2"
                                title="Xóa file">
                                <i class="ri-close-circle-line tw-text-lg"></i>
                            </button>
                        </div>
                    </template>
                </div>

                <!-- Input file ẩn -->
                <input type="file" x-ref="editFileInput" accept=".pdf,.doc,.docx,.txt" multiple class="tw-hidden"
                    @change="handleFileSelect($event)">

                <!-- Vùng kéo thả / click chọn thêm file -->
                <div @click="$refs.editFileInput.click()"
                    @dragover.prevent="$event.currentTarget.classList.add('tw-border-green-400', 'tw-bg-green-50')"
                    @dragleave.prevent="$event.currentTarget.classList.remove('tw-border-green-400', 'tw-bg-green-50')"
                    @drop.prevent="
                        $event.currentTarget.classList.remove('tw-border-green-400', 'tw-bg-green-50');
                        const dt = new DataTransfer();
                        for (const f of $event.dataTransfer.files) dt.items.add(f);
                        $refs.editFileInput.files = dt.files;
                        $refs.editFileInput.dispatchEvent(new Event('change'));
                    "
                    class="tw-border-2 tw-border-dashed tw-border-gray-200 tw-rounded-xl tw-p-4 tw-text-center hover:tw-bg-gray-50 tw-transition tw-cursor-pointer">
                    <div class="tw-flex tw-flex-col tw-items-center tw-justify-center tw-gap-1">
                        <i class="ri-upload-cloud-2-line tw-text-xl tw-text-gray-400"></i>
                        <span class="tw-font-bold tw-text-sm tw-text-gray-800">Thêm tài liệu</span>
                        <span class="tw-text-xs tw-text-gray-400">PDF, DOC, DOCX, TXT (tối đa 10MB/file)</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="tw-px-8 tw-py-6 tw-bg-white tw-flex tw-items-center tw-justify-between tw-gap-3">
            <!-- Thông báo upload progress -->
            <div class="tw-flex-1">
                <p x-show="uploadProgress" x-text="uploadProgress" class="tw-text-sm tw-text-blue-600"></p>
                <!-- Thông báo nếu có file sẽ bị xóa -->
                <p x-show="deletedFileIds.length > 0 && !uploadProgress" class="tw-text-sm tw-text-red-500">
                    <span x-text="deletedFileIds.length"></span> file sẽ bị xóa khi lưu
                </p>
            </div>
            <div class="tw-flex tw-gap-3">
                <button @click="openEditModal = false"
                    class="tw-px-6 tw-py-2.5 tw-border tw-border-gray-300 tw-rounded-lg tw-text-gray-700 tw-font-semibold hover:tw-bg-gray-50 tw-transition">
                    Hủy
                </button>
                <button @click="saveAgent()" :disabled="isSaving"
                    class="tw-px-6 tw-py-2.5 tw-bg-[#1AA24C] tw-text-white tw-rounded-lg tw-font-semibold hover:tw-bg-[#15803d] tw-transition disabled:tw-opacity-50 disabled:tw-cursor-not-allowed tw-flex tw-items-center tw-gap-2">
                    <span x-show="isSaving" class="tw-animate-spin"><i class="ri-loader-4-line"></i></span>
                    <span
                        x-text="isSaving ? (uploadProgress ? 'Đang xử lý...' : 'Đang lưu...') : 'Lưu thay đổi'"></span>
                </button>
            </div>
        </div>
    </div>
</div>