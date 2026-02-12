<div class="tw-flex tw-flex-col tw-h-full" x-data="chatComponent({
        convId: @entangle('convId'),
        agentType: @entangle('agentType'),
        agentId: @entangle('agentId'),
        brandId: @entangle('brandId'),
        messages: @entangle('messages'),
        chatModel: '{{ $chatModel }}',
        isModelLocked: {{ $isModelLocked ? 'true' : 'false' }}
    })">

    <!-- Header info passed from props/mount -->
    <div class="tw-hidden md:tw-flex tw-px-3 tw-py-3 tw-border-b tw-border-gray-100 tw-items-center tw-gap-3">
        <div id="logo-chat">
            <img src="{{ asset('assets/img/logo-nenTangDuLieu.svg') }}"
                class="tw-w-[38px] tw-h-[38px] tw-object-contain" />
        </div>
        <div class="tw-flex-1 tw-min-w-0">
            <div class="tw-font-semibold tw-truncate tw-overflow-hidden tw-whitespace-nowrap">
                {{ $title }}
            </div>
            <div class="tw-text-sm tw-text-gray-500 tw-truncate tw-overflow-hidden tw-whitespace-nowrap">
                {{ $description }}
            </div>
        </div>
    </div>

    <!-- Messages Area -->
    <section id="chat-messages-container" class="tw-flex-1 tw-overflow-y-auto tw-px-5 tw-py-4 tw-bg-[#F3F7F5]">
        <div class="tw-space-y-4"> <!-- Increased spacing -->
            <template x-for="(msg, index) in messages" :key="index">
                <div>
                    <!-- User Message -->
                    <template x-if="msg.role === 'user'">
                        <div class="tw-flex tw-justify-end tw-gap-3">
                            <div class="tw-flex tw-flex-col tw-items-end tw-gap-1 tw-max-w-[80%]">
                                <div class="tw-text-xs tw-text-gray-500">User</div> <!-- Or name if avail -->
                                <div
                                    class="tw-bg-[#45C974] tw-text-white tw-px-4 tw-py-3 tw-rounded-2xl tw-rounded-tr-none">
                                    <div x-html="formatMessage(msg.content)"></div>
                                </div>
                            </div>
                        </div>
                    </template>

                    <!-- Assistant Message -->
                    <template x-if="msg.role === 'assistant'">
                        <div class="tw-flex tw-justify-start tw-gap-3">
                            <div class="tw-flex-shrink-0">
                                <img src="{{ asset('assets/img/logo-nenTangDuLieu.svg') }}"
                                    class="tw-w-8 tw-h-8 tw-object-contain tw-rounded-full tw-bg-white tw-border tw-border-gray-100 tw-p-1" />
                            </div>
                            <div class="tw-flex tw-flex-col tw-items-start tw-gap-1 tw-max-w-[80%]">
                                <div class="tw-text-xs tw-text-gray-500">Brand AI</div>
                                <div
                                    class="tw-bg-white tw-text-gray-800 tw-border tw-border-gray-200 tw-px-4 tw-py-3 tw-rounded-2xl tw-rounded-tl-none">
                                    <!-- Use html for markdown support later, but text for now -->
                                    <!-- Use html for markdown support later, but text for now -->
                                    <div x-html="formatMessage(msg.content)"></div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </template>

            <!-- Loading Indicator (Optional) -->
            <div x-show="isStreaming && messages[messages.length-1].content === ''"
                class="tw-flex tw-justify-start tw-gap-3">
                <div class="tw-flex-shrink-0">
                    <img src="{{ asset('assets/img/logo-nenTangDuLieu.svg') }}"
                        class="tw-w-8 tw-h-8 tw-object-contain tw-rounded-full tw-bg-white tw-border tw-border-gray-100 tw-p-1" />
                </div>
                <div class="tw-flex tw-items-center tw-bg-gray-100 tw-px-4 tw-py-2 tw-rounded-full">
                    <svg class="tw-animate-spin tw-h-5 tw-w-5 tw-text-gray-500" xmlns="http://www.w3.org/2000/svg"
                        fill="none" viewBox="0 0 24 24">
                        <circle class="tw-opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                        </circle>
                        <path class="tw-opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                </div>
            </div>

        </div>
    </section>

    <!-- Input Bar -->
    <div
        class="tw-w-full tw-bg-white tw-border-t tw-border-gray-200 tw-px-5 tw-py-4 tw-flex tw-flex-col tw-justify-end">
        <div class="tw-flex-1 tw-space-y-2">
            <div class="tw-flex tw-items-end tw-gap-3 tw-flex-nowrap">
                <!-- Input file ẩn để upload file (image, pdf, word, txt) -->
                <input type="file" id="chat-file-upload" class="tw-hidden"
                    accept="image/*,.pdf,.doc,.docx,.txt,text/plain,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document"
                    multiple @change="handleFileUpload($event)" />

                <!-- Nút bấm để trigger chọn file -->
                <button type="button" id="chat-file-upload-btn"
                    @click="document.getElementById('chat-file-upload').click()"
                    title="Đính kèm file (Hình ảnh, PDF, Word, TXT)"
                    class="tw-w-12 tw-h-12 tw-bg-vlbcgreen tw-text-white tw-rounded-md tw-flex tw-items-center tw-justify-center hover:tw-bg-[#15803d] tw-transition-colors tw-relative"
                    :class="{ 'tw-opacity-50': isUploading }">
                    <img src="{{ asset('assets/img/icon-plus-white.svg') }}"
                        class="tw-w-[20px] tw-h-[20px] tw-object-contain" />
                    <!-- Badge số file đã upload -->
                    <span x-show="uploadedFiles.length > 0"
                        class="tw-absolute tw--top-1 tw--right-1 tw-bg-red-500 tw-text-white tw-text-[10px] tw-w-5 tw-h-5 tw-rounded-full tw-flex tw-items-center tw-justify-center tw-font-bold"
                        x-text="uploadedFiles.length"></span>
                </button>

                <!-- Input Textarea bound to x-model userInput -->
                <!-- Shift+Enter: xuống dòng, Enter: gửi tin nhắn -->
                <textarea x-ref="userInput" x-model="userInput"
                    @keydown.enter="if(!$event.shiftKey) { $event.preventDefault(); sendMessage() }"
                    @paste="handlePaste($event)" rows="1"
                    class="tw-flex-1 tw-min-h-12 tw-resize-none tw-overflow-y-auto tw-border tw-border-gray-200 tw-rounded-md tw-px-3 tw-py-2 tw-text-sm focus:tw-outline-none focus:tw-ring-2 focus:tw-ring-[#16a34a]/40"
                    placeholder="Ask anything..." :disabled="isStreaming || isUploading"></textarea>

                <button @click="sendMessage()" :disabled="!userInput.trim() || isStreaming || isUploading">
                    <img src="{{ asset('assets/img/enter-button.svg') }}"
                        class="tw-w-[48px] tw-h-[48px] tw-object-contain"
                        :class="{'tw-opacity-50': !userInput.trim() || isStreaming || isUploading}" />
                </button>
            </div>

            <!-- Hiển thị danh sách file đã upload và trạng thái -->
            <div x-show="uploadedFiles.length > 0" class="tw-mt-2 tw-flex tw-flex-wrap tw-gap-2">
                <template x-for="(file, idx) in uploadedFiles" :key="file.id || idx">
                    <div
                        class="tw-flex tw-items-center tw-gap-1 tw-px-2 tw-py-1 tw-bg-gray-100 tw-rounded-md tw-text-xs">
                        <!-- Icon theo trạng thái -->
                        <span x-show="file.status === 'pending' || file.status === 'processing'"
                            class="tw-animate-spin tw-text-yellow-500">⌛</span>
                        <span x-show="file.status === 'completed'" class="tw-text-green-600">✅</span>
                        <span x-show="file.status === 'failed'" class="tw-text-red-500">❌</span>

                        <!-- Tên file (truncate) -->
                        <span class="tw-max-w-[100px] tw-truncate" x-text="file.filename" :title="file.filename"></span>

                        <!-- Nút xóa file -->
                        <button @click="removeFile(file.id, idx)" class="tw-text-gray-400 hover:tw-text-red-500 tw-ml-1"
                            title="Xóa file">
                            <svg class="tw-w-3 tw-h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </template>
            </div>

            <div class="tw-mt-2 tw-flex tw-items-center tw-justify-between tw-text-[11px] tw-text-gray-500 tw-gap-4">
                <div class="tw-flex tw-items-center tw-gap-4 tw-flex-1 tw-min-w-0">
                    <!-- Model Selector -->
                    <div class="tw-relative">
                        <select x-model="selectedModel" :disabled="isModelLocked"
                            class="tw-appearance-none tw-border tw-text-gray-700 tw-text-xs tw-rounded-md tw-py-1.5 tw-pl-3 tw-pr-8 focus:tw-outline-none focus:tw-ring-2 focus:tw-ring-[#16a34a]/20 focus:tw-border-[#16a34a] tw-font-medium"
                            :class="isModelLocked ? 'tw-bg-gray-200 tw-border-gray-300 tw-cursor-not-allowed' : 'tw-bg-gray-50 tw-border-gray-200 tw-cursor-pointer'">
                            <option value="ChatGPT">ChatGPT</option>
                            <option value="Gemini">Gemini</option>
                        </select>
                        <div
                            class="tw-pointer-events-none tw-absolute tw-inset-y-0 tw-right-0 tw-flex tw-items-center tw-px-2 tw-text-gray-500">
                            <svg class="tw-h-3 tw-w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </div>

                    <div class="tw-h-4 tw-w-[1px] tw-bg-gray-200"></div>

                    <div class="tw-truncate tw-overflow-hidden tw-whitespace-nowrap">
                        Kết quả phân tích sẽ hiển thị ở bên phải
                    </div>
                </div>
                <button @click="openSaveModal()" id="btn-confirm-analysis"
                    class="tw-px-4 tw-py-2 tw-rounded-md tw-text-sm tw-font-medium tw-transition-all tw-duration-200"
                    :class="isConfirmationActive ? 'tw-bg-[#16a34a] tw-text-white tw-cursor-pointer' : 'tw-bg-gray-200 tw-text-gray-400 tw-cursor-not-allowed'"
                    :disabled="!isConfirmationActive">
                    Xác nhận phân tích
                </button>
            </div>
        </div>
    </div>

    <!-- Modal 1: Edit & Save -->
    <div x-show="showSaveModal" style="display: none;"
        class="tw-fixed tw-inset-0 tw-z-50 tw-flex tw-items-center tw-justify-center tw-bg-black/50 tw-backdrop-blur-sm"
        x-transition:enter="tw-transition tw-ease-out tw-duration-300" x-transition:enter-start="tw-opacity-0"
        x-transition:enter-end="tw-opacity-100" x-transition:leave="tw-transition tw-ease-in tw-duration-200"
        x-transition:leave-start="tw-opacity-100" x-transition:leave-end="tw-opacity-0">

        <div class="tw-bg-white tw-rounded-xl tw-shadow-xl tw-w-[90%] md:tw-w-[80%] tw-max-w-4xl tw-flex tw-flex-col tw-max-h-[90vh]"
            @click.away="showSaveModal = false">

            <!-- Header -->
            <div class="tw-px-6 tw-py-4 tw-border-b tw-border-gray-100 tw-flex tw-items-center tw-justify-between">
                <div class="tw-flex tw-items-center tw-gap-3">
                    <h3 class="tw-text-lg tw-font-semibold tw-text-gray-800">Xác nhận và Lưu thông tin</h3>
                    <button
                        class="tw-px-3 tw-py-1.5 tw-text-xs tw-font-medium tw-text-blue-600 tw-bg-blue-50 tw-rounded-md hover:tw-bg-blue-100 tw-transition-colors">
                        Trích xuất dữ liệu
                    </button>
                </div>
                <button @click="showSaveModal = false" class="tw-text-gray-400 hover:tw-text-gray-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="tw-h-6 tw-w-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Body -->
            <div class="tw-flex-1 tw-p-6 tw-overflow-y-auto">
                <textarea x-model="editingContent" spellcheck="false"
                    class="tw-w-full tw-h-[60vh] tw-p-4 tw-border tw-border-gray-200 tw-rounded-lg focus:tw-ring-2 focus:tw-ring-[#16a34a] focus:tw-border-transparent tw-resize-none tw-text-sm"
                    style="font-family: inherit;" placeholder="Hãy bấm nút trích xuất dữ liệu ở trên"></textarea>
            </div>

            <!-- Footer -->
            <div class="tw-px-6 tw-py-4 tw-bg-gray-50 tw-rounded-b-xl tw-flex tw-justify-end tw-gap-3">
                <button @click="showSaveModal = false"
                    class="tw-px-4 tw-py-2 tw-text-gray-600 tw-bg-white tw-border tw-border-gray-300 tw-rounded-lg hover:tw-bg-gray-50">
                    Hủy bỏ
                </button>
                <button @click="saveAnalysis()" :disabled="isSaving"
                    class="tw-px-4 tw-py-2 tw-text-white tw-bg-[#16a34a] tw-rounded-lg hover:tw-bg-[#15803d] tw-flex tw-items-center tw-gap-2">
                    <span x-show="isSaving" class="tw-animate-spin">⏳</span>
                    <span x-text="isSaving ? 'Đang lưu...' : 'Lưu vào thương hiệu'"></span>
                </button>
            </div>
        </div>
    </div>

    <!-- Modal 2: Success & Navigate -->
    <div x-show="showSuccessModal" style="display: none;"
        class="tw-fixed tw-inset-0 tw-z-50 tw-flex tw-items-center tw-justify-center tw-bg-black/50 tw-backdrop-blur-sm"
        x-transition:enter="tw-transition tw-ease-out tw-duration-300"
        x-transition:enter-start="tw-opacity-0 tw-scale-95" x-transition:enter-end="tw-opacity-100 tw-scale-100"
        x-transition:leave="tw-transition tw-ease-in tw-duration-200"
        x-transition:leave-start="tw-opacity-100 tw-scale-100" x-transition:leave-end="tw-opacity-0 tw-scale-95">

        <div class="tw-bg-white tw-rounded-xl tw-shadow-xl tw-w-[90%] tw-max-w-md tw-p-6 tw-text-center">
            <div class="tw-mb-4 tw-text-[#16a34a] tw-flex tw-justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="tw-h-16 tw-w-16" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>

            <h3 class="tw-text-xl tw-font-bold tw-text-gray-800 tw-mb-2">Đã lưu thành công!</h3>
            <p class="tw-text-gray-600 tw-mb-6">Dữ liệu đã được lưu vào thương hiệu. Bạn sẵn sàng qua bước tiếp theo
                chứ?</p>

            <div class="tw-flex tw-flex-col md:tw-flex-row tw-gap-3 tw-justify-center">
                <button @click="showSuccessModal = false"
                    class="tw-px-4 tw-py-2 tw-text-gray-600 tw-bg-gray-100 tw-rounded-lg hover:tw-bg-gray-200 tw-flex-1">
                    Ở lại trang hiện tại
                </button>
                <button @click="navigateToNextStep()"
                    class="tw-px-4 tw-py-2 tw-text-white tw-bg-[#16a34a] tw-rounded-lg hover:tw-bg-[#15803d] tw-flex-1">
                    Qua bước tiếp theo
                </button>
            </div>
        </div>
    </div>

