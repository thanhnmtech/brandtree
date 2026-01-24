<div class="tw-flex tw-flex-col tw-h-full" x-data="chatComponent({
        convId: @entangle('convId'),
        agentType: @entangle('agentType'),
        agentId: @entangle('agentId'),
        brandId: @entangle('brandId'),
        messages: @entangle('messages')
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
                <button type="newchat"
                    class="tw-w-12 tw-h-12 tw-bg-vlbcgreen tw-text-white tw-rounded-md tw-flex tw-items-center tw-justify-center"
                    @click="window.location.href='/chat/' + brandId + '/root/1/new'">
                    <img src="{{ asset('assets/img/icon-plus-white.svg') }}"
                        class="tw-w-[20px] tw-h-[20px] tw-object-contain" />
                </button>

                <!-- Input Textarea bound to x-model userInput -->
                <textarea x-ref="userInput" x-model="userInput"
                    @keydown.enter.prevent="if(!$event.shiftKey) sendMessage()" rows="1"
                    class="tw-flex-1 tw-min-h-12 tw-resize-none tw-overflow-y-auto tw-border tw-border-gray-200 tw-rounded-md tw-px-3 tw-py-2 tw-text-sm focus:tw-outline-none focus:tw-ring-2 focus:tw-ring-[#16a34a]/40"
                    placeholder="Ask anything..." :disabled="isStreaming"></textarea>

                <button @click="sendMessage()" :disabled="!userInput.trim() || isStreaming">
                    <img src="{{ asset('assets/img/enter-button.svg') }}"
                        class="tw-w-[48px] tw-h-[48px] tw-object-contain"
                        :class="{'tw-opacity-50': !userInput.trim() || isStreaming}" />
                </button>
            </div>

            <div class="tw-mt-1 tw-flex tw-items-center tw-justify-between tw-text-[11px] tw-text-gray-500">
                <div class="tw-flex-1 tw-min-w-0">
                    <span class="tw-hidden md:tw-block tw-truncate tw-overflow-hidden tw-whitespace-nowrap">
                        Kết quả phân tích sẽ hiển thị ở bên phải
                    </span>
                </div>
                {{-- <button @click="openSaveModal()" id="btn-confirm-analysis"
                    class="tw-px-4 tw-py-2 tw-rounded-md tw-text-sm tw-font-medium tw-transition-all tw-duration-200"
                    :class="isConfirmationActive ? 'tw-bg-[#16a34a] tw-text-white tw-cursor-pointer' : 'tw-bg-gray-200 tw-text-gray-400 tw-cursor-not-allowed'"
                    :disabled="!isConfirmationActive"
                    :title="!isConfirmationActive ? 'Yêu cầu AI thêm từ \'xác nhận\' và \'lưu thông tin\' vào câu trả lời để lưu lại!' : ''">
                    Xác nhận phân tích
                </button> --}}
                <div class="tw-relative tw-inline-block">
                    <button @click="openSaveModal()" id="btn-confirm-analysis"
                        class="tw-px-4 tw-py-2 tw-rounded-md tw-text-sm tw-font-medium tw-transition-all tw-duration-200"
                        :class="isConfirmationActive ? 'tw-bg-[#16a34a] tw-text-white tw-cursor-pointer' : 'tw-bg-gray-200 tw-text-gray-400 tw-cursor-not-allowed'"
                        :disabled="!isConfirmationActive"
                        @mouseenter="!isConfirmationActive && (showTooltip = true)"
                        @mouseleave="showTooltip = false">
                        Xác nhận phân tích
                    </button>
                    
                    <!-- Custom Tooltip -->
                    <div x-show="showTooltip && !isConfirmationActive"
                        x-transition:enter="tw-transition tw-ease-out tw-duration-200"
                        x-transition:enter-start="tw-opacity-0 tw-scale-95"
                        x-transition:enter-end="tw-opacity-100 tw-scale-100"
                        x-transition:leave="tw-transition tw-ease-in tw-duration-150"
                        x-transition:leave-start="tw-opacity-100 tw-scale-100"
                        x-transition:leave-end="tw-opacity-0 tw-scale-95"
                        style="display: none;"
                        class="tw-absolute tw-bottom-full tw-right-0 tw-mb-3 tw-w-72 tw-bg-gradient-to-r tw-from-gray-800 tw-to-gray-900 tw-text-white tw-rounded-lg tw-px-4 tw-py-3 tw-shadow-2xl tw-z-50 tw-border tw-border-gray-700">
                        
                        <!-- Icon + Content -->
                        <div class="tw-flex tw-items-start tw-gap-2">
                            <div class="tw-flex-shrink-0 tw-mt-0.5">
                                <svg class="tw-w-5 tw-h-5 tw-text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="tw-text-sm tw-leading-relaxed">
                                <div class="tw-font-semibold tw-mb-1 tw-text-yellow-400">Cách kích hoạt nút này:</div>
                                <div class="tw-text-gray-200">
                                    Yêu cầu AI thêm cụm từ 
                                    <span class="tw-inline-block tw-bg-green-500/20 tw-text-green-300 tw-px-2 tw-py-0.5 tw-rounded tw-font-semibold tw-mx-0.5">"xác nhận"</span>
                                    và
                                    <span class="tw-inline-block tw-bg-green-500/20 tw-text-green-300 tw-px-2 tw-py-0.5 tw-rounded tw-font-semibold tw-mx-0.5">"lưu thông tin"</span>
                                    vào câu trả lời
                                </div>
                            </div>
                        </div>
                        
                        <!-- Arrow -->
                        <div class="tw-absolute tw-top-full tw-right-8 tw-w-0 tw-h-0" 
                            style="border-left: 8px solid transparent; border-right: 8px solid transparent; border-top: 8px solid #1f2937;"></div>
                    </div>
                </div>
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
                <h3 class="tw-text-lg tw-font-semibold tw-text-gray-800">Xác nhận và Lưu thông tin</h3>
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
                    style="font-family: inherit;" placeholder="Nội dung phân tích..."></textarea>
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
            userInput: '',
            isStreaming: false,
            convId: params.convId,
            agentType: params.agentType,
            agentId: params.agentId,
            brandId: params.brandId,
            messages: params.messages,

            // New State
            isConfirmationActive: false,
            showSaveModal: false,
            showSuccessModal: false,
            editingContent: '',
            isSaving: false,
            showTooltip: false,

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
                if (lastMsg.role !== 'assistant') return;

                const content = lastMsg.content.toLowerCase();
                // Check keywords: "xác nhận" AND "lưu thông tin"
                if (content.includes('xác nhận') && content.includes('lưu thông tin')) {
                    this.isConfirmationActive = true;
                    this.editingContent = lastMsg.content; // Pre-fill content
                } else {
                    this.isConfirmationActive = false;
                }
            },

            openSaveModal() {
                if (this.isConfirmationActive) {
                    this.showSaveModal = true;
                }
            },

            // encoding
            ensureUTF8(text) {
                if (!text || typeof text !== 'string') {
                    return '';
                }
                if (typeof text.normalize === 'function') {
                    try {
                        text = text.normalize('NFC');
                    } catch (e) {
                        console.warn('Unicode normalization failed:', e);
                    }
                }
                text = text
                    .replace(/[\u200B-\u200D\uFEFF]/g, '')
                    // Soft Hyphen
                    .replace(/\u00AD/g, '');
                text = text.replace(/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/g, '');
                text = text.replace(/\r\n/g, '\n').replace(/\r/g, '\n');
                text = text
                    .replace(/[\u201C\u201D]/g, '"')  // " " → "
                    .replace(/[\u2018\u2019]/g, "'")  // ' ' → '
                    .replace(/[\u2013\u2014]/g, '-'); // – — → -
                if (text.charCodeAt(0) === 0xFEFF) {
                    text = text.substring(1);
                }
                text = text.trim();
                if (this.debugMode) {
                    this.validateUTF8(text);
                }
                
                return text;
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

                        window.dispatchEvent(new Event('data-saved'));
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
                    // Call streaming API
                    const response = await fetch('/api/chat_stream', {
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
                            brandId: this.brandId
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
                    this.$nextTick(() => this.$refs.userInput.focus());

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
            }
        }
    }

    // Alpine component for Result Bar - UI -- 25-01-2026
    function resultBarComponent(config) {
        return {
            brandId: config.brandId,
            agentType: config.agentType,
            savedData: null,
            loading: false,

            init() {
                this.fetchSavedData();
                window.addEventListener('data-saved', () => {
                    this.fetchSavedData();
                });
            },

            async fetchSavedData() {
                if (!this.brandId || !this.agentType) return;
                
                this.loading = true;
                
                try {
                    const brandSlug = window.location.pathname.split('/')[2];
                    const response = await fetch(`/brands/${brandSlug}/chat/get-data?agentType=${this.agentType}`);
                    const result = await response.json();
                    
                    if (result.status === 'success' && result.data) {
                        // Data đã được chuẩn hóa từ backend
                        this.savedData = result.data;
                    } else {
                        this.savedData = null;
                    }
                } catch (error) {
                    console.error('Error fetching saved data:', error);
                    this.savedData = null;
                } finally {
                    this.loading = false;
                }
            },

            formatContent(content) {
                if (!content) return '';
                
                let formatted = content
                    .replace(/&/g, "&amp;")
                    .replace(/</g, "&lt;")
                    .replace(/>/g, "&gt;")
                    .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
                    .replace(/\*(.*?)\*/g, '<em>$1</em>')
                    .replace(/\n/g, '<br>');
                
                return formatted;
            },

            // async editData() {
            //     // Populate modal với raw_content
            //     const editContent = this.savedData.raw_content || this.savedData.body;
                
            //     // Trigger edit modal (có thể dispatch event cho chat component)
            //     window.dispatchEvent(new CustomEvent('edit-data', { 
            //         detail: { 
            //             content: editContent,
            //             agentType: this.agentType 
            //         } 
            //     }));
            // },

            // async deleteData() {
            //     if (!confirm('Bạn có chắc muốn xóa dữ liệu này?')) return;
                
            //     try {
            //         const brandSlug = window.location.pathname.split('/')[2];
            //         const response = await fetch(`/brands/${brandSlug}/chat/delete-data`, {
            //             method: 'POST',
            //             headers: {
            //                 'Content-Type': 'application/json',
            //                 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
            //             },
            //             body: JSON.stringify({
            //                 agentType: this.agentType
            //             })
            //         });
                    
            //         const result = await response.json();
            //         if (result.status === 'success') {
            //             this.savedData = null;
            //         }
            //     } catch (error) {
            //         console.error('Delete error:', error);
            //     }
            // }
        }
    }
</script>