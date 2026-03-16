@php
    // Extract agentType from URL: /brands/{slug}/chat/{agentType}/{agentId}/{convId}
    $agentType = request()->segment(4) ?? 'root1';

    // Get brand slug - ensure $brand is available
    $brandSlug = $brand->slug ?? request()->segment(2) ?? '';

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

@php
    $nextUrl = '#';
    
    // Define the sequence of agents
    $agentSequence = ['root1', 'root2', 'root3', 'trunk1', 'trunk2'];
    $currentIndex = array_search($agentType, $agentSequence);

    if ($currentIndex !== false) {
        if ($currentIndex < count($agentSequence) - 1) {
            // Next agent in the sequence
            $nextAgentType = $agentSequence[$currentIndex + 1];
            $nextUrl = route('chat', ['brand' => $brandSlug, 'agentType' => $nextAgentType]);
        } else {
            // After trunk2, go to canopy
            $nextUrl = route('brands.canopy.show', ['brand' => $brandSlug]);
        }
    }
@endphp

<div class="tw-flex tw-flex-col tw-h-full" x-data="{
        brandSlug: '{{ $brand->slug }}',
        data: @js($initialData),
        briefData: @js($brand->root_brief_data ?? []),
        briefDataTrunk: @js($brand->trunk_brief_data ?? []),
        pollingTimers: {},
        loadingAgents: {},
        showToast: false,
        toastMessage: '',
        toastTimeout: null,

        init() {
            // Lắng nghe event khi analysis được save từ chat page hoặc popup modal
            window.addEventListener('analysis-saved', (e) => {
                const agentType = e.detail?.agentType;
                const content = e.detail?.content !== undefined ? e.detail?.content : null;

                if (agentType) {
                    this.data[agentType] = content;
                    
                    if (content && content.trim() !== '') {
                        // Bắt đầu polling kiểm tra brief data nếu có nội dung
                        this.startPollingBrief(agentType);
                    } else {
                        // Cập nhật giao diện về mặc định nếu clear data
                        this.clearBriefData(agentType);
                    }
                }
            });

            // Lắng nghe event data-saved từ cả 2 luồng lưu
            window.addEventListener('data-saved', () => {
                const agentType = this.getAgentTypeFromUrl();
                if (agentType) {
                    this.startPollingBrief(agentType);
                }
            });
        },

        // Polling kiểm tra brief data đã sẵn sàng chưa
        startPollingBrief(agentType) {
            // Mark as loading
            this.loadingAgents[agentType] = true;

            // Dừng polling cũ nếu có
            if (this.pollingTimers[agentType]) {
                clearInterval(this.pollingTimers[agentType]);
            }

            let attempts = 0;
            const maxAttempts = 20; // 20 x 3s = 60 giây tối đa

            this.pollingTimers[agentType] = setInterval(async () => {
                attempts++;
                if (attempts > maxAttempts) {
                    clearInterval(this.pollingTimers[agentType]);
                    delete this.pollingTimers[agentType];
                    this.loadingAgents[agentType] = false;
                    return;
                }

                try {
                    const res = await fetch(`/brands/${this.brandSlug}/brief-status?key=${agentType}`);
                    const result = await res.json();

                    if (result.ready && result.content) {
                        // Cập nhật brief data
                        const rootTypes = ['root1', 'root2', 'root3'];
                        if (rootTypes.includes(agentType)) {
                            this.briefData[agentType] = result.content;
                        } else {
                            this.briefDataTrunk[agentType] = result.content;
                        }

                        // Dừng polling
                        delete this.pollingTimers[agentType];
                        this.loadingAgents[agentType] = false;
                        
                        // Show toast notification
                        this.showToastNotification(`✓ Đã hoàn tất tóm tắt ${this.getLevelLabel(agentType)}`);
                    }
                } catch (e) {
                    console.warn('Polling brief status error:', e);
                }
            }, 3000);
        },

        showToastNotification(message) {
            this.toastMessage = message;
            this.showToast = true;
            
            // Clear previous timeout if any
            if (this.toastTimeout) {
                clearTimeout(this.toastTimeout);
            }
            
            // Auto-dismiss after 5 seconds
            this.toastTimeout = setTimeout(() => {
                this.showToast = false;
                this.toastTimeout = null;
            }, 5000);
        },

        closeToast() {
            this.showToast = false;
            if (this.toastTimeout) {
                clearTimeout(this.toastTimeout);
                this.toastTimeout = null;
            }
        },

        clearBriefData(agentType) {
            const rootTypes = ['root1', 'root2', 'root3'];
            
            // Cập nhật data local thành rỗng
            if (rootTypes.includes(agentType)) {
                this.briefData[agentType] = '';
            } else {
                this.briefDataTrunk[agentType] = '';
            }

            // Ngừng polling nếu đang chạy
            if (this.pollingTimers[agentType]) {
                clearInterval(this.pollingTimers[agentType]);
                delete this.pollingTimers[agentType];
            }
            this.loadingAgents[agentType] = false;
        },

        // Tính tiến độ hoàn thành thương hiệu (root + trunk = 5 steps)
        get brandProgress() {
            const allKeys = ['root1', 'root2', 'root3', 'trunk1', 'trunk2'];
            const completed = allKeys.filter(k => this.data[k] && this.data[k].length > 0).length;
            return Math.round((completed / allKeys.length) * 100);
        },

        getAgentTypeFromUrl() {
            const pathParts = window.location.pathname.split('/');
            const chatIndex = pathParts.indexOf('chat');
            return chatIndex !== -1 && pathParts[chatIndex + 1] ? pathParts[chatIndex + 1] : 'root1';
        },

        getLevelLabel(agentType) {
            const labels = {
                'root1': 'AI Thiết kế Văn Hóa Dịch Vụ',
                'root2': 'AI Phân tích Thổ Nhưỡng',
                'root3': 'AI Định vị Giá Trị Giải Pháp',
                'trunk1': 'AI Định vị Thương Hiệu',
                'trunk2': 'AI Nhận diện Ngôn ngữ'
            };
            return labels[agentType] || 'Không xác định';
        },

        hasPassedStep(key) {
            const allKeys = ['root1', 'root2', 'root3', 'trunk1', 'trunk2'];
            if (this.data[key] && this.data[key].toString().trim() !== '') return true;
            
            const index = allKeys.indexOf(key);
            if (index === -1) return false;
            
            // Căn cứ vào maxReachedIndex: nếu có bất kỳ bước nào phía sau đã hoàn thành thì bước hiện tại mặc định đã được đi qua
            for (let i = index + 1; i < allKeys.length; i++) {
                if (this.data[allKeys[i]] && this.data[allKeys[i]].toString().trim() !== '') {
                    return true;
                }
            }
            return false;
        }
    }">
    <div class="tw-px-3 tw-py-3 tw-border-b tw-border-gray-100 tw-flex tw-items-center tw-gap-3">
        <div>
            <img src="{{ asset('assets/img/logo-resultbar.svg') }}" class="tw-w-[38px] tw-h-[38px] tw-object-contain" />
        </div>
        <div class="tw-flex-1 tw-min-w-0">
            <div class="tw-font-bold tw-truncate tw-overflow-hidden tw-whitespace-nowrap">
                Kết quả phân tích
            </div>
            <div class="tw-text-sm tw-text-gray-500 tw-truncate tw-overflow-hidden tw-whitespace-nowrap">
                Dữ liệu đã được AI xử lý và trực quan hóa
            </div>
        </div>
    </div>

    <!-- Header -> Progress bar (Cố định ở trên) -->
    <div class="tw-px-3 tw-py-3 tw-border-b tw-border-gray-100 tw-items-center tw-bg-white tw-z-10 tw-shrink-0">
        <div
            class="tw-w-full tw-px-3 tw-py-2 tw-bg-[#F7F8F9] tw-rounded-xl tw-border tw-border-[#E0EAE6] tw-shadow-[0_4px_4px_rgba(0,0,0,0.05)] tw-flex tw-flex-col">
            <div class="tw-flex-1 tw-min-w-0">
                <div class="tw-text-sm tw-font-semibold tw-truncate tw-overflow-hidden tw-whitespace-nowrap">
                    Tiến độ hoàn thành thương hiệu
                </div>
            </div>

            <div>
                <div class="tw-w-full tw-h-[6px] tw-bg-[#e8eeeb] tw-rounded-full">
                    <div class="tw-h-full tw-bg-[linear-gradient(90deg,#329C59_-83.33%,#45C974_80.61%)] tw-rounded-l-full"
                        :style="`width: ${brandProgress}%`" :class="{ 'tw-rounded-full': brandProgress >= 100 }"></div>
                </div>
            </div>

            <div>
                <div class="tw-text-sm tw-text-gray-500 tw-truncate tw-overflow-hidden tw-whitespace-nowrap"
                    x-text="brandProgress + '% hoàn thành'">
                </div>
            </div>
        </div>
    </div>

    <!-- Content Area -->
    <div class="tw-flex-1 tw-overflow-y-auto tw-p-3 tw-overflow-y-scroll">
        <div id=" result-panel" class="tw-flex tw-flex-col tw-gap-3">

        @php
            $agentLabels = [
                'root1' => 'Thiết kế Văn Hóa Dịch Vụ',
                'root2' => 'Phân tích Thổ Nhưỡng',
                'root3' => 'Định vị Giá Trị Giải Pháp',
                'trunk1' => 'Định vị Thương Hiệu',
                'trunk2' => 'Nhận diện Ngôn ngữ',
            ];
            
            // Xây dựng mảng thứ tự các agent: Đưa agent hiện tại lên đầu tiên (nếu hợp lệ).
            // Sau đó lần lượt in các agent còn lại theo thứ tự: root1 -> root2 -> root3 -> trunk1 -> trunk2
            $defaultOrder = ['root1', 'root2', 'root3', 'trunk1', 'trunk2'];
            $agentOrder = [];
            
            if (in_array($agentType, $defaultOrder)) {
                $agentOrder[] = $agentType;
            }
            
            foreach ($defaultOrder as $ag) {
                if ($ag !== $agentType) {
                    $agentOrder[] = $ag;
                }
            }
        @endphp

        @foreach($agentOrder as $agent)
            @php
                $isRootAgent = in_array($agent, ['root1', 'root2', 'root3']);
            @endphp

            <!-- Wrapper ẩn hiện theo Alpine JS logic (chỉ show khu vực của agent nào đang tải hoặc đã có data) -->
            <div x-show="loadingAgents['{{ $agent }}'] || ({{ $isRootAgent ? "briefData['{$agent}']" : "briefDataTrunk['{$agent}']" }})"
                 style="display: none;" 
            >
                <!-- Loading Skeleton -->
                <template x-if="loadingAgents['{{ $agent }}']">
                    <div class="tw-flex tw-flex-col tw-gap-1 tw-w-full">
                        <div class="tw-w-full tw-px-3 tw-py-2.5 tw-rounded-xl tw-bg-[#F9FBF9] tw-border tw-border-[#E8F3EE] tw-animate-pulse">
                            <div class="tw-flex tw-items-start tw-gap-2">
                                <div class="tw-flex-1 tw-min-w-0 tw-flex tw-flex-col tw-gap-3 tw-mt-1">
                                    <div class="tw-h-3 tw-bg-gray-200/80 tw-rounded tw-w-1/2"></div>
                                    <div class="tw-h-2 tw-bg-gray-200/60 tw-rounded tw-w-full"></div>
                                    <div class="tw-h-2 tw-bg-gray-200/60 tw-rounded tw-w-5/6"></div>
                                    <div class="tw-h-2 tw-bg-gray-200/60 tw-rounded tw-w-4/5"></div>
                                    <div class="tw-h-2 tw-bg-gray-200/60 tw-rounded tw-w-2/3"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>

                <!-- Brief Data Article for {{ $agent }} -->
                <template x-if="!loadingAgents['{{ $agent }}'] && ({{ $isRootAgent ? "briefData['{$agent}']" : "briefDataTrunk['{$agent}']" }})">
                    <article class="tw-w-full tw-px-3 tw-py-2 tw-rounded-xl tw-border tw-border-[#E0EAE6] tw-shadow-[0_4px_4px_rgba(0,0,0,0.05)] tw-flex tw-flex-col tw-bg-white">
                        <div>
                            <h3 class="tw-font-semibold tw-text-gray-900 tw-mb-2">{{ $agentLabels[$agent] ?? 'Phân tích' }}</h3>
                        </div>
                        <div>
                            <div class="tw-text-sm tw-text-gray-600 tw-leading-relaxed tw-whitespace-pre-wrap" x-text="{{ $isRootAgent ? "briefData['{$agent}']" : "briefDataTrunk['{$agent}']" }}"></div>
                        </div>
                    </article>
                </template>
            </div>
        @endforeach
        </div>
    </div>

    <!-- Toast Notification -->
    <template x-if="showToast">
        <div class="tw-fixed tw-bottom-4 tw-right-4 tw-bg-[#1AA24C] tw-text-white tw-px-6 tw-py-3 tw-rounded-lg tw-shadow-lg tw-flex tw-items-center tw-gap-3 tw-max-w-md tw-animate-fade-in tw-z-[10000]"
            x-transition:enter="tw-transition tw-duration-300" x-transition:enter-start="tw-opacity-0 tw-translate-y-2"
            x-transition:enter-end="tw-opacity-100 tw-translate-y-0" x-transition:leave="tw-transition tw-duration-300"
            x-transition:leave-start="tw-opacity-100 tw-translate-y-0"
            x-transition:leave-end="tw-opacity-0 tw-translate-y-2">
            <div class="tw-text-lg">
                <i class="ri-check-circle-line"></i>
            </div>
            <span class="tw-flex-1 tw-text-sm tw-font-medium" x-text="toastMessage"></span>
            <button @click="closeToast()" class="tw-text-white/70 hover:tw-text-white tw-transition">
                <i class="ri-close-line tw-text-lg"></i>
            </button>
        </div>
    </template>

    <!-- Floating Button Footer -->
    @if($nextUrl !== '#')
        <div class="tw-w-full tw-border-t tw-border-gray-100 tw-p-4 tw-bg-transparent tw-z-20">
            <a :href="hasPassedStep('{{ $agentType }}') ? '{{ $nextUrl }}' : 'javascript:void(0)'" :class="hasPassedStep('{{ $agentType }}') 
                                            ? 'tw-bg-[#16a34a] hover:tw-bg-[#15803d] tw-text-white tw-cursor-pointer tw-shadow-lg hover:tw-shadow-xl hover:tw--translate-y-0.5' 
                                            : 'tw-bg-gray-200 tw-text-gray-400 tw-cursor-not-allowed tw-pointer-events-none'"
                class="tw-flex tw-items-center tw-justify-center tw-w-full tw-px-4 tw-py-3 tw-text-sm tw-font-bold tw-rounded-xl tw-transition-all tw-duration-300">
                <span>Bước tiếp theo</span>
                <svg xmlns="http://www.w3.org/2000/svg" class="tw-w-5 tw-h-5 tw-ml-2" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                </svg>
            </a>
        </div>
    @endif
</div>
