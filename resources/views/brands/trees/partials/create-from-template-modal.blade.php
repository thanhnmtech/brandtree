<div x-show="openTemplateModal" style="display: none;" x-data="{
        selectedAgents: [],
        isCreating: false,
        brandSlug: '{{ request()->route('brand')->slug ?? '' }}',

        selectAll() {
            this.selectedAgents = [...document.querySelectorAll('[data-agent-id]')].map(el => parseInt(el.dataset.agentId));
        },
        
        deselectAll() {
            this.selectedAgents = [];
        },

        toggleAgent(id) {
            if (this.selectedAgents.includes(id)) {
                this.selectedAgents = this.selectedAgents.filter(a => a !== id);
            } else {
                this.selectedAgents.push(id);
            }
        },

        async createAgents() {
            if (this.selectedAgents.length === 0) {
                alert('Vui lòng chọn ít nhất một Agent');
                return;
            }

            this.isCreating = true;

            try {
                const response = await fetch(`/brands/${this.brandSlug}/agents/from-template`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                    },
                    body: JSON.stringify({
                        agent_library_ids: this.selectedAgents
                    })
                });

                const result = await response.json();

                if (result.status === 'success') {
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
        @click.away="openTemplateModal = false">

        <!-- Close Button -->
        <button @click="openTemplateModal = false"
            class="tw-absolute tw-top-4 tw-right-4 tw-text-gray-400 hover:tw-text-gray-600">
            <i class="ri-close-line tw-text-2xl"></i>
        </button>

        <!-- Header -->
        <div class="tw-px-8 tw-pt-8 tw-pb-2">
            <h2 class="tw-text-2xl tw-font-bold tw-text-gray-800">Tạo Agent từ mẫu</h2>
            <p class="tw-text-sm tw-text-gray-500 tw-mt-1">Chọn các Agent mẫu từ thư viện để thêm vào thương hiệu của
                bạn</p>
        </div>

        <!-- Body -->
        <div class="tw-px-8 tw-py-6">
            <!-- Nút chọn tất cả / bỏ chọn -->
            <div class="tw-flex tw-gap-3 tw-mb-4">
                <button @click="selectAll()" type="button"
                    class="tw-px-4 tw-py-2 tw-border tw-border-gray-300 tw-rounded-lg tw-text-sm tw-font-medium tw-text-gray-700 hover:tw-bg-gray-50 tw-transition">
                    <i class="ri-checkbox-multiple-line tw-mr-1"></i> Chọn tất cả
                </button>
                <button @click="deselectAll()" type="button"
                    class="tw-px-4 tw-py-2 tw-border tw-border-gray-300 tw-rounded-lg tw-text-sm tw-font-medium tw-text-gray-700 hover:tw-bg-gray-50 tw-transition">
                    <i class="ri-checkbox-multiple-blank-line tw-mr-1"></i> Bỏ chọn tất cả
                </button>
                <span class="tw-ml-auto tw-text-sm tw-text-gray-500 tw-self-center">
                    Đã chọn: <span class="tw-font-bold tw-text-vlbcgreen" x-text="selectedAgents.length"></span> agent
                </span>
            </div>

            <!-- Danh sách Agents -->
            <div class="tw-space-y-3 tw-max-h-[400px] tw-overflow-y-auto tw-pr-2">
                @foreach($agentLibrary as $agent)
                    <div data-agent-id="{{ $agent->id }}" @click="toggleAgent({{ $agent->id }})"
                        :class="selectedAgents.includes({{ $agent->id }}) ? 'tw-border-vlbcgreen tw-bg-green-50' : 'tw-border-gray-200'"
                        class="tw-flex tw-items-start tw-gap-4 tw-p-4 tw-border-2 tw-rounded-xl tw-cursor-pointer hover:tw-border-vlbcgreen tw-transition">

                        <!-- Checkbox -->
                        <div class="tw-flex-shrink-0 tw-mt-1">
                            <div :class="selectedAgents.includes({{ $agent->id }}) ? 'tw-bg-vlbcgreen tw-border-vlbcgreen' : 'tw-border-gray-300'"
                                class="tw-w-5 tw-h-5 tw-border-2 tw-rounded tw-flex tw-items-center tw-justify-center tw-transition">
                                <i x-show="selectedAgents.includes({{ $agent->id }})"
                                    class="ri-check-line tw-text-white tw-text-sm"></i>
                            </div>
                        </div>

                        <!-- Agent Info -->
                        <div class="tw-flex-1">
                            <h4 class="tw-font-bold tw-text-gray-900">{{ $agent->name }}</h4>
                            <p class="tw-text-sm tw-text-gray-500 tw-mt-1 tw-line-clamp-2">
                                {{ $agent->description ?? 'Chưa có mô tả' }}
                            </p>
                        </div>

                        <!-- Icon -->
                        <div
                            class="tw-flex-shrink-0 tw-w-10 tw-h-10 tw-bg-[#E6F6EC] tw-rounded-lg tw-flex tw-items-center tw-justify-center tw-text-vlbcgreen">
                            <i class="ri-robot-2-line tw-text-xl"></i>
                        </div>
                    </div>
                @endforeach

                @if($agentLibrary->isEmpty())
                    <div class="tw-text-center tw-py-8 tw-text-gray-500">
                        Chưa có Agent mẫu nào trong thư viện.
                    </div>
                @endif
            </div>
        </div>

        <!-- Footer -->
        <div class="tw-px-8 tw-py-6 tw-bg-gray-50 tw-rounded-b-2xl tw-flex tw-justify-end tw-gap-3">
            <button @click="openTemplateModal = false"
                class="tw-px-6 tw-py-2.5 tw-border tw-border-gray-300 tw-rounded-lg tw-text-gray-700 tw-font-semibold hover:tw-bg-gray-100 tw-transition">
                Hủy
            </button>
            <button @click="createAgents()" :disabled="isCreating || selectedAgents.length === 0"
                class="tw-px-6 tw-py-2.5 tw-bg-[#1AA24C] tw-text-white tw-rounded-lg tw-font-semibold hover:tw-bg-[#15803d] tw-transition disabled:tw-opacity-50 disabled:tw-cursor-not-allowed tw-flex tw-items-center tw-gap-2">
                <span x-show="isCreating" class="tw-animate-spin"><i class="ri-loader-4-line"></i></span>
                <span>Tạo Agent</span>
            </button>
        </div>
    </div>
</div>