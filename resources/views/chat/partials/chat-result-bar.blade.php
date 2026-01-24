@php
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

<div class="tw-flex tw-flex-col" x-data="{
    openModal: false,
    modalTitle: '',
    modalContent: '',
    currentKey: '',
    brandSlug: '{{ $brand->slug }}',
    isSaving: false,
    saveStatus: '',
    data: @js($initialData),

    getAgentTypeFromUrl() {
        const pathParts = window.location.pathname.split('/');
        const chatIndex = pathParts.indexOf('chat');
        return chatIndex !== -1 && pathParts[chatIndex + 1] ? pathParts[chatIndex + 1] : 'root1';
    },

    getLevelLabel(agentType) {
        const labels = {
            'root1': 'Văn Hóa Dịch Vụ',
            'root2': 'Thổ Nhưỡng',
            'root3': 'Giá Trị Giải Pháp',
            'trunk1': 'Trunk 1',
            'trunk2': 'Trunk 2'
        };
        return labels[agentType] || 'Không xác định';
    },

    openInfo(title, key) {
        this.modalTitle = title;
        this.currentKey = key;
        // Load content from central data state
        this.modalContent = this.data[key] || '';
        this.openModal = true;
        this.saveStatus = '';
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

  <div class="tw-px-3 tw-py-3 tw-border-b tw-border-gray-100 tw-items-center tw-gap-3">
    <div
      class="tw-w-full tw-px-3 tw-py-2 tw-bg-[#F7F8F9] tw-rounded-xl tw-border tw-border-[#E0EAE6] tw-shadow-[0_4px_4px_rgba(0,0,0,0.05)] tw-flex tw-flex-col">
      <div class="tw-flex-1 tw-min-w-0">
        <div class="tw-text-sm tw-font-semibold tw-truncate tw-overflow-hidden tw-whitespace-nowrap">
          Tiến độ hoàn thành phần gốc
        </div>
      </div>

      <div>
        <div class="tw-w-full tw-h-[6px] tw-bg-[#e8eeeb] tw-rounded-full">
          <div class="tw-h-full tw-bg-[linear-gradient(90deg,#329C59_-83.33%,#45C974_80.61%)] tw-rounded-l-full"
            style="width: 15%"></div>
        </div>
      </div>

      <div>
        <div class="tw-text-sm tw-text-gray-500 tw-truncate tw-overflow-hidden tw-whitespace-nowrap">
          15% hoàn thành
        </div>
      </div>
    </div>
  </div>

  <div class="tw-px-3 tw-py-3 tw-border-b tw-border-gray-100 tw-items-center">
    <div id="result-panel" class="tw-flex tw-flex-col tw-gap-3">
      <!-- Output Item -->
      <button @click="openInfo(getLevelLabel(getAgentTypeFromUrl()), getAgentTypeFromUrl())"
          x-show="['root1', 'root2', 'root3'].includes(getAgentTypeFromUrl())"
          class="tw-w-full tw-text-left tw-px-4 tw-py-3 tw-bg-[#F9FBF9] tw-border tw-border-[#E8F3EE] tw-rounded-lg hover:tw-bg-[#E6F6EC] hover:tw-border-[#1AA24C] tw-transition tw-group">
          <div class="tw-flex tw-items-center tw-justify-between">
              <span class="tw-font-medium tw-text-gray-700 group-hover:tw-text-[#1AA24C]" x-text="'Output của ' + getLevelLabel(getAgentTypeFromUrl())"></span>
              <i class="ri-arrow-right-s-line tw-text-gray-400 group-hover:tw-text-[#1AA24C]"></i>
          </div>
      </button>

      <!-- Modal -->
      <div x-show="openModal" style="display: none;"
          class="tw-fixed tw-inset-0 tw-z-50 tw-flex tw-items-center tw-justify-center tw-bg-black/50 tw-backdrop-blur-sm"
          x-transition:enter="tw-transition tw-ease-out tw-duration-300" x-transition:enter-start="tw-opacity-0"
          x-transition:enter-end="tw-opacity-100" x-transition:leave="tw-transition tw-ease-in tw-duration-200"
          x-transition:leave-start="tw-opacity-100" x-transition:leave-end="tw-opacity-0">

          <div class="tw-bg-white tw-rounded-xl tw-shadow-xl tw-w-[90%] md:tw-w-[800px] tw-max-h-[90vh] tw-flex tw-flex-col"
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
              <div class="tw-p-6 tw-flex-1 tw-overflow-y-auto">
                  <textarea
                      class="tw-w-full tw-h-[400px] tw-border tw-border-gray-200 tw-rounded-lg tw-p-4 tw-text-gray-700 focus:tw-outline-none focus:tw-ring-2 focus:tw-ring-[#1AA24C] tw-resize-none"
                      x-model="modalContent" spellcheck="false" placeholder="Chưa có thông tin..."></textarea>

                  <!-- Footer Action -->
                  <div class="tw-mt-4 tw-flex tw-items-center tw-gap-3">
                      <button @click="saveInfo()" :disabled="isSaving"
                          class="tw-bg-[#1AA24C] tw-text-white tw-px-6 tw-py-2 tw-rounded-lg tw-font-medium hover:tw-bg-[#15803d] tw-transition disabled:tw-opacity-50 disabled:tw-cursor-not-allowed tw-flex tw-items-center tw-gap-2">
                          <span x-show="isSaving" class="tw-animate-spin"><i class="ri-loader-4-line"></i></span>
                          <span>Lưu</span>
                      </button>

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

  @php
    $nextUrl = '#';
    $brandSlug = request()->route('brand')->slug ?? request()->segment(2); // Fallback if route binding isn't directly available in view check

    // Ensure we have a brand object or slug to work with
    if (isset($brand) && is_object($brand)) {
      $brandSlug = $brand->slug;
    }

    switch ($agentType ?? '') {
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