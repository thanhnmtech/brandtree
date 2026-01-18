<div class="tw-flex tw-flex-col">
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
      <!-- render result card ở đây -->
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