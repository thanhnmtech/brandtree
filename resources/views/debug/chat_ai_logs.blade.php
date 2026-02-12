<x-app-layout>
    <div class="tw-py-12">
        <div class="tw-max-w-full tw-mx-auto tw-px-4 sm:tw-px-6 lg:tw-px-8">
            <div class="tw-bg-white tw-overflow-hidden tw-shadow-sm sm:tw-rounded-lg">
                <div class="tw-p-6 tw-bg-white tw-border-b tw-border-gray-200">
                    <h2 class="tw-text-2xl tw-font-bold tw-mb-4">Chat AI Logs (Debug)</h2>

                    <div class="tw-overflow-x-auto">
                        <table class="tw-min-w-full tw-divide-y tw-divide-gray-200 tw-text-sm">
                            <thead class="tw-bg-gray-50">
                                <tr>
                                    <th
                                        class="tw-px-3 tw-py-2 tw-text-left tw-text-xs tw-font-medium tw-text-gray-500 tw-uppercase tw-tracking-wider">
                                        ID</th>
                                    <th
                                        class="tw-px-3 tw-py-2 tw-text-left tw-text-xs tw-font-medium tw-text-gray-500 tw-uppercase tw-tracking-wider">
                                        Time</th>
                                    <th
                                        class="tw-px-3 tw-py-2 tw-text-left tw-text-xs tw-font-medium tw-text-gray-500 tw-uppercase tw-tracking-wider">
                                        User/Chat</th>
                                    <th
                                        class="tw-px-3 tw-py-2 tw-text-left tw-text-xs tw-font-medium tw-text-gray-500 tw-uppercase tw-tracking-wider">
                                        Agent/Brand</th>
                                    <th
                                        class="tw-px-3 tw-py-2 tw-text-left tw-text-xs tw-font-medium tw-text-gray-500 tw-uppercase tw-tracking-wider">
                                        Model</th>
                                    <th
                                        class="tw-px-3 tw-py-2 tw-text-left tw-text-xs tw-font-medium tw-text-gray-500 tw-uppercase tw-tracking-wider">
                                        User Content</th>
                                    <th
                                        class="tw-px-3 tw-py-2 tw-text-left tw-text-xs tw-font-medium tw-text-gray-500 tw-uppercase tw-tracking-wider">
                                        System Prompt</th>
                                    <th
                                        class="tw-px-3 tw-py-2 tw-text-left tw-text-xs tw-font-medium tw-text-gray-500 tw-uppercase tw-tracking-wider">
                                        AI Response</th>
                                    <th
                                        class="tw-px-3 tw-py-2 tw-text-left tw-text-xs tw-font-medium tw-text-gray-500 tw-uppercase tw-tracking-wider">
                                        Tokens (In/Out)</th>
                                </tr>
                            </thead>
                            <tbody class="tw-bg-white tw-divide-y tw-divide-gray-200">
                                @foreach($logs as $log)
                                    <tr class="hover:tw-bg-gray-50">
                                        <td class="tw-px-3 tw-py-2 tw-whitespace-nowrap">{{ $log->id }}</td>
                                        <td class="tw-px-3 tw-py-2 tw-whitespace-nowrap" title="{{ $log->created_at }}">
                                            {{ $log->created_at->diffForHumans() }}
                                        </td>
                                        <td class="tw-px-3 tw-py-2">
                                            <div class="tw-font-bold">{{ $log->user ? $log->user->name : 'N/A' }}</div>
                                            <div class="tw-text-xs tw-text-gray-500">Chat: {{ $log->chat_id }}</div>
                                        </td>
                                        <td class="tw-px-3 tw-py-2">
                                            <div class="tw-font-bold">{{ $log->agent_name ?? $log->agent_id }}</div>
                                            <div class="tw-text-xs tw-text-gray-500">Brand:
                                                {{ $log->brand_name ?? $log->brand_id }}</div>
                                            <div class="tw-text-xs tw-text-gray-400">Type: {{ $log->agent_type }}</div>
                                        </td>
                                        <td class="tw-px-3 tw-py-2 tw-whitespace-nowrap">{{ $log->model }}</td>

                                        <!-- Content Columns with Click to View -->
                                        <td class="tw-px-3 tw-py-2 tw-cursor-pointer tw-text-blue-600 hover:tw-underline tw-max-w-xs tw-truncate"
                                            onclick="showModal('User Content', `{{ str_replace('`', '\`', $log->final_user_content) }}`)">
                                            {{ Str::limit($log->final_user_content, 50) }}
                                        </td>
                                        <td class="tw-px-3 tw-py-2 tw-cursor-pointer tw-text-blue-600 hover:tw-underline tw-max-w-xs tw-truncate"
                                            onclick="showModal('System Prompt', `{{ str_replace('`', '\`', $log->final_system_instruction) }}`)">
                                            {{ Str::limit($log->final_system_instruction, 50) }}
                                        </td>
                                        <td class="tw-px-3 tw-py-2 tw-cursor-pointer tw-text-blue-600 hover:tw-underline tw-max-w-xs tw-truncate"
                                            onclick="showModal('AI Response', `{{ str_replace('`', '\`', $log->ai_response) }}`)">
                                            {{ Str::limit($log->ai_response, 50) }}
                                        </td>

                                        <td class="tw-px-3 tw-py-2 tw-whitespace-nowrap">
                                            {{ $log->input_tokens }} / {{ $log->output_tokens }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="tw-mt-4">
                        {{ $logs->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div id="contentModal" class="tw-fixed tw-inset-0 tw-z-50 tw-hidden tw-overflow-y-auto"
        aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div
            class="tw-flex tw-items-end tw-justify-center tw-min-h-screen tw-pt-4 tw-px-4 tw-pb-20 tw-text-center sm:tw-block sm:tw-p-0">
            <!-- Background overlay -->
            <div class="tw-fixed tw-inset-0 tw-bg-gray-500 tw-bg-opacity-75 tw-transition-opacity" aria-hidden="true"
                onclick="closeModal()"></div>

            <!-- Modal panel -->
            <span class="tw-hidden sm:tw-inline-block sm:tw-align-middle sm:tw-h-screen"
                aria-hidden="true">&#8203;</span>
            <div
                class="tw-inline-block tw-align-bottom tw-bg-white tw-rounded-lg tw-text-left tw-overflow-hidden tw-shadow-xl tw-transform tw-transition-all sm:tw-my-8 sm:tw-align-middle sm:tw-max-w-4xl sm:tw-w-full">
                <div class="tw-bg-white tw-px-4 tw-pt-5 tw-pb-4 sm:tw-p-6 sm:tw-pb-4">
                    <div class="sm:tw-flex sm:tw-items-start">
                        <div class="tw-mt-3 tw-text-center sm:tw-mt-0 sm:tw-ml-4 sm:tw-text-left tw-w-full">
                            <h3 class="tw-text-lg tw-leading-6 tw-font-medium tw-text-gray-900" id="modal-title">
                                Content View
                            </h3>
                            <div
                                class="tw-mt-2 tw-max-h-[70vh] tw-overflow-y-auto tw-p-4 tw-bg-gray-50 tw-rounded tw-border tw-border-gray-200">
                                <div id="modal-content" class="tw-prose tw-max-w-none">
                                    <!-- Content will be injected here -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tw-bg-gray-50 tw-px-4 tw-py-3 sm:tw-px-6 sm:tw-flex sm:tw-flex-row-reverse">
                    <button type="button"
                        class="tw-mt-3 tw-w-full tw-inline-flex tw-justify-center tw-rounded-md tw-border tw-border-gray-300 tw-shadow-sm tw-px-4 tw-py-2 tw-bg-white tw-text-base tw-font-medium tw-text-gray-700 hover:tw-bg-gray-50 focus:tw-outline-none focus:tw-ring-2 focus:tw-ring-offset-2 focus:tw-ring-indigo-500 sm:tw-mt-0 sm:tw-ml-3 sm:tw-w-auto sm:tw-text-sm"
                        onclick="closeModal()">
                        Close
                    </button>
                    <button type="button"
                        class="tw-mt-3 tw-w-full tw-inline-flex tw-justify-center tw-rounded-md tw-border tw-border-transparent tw-shadow-sm tw-px-4 tw-py-2 tw-bg-blue-600 tw-text-base tw-font-medium tw-text-white hover:tw-bg-blue-700 focus:tw-outline-none focus:tw-ring-2 focus:tw-ring-offset-2 focus:tw-ring-blue-500 sm:tw-mt-0 sm:tw-ml-3 sm:tw-w-auto sm:tw-text-sm"
                        onclick="copyContent()">
                        Copy Raw
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
        <script>
            let currentRawContent = '';

            function showModal(title, content) {
                document.getElementById('modal-title').textContent = title;
                currentRawContent = content; // Store raw content for copy

                // Try to render markdown, fallback to plain text if error
                try {
                    // Configure marked directly or use default
                    document.getElementById('modal-content').innerHTML = marked.parse(content || '');
                } catch (e) {
                    console.error('Markdown parsing error:', e);
                    document.getElementById('modal-content').textContent = content;
                }

                document.getElementById('contentModal').classList.remove('tw-hidden');
                document.body.style.overflow = 'hidden'; // Prevent background scrolling
            }

            function closeModal() {
                document.getElementById('contentModal').classList.add('tw-hidden');
                document.body.style.overflow = 'auto';
            }

            function copyContent() {
                navigator.clipboard.writeText(currentRawContent).then(() => {
                    // Simple feedback - could be replaced with toast if available
                    alert('Copied to clipboard!');
                }).catch(err => {
                    console.error('Failed to copy: ', err);
                });
            }

            // Close modal on Escape key
            document.addEventListener('keydown', function (event) {
                if (event.key === "Escape") {
                    closeModal();
                }
            });
        </script>
    @endpush
</x-app-layout>