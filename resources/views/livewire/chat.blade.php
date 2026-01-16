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
                                    <p x-text="msg.content" class="tw-whitespace-pre-wrap"></p>
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
                                    <p x-text="msg.content" class="tw-whitespace-pre-wrap"></p>
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
                     <svg class="tw-animate-spin tw-h-5 tw-w-5 tw-text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="tw-opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="tw-opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
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
                <textarea x-ref="userInput" x-model="userInput" @keydown.enter.prevent="if(!$event.shiftKey) sendMessage()" rows="1"
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
                <button @click="sendMessage()"
                    class="tw-bg-[#16a34a] tw-text-white tw-px-4 tw-py-2 tw-rounded-md tw-text-sm tw-font-medium"
                    :class="{'tw-opacity-50 tw-cursor-not-allowed': !userInput.trim() || isStreaming}">
                    Xác nhận phân tích
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

            // Setup
            init() {
                // Scroll to bottom on load
                this.scrollToBottom();

                // Watch for new messages
                this.$watch('messages', () => {
                    this.$nextTick(() => this.scrollToBottom());
                });
            },

            scrollToBottom() {
                const container = document.getElementById('chat-messages-container');
                if (container) container.scrollTop = container.scrollHeight;
            },

            // Action
            async sendMessage() {
                if (!this.userInput.trim() || this.isStreaming) return;

                const messageContent = this.userInput;
                this.userInput = ''; // Clear input
                this.isStreaming = true;

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
</script>