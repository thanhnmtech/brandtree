f@php
    // Extract agentType from URL: /brands/{slug}/chat/{agentType}/{agentId}/{convId}
    $agentType = request()->segment(4) ?? 'root1';
    
    // Get brand slug - ensure $brand is available
    $brandSlug = $brand->slug ?? request()->segment(2) ?? '';
    
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
@endphp

<div class="tw-flex tw-flex-col" x-data="{
    openModal: false,
    itemModal: false,
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
    },
    pollingTimers: {},
    loadingAgents: {},
    showingBrief: false,
    isFromResultBar: false,
    showToast: false,
    toastMessage: '',
    toastTimeout: null,
    selectedItemKey: null,
    expandedItem: null,
    detailTags: {
        'root1': [
            { id: 'r1-core', label: 'Giá trị cốt lõi', icon: '💎' },
            { id: 'r1-mission', label: 'Sứ mệnh', icon: '🎯' },
            { id: 'r1-vision', label: 'Tầm nhìn', icon: '👁️' },
            { id: 'r1-values', label: 'Giá trị dịch vụ', icon: '⭐' }
        ],
        'root2': [
            { id: 'r2-customer', label: 'Đối tượng khách hàng', icon: '👥' },
            { id: 'r2-needs', label: 'Nhu cầu thị trường', icon: '📊' },
            { id: 'r2-trend', label: 'Xu hướng hiện tại', icon: '📈' },
            { id: 'r2-insight', label: 'Insight thị trường', icon: '🔍' }
        ],
        'root3': [
            { id: 'r3-solution', label: 'Giải pháp đề xuất', icon: '💡' },
            { id: 'r3-benefits', label: 'Lợi ích nổi bật', icon: '✨' },
            { id: 'r3-positioning', label: 'Định vị giải pháp', icon: '🎪' },
            { id: 'r3-value-prop', label: 'Đề xuất giá trị', icon: '🏆' }
        ],
        'trunk1': [
            { id: 't1-identity', label: 'Nhận diện thương hiệu', icon: '🏷️' },
            { id: 't1-difference', label: 'Điểm khác biệt', icon: '🌟' },
            { id: 't1-promise', label: 'Lời hứa thương hiệu', icon: '🤝' },
            { id: 't1-personality', label: 'Tính cách thương hiệu', icon: '😊' }
        ],
        'trunk2': [
            { id: 't2-tone', label: 'Giọng điệu', icon: '🎤' },
            { id: 't2-message', label: 'Thông điệp chính', icon: '💬' },
            { id: 't2-language', label: 'Ngôn ngữ sử dụng', icon: '📝' },
            { id: 't2-story', label: 'Câu chuyện thương hiệu', icon: '📖' }
        ]
    },

    init() {
        // Lắng nghe event khi analysis được save từ chat page
        window.addEventListener('analysis-saved', (e) => {
            const agentType = e.detail?.agentType;
            const content = e.detail?.content;
            
            if (agentType && content) {
                this.data[agentType] = content;
                // Bắt đầu polling kiểm tra brief data
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
            const itemKey = e.detail?.itemKey;
            if (itemKey) {
                this.selectedItemKey = itemKey;
                this.expandedItem = itemKey;
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
                    clearInterval(this.pollingTimers[agentType]);
                    delete this.pollingTimers[agentType];
                    this.loadingAgents[agentType] = false;
                    
                    // Auto-update modal content nếu nó đang open và showing brief
                    if (this.openModal && this.currentKey === agentType && this.showingBrief && this.isFromResultBar) {
                        if (rootTypes.includes(agentType)) {
                            this.modalContent = this.briefData[agentType] || '';
                        } else {
                            this.modalContent = this.briefDataTrunk[agentType] || '';
                        }
                    }
                    
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

    openInfo(title, key, isFromResultBar = true) {
        this.modalTitle = title;
        this.currentKey = key;
        this.isFromResultBar = isFromResultBar;
        this.saveStatus = '';
        
        const rootTypes = ['root1', 'root2', 'root3'];
        
        if (isFromResultBar) {
            // Mở từ result-bar: mặc định show brief content
            if (rootTypes.includes(key)) {
                this.modalContent = this.briefData[key] || '';
            } else {
                this.modalContent = this.briefDataTrunk[key] || '';
            }
            this.showingBrief = true;
        } else {
            // Mở từ dataPlatformMenu: show full content only
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
            // Switching to brief
            if (!this.isBriefReady(key)) {
                // Brief not ready yet, revert and don't toggle
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

    // Mở modal để xem chi tiết item cụ thể
    openItemModal(itemKey, agentType) {
        this.currentKey = agentType;
        this.currentItemKey = itemKey;
        this.isFromResultBar = false;  // Don't show tab toggle for individual items
        this.showingBrief = false;
        this.saveStatus = '';
        
        // Load full content của item
        if (this.dataItems[agentType] && this.dataItems[agentType][itemKey]) {
            this.modalContent = this.dataItems[agentType][itemKey] || '';
        } else {
            this.modalContent = '';
        }
        
        this.modalTitle = `${itemKey} - ${this.getLevelLabel(agentType)}`;
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

        return `/brands/${this.brandSlug}/chat/${agentType}/${agentId}/new`;
    },

    async saveInfo() {
        this.isSaving = true;
        this.saveStatus = '';

        try {
            const response = await fetch(`/brands/${this.brandSlug}/update-section`, {
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
                
                // Update local state so reopen shows new data
                this.data[this.currentKey] = this.modalContent;
            } else {
                this.saveStatus = 'Lỗi: ' + (result.message || 'Không thể lưu');
            }
        } catch (error) {
            console.error(error);
            this.saveStatus = 'Lỗi kết nối';
        } finally {
            this.isSaving = false;
        }
    },

    // Initialize event listeners for real-time sync
    init() {
        // Listen for data items updates from sidebar
        window.addEventListener('brandDataItemsUpdated', (event) => {
            const { agentType, dataItems } = event.detail;
            if (dataItems) {
                this.dataItems[agentType] = dataItems;
                console.log(`✓ Updated dataItems for ${agentType}:`, dataItems);
            }
        });
    }
}" @init="init()">
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
            :style="`width: ${brandProgress}%`"
            :class="{ 'tw-rounded-full': brandProgress >= 100 }"></div>
        </div>
      </div>

      <div>
        <div class="tw-text-sm tw-text-gray-500 tw-truncate tw-overflow-hidden tw-whitespace-nowrap"
          x-text="brandProgress + '% hoàn thành'">
        </div>
      </div>
    </div>
  </div>

  <div class="tw-px-3 tw-py-3 tw-border-b tw-border-gray-100 tw-items-center">
    <div id="result-panel" class="tw-flex tw-flex-col tw-gap-3">
      <!-- Selected Item Detail Tags - Hiển thị các thẻ chi tiết khi chọn item từ sidebar -->
      <template x-if="selectedItemKey">
        <div class="tw-bg-gradient-to-r tw-from-[#F0F9F5] tw-to-white tw-p-4 tw-rounded-lg tw-border tw-border-[#D9F2E2]">
          <div class="tw-flex tw-items-center tw-justify-between tw-mb-3">
            <h3 class="tw-font-semibold tw-text-gray-800">Chi tiết - <span x-text="getLevelLabel(selectedItemKey)" class="tw-text-[#1AA24C]"></span></h3>
            <button @click="selectedItemKey = null" class="tw-text-gray-400 hover:tw-text-gray-600">
              <i class="ri-close-line tw-text-xl"></i>
            </button>
          </div>
          
          <!-- Grid của detail tags -->
          <div class="tw-flex tw-flex-wrap tw-gap-2">
            <template x-for="tag in detailTags[selectedItemKey] || []" :key="tag.id">
              <button @click="expandedItem = expandedItem === tag.id ? null : tag.id"
                :class="expandedItem === tag.id ? 'tw-bg-[#1AA24C] tw-text-white tw-ring-2 tw-ring-[#45C974]' : 'tw-bg-white tw-text-gray-700 tw-border tw-border-[#1AA24C] hover:tw-bg-[#F0F9F5]'"
                class="tw-px-3 tw-py-2 tw-rounded-lg tw-transition tw-duration-200 tw-text-sm tw-font-medium tw-flex tw-items-center tw-gap-2 tw-cursor-pointer">
                <span x-text="tag.icon"></span>
                <span x-text="tag.label"></span>
              </button>
            </template>
          </div>

          <!-- Expanded tag detail -->
          <template x-if="expandedItem">
            <div x-transition class="tw-mt-3 tw-p-3 tw-bg-white tw-rounded-lg tw-border tw-border-[#E8F3EE]">
              <p class="tw-text-xs tw-text-gray-600">
                <span x-show="expandedItem === 'r1-core'">Những giá trị cốt lõi mà thương hiệu của bạn đại diện, là nền tảng của mọi quyết định chiến lược.</span>
                <span x-show="expandedItem === 'r1-mission'">Sứ mệnh của công ty - mục đích cơ bản của sự tồn tại và hoạt động.</span>
                <span x-show="expandedItem === 'r1-vision'">Tầm nhìn tương lai - nơi bạn muốn công ty đạt được trong 5-10 năm.</span>
                <span x-show="expandedItem === 'r1-values'">Các giá trị dịch vụ liên quan đến cách bạn phục vụ khách hàng.</span>
                
                <span x-show="expandedItem === 'r2-customer'">Xác định rõ ràng ai là khách hàng lý tưởng của bạn.</span>
                <span x-show="expandedItem === 'r2-needs'">Phân tích nhu cầu chính của thị trường mà bạn nhắm tới.</span>
                <span x-show="expandedItem === 'r2-trend'">Các xu hướng hiện tại ảnh hưởng đến ngành và thị trường của bạn.</span>
                <span x-show="expandedItem === 'r2-insight'">Những hiểu biết sâu sắc về hành vi và tâm lý của khách hàng.</span>

                <span x-show="expandedItem === 'r3-solution'">Giải pháp cụ thể mà bạn đề xuất để giải quyết nhu cầu của thị trường.</span>
                <span x-show="expandedItem === 'r3-benefits'">Những lợi ích nổi bật và tập trung nhất mà khách hàng sẽ nhận được.</span>
                <span x-show="expandedItem === 'r3-positioning'">Cách bạn định vị giải pháp của mình so với các đối thủ cạnh tranh.</span>
                <span x-show="expandedItem === 'r3-value-prop'">Đề xuất giá trị duy nhất - tại sao khách hàng nên chọn bạn.</span>

                <span x-show="expandedItem === 't1-identity'">Nhận diện thương hiệu - tất cả những yếu tố trực quan và khái niệm định nghĩa thương hiệu.</span>
                <span x-show="expandedItem === 't1-difference'">Những điểm khác biệt chính tạo nên sự độc đáo của thương hiệu bạn.</span>
                <span x-show="expandedItem === 't1-promise'">Lời hứa thương hiệu - cam kết cơ bản mà bạn đưa ra cho khách hàng.</span>
                <span x-show="expandedItem === 't1-personality'">Tính cách thương hiệu - nếu thương hiệu là một người, thì sẽ như thế nào.</span>

                <span x-show="expandedItem === 't2-tone'">Giọng điệu nói chuyện - cách bạn giao tiếp với khách hàng qua tất cả các kênh.</span>
                <span x-show="expandedItem === 't2-message'">Thông điệp chính - những câu nói quan trọng nhất cần được nhắc lại liên tục.</span>
                <span x-show="expandedItem === 't2-language'">Ngôn ngữ sử dụng - từ vựng, cụm từ và cách phát biểu đặc trưng.</span>
                <span x-show="expandedItem === 't2-story'">Câu chuyện thương hiệu - cách kể lại hành trình và giá trị của bạn.</span>
              </p>
            </div>
          </template>
        </div>
      </template>

      <!-- Root1 Items Grid -->
      <div class="tw-border tw-border-gray-200 tw-rounded-lg tw-overflow-hidden">
          <button @click="expandedSections['root1'] = !expandedSections['root1']"
              class="tw-w-full tw-text-left tw-px-4 tw-py-3 tw-bg-[#F9FBF9] hover:tw-bg-[#E6F6EC] tw-border-b tw-border-gray-200 tw-transition tw-flex tw-items-center tw-justify-between tw-group">
              <span class="tw-font-medium tw-text-gray-700 group-hover:tw-text-[#1AA24C]">Thiết kế Văn Hóa Dịch Vụ</span>
              <i class="ri-arrow-down-s-line tw-transition" :class="{ 'tw-rotate-180': expandedSections['root1'] }"></i>
          </button>
          <template x-if="expandedSections['root1']">
              <div class="tw-p-4 tw-bg-white tw-space-y-3">
                  <div class="tw-grid tw-grid-cols-2 tw-gap-2">
                      <template x-for="(value, key) in dataItems.root1" :key="key">
                          <button @click="openItemModal(key, 'root1')"
                              :disabled="!value"
                              :class="value ? 'hover:tw-bg-[#E6F6EC] hover:tw-border-[#1AA24C] tw-cursor-pointer' : 'tw-bg-gray-100 tw-opacity-50 tw-cursor-not-allowed'"
                              class="tw-text-left tw-px-3 tw-py-2 tw-bg-white tw-border tw-border-gray-200 tw-rounded tw-transition tw-text-sm">
                              <div class="tw-font-medium tw-text-gray-700" x-text="key"></div>
                              <div class="tw-text-xs tw-text-gray-500 tw-line-clamp-2" x-text="(value || '').substring(0, 60) + '...'"></div>
                          </button>
                      </template>
                  </div>
              </div>
          </template>
      </div>

      <!-- Root2 Items Grid -->
      <div class="tw-border tw-border-gray-200 tw-rounded-lg tw-overflow-hidden tw-mt-2">
          <button @click="expandedSections['root2'] = !expandedSections['root2']"
              class="tw-w-full tw-text-left tw-px-4 tw-py-3 tw-bg-[#F9FBF9] hover:tw-bg-[#E6F6EC] tw-border-b tw-border-gray-200 tw-transition tw-flex tw-items-center tw-justify-between tw-group">
              <span class="tw-font-medium tw-text-gray-700 group-hover:tw-text-[#1AA24C]">Phân tích Thổ Nhưỡng</span>
              <i class="ri-arrow-down-s-line tw-transition" :class="{ 'tw-rotate-180': expandedSections['root2'] }"></i>
          </button>
          <template x-if="expandedSections['root2']">
              <div class="tw-p-4 tw-bg-white tw-space-y-3">
                  <div class="tw-grid tw-grid-cols-2 tw-gap-2">
                      <template x-for="(value, key) in dataItems.root2" :key="key">
                          <button @click="openItemModal(key, 'root2')"
                              :disabled="!value"
                              :class="value ? 'hover:tw-bg-[#E6F6EC] hover:tw-border-[#1AA24C] tw-cursor-pointer' : 'tw-bg-gray-100 tw-opacity-50 tw-cursor-not-allowed'"
                              class="tw-text-left tw-px-3 tw-py-2 tw-bg-white tw-border tw-border-gray-200 tw-rounded tw-transition tw-text-sm">
                              <div class="tw-font-medium tw-text-gray-700" x-text="key"></div>
                              <div class="tw-text-xs tw-text-gray-500 tw-line-clamp-2" x-text="(value || '').substring(0, 60) + '...'"></div>
                          </button>
                      </template>
                  </div>
              </div>
          </template>
      </div>

      <!-- Root3 Items Grid -->
      <div class="tw-border tw-border-gray-200 tw-rounded-lg tw-overflow-hidden tw-mt-2">
          <button @click="expandedSections['root3'] = !expandedSections['root3']"
              class="tw-w-full tw-text-left tw-px-4 tw-py-3 tw-bg-[#F9FBF9] hover:tw-bg-[#E6F6EC] tw-border-b tw-border-gray-200 tw-transition tw-flex tw-items-center tw-justify-between tw-group">
              <span class="tw-font-medium tw-text-gray-700 group-hover:tw-text-[#1AA24C]">Định vị Giá Trị Giải Pháp</span>
              <i class="ri-arrow-down-s-line tw-transition" :class="{ 'tw-rotate-180': expandedSections['root3'] }"></i>
          </button>
          <template x-if="expandedSections['root3']">
              <div class="tw-p-4 tw-bg-white tw-space-y-3">
                  <div class="tw-grid tw-grid-cols-2 tw-gap-2">
                      <template x-for="(value, key) in dataItems.root3" :key="key">
                          <button @click="openItemModal(key, 'root3')"
                              :disabled="!value"
                              :class="value ? 'hover:tw-bg-[#E6F6EC] hover:tw-border-[#1AA24C] tw-cursor-pointer' : 'tw-bg-gray-100 tw-opacity-50 tw-cursor-not-allowed'"
                              class="tw-text-left tw-px-3 tw-py-2 tw-bg-white tw-border tw-border-gray-200 tw-rounded tw-transition tw-text-sm">
                              <div class="tw-font-medium tw-text-gray-700" x-text="key"></div>
                              <div class="tw-text-xs tw-text-gray-500 tw-line-clamp-2" x-text="(value || '').substring(0, 60) + '...'"></div>
                          </button>
                      </template>
                  </div>
              </div>
          </template>
      </div>

      <!-- Trunk1 Items Grid -->
      <div class="tw-border tw-border-gray-200 tw-rounded-lg tw-overflow-hidden tw-mt-2">
          <button @click="expandedSections['trunk1'] = !expandedSections['trunk1']"
              class="tw-w-full tw-text-left tw-px-4 tw-py-3 tw-bg-[#F9FBF9] hover:tw-bg-[#E6F6EC] tw-border-b tw-border-gray-200 tw-transition tw-flex tw-items-center tw-justify-between tw-group">
              <span class="tw-font-medium tw-text-gray-700 group-hover:tw-text-[#1AA24C]">Định vị Thương Hiệu</span>
              <i class="ri-arrow-down-s-line tw-transition" :class="{ 'tw-rotate-180': expandedSections['trunk1'] }"></i>
          </button>
          <template x-if="expandedSections['trunk1']">
              <div class="tw-p-4 tw-bg-white tw-space-y-3">
                  <div class="tw-grid tw-grid-cols-2 tw-gap-2">
                      <template x-for="(value, key) in dataItems.trunk1" :key="key">
                          <button @click="openItemModal(key, 'trunk1')"
                              :disabled="!value"
                              :class="value ? 'hover:tw-bg-[#E6F6EC] hover:tw-border-[#1AA24C] tw-cursor-pointer' : 'tw-bg-gray-100 tw-opacity-50 tw-cursor-not-allowed'"
                              class="tw-text-left tw-px-3 tw-py-2 tw-bg-white tw-border tw-border-gray-200 tw-rounded tw-transition tw-text-sm">
                              <div class="tw-font-medium tw-text-gray-700" x-text="key"></div>
                              <div class="tw-text-xs tw-text-gray-500 tw-line-clamp-2" x-text="(value || '').substring(0, 60) + '...'"></div>
                          </button>
                      </template>
                  </div>
              </div>
          </template>
      </div>

      <!-- Trunk2 Items Grid -->
      <div class="tw-border tw-border-gray-200 tw-rounded-lg tw-overflow-hidden tw-mt-2">
          <button @click="expandedSections['trunk2'] = !expandedSections['trunk2']"
              class="tw-w-full tw-text-left tw-px-4 tw-py-3 tw-bg-[#F9FBF9] hover:tw-bg-[#E6F6EC] tw-border-b tw-border-gray-200 tw-transition tw-flex tw-items-center tw-justify-between tw-group">
              <span class="tw-font-medium tw-text-gray-700 group-hover:tw-text-[#1AA24C]">Nhận diện Ngôn ngữ</span>
              <i class="ri-arrow-down-s-line tw-transition" :class="{ 'tw-rotate-180': expandedSections['trunk2'] }"></i>
          </button>
          <template x-if="expandedSections['trunk2']">
              <div class="tw-p-4 tw-bg-white tw-space-y-3">
                  <div class="tw-grid tw-grid-cols-2 tw-gap-2">
                      <template x-for="(value, key) in dataItems.trunk2" :key="key">
                          <button @click="openItemModal(key, 'trunk2')"
                              :disabled="!value"
                              :class="value ? 'hover:tw-bg-[#E6F6EC] hover:tw-border-[#1AA24C] tw-cursor-pointer' : 'tw-bg-gray-100 tw-opacity-50 tw-cursor-not-allowed'"
                              class="tw-text-left tw-px-3 tw-py-2 tw-bg-white tw-border tw-border-gray-200 tw-rounded tw-transition tw-text-sm">
                              <div class="tw-font-medium tw-text-gray-700" x-text="key"></div>
                              <div class="tw-text-xs tw-text-gray-500 tw-line-clamp-2" x-text="(value || '').substring(0, 60) + '...'"></div>
                          </button>
                      </template>
                  </div>
              </div>
          </template>
      </div>

      <!-- Modal -->
      <div x-show="openModal" style="display: none;"
          class="tw-fixed tw-inset-0 tw-z-50 tw-flex tw-items-center tw-justify-center tw-bg-black/50 tw-backdrop-blur-sm"
          x-transition:enter="tw-transition tw-ease-out tw-duration-300" x-transition:enter-start="tw-opacity-0"
          x-transition:enter-end="tw-opacity-100" x-transition:leave="tw-transition tw-ease-in tw-duration-200"
          x-transition:leave-start="tw-opacity-100" x-transition:leave-end="tw-opacity-0">

          <div class="tw-bg-white tw-rounded-xl tw-shadow-xl tw-w-[90%] md:tw-w-[800px] tw-h-[600px] tw-flex tw-flex-col"
              @click.away="openModal = false">

              <!-- Modal Header -->
              <div class="tw-px-6 tw-py-4 tw-border-b tw-border-gray-100 tw-flex tw-items-center tw-justify-between">
                  <div class="tw-flex tw-items-center tw-gap-4">
                      <h3 class="tw-text-xl tw-font-bold tw-text-gray-800" x-text="modalTitle"></h3>

                      <a :href="getChatUrl()"
                          class="tw-inline-flex tw-items-center tw-gap-1 tw-bg-[#1AA24C] tw-text-white tw-text-xs tw-font-medium tw-px-3 tw-py-1.5 tw-rounded-full hover:tw-bg-[#15803d] tw-transition">
                          <i class="ri-chat-smile-3-line"></i>
                          Chat ngay với trợ lý AI
                      </a>
                  </div>

                  <button @click="openModal = false" class="tw-text-gray-400 hover:tw-text-gray-600">
                      <i class="ri-close-line tw-text-2xl"></i>
                  </button>
              </div>

              <!-- Modal Body -->
              <div class="tw-p-6 tw-flex-1 tw-overflow-y-auto tw-flex tw-flex-col tw-relative">
                  <!-- Tab Toggle - Chỉ hiển thị khi mở từ result-bar -->
                  <template x-if="isFromResultBar">
                      <div class="tw-mb-4 tw-flex tw-gap-2 tw-border-b tw-border-gray-200">
                          <button @click="toggleBriefView()"
                              :disabled="!isBriefReady(currentKey)"
                              :class="showingBrief ? 'tw-border-b-2 tw-border-[#1AA24C] tw-text-[#1AA24C] tw-font-semibold' : 'tw-text-gray-500 tw-font-medium'"
                              :class="!isBriefReady(currentKey) && !showingBrief ? 'tw-opacity-50 tw-cursor-not-allowed' : ''"
                              class="tw-px-4 tw-py-2 tw-transition">
                              Nội dung tóm tắt
                          </button>
                          <button @click="toggleBriefView()"
                              :class="!showingBrief ? 'tw-border-b-2 tw-border-[#1AA24C] tw-text-[#1AA24C] tw-font-semibold' : 'tw-text-gray-500 tw-font-medium'"
                              class="tw-px-4 tw-py-2 tw-transition">
                              Nội dung đầy đủ
                          </button>
                      </div>
                  </template>

                  <!-- Textarea with loading overlay -->
                  <div class="tw-relative tw-flex-1 tw-flex tw-flex-col">
                      <textarea
                          :disabled="currentItemKey || (showingBrief && !isBriefReady(currentKey))"
                          class="tw-w-full tw-flex-1 tw-border tw-border-gray-200 tw-rounded-lg tw-p-4 tw-text-gray-700 focus:tw-outline-none focus:tw-ring-2 focus:tw-ring-[#1AA24C] tw-resize-none disabled:tw-bg-gray-100 disabled:tw-opacity-60 disabled:tw-cursor-not-allowed tw-transition"
                          x-model="modalContent" spellcheck="false" placeholder="Chưa có thông tin..."></textarea>
                      
                      <!-- Loading Overlay - show when brief is loading -->
                      <template x-if="showingBrief && !isBriefReady(currentKey) && loadingAgents[currentKey]">
                          <div class="tw-absolute tw-inset-0 tw-bg-white/80 tw-rounded-lg tw-flex tw-flex-col tw-items-center tw-justify-center tw-gap-3">
                              <div class="tw-animate-spin tw-text-[#1AA24C] tw-text-3xl">
                                  <i class="ri-loader-4-line"></i>
                              </div>
                              <p class="tw-text-sm tw-font-medium tw-text-gray-600">Đang tóm tắt nội dung...</p>
                          </div>
                      </template>
                  </div>

                  <!-- Footer Action -->
                  <div class="tw-mt-4 tw-flex tw-items-center tw-gap-3">
                      <!-- Save Button - Hidden when viewing individual items -->
                      <template x-if="!currentItemKey">
                          <button @click="saveInfo()" :disabled="isSaving || (showingBrief && !isBriefReady(currentKey))"
                              class="tw-bg-[#1AA24C] tw-text-white tw-px-6 tw-py-2 tw-rounded-lg tw-font-medium hover:tw-bg-[#15803d] tw-transition disabled:tw-opacity-50 disabled:tw-cursor-not-allowed tw-flex tw-items-center tw-gap-2">
                              <span x-show="isSaving" class="tw-animate-spin"><i class="ri-loader-4-line"></i></span>
                              <span>Lưu</span>
                          </button>
                      </template>

                      <!-- Status Message -->
                      <span x-show="saveStatus" x-text="saveStatus" class="tw-text-sm tw-font-medium"
                          :class="saveStatus.includes('Lỗi') ? 'tw-text-red-600' : 'tw-text-[#1AA24C]'">
                      </span>
                  </div>
              </div>
          </div>
      </div>
    </div>
  </div>

  <!-- Toast Notification -->
  <template x-if="showToast">
      <div class="tw-fixed tw-bottom-4 tw-right-4 tw-bg-[#1AA24C] tw-text-white tw-px-6 tw-py-3 tw-rounded-lg tw-shadow-lg tw-flex tw-items-center tw-gap-3 tw-max-w-md tw-animate-fade-in tw-z-[10000]"
          x-transition:enter="tw-transition tw-duration-300"
          x-transition:enter-start="tw-opacity-0 tw-translate-y-2"
          x-transition:enter-end="tw-opacity-100 tw-translate-y-0"
          x-transition:leave="tw-transition tw-duration-300"
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
        $nextUrl = '#'; // Or hide the button
        break;
    }
  @endphp

  @if($nextUrl !== '#')
    <div class="tw-p-4 tw-mt-auto">
      <a href="{{ $nextUrl }}"
        class="tw-flex tw-items-center tw-justify-center tw-w-full tw-px-4 tw-py-2 tw-text-sm tw-font-medium tw-text-white tw-bg-[#16a34a] tw-rounded-lg hover:tw-bg-[#15803d] tw-transition-colors">
        <span>Bước tiếp theo</span>
        <svg xmlns="http://www.w3.org/2000/svg" class="tw-w-4 tw-h-4 tw-ml-2" fill="none" viewBox="0 0 24 24"
          stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
        </svg>
      </a>
    </div>
  @endif
</div>
