@foreach($trunkSteps as $item)
@php
    // Kiểm tra xem có $item không, nếu có thì lấy status, không thì mặc định là locked
    $state = $item['status'] ?? 'locked'; 
    $step = $item['label_short'] ?? '0';
    $title = $item['label'] ?? '';
    $description = $item['description'] ?? '';
    $aifeature = 'AI ' . ($item['label'] ?? '');
    
    // Logic link
    $isLocked = $state === 'locked';
    if (!$isLocked) {
        $actionUrl = route('chat', [
            'brand' => $brand->slug, 
            'agentType' => $item['key'] 
        ]);
    } else {
        $actionUrl = '#';
    }
    $resultUrl = '#'; // Hoặc logic url kết quả của bạn
@endphp
<div
  data-controller="step-card"
  data-step-card-state-value="{{ $state }}"
  data-step-card-target="card"
  class="step-card tw-rounded-xl tw-p-8 tw-space-y-4 tw-shadow-[0_4px_4px_0_rgba(0,0,0,0.05)]"
>
  <div class="tw-flex tw-items-center tw-gap-4">
    <div
      class="icon-wrap tw-h-12 tw-w-12 tw-rounded-full tw-bg-white tw-flex tw-items-center tw-justify-center"
    >
      <i data-step-card-target="icon" class="icon tw-text-2xl"></i>
    </div>

    <div class="tw-flex-1 tw-flex-col">
      <div class="tw-flex tw-items-center tw-gap-3">
        <span data-step-card-target="stepText" class="step-number tw-font-semibold">
             BƯỚC {{ $step }}
        </span>

        <span
          data-step-card-target="badge"
          class="badge tw-px-3 tw-py-1 tw-text-xs tw-rounded-full tw-font-medium"
        ></span>
      </div>

      <h3 data-step-card-target="title" class="title tw-text-xl tw-font-semibold tw-mt-1">
        {{ $title }}
      </h3>
    </div>
  </div>

  <p data-step-card-target="description" class="description tw-leading-relaxed">
    {{ $description }}
  </p>

  <div data-step-card-target="aiFeature" class="ai-box tw-rounded-lg tw-p-4 tw-text-sm tw-border">
    <span class="tw-font-semibold">Chức năng AI:</span>
    <span class="ai-feature">{{ $aifeature }}</span>
  </div>

  <div class="tw-flex tw-flex-col md:tw-flex-row tw-gap-4 tw-mt-2">
    <a
      href="{{ $actionUrl }}"
      data-step-card-target="startButton"
      class="start-btn tw-flex tw-items-center tw-gap-2 tw-bg-vlbcgreen tw-text-white tw-font-semibold tw-text-sm tw-px-5 tw-py-2 tw-rounded-lg"
    >
      <i class="ri-play-circle-line tw-text-lg"></i>
      Bắt Đầu Phân Tích
    </a>

    <button
      type="button"
      data-action="result-modal#open"
      data-result-modal-title-param="{{ $title }}"
      data-result-modal-key-param="{{ $item['key'] ?? '' }}"
      data-step-card-target="resultButton"
      class="result-btn tw-flex tw-items-center tw-gap-2 tw-bg-white tw-text-black tw-font-semibold tw-text-sm tw-px-5 tw-py-2 tw-rounded-lg tw-border tw-border-white/40 tw-cursor-pointer hover:tw-bg-gray-50 tw-transition"
    >
      <i class="ri-bar-chart-line tw-text-lg"></i>
      Kết Quả Phân Tích
    </button>

    <button
      data-step-card-target="lockedButton"
      disabled
      class="locked-btn tw-flex tw-items-center tw-gap-2 tw-bg-[#e7e5df] tw-text-gray-600 tw-text-sm tw-font-medium tw-px-5 tw-py-2 tw-rounded-lg tw-border tw-border-gray-300"
    >
      <i class="ri-lock-line tw-text-lg"></i>
      Chưa khả dụng
    </button>
  </div>
</div>
@endforeach