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
      <div>
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
        <div
          id="chatHistoryMenu-mobile"
          class="tw-pl-10 tw-mt-3 tw-space-y-4 tw-text-gray-700 tw-text-[14px] tw-hidden"
        >
          <!-- Mình tự thiết kế bạn yêu cầu -->
          <div class="tw-cursor-pointer tw-flex tw-flex-col tw-gap-1">
            <div class="tw-font-medium">Chat #1</div>
            <div class="tw-text-gray-500 tw-text-xs line-clamp-1">
              Bạn đã nói: “Phân tích cảm xúc khách hàng…”
            </div>
          </div>

          <div class="tw-cursor-pointer tw-flex tw-flex-col tw-gap-1">
            <div class="tw-font-medium">Chat #2</div>
            <div class="tw-text-gray-500 tw-text-xs line-clamp-1">
              Bạn đã nói: “Hãy giúp tôi định vị thương hiệu…”
            </div>
          </div>

          <div class="tw-cursor-pointer tw-flex tw-flex-col tw-gap-1">
            <div class="tw-font-medium">Chat #3</div>
            <div class="tw-text-gray-500 tw-text-xs line-clamp-1">
              Bạn đã nói: “Tạo nội dung truyền thông…”
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div onclick="event.stopPropagation()">
    <x-result-modal :brand="$brand" />
  </div>
</div>