</div>

<script>
    function chatComponent(params) {
        return {
            // State
            params: params, // Store params if needed
            userInput: '',
            isStreaming: false,
            convId: params.convId,
            agentType: params.agentType,
            agentId: params.agentId,
            brandId: params.brandId,
            messages: params.messages,

            // Model Selection
            selectedModel: params.chatModel || 'ChatGPT',
            isModelLocked: params.isModelLocked || false,

            // New State
            isConfirmationActive: false,
            showSaveModal: false,
            showSuccessModal: false,
            editingContent: '',
            isSaving: false,

            // File Upload State
            uploadedFiles: [], // [{id, filename, status: 'pending'|'processing'|'completed'|'failed'}]
            isUploading: false,

            // Setup
            init() {
                // Scroll to bottom on load
                this.scrollToBottom();

                // Auto-focus input
                this.$nextTick(() => {
                    if (this.$refs.userInput) {
                        this.$refs.userInput.focus();
                    }
                });

                // Watch for new messages
                this.$watch('messages', () => {
                    this.$nextTick(() => this.scrollToBottom());
                });
                // Check conditions on load mostly useful if history loaded
                this.checkConfirmationCondition();
            },

            scrollToBottom() {
                const container = document.getElementById('chat-messages-container');
                if (container) container.scrollTop = container.scrollHeight;
            },

            formatMessage(content) {
                if (!content) return '';

                // 1. Escape HTML
                let safeContent = content
                    .replace(/&/g, "&amp;")
                    .replace(/</g, "&lt;")
                    .replace(/>/g, "&gt;")
                    .replace(/"/g, "&quot;")
                    .replace(/'/g, "&#039;");

                // 2. Header: ### Text
                // Matches ### at start or after newline, captures content until end of line
                safeContent = safeContent.replace(/(^|\n)###\s+(.*?)(\n|$)/g, function (match, p1, p2, p3) {
                    return p1 + '<div style="font-size: 1.5em; font-weight: bold; margin-top: 0.5em; margin-bottom: 0.25em;">' + p2 + '</div>' + p3;
                });

                // 3. Bold: **text**
                safeContent = safeContent.replace(/\*\*(.*?)\*\*/g, '<b>$1</b>');

                // 3.5 Regular Italic: *text* (must come after bold)
                safeContent = safeContent.replace(/\*(.*?)\*/g, '<i>$1</i>');

                // 4. Newlines to <br>
                safeContent = safeContent.replace(/\n/g, '<br>');

                return safeContent;
            },

            // Logic to check verification keywords
            checkConfirmationCondition() {
                if (this.messages.length === 0) return;

                // Get last message (assistant)
                const lastMsg = this.messages[this.messages.length - 1];

                // Active if the last message is from assistant (removed keyword check)
                if (lastMsg.role === 'assistant') {
                    this.isConfirmationActive = true;
                    // this.editingContent = lastMsg.content; // Pre-fill content removed
                } else {
                    this.isConfirmationActive = false;
                }
            },

            openSaveModal() {
                if (this.isConfirmationActive) {
                    // Auto-fill if empty
                    if (!this.editingContent || this.editingContent.trim() === '') {
                        const lastMsg = this.messages[this.messages.length - 1];
                        if (lastMsg && lastMsg.role === 'assistant') {
                            this.editingContent = lastMsg.content;
                        }
                    }
                    this.showSaveModal = true;
                }
            },

            async saveAnalysis() {
                this.isSaving = true;

                // Get slug from URL 
                const pathParts = window.location.pathname.split('/');
                const brandSlug = pathParts[2]; // assuming /brands/{slug}/...

                try {
                    const response = await fetch(`/brands/${brandSlug}/chat/save-data`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                        },
                        body: JSON.stringify({
                            agentType: this.agentType,
                            content: this.editingContent
                        })
                    });

                    const result = await response.json();

                    if (result.status === 'success') {
                        this.showSaveModal = false;
                        this.showSuccessModal = true;
                    } else {
                        alert('Lỗi: ' + result.message);
                    }
                } catch (error) {
                    console.error('Save error:', error);
                    alert('Đã xảy ra lỗi khi lưu dữ liệu.');
                } finally {
                    this.isSaving = false;
                }
            },

            navigateToNextStep() {
                const pathParts = window.location.pathname.split('/');
                const brandSlug = pathParts[2];
                let nextUrl = '';

                // IDs are hardcoded as per user request (2, 3, 4, 5, etc.) but should ideally be dynamic.
                // Request: root1 -> root2/2, root2 -> root3/3, root3 -> trunk1/4, trunk1 -> trunk2/5, trunk2 -> canopy
                switch (this.agentType) {
                    case 'root1':
                        nextUrl = `/brands/${brandSlug}/chat/root2/2/new`;
                        break;
                    case 'root2':
                        nextUrl = `/brands/${brandSlug}/chat/root3/3/new`;
                        break;
                    case 'root3':
                        nextUrl = `/brands/${brandSlug}/chat/trunk1/4/new`;
                        break;
                    case 'trunk1':
                        nextUrl = `/brands/${brandSlug}/chat/trunk2/5/new`;
                        break;
                    case 'trunk2':
                        nextUrl = `/brands/${brandSlug}/canopy`;
                        break;
                    default:
                        // Fallback or stay
                        nextUrl = window.location.href;
                }

                window.location.href = nextUrl;
            },

            // Action
            async sendMessage() {
                if (!this.userInput.trim() || this.isStreaming) return;

                const messageContent = this.userInput;
                this.userInput = ''; // Clear input
                this.isStreaming = true;
                this.isConfirmationActive = false; // Reset on new message
                this.editingContent = ''; // Reset editing content on new turn

                // 1. Optimistically append User Message
                this.messages.push({
                    role: 'user',
                    content: messageContent
                });
                this.scrollToBottom();

                // 2. Prepare for Assistant Message Stream
                const assistantMsgIndex = this.messages.push({
                    role: 'assistant',
                    content: '' // Streaming content goes here
                }) - 1;


                try {
                    // Determine API URL based on selected model
                    const apiUrl = (this.selectedModel === 'Gemini')
                        ? '/api/chat_stream_gemini'
                        : '/api/chat_stream';

                    // Call streaming API
                    const response = await fetch(apiUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                        },
                        body: JSON.stringify({
                            message: messageContent,
                            agentType: this.agentType,
                            agentId: this.agentId,
                            convId: this.convId,
                            brandId: this.brandId,
                            model: this.selectedModel
                        })
                    });

                    if (!response.ok) throw new Error('Network response was not ok');

                    const reader = response.body.getReader();
                    const decoder = new TextDecoder();
                    let fullContent = '';
                    let dbChatId = null;
                    let buffer = '';

                    while (true) {
                        const { done, value } = await reader.read();
                        if (done) break;

                        const chunk = decoder.decode(value, { stream: true });
                        buffer += chunk;

                        const lines = buffer.split('\n');
                        buffer = lines.pop(); // Keep the last incomplete line in buffer

                        for (const line of lines) {
                            const trimmedLine = line.trim();
                            if (!trimmedLine) continue;

                            // 1. Check for custom metadata line (usually first line)
                            if (trimmedLine.startsWith('{') && trimmedLine.includes('db_chat_id')) {
                                try {
                                    const meta = JSON.parse(trimmedLine);
                                    if (meta.db_chat_id) {
                                        dbChatId = meta.db_chat_id;
                                        this.convId = dbChatId;
                                    }
                                } catch (e) {
                                    console.warn('Failed to parse metadata:', e);
                                }
                                continue;
                            }

                            // 2. Parse SSE data lines
                            if (trimmedLine.startsWith('data: ')) {
                                const jsonStr = trimmedLine.substring(6).trim();
                                if (jsonStr === '[DONE]') continue; // Standard end (though responses api might differ, harmless)

                                try {
                                    const data = JSON.parse(jsonStr);

                                    // Handle Response API 'response.output_text.delta'
                                    if (data.type === 'response.output_text.delta' && data.delta) {
                                        fullContent += data.delta;

                                        // Update UI live
                                        this.messages[assistantMsgIndex].content = fullContent;
                                        this.scrollToBottom();
                                    }
                                } catch (e) {
                                    console.warn('Failed to parse SSE JSON:', e);
                                }
                            }
                        }
                    }

                    // Flush any remaining buffer if needed (unlikely to be valid complete line, but good practice)
                    if (buffer.trim()) {
                        // handle last line if valid
                    }

                    // Stream finished. 
                    this.isStreaming = false;
                    this.isModelLocked = true; // Lock model after first message
                    this.$nextTick(() => this.$refs.userInput.focus());

                    // ✨ Đổi URL mượt mà nếu là chat mới (từ /new sang /{id})
                    if (dbChatId && this.convId === dbChatId) {
                        const pathParts = window.location.pathname.split('/');
                        const lastPart = pathParts[pathParts.length - 1];

                        if (lastPart === 'new') {
                            // Đổi URL mà không reload trang
                            pathParts[pathParts.length - 1] = dbChatId;
                            const newUrl = pathParts.join('/');
                            window.history.replaceState({}, '', newUrl);

                            // 🔄 Dispatch event để sidebar refresh danh sách chat
                            window.dispatchEvent(new CustomEvent('chat-created', {
                                detail: { chatId: dbChatId }
                            }));
                        }
                    }

                    // CHECK CONFIRMATION
                    this.checkConfirmationCondition();

                    // 3. Save Assistant Message to DB
                    if (dbChatId && fullContent) {
                        await fetch('/api/chat/save_message', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                            },
                            body: JSON.stringify({
                                chat_id: dbChatId,
                                content: fullContent
                            })
                        });
                    }

                } catch (error) {
                    console.error('Stream error:', error);
                    this.isStreaming = false;
                    this.messages[assistantMsgIndex].content += '\n[Lỗi kết nối hoặc xử lý]';
                }
            },

            // === FILE UPLOAD METHODS ===
             /**              * Xử lý khi người dùng chọn file              */
            async handleFileUpload(event) {
                const files = event.target.files;
                if (!files || files.length === 0) return;

                this.isUploading = true;
                const csrfToken = document.querySelector('meta[name=csrf-token]').content;

                for (const file of files) {
                    // Kiểm tra size (max 10MB)
                    if (file.size > 10 * 1024 * 1024) {
                        alert(`File "${file.name}" quá lớn (tối đa 10MB)`);
                        continue;
                    }

                    const formData = new FormData();
                    formData.append('file', file);
                    formData.append('chat_id', this.convId || '');
                    // Gửi thêm thông tin để tạo chat mới nếu chat_id rỗng
                    formData.append('brand_id', this.brandId || '');
                    formData.append('agent_type', this.agentType || '');
                    formData.append('agent_id', this.agentId || '');

                    try {
                        const response = await fetch('/api/files/upload', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken
                            },
                            body: formData
                        });

                        const result = await response.json();

                        if (response.ok && result.success) {
                            // Nếu server tạo chat mới → cập nhật convId và URL
                            if (result.is_new_chat && result.chat_id) {
                                this.convId = result.chat_id;

                                // Đổi URL trên thanh địa chỉ: /new → /{chatId}
                                const pathParts = window.location.pathname.split('/');
                                const lastPart = pathParts[pathParts.length - 1];
                                if (lastPart === 'new') {
                                    pathParts[pathParts.length - 1] = result.chat_id;
                                    const newUrl = pathParts.join('/');
                                    window.history.replaceState({}, '', newUrl);
                                }

                                // Dispatch event để sidebar refresh danh sách chat
                                window.dispatchEvent(new CustomEvent('chat-created', {
                                    detail: { chatId: result.chat_id }
                                }));
                            }

                            // Thêm file vào danh sách
                            const newFile = {
                                id: result.file_id,
                                filename: result.filename || file.name,
                                status: result.status || 'pending'
                            };
                            this.uploadedFiles.push(newFile);

                            // Poll status nếu chưa completed
                            if (newFile.status !== 'completed') {
                                this.pollFileStatus(newFile.id);
                            }
                        } else {
                            alert(`Lỗi upload "${file.name}": ${result.error || result.message || 'Unknown error'}`);
                        }
                    } catch (error) {
                        console.error('Upload error:', error);
                        alert(`Lỗi upload "${file.name}": ${error.message}`);
                    }
                }

                this.isUploading = false;
                // Reset input
                event.target.value = '';

                // Focus back to input
                this.$nextTick(() => {
                    if (this.$refs.userInput) {
                        this.$refs.userInput.focus();
                    }
                });
            },

            /**
             * Xử lý paste hình từ clipboard (Ctrl+V)
             * Nếu clipboard có hình → tạo File object → upload như file thông thường
             */
            handlePaste(event) {
                const clipboardData = event.clipboardData || window.clipboardData;
                if (!clipboardData) return;

                const items = clipboardData.items;
                if (!items) return;

                // Tìm item là hình ảnh trong clipboard
                const imageFiles = [];
                for (let i = 0; i < items.length; i++) {
                    if (items[i].type.startsWith('image/')) {
                        const blob = items[i].getAsFile();
                        if (blob) {
                            // Tạo File object với tên có timestamp
                            const ext = items[i].type.split('/')[1] || 'png';
                            const fileName = `pasted_image_${Date.now()}.${ext}`;
                            const file = new File([blob], fileName, { type: items[i].type });
                            imageFiles.push(file);
                        }
                    }
                }

                // Nếu không có hình → để paste text bình thường
                if (imageFiles.length === 0) return;

                // Ngăn paste text/hình mặc định vào textarea
                event.preventDefault();

                // Tái sử dụng logic upload file
                this.handleFileUpload({ target: { files: imageFiles } });
            },

             /**              * Poll trạng thái file processing              */
            async pollFileStatus(fileId) {
                const maxAttempts = 60; // 60 * 2s = 2 phút max
                let attempts = 0;

                const pollInterval = setInterval(async () => {
                    attempts++;

                    try {
                        const response = await fetch(`/api/files/${fileId}/status`);
                        const result = await response.json();

                        if (result.status) {
                            // Cập nhật status trong uploadedFiles
                            const fileIndex = this.uploadedFiles.findIndex(f => f.id === fileId);
                            if (fileIndex !== -1) {
                                this.uploadedFiles[fileIndex].status = result.status;
                            }

                            // Nếu đã xong (completed/failed), dừng poll
                            if (result.status === 'completed' || result.status === 'failed') {
                                clearInterval(pollInterval);
                            }
                        }
                    } catch (error) {
                        console.error('Poll status error:', error);
                    }

                    // Timeout
                    if (attempts >= maxAttempts) {
                        clearInterval(pollInterval);
                    }
                }, 2000); // Poll mỗi 2 giây
            },
             /**              * Xóa file đã upload              */
            async removeFile(fileId, index) {
                if (!fileId) {
                    this.uploadedFiles.splice(index, 1);
                    return;
                }

                try {
                    const response = await fetch(`/api/files/${fileId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                        }
                    });

                    if (response.ok) {
                        this.uploadedFiles.splice(index, 1);
                    } else {
                        const result = await response.json();
                        alert(`Lỗi xóa file: ${result.message || 'Unknown error'}`);
                    }
                } catch (error) {
                    console.error('Delete file error:', error);
                    // Vẫn xóa khỏi UI
                    this.uploadedFiles.splice(index, 1);
                }
            }
        }
    }
</script>