@php
    // Extract agentType from route parameters: /brands/{brand}/chat/{agentType}/{agentId}/{convId}
    $agentType = $agentType ?? request()->route('agentType') ?? 'root1';

    // Get brand slug - ensure $brand is available
    $brandSlug = $brand->slug ?? request()->route('brand')->slug ?? '';

    $rootData = $brand->root_data ?? [];
    $trunkData = $brand->trunk_data ?? [];

    // Structured items
    $root1Items = $brand->root1_data_items ?? [];
    $root2Items = $brand->root2_data_items ?? [];
    $root3Items = $brand->root3_data_items ?? [];
    $trunk1Items = $brand->trunk1_data_items ?? [];
    $trunk2Items = $brand->trunk2_data_items ?? [];

    $root1BriefItems = $brand->root1_brief_items ?? [];
    $root2BriefItems = $brand->root2_brief_items ?? [];
    $root3BriefItems = $brand->root3_brief_items ?? [];
    $trunk1BriefItems = $brand->trunk1_brief_items ?? [];
    $trunk2BriefItems = $brand->trunk2_brief_items ?? [];

    // Normalize data to ensure keys exist for JS object
    $initialData = [
        'root1' => $rootData['root1'] ?? '',
        'root2' => $rootData['root2'] ?? '',
        'root3' => $rootData['root3'] ?? '',
        'trunk1' => $trunkData['trunk1'] ?? '',
        'trunk2' => $trunkData['trunk2'] ?? '',
    ];

    // Get dynamic labels for brief items
    $briefKeywordsMap = \App\Services\BriefContentParser::getKeywordMap();
    $dynamicBriefLabels = [];
    foreach ($briefKeywordsMap as $agentKey => $items) {
        $dynamicBriefLabels[$agentKey] = [];
        foreach ($items as $key => $keywords) {
            $dynamicBriefLabels[$agentKey][$key] = $keywords[0] ?? $key;
        }
    }
@endphp

