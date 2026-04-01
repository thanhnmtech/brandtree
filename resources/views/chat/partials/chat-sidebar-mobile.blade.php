<!-- Mobile Sidebar -->
<div
  id="mobileSidebar"
  class="tw-fixed tw-inset-0 tw-bg-black/30 tw-backdrop-blur-sm tw-z-[60] tw-hidden"
  onclick="toggleSidebarMobile()"
  data-controller="result-modal"
  data-result-modal-brand-slug-value="{{ $brand->slug }}"
  data-result-modal-data-value='@json(array_merge($brand->root_data ?? [], $brand->trunk_data ?? []))'
>
  <!-- Drawer Panel -->
  <div
    id="mobileSidebarPanel"
    class="tw-w-72 tw-max-w-[80%] tw-h-full tw-bg-[#F4FAF6] tw-shadow-xl tw-p-5 tw-relative tw-transform tw--translate-x-full tw-transition-all tw-duration-300"
    onclick="event.stopPropagation()"
  >
    <!-- Header -->
    <div class="tw-flex tw-items-center tw-justify-between tw-mb-4">
      <i class="ri-plant-line tw-text-green-700 tw-text-xl"></i>

      <button
        onclick="toggleSidebarMobile()"
        class="tw-text-xl tw-text-gray-600"
      >
        <i class="ri-close-line"></i>
      </button>
    </div>

    <!-- Search -->
    <div
      class="tw-flex tw-items-center tw-bg-green-600 tw-text-white tw-rounded-full tw-mb-5 tw-px-4 tw-py-2"
    >
      <i class="ri-search-line tw-mr-2"></i>
      <input
        type="text"
        placeholder="Tìm kiếm trò chuyện"
        class="tw-flex-grow tw-text-sm tw-bg-transparent tw-border-none tw-focus:outline-none"
      />
    </div>

    <!-- Menu List -->
    <div class="tw-space-y-5 tw-text-gray-800 tw-text-[15px]">
      <!-- Cuộc trò chuyện mới -->
      <div class="tw-flex tw-items-center tw-gap-3 tw-cursor-pointer">
        <i class="ri-edit-2-line tw-text-lg"></i>
        <span>Cuộc trò chuyện mới</span>
      </div>

      <!-- Nền tảng dữ liệu -->
      <div>
        <button
          onclick="toggleMenu('dataPlatformMenu-mobile', 'dataArrow-mobile')"
          class="tw-flex tw-items-center tw-justify-between tw-w-full tw-cursor-pointer tw-font-medium"
        >
          <div class="tw-flex tw-items-center tw-gap-3">
            <i class="ri-database-2-line tw-text-lg"></i>
            <span>Nền tảng dữ liệu</span>
          </div>
          <i
            id="dataArrow-mobile"
            class="ri-arrow-down-s-line tw-text-lg tw-rotate-[-90deg] tw-transition tw-duration-500"
          ></i>
        </button>

        <!-- Submenu -->
        <div
          id="dataPlatformMenu-mobile"
          class="tw-pl-10 tw-mt-3 tw-space-y-3 tw-text-gray-600 tw-text-[14px] tw-hidden"
        >
          @php $rootData = $brand->root_data ?? []; $trunkData = $brand->trunk_data ?? []; @endphp
          
          @foreach(config('timeline_steps.root') ?? [] as $key => $step)
            @php $hasData = !empty($rootData[$key]); @endphp
            <div class="{{ $hasData ? 'tw-cursor-pointer tw-text-gray-800' : 'tw-cursor-not-allowed tw-text-gray-400' }}">
              @if($hasData)
                <button type="button"
                  data-action="result-modal#open"
                  data-result-modal-title-param="{{ $step['label'] }}"
                  data-result-modal-key-param="{{ $key }}"
                  class="tw-w-full tw-text-left tw-bg-transparent tw-border-none tw-p-0 tw-text-[14px] hover:tw-text-[#16A048]">
                  <span>{{ $step['label'] }}</span>
                </button>
              @else
                <span>{{ $step['label'] }}</span>
              @endif
            </div>
          @endforeach

          @foreach(config('timeline_steps.trunk') ?? [] as $key => $step)
            @php $hasData = !empty($trunkData[$key]); @endphp
            <div class="{{ $hasData ? 'tw-cursor-pointer tw-text-gray-800' : 'tw-cursor-not-allowed tw-text-gray-400' }}">
              @if($hasData)
                <button type="button"
                  data-action="result-modal#open"
                  data-result-modal-title-param="{{ $step['label'] }}"
                  data-result-modal-key-param="{{ $key }}"
                  class="tw-w-full tw-text-left tw-bg-transparent tw-border-none tw-p-0 tw-text-[14px] hover:tw-text-[#16A048]">
                  <span>{{ $step['label'] }}</span>
                </button>
              @else
                <span>{{ $step['label'] }}</span>
              @endif
            </div>
          @endforeach
        </div>
      </div>

      <!-- Kết quả phân tích -->
      <div
        class="tw-flex tw-items-center tw-gap-3 tw-font-medium tw-cursor-pointer"
      >
        <i class="ri-bar-chart-line tw-text-lg"></i>
        <span>Kết quả phân tích</span>
      </div>

      <!-- Lịch sử Chat -->
      <div x-data="chatHistorySidebar({
         brandId: '{{ $brand->id }}',
         brandSlug: '{{ $brand->slug }}',
         agentId: '{{ $agentId }}',
         agentType: '{{ $agentType }}'
       })">
        <button
          onclick="toggleMenu('chatHistoryMenu-mobile', 'chatArrow-mobile')"
          class="tw-flex tw-items-center tw-justify-between tw-w-full tw-cursor-pointer tw-font-medium"
        >
          <div class="tw-flex tw-items-center tw-gap-3">
            <i class="ri-time-line tw-text-lg"></i>
            <span>Lịch sử Chat</span>
          </div>

          <i
            id="chatArrow-mobile"
            class="ri-arrow-down-s-line tw-text-lg tw-rotate-[-90deg] tw-transition tw-duration-500"
          ></i>
        </button>

        <!-- Submenu Lịch sử chat -->
        <ul
          id="chatHistoryMenu-mobile"
          class="tw-pl-10 tw-mt-3 tw-space-y-4 tw-text-gray-700 tw-text-[14px] tw-hidden tw-max-h-[30vh] tw-overflow-y-auto"
          @scroll="handleScroll"
        >
          <template x-for="chat in chats" :key="chat.id">
            <li
              class="tw-group tw-cursor-pointer tw-flex tw-items-center tw-gap-2 hover:tw-text-[#16A048] tw-transition-colors"
              @mouseenter="hoveredChatId = chat.id" @mouseleave="hoveredChatId = null">

              <!-- Chế độ xem bình thường -->
              <template x-if="editingChatId !== chat.id">
                <div class="tw-flex tw-items-center tw-gap-2 tw-w-full">
                  <a :href="getChatLink(chat)" class="tw-block tw-flex-1 tw-min-w-0">
                    <div class="tw-font-medium tw-truncate" x-text="chat.title"></div>
                    <div class="tw-text-gray-500 tw-text-xs" x-text="formatDate(chat.created_at)"></div>
                  </a>

                  <!-- Nút chỉnh sửa - hiện khi hover -->
                  <button x-show="hoveredChatId === chat.id"
                    x-transition:enter="tw-transition tw-ease-out tw-duration-150" x-transition:enter-start="tw-opacity-0"
                    x-transition:enter-end="tw-opacity-100" @click.prevent="startEdit(chat)"
                    class="tw-p-1 tw-rounded hover:tw-bg-gray-200 tw-transition-colors tw-flex-shrink-0" title="Đổi tên">
                    <svg class="tw-w-4 tw-h-4 tw-text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z">
                      </path>
                    </svg>
                  </button>
                </div>
              </template>

              <!-- Chế độ chỉnh sửa -->
              <template x-if="editingChatId === chat.id">
                <div class="tw-w-full tw-pr-2">
                  <input type="text" x-model="editingTitle" x-ref="editInput" @keydown.enter.prevent="saveEdit(chat)"
                    @keydown.escape.prevent="cancelEdit()" @blur="saveEdit(chat)"
                    class="tw-w-full tw-px-2 tw-py-1 tw-text-sm tw-border tw-border-green-500 tw-rounded tw-outline-none tw-ring-2 tw-ring-green-200"
                    placeholder="Nhập tên mới..." />
                  <div class="tw-text-xs tw-text-gray-400 tw-mt-1">Nhấn Enter để lưu, Esc để hủy</div>
                </div>
              </template>
            </li>
          </template>

          <li x-show="loading" class="tw-py-2 tw-text-center tw-text-gray-400 tw-text-xs">
            <span class="tw-animate-pulse">Đang tải...</span>
          </li>

          <li x-show="!loading && chats.length === 0" class="tw-py-2 tw-text-left tw-text-gray-400 tw-text-xs">
            Chưa có đoạn chat nào
          </li>
        </ul>
      </div>
    </div>
  </div>

  <div onclick="event.stopPropagation()">
    <x-result-modal :brand="$brand" />
  </div>
</div>
