@php
  // Kiểm tra trạng thái của cả root và trunk
  $rootCompleted = collect($rootSteps)->every(fn($step) => ($step['status'] ?? 'locked') === 'completed');
  $trunkCompleted = collect($trunkSteps)->every(fn($step) => ($step['status'] ?? 'locked') === 'completed');
  
  // Tìm bước ready trong root trước
  $nextRootStep = collect($rootSteps)->first(fn($step) => ($step['status'] ?? 'locked') === 'ready');
  // Tìm bước ready trong trunk
  $nextTrunkStep = collect($trunkSteps)->first(fn($step) => ($step['status'] ?? 'locked') === 'ready');
  
  if ($rootCompleted && $trunkCompleted) {
    // Nếu cả root và trunk đã completed, chuyển đến canopy (tán cây)
    $nextUrl = route('brands.canopy.show', $brand);
    $nextLabel = 'Tiếp Tục Tán Cây';
    $nextDescription = 'Hoàn thành Gốc Cây và Thân Cây! Hãy tiếp tục với giai đoạn Tán Cây.';
    $nextIcon = 'ri-leaf-fill';
    $allCompleted = true;
  } elseif ($nextRootStep) {
    // Nếu còn bước ready trong root, chuyển đến chat của bước đó
    $nextUrl = route('chat', ['brand' => $brand->slug, 'agentType' => $nextRootStep['key']]);
    $stepIndex = array_search($nextRootStep, $rootSteps) + 1;
    $nextLabel = 'Bắt Đầu Phân Tích';
    $nextDescription = 'Tuyệt vời! Hãy tiếp tục với G' . $stepIndex . ': ' . ($nextRootStep['label'] ?? '');
    $nextIcon = 'ri-seedling-fill';
    $allCompleted = false;
  } elseif ($nextTrunkStep) {
    // Nếu root xong và có bước ready trong trunk, chuyển đến chat của bước đó
    $nextUrl = route('chat', ['brand' => $brand->slug, 'agentType' => $nextTrunkStep['key']]);
    $stepIndex = array_search($nextTrunkStep, $trunkSteps) + 1;
    $nextLabel = 'Bắt Đầu Phân Tích';
    $nextDescription = 'Tuyệt vời! Hãy tiếp tục với T' . $stepIndex . ': ' . ($nextTrunkStep['label'] ?? '');
    $nextIcon = 'ri-seedling-fill';
    $allCompleted = false;
  } else {
    // Trường hợp không có bước nào ready (locked hết)
    $nextUrl = '#';
    $nextLabel = 'Chưa Khả Dụng';
    $nextDescription = 'Vui lòng hoàn thành các bước trước để mở khóa bước tiếp theo.';
    $nextIcon = 'ri-lock-line';
    $allCompleted = false;
  }
@endphp
<div
  class="tw-bg-white tw-rounded-xl tw-border tw-border-gray-200 tw-shadow-sm tw-p-6 tw-relative tw-overflow-hidden"
  style="
      background: linear-gradient(135deg, #ffffff 0%, #f9f5ee 100%);
    ">
  <div class="tw-flex tw-items-center tw-justify-between">
    <div class="tw-flex tw-items-center tw-gap-2">
      <div class="tw-h-9 tw-w-9 tw-rounded-full tw-bg-[#e8f7eb] tw-flex tw-items-center tw-justify-center">
        <i class="{{ $nextIcon }} tw-text-vlbcgreen tw-text-lg"></i>
      </div>

      <h4 class="tw-font-semibold tw-text-gray-800 tw-text-base">
        {{ $allCompleted ?? false ? 'Giai Đoạn Tiếp Theo' : 'Bước Tiếp Theo' }}
      </h4>
    </div>

    <span
      class="tw-flex tw-items-center tw-gap-1 tw-bg-gray-100 tw-text-gray-600 tw-text-xs tw-py-1 tw-px-3 tw-rounded-full">
      <i class="ri-robot-line"></i>
      AI Điều Phối
    </span>
  </div>

  <p class="tw-text-sm tw-text-gray-700 tw-mt-3 tw-leading-relaxed">
    {{ $nextDescription }}
  </p>

  <p class="tw-text-sm tw-text-vlbcgreen tw-mt-4 tw-font-medium">
    Được hướng dẫn bởi Orchestrator Agent
  </p>

  <a
    href="{{ $nextUrl }}"
    class="tw-mt-3 tw-bg-vlbcgreen tw-text-white tw-text-sm tw-font-medium tw-py-2 tw-px-5 tw-rounded-lg tw-flex tw-items-center tw-gap-2 tw-w-fit {{ $nextUrl === '#' ? 'tw-opacity-50 tw-cursor-not-allowed' : 'hover:tw-bg-vlbcgreen/90' }}">
    {{ $nextLabel }}
    <i class="ri-arrow-right-line tw-text-base"></i>
  </a>
</div>