<div class="tw-flex tw-flex-col" x-data="{
    openModal: false,
    modalTitle: '',
    modalContent: '',
    currentKey: '',
    currentItemKey: '',
    brandSlug: '{{ $brand->slug }}',
    isSaving: false,
    saveStatus: '',
    data: @js($initialData),
    briefData: @js($brand->root_brief_data ?? []),
    briefDataTrunk: @js($brand->trunk_brief_data ?? []),
    dataItems: @js([
        'root1' => $root1Items,
        'root2' => $root2Items,
        'root3' => $root3Items,
        'trunk1' => $trunk1Items,
        'trunk2' => $trunk2Items
    ]),
    briefItems: @js([
        'root1' => $root1BriefItems,
        'root2' => $root2BriefItems,
        'root3' => $root3BriefItems,
        'trunk1' => $trunk1BriefItems,
        'trunk2' => $trunk2BriefItems
    ]),
    expandedSections: {
        'root1': false,
        'root2': false,
        'root3': false,
        'trunk1': false,
        'trunk2': false,
        'root1_brief': false,
        'root2_brief': false,
        'root3_brief': false,
        'trunk1_brief': false,
        'trunk2_brief': false,
    },
    pollingTimers: {},
    loadingAgents: {},
    showingBrief: false,
    isFromResultBar: false,
    showToast: false,
    toastMessage: '',
    toastTimeout: null,

    // Vietnamese labels cho từng keyword item
    itemLabels: {
        'root1': {
            'Purpose': 'Mục đích (Purpose)',
            'Core Values': 'Giá trị cốt lõi (Core Values)',
            'Expected Behaviors': 'Hành vi kỳ vọng (Behaviors)',
            'Symbols': 'Biểu tượng (Symbols)',
            'Rules/Decisions': 'Quy tắc thương hiệu'
        },
        'root2': {
            'Tổng quan Thị trường': 'Tổng quan Thị trường',
            'Chân dung & Insight': 'Chân dung & Insight KH',
            'Đối thủ & Khoảng trống': 'Đối thủ & Khoảng trống',
            'Cơ hội Tăng trưởng': 'Cơ hội Tăng trưởng',
            'Định hướng Tiếp theo': 'Định hướng Tiếp theo'
        },
        'root3': {
            'Customer Profile': 'Chân dung KH (Customer Profile)',
            'Value Map': 'Bản đồ Giá trị (Value Map)',
            'Value Proposition': 'Tuyên bố Giá trị (Value Proposition)'
        },
        'trunk1': {
            'Brand Name': 'Tên Thương hiệu (Brand Name)',
            'Tagline': 'Khẩu hiệu (Tagline)',
            'Positioning Statement': 'Tuyên bố Định vị',
            'Reasons to Believe': 'Lý do tin tưởng (RTB)',
            'Brand Personality': 'Tính cách TH (Brand Personality)',
            'Tone of Voice': 'Giọng điệu (Tone of Voice)',
            'Brand Promise': 'Lời hứa TH (Brand Promise)'
        },
        'trunk2': {
            'Định nghĩa giọng nói': 'Định nghĩa giọng nói TH',
            'Đặc điểm giọng điệu': 'Đặc điểm giọng điệu',
            'Giọng nói nên tránh': 'Giọng nói nên tránh',
            'Cách ứng dụng': 'Cách ứng dụng',
            'Tone map': 'Tone map',
            'Ví dụ cụ thể': 'Ví dụ cụ thể'
        }
    },

    // Brief item labels (map key snake_case → Vietnamese)
    briefItemLabels: @js($dynamicBriefLabels),

    init() {
        // Lắng nghe event khi analysis được save từ chat page
        window.addEventListener('analysis-saved', (e) => {
            const agentType = e.detail?.agentType;
            const content = e.detail?.content;
            
            if (agentType && content) {
                this.data[agentType] = content;
                this.startPollingBrief(agentType);
            }
        });

        // Lắng nghe event data-saved từ cả 2 luồng lưu
        window.addEventListener('data-saved', () => {
            const agentType = this.getAgentTypeFromUrl();
            if (agentType) {
                this.startPollingBrief(agentType);
            }
        });

        // Lắng nghe khi item được chọn từ sidebar
        window.addEventListener('sidebarItemSelected', (e) => {
            const agentType = e.detail?.agentType;
            const itemKey = e.detail?.itemKey;
            if (agentType && itemKey) {
                const briefSectionKey = agentType + '_brief';
                this.expandedSections[briefSectionKey] = true;
            }
        });

        // Lắng nghe data items updates từ sidebar save
        window.addEventListener('brandDataItemsUpdated', (event) => {
            const { agentType, dataItems } = event.detail;
            if (dataItems) {
                this.dataItems[agentType] = dataItems;
            }
        });

        // Lắng nghe brandDataUpdated từ sidebar để đồng bộ full data
        window.addEventListener('brandDataUpdated', (e) => {
            const { key, content, dataItems } = e.detail || {};
            if (!key) return;
            this.data[key] = content;
            if (dataItems) {
                this.dataItems[key] = dataItems;
            }
        });
    },

    // Polling kiểm tra brief data đã sẵn sàng chưa
    startPollingBrief(agentType) {
        this.loadingAgents[agentType] = true;
        
        if (this.pollingTimers[agentType]) {
            clearInterval(this.pollingTimers[agentType]);
        }

        let attempts = 0;
        const maxAttempts = 20;

        this.pollingTimers[agentType] = setInterval(async () => {
            attempts++;
            if (attempts > maxAttempts) {
                clearInterval(this.pollingTimers[agentType]);
                delete this.pollingTimers[agentType];
                this.loadingAgents[agentType] = false;
                return;
            }

            try {
                const localePrefix = '{{ \Mcamara\LaravelLocalization\Facades\LaravelLocalization::setLocale() ? '/' . \Mcamara\LaravelLocalization\Facades\LaravelLocalization::setLocale() : '' }}';
                const res = await fetch(localePrefix + '/brands/' + this.brandSlug + '/brief-status?key=' + agentType);
                const result = await res.json();

                if (result.ready && result.content) {
                    const rootTypes = ['root1', 'root2', 'root3'];
                    if (rootTypes.includes(agentType)) {
                        this.briefData[agentType] = result.content;
                    } else {
                        this.briefDataTrunk[agentType] = result.content;
                    }
                    
                    clearInterval(this.pollingTimers[agentType]);
                    delete this.pollingTimers[agentType];
                    this.loadingAgents[agentType] = false;

                    // Fetch updated brief items
                    this.fetchBriefItems(agentType);
                    
                    // Auto-update modal content nếu nó đang open
                    if (this.openModal && this.currentKey === agentType && this.showingBrief && this.isFromResultBar) {
                        if (rootTypes.includes(agentType)) {
                            this.modalContent = this.briefData[agentType] || '';
                        } else {
                            this.modalContent = this.briefDataTrunk[agentType] || '';
                        }
                    }
                    
                    this.showToastNotification('✓ Đã hoàn tất tóm tắt ' + this.getLevelLabel(agentType));
                }
            } catch (e) {
                console.warn('Polling brief status error:', e);
            }
        }, 3000);
    },

    // Fetch brief items mới từ server sau khi brief hoàn tất
    async fetchBriefItems(agentType) {
        try {
            const localePrefix = '{{ \Mcamara\LaravelLocalization\Facades\LaravelLocalization::setLocale() ? '/' . \Mcamara\LaravelLocalization\Facades\LaravelLocalization::setLocale() : '' }}';
            const res = await fetch(localePrefix + '/brands/' + this.brandSlug + '/brief-status?key=' + agentType);
            const result = await res.json();
            if (result.ready && result.brief_items) {
                this.briefItems[agentType] = result.brief_items;
            }
        } catch (e) {
            console.warn('Fetch brief items error:', e);
        }
    },

    showToastNotification(message) {
        this.toastMessage = message;
        this.showToast = true;
        
        if (this.toastTimeout) {
            clearTimeout(this.toastTimeout);
        }
        
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

    // Tính tiến độ hoàn thành thương hiệu
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
            'root1': 'Thiết kế Văn Hóa Dịch Vụ',
            'root2': 'Phân tích Thổ Nhưỡng',
            'root3': 'Định vị Giá Trị Giải Pháp',
            'trunk1': 'Định vị Thương Hiệu',
            'trunk2': 'Nhận diện Ngôn ngữ'
        };
        return labels[agentType] || 'Không xác định';
    },

    // Lấy label hiển thị cho item key
    getItemLabel(agentType, itemKey) {
        if (this.itemLabels[agentType] && this.itemLabels[agentType][itemKey]) {
            return this.itemLabels[agentType][itemKey];
        }
        return itemKey;
    },

    // Lấy label hiển thị cho brief item key
    getBriefItemLabel(agentType, itemKey) {
        if (this.briefItemLabels[agentType] && this.briefItemLabels[agentType][itemKey]) {
            return this.briefItemLabels[agentType][itemKey];
        }
        return itemKey.replace(/_/g, ' ');
    },

    // Kiểm tra agent type có data hay không
    hasAgentData(agentType) {
        return this.data[agentType] && this.data[agentType].length > 0;
    },

    // Đếm số items có dữ liệu
    countFilledItems(agentType) {
        const items = this.dataItems[agentType] || {};
        return Object.values(items).filter(v => v && v.length > 0).length;
    },

    countTotalItems(agentType) {
        return Object.keys(this.itemLabels[agentType] || {}).length;
    },

    openInfo(title, key, isFromResultBar = true) {
        this.modalTitle = title;
        this.currentKey = key;
        this.currentItemKey = '';
        this.isFromResultBar = isFromResultBar;
        this.saveStatus = '';
        
        const rootTypes = ['root1', 'root2', 'root3'];
        
        if (isFromResultBar) {
            if (rootTypes.includes(key)) {
                this.modalContent = this.briefData[key] || '';
            } else {
                this.modalContent = this.briefDataTrunk[key] || '';
            }
            this.showingBrief = true;
        } else {
            this.modalContent = this.data[key] || '';
            this.showingBrief = false;
        }
        
        this.openModal = true;
    },

    isBriefReady(key) {
        const rootTypes = ['root1', 'root2', 'root3'];
        if (rootTypes.includes(key)) {
            return !!(this.briefData[key] && this.briefData[key].length > 0);
        } else {
            return !!(this.briefDataTrunk[key] && this.briefDataTrunk[key].length > 0);
        }
    },

    toggleBriefView() {
        const rootTypes = ['root1', 'root2', 'root3'];
        this.showingBrief = !this.showingBrief;
        const key = this.currentKey;
        
        if (this.showingBrief) {
            if (!this.isBriefReady(key)) {
                this.showingBrief = false;
                return;
            }
            if (rootTypes.includes(key)) {
                this.modalContent = this.briefData[key] || '';
            } else {
                this.modalContent = this.briefDataTrunk[key] || '';
            }
        } else {
            this.modalContent = this.data[key] || '';
        }
    },

    // Mở modal để xem chi tiết một keyword item cụ thể (full)
    openItemModal(itemKey, agentType) {
        this.currentKey = agentType;
        this.currentItemKey = itemKey;
        this.isFromResultBar = false;
        this.showingBrief = false;
        this.saveStatus = '';
        
        if (this.dataItems[agentType] && this.dataItems[agentType][itemKey]) {
            this.modalContent = this.dataItems[agentType][itemKey] || '';
        } else {
            this.modalContent = '';
        }
        
        this.modalTitle = this.getItemLabel(agentType, itemKey) + ' — ' + this.getLevelLabel(agentType);
        this.openModal = true;
    },

    // Mở modal để xem chi tiết một keyword item (brief)
    openBriefItemModal(itemKey, agentType) {
        this.currentKey = agentType;
        this.currentItemKey = itemKey;
        this.isFromResultBar = false;
        this.showingBrief = false;
        this.saveStatus = '';
        
        if (this.briefItems[agentType] && this.briefItems[agentType][itemKey]) {
            this.modalContent = this.briefItems[agentType][itemKey] || '';
        } else {
            this.modalContent = '';
        }
        
        this.modalTitle = this.getBriefItemLabel(agentType, itemKey) + ' — ' + this.getLevelLabel(agentType);
        this.openModal = true;
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

        const localePrefix = '{{ \Mcamara\LaravelLocalization\Facades\LaravelLocalization::setLocale() ? '/' . \Mcamara\LaravelLocalization\Facades\LaravelLocalization::setLocale() : '' }}';
        return localePrefix + '/brands/' + this.brandSlug + '/chat/' + agentType + '/' + agentId + '/new';
    },

    async saveInfo() {
        this.isSaving = true;
        this.saveStatus = '';

        try {
            const localePrefix = '{{ \Mcamara\LaravelLocalization\Facades\LaravelLocalization::setLocale() ? '/' . \Mcamara\LaravelLocalization\Facades\LaravelLocalization::setLocale() : '' }}';
            const response = await fetch(localePrefix + '/brands/' + this.brandSlug + '/update-section', {
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
                
                // Update local full data
                this.data[this.currentKey] = this.modalContent;

                // Update local data items nếu server trả về
                if (result.data_items) {
                    this.dataItems[this.currentKey] = result.data_items;
                }

                // Dispatch event để sidebar đồng bộ dữ liệu
                window.dispatchEvent(new CustomEvent('brandDataUpdated', {
                    detail: {
                        key: this.currentKey,
                        content: this.modalContent,
                        dataItems: result.data_items || null
                    }
                }));

                // Dispatch analysis-saved để trigger polling brief
                window.dispatchEvent(new CustomEvent('analysis-saved', {
                    detail: {
                        agentType: this.currentKey,
                        content: this.modalContent
                    }
                }));
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
    {{-- HEADER --}}
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

    {{-- PROGRESS BAR --}}
    <div class="tw-px-3 tw-py-3 tw-border-b tw-border-gray-100 tw-items-center tw-gap-3">
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
                        :style="'width: ' + brandProgress + '%'" :class="{ 'tw-rounded-full': brandProgress >= 100 }">
                    </div>
                </div>
            </div>

            <div>
                <div class="tw-text-sm tw-text-gray-500 tw-truncate tw-overflow-hidden tw-whitespace-nowrap"
                    x-text="brandProgress + '% hoàn thành'">
                </div>
            </div>
        </div>
    </div>

    {{-- KEYWORD ITEMS PANEL --}}
    <div class="tw-px-3 tw-py-3 tw-border-b tw-border-gray-100 tw-items-center tw-overflow-y-auto"
        style="max-height: calc(100vh - 320px);">
        <div id="result-panel" class="tw-flex tw-flex-col tw-gap-3">

            {{-- ===== BRIEF ITEMS (Tóm tắt) — CHỈ HIỂN THỊ AGENT HIỆN TẠI ===== --}}
            @php
                $allAgents = [
                    'root1' => 'Thiết kế Văn Hóa Dịch Vụ',
                    'root2' => 'Phân tích Thổ Nhưỡng',
                    'root3' => 'Định vị Giá Trị Giải Pháp',
                    'trunk1' => 'Định vị Thương Hiệu',
                    'trunk2' => 'Nhận diện Ngôn ngữ',
                ];
                $currentLabel = $allAgents[$agentType] ?? 'Không xác định';
              @endphp

            @php $briefSectionKey = $agentType . '_brief'; @endphp
            <div class="tw-border tw-rounded-lg tw-overflow-hidden"
                :class="isBriefReady('{{ $agentType }}') ? 'tw-border-green-200' : 'tw-border-gray-200 tw-opacity-60'">
                <button
                    @click="expandedSections['{{ $briefSectionKey }}'] = !expandedSections['{{ $briefSectionKey }}']"
                    class="tw-w-full tw-text-left tw-px-4 tw-py-3 tw-border-b tw-border-gray-200 tw-transition tw-flex tw-items-center tw-justify-between tw-group"
                    :class="isBriefReady('{{ $agentType }}') ? 'tw-bg-green-50 hover:tw-bg-green-100' : 'tw-bg-gray-50 tw-cursor-default'">
                    <span class="tw-font-medium tw-text-sm tw-truncate"
                        :class="isBriefReady('{{ $agentType }}') ? 'tw-text-gray-700 group-hover:tw-text-green-600' : 'tw-text-gray-400'">
                        {{ $currentLabel }}
                        <span class="tw-text-xs tw-font-normal tw-text-gray-400">(Tóm tắt)</span>
                    </span>
                    <div class="tw-flex tw-items-center tw-gap-2 tw-flex-shrink-0">
                        <template x-if="loadingAgents['{{ $agentType }}']">
                            <span class="tw-animate-spin tw-text-green-500 tw-text-sm"><i
                                    class="ri-loader-4-line"></i></span>
                        </template>
                        <i class="ri-arrow-down-s-line tw-transition"
                            :class="{ 'tw-rotate-180': expandedSections['{{ $briefSectionKey }}'] }"></i>
                    </div>
                </button>

                <template x-if="expandedSections['{{ $briefSectionKey }}']">
                    <div class="tw-p-3 tw-bg-white tw-space-y-2">
                        {{-- Grid các brief keyword items --}}
                        <div class="tw-grid tw-grid-cols-1 tw-gap-2">
                            <template x-for="(label, key) in briefItemLabels['{{ $agentType }}']" :key="key">
                                <button
                                    @click="briefItems['{{ $agentType }}'] && briefItems['{{ $agentType }}'][key] ? openBriefItemModal(key, '{{ $agentType }}') : null"
                                    :disabled="!briefItems['{{ $agentType }}'] || !briefItems['{{ $agentType }}'][key]"
                                    :class="(briefItems['{{ $agentType }}'] && briefItems['{{ $agentType }}'][key])
                    ? 'tw-bg-white tw-border-green-200 hover:tw-bg-green-50 hover:tw-border-green-400 tw-cursor-pointer'
                    : 'tw-bg-gray-50 tw-border-gray-200 tw-opacity-50 tw-cursor-not-allowed'"
                                    class="tw-text-left tw-px-3 tw-py-2.5 tw-border tw-rounded-lg tw-transition tw-text-sm">
                                    <div class="tw-flex tw-items-center tw-gap-2">
                                        <div class="tw-w-2 tw-h-2 tw-rounded-full tw-flex-shrink-0"
                                            :class="(briefItems['{{ $agentType }}'] && briefItems['{{ $agentType }}'][key]) ? 'tw-bg-green-500' : 'tw-bg-gray-300'">
                                        </div>
                                        <span class="tw-font-medium tw-truncate"
                                            :class="(briefItems['{{ $agentType }}'] && briefItems['{{ $agentType }}'][key]) ? 'tw-text-gray-700' : 'tw-text-gray-400'"
                                            x-text="label"></span>
                                    </div>
                                    <template
                                        x-if="briefItems['{{ $agentType }}'] && briefItems['{{ $agentType }}'][key]">
                                        <div class="tw-text-xs tw-text-gray-500 tw-line-clamp-2 tw-mt-1 tw-ml-4"
                                            x-text="briefItems['{{ $agentType }}'][key].substring(0, 80) + (briefItems['{{ $agentType }}'][key].length > 80 ? '...' : '')">
                                        </div>
                                    </template>
                                </button>
                            </template>
                        </div>
                    </div>
                </template>
            </div>

            {{-- ===== MODAL ===== --}}
            <div x-show="openModal" style="display: none;"
                class="tw-fixed tw-inset-0 tw-z-50 tw-flex tw-items-center tw-justify-center tw-bg-black/50 tw-backdrop-blur-sm"
                x-transition:enter="tw-transition tw-ease-out tw-duration-300" x-transition:enter-start="tw-opacity-0"
                x-transition:enter-end="tw-opacity-100" x-transition:leave="tw-transition tw-ease-in tw-duration-200"
                x-transition:leave-start="tw-opacity-100" x-transition:leave-end="tw-opacity-0">

                <div class="tw-bg-white tw-rounded-xl tw-shadow-xl tw-w-[90%] md:tw-w-[800px] tw-h-[600px] tw-flex tw-flex-col"
                    @click.away="openModal = false">

                    {{-- Modal Header --}}
                    <div
                        class="tw-px-6 tw-py-4 tw-border-b tw-border-gray-100 tw-flex tw-items-center tw-justify-between">
                        <div class="tw-flex tw-items-center tw-gap-4 tw-min-w-0">
                            <h3 class="tw-text-lg tw-font-bold tw-text-gray-800 tw-truncate" x-text="modalTitle"></h3>

                            <a :href="getChatUrl()"
                                class="tw-inline-flex tw-items-center tw-gap-1 tw-bg-[#1AA24C] tw-text-white tw-text-xs tw-font-medium tw-px-3 tw-py-1.5 tw-rounded-full hover:tw-bg-[#15803d] tw-transition tw-flex-shrink-0">
                                <i class="ri-chat-smile-3-line"></i>
                                Chat AI
                            </a>
                        </div>

                        <button @click="openModal = false"
                            class="tw-text-gray-400 hover:tw-text-gray-600 tw-flex-shrink-0">
                            <i class="ri-close-line tw-text-2xl"></i>
                        </button>
                    </div>

                    {{-- Modal Body --}}
                    <div class="tw-p-6 tw-flex-1 tw-overflow-y-auto tw-flex tw-flex-col tw-relative">
                        {{-- Tab Toggle - Chỉ hiển thị khi mở từ result-bar (xem toàn bộ) --}}
                        <template x-if="isFromResultBar">
                            <div class="tw-mb-4 tw-flex tw-gap-2 tw-border-b tw-border-gray-200">
                                <button @click="toggleBriefView()" :disabled="!isBriefReady(currentKey)"
                                    :class="showingBrief ? 'tw-border-b-2 tw-border-[#1AA24C] tw-text-[#1AA24C] tw-font-semibold' : 'tw-text-gray-500 tw-font-medium'"
                                    class="tw-px-4 tw-py-2 tw-transition"
                                    :class="!isBriefReady(currentKey) && !showingBrief ? 'tw-opacity-50 tw-cursor-not-allowed' : ''">
                                    Nội dung tóm tắt
                                </button>
                                <button @click="toggleBriefView()"
                                    :class="!showingBrief ? 'tw-border-b-2 tw-border-[#1AA24C] tw-text-[#1AA24C] tw-font-semibold' : 'tw-text-gray-500 tw-font-medium'"
                                    class="tw-px-4 tw-py-2 tw-transition">
                                    Nội dung đầy đủ
                                </button>
                            </div>
                        </template>

                        {{-- Textarea with loading overlay --}}
                        <div class="tw-relative tw-flex-1 tw-flex tw-flex-col">
                            <textarea :disabled="currentItemKey || (showingBrief && !isBriefReady(currentKey))"
                                class="tw-w-full tw-flex-1 tw-border tw-border-gray-200 tw-rounded-lg tw-p-4 tw-text-gray-700 focus:tw-outline-none focus:tw-ring-2 focus:tw-ring-[#1AA24C] tw-resize-none disabled:tw-bg-gray-100 disabled:tw-opacity-60 disabled:tw-cursor-not-allowed tw-transition"
                                x-model="modalContent" spellcheck="false" placeholder="Chưa có thông tin..."></textarea>

                            {{-- Loading Overlay --}}
                            <template x-if="showingBrief && !isBriefReady(currentKey) && loadingAgents[currentKey]">
                                <div
                                    class="tw-absolute tw-inset-0 tw-bg-white/80 tw-rounded-lg tw-flex tw-flex-col tw-items-center tw-justify-center tw-gap-3">
                                    <div class="tw-animate-spin tw-text-[#1AA24C] tw-text-3xl">
                                        <i class="ri-loader-4-line"></i>
                                    </div>
                                    <p class="tw-text-sm tw-font-medium tw-text-gray-600">Đang tóm tắt nội dung...</p>
                                </div>
                            </template>
                        </div>

                        {{-- Footer Action --}}
                        <div class="tw-mt-4 tw-flex tw-items-center tw-gap-3">
                            {{-- Save Button - Hidden when viewing individual items --}}
                            <template x-if="!currentItemKey">
                                <button @click="saveInfo()"
                                    :disabled="isSaving || (showingBrief && !isBriefReady(currentKey))"
                                    class="tw-bg-[#1AA24C] tw-text-white tw-px-6 tw-py-2 tw-rounded-lg tw-font-medium hover:tw-bg-[#15803d] tw-transition disabled:tw-opacity-50 disabled:tw-cursor-not-allowed tw-flex tw-items-center tw-gap-2">
                                    <span x-show="isSaving" class="tw-animate-spin"><i
                                            class="ri-loader-4-line"></i></span>
                                    <span>Lưu</span>
                                </button>
                            </template>

                            {{-- Status Message --}}
                            <span x-show="saveStatus" x-text="saveStatus" class="tw-text-sm tw-font-medium"
                                :class="saveStatus.includes('Lỗi') ? 'tw-text-red-600' : 'tw-text-[#1AA24C]'">
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- TOAST NOTIFICATION --}}
    <template x-if="showToast">
        <div class="tw-fixed tw-bottom-4 tw-right-4 tw-bg-[#1AA24C] tw-text-white tw-px-6 tw-py-3 tw-rounded-lg tw-shadow-lg tw-flex tw-items-center tw-gap-3 tw-max-w-md tw-z-[10000]"
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

    {{-- NEXT STEP BUTTON --}}
    @php
        $nextUrl = '#';

        switch ($agentType) {
            case 'root1':
                $nextUrl = route('chat', ['brand' => $brandSlug, 'agentType' => 'root2', 'agentId' => 2, 'convId' => 'new']);
                break;
            case 'root2':
                $nextUrl = route('chat', ['brand' => $brandSlug, 'agentType' => 'root3', 'agentId' => 3, 'convId' => 'new']);
                break;
            case 'root3':
                $nextUrl = route('chat', ['brand' => $brandSlug, 'agentType' => 'trunk1', 'agentId' => 4, 'convId' => 'new']);
                break;
            case 'trunk1':
                $nextUrl = route('chat', ['brand' => $brandSlug, 'agentType' => 'trunk2', 'agentId' => 5, 'convId' => 'new']);
                break;
            case 'trunk2':
                $nextUrl = route('brands.canopy.show', ['brand' => $brandSlug]);
                break;
            default:
                $nextUrl = '#';
                break;
        }
      @endphp

    @if($nextUrl !== '#')
        <div class="tw-p-4 tw-mt-auto">
            <template x-if="hasAgentData('{{ $agentType }}') && isBriefReady('{{ $agentType }}')">
                <a href="{{ $nextUrl }}"
                    class="tw-flex tw-items-center tw-justify-center tw-w-full tw-px-4 tw-py-2 tw-text-sm tw-font-medium tw-text-white tw-bg-[#16a34a] tw-rounded-lg hover:tw-bg-[#15803d] tw-transition-colors">
                    <span>Bước tiếp theo</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="tw-w-4 tw-h-4 tw-ml-2" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </a>
            </template>
            <template x-if="!(hasAgentData('{{ $agentType }}') && isBriefReady('{{ $agentType }}'))">
                <div
                    class="tw-flex tw-items-center tw-justify-center tw-w-full tw-px-4 tw-py-2 tw-text-sm tw-font-medium tw-text-gray-400 tw-bg-gray-100 tw-rounded-lg tw-cursor-not-allowed tw-border tw-border-gray-200">
                    <span>Bước tiếp theo</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="tw-w-4 tw-h-4 tw-ml-2" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
            </template>
        </div>
    @endif
</div>