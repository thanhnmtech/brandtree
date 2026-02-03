<div class="tw-bg-white tw-rounded-xl tw-border tw-border-gray-200 tw-shadow-sm tw-p-6 tw-space-y-5">
  <div class="tw-flex tw-items-start tw-justify-between">
    <h3 class="tw-text-xl tw-font-semibold tw-text-gray-900 leading-tight">
      Tiến Độ Giai Đoạn<br />{{ $phaseTitle }}
    </h3>

      <div class="tw-flex-col tw-text-right">
        @php
            $total = count($steps);
            $completedCount = collect($steps)->where('status', 'completed')->count();
            $progressPercent = $total > 0 ? ($completedCount / $total) * 100 : 0;
        @endphp
        <p class="tw-text-[#829B99] tw-font-semibold tw-text-md">
          {{ $completedCount }}/{{ $total }} bước
        </p>
        <p
          class="tw-text-[#829B99] tw-font-semibold tw-text-md tw--mt-1"
        >
          hoàn thành
        </p>
      </div>
  </div>

  <div class="tw-w-full tw-h-3 tw-rounded-full tw-bg-gray-200 tw-overflow-hidden">
      <div class="tw-h-full tw-bg-vlbcgreen tw-transition-all tw-duration-500" style="width: {{ $progressPercent }}%"></div>
  </div>

  <div class="tw-flex tw-items-center tw-justify-between tw-text-sm tw-text-gray-600">
    <span class="tw-flex-1 tw-text-left">Bắt Đầu</span>
    <span class="tw-flex-1 tw-text-center">50%</span>
    <span class="tw-flex-1 tw-text-right">Hoàn Thành</span>
  </div>

  {{-- Hiển thị các bước với trạng thái động --}}
  <div class="tw-flex tw-items-start tw-justify-between tw-pt-1">
    @foreach($steps as $index => $stepItem)
      @php
        // Lấy trạng thái của bước: active, completed = active style, locked = inactive style
        $stepState = $stepItem['status'] ?? 'locked';
        $isActive = in_array($stepState, ['ready', 'completed']);
        $stepNumber = ($stepPrefix ?? 'S') . ($index + 1);
        $stepLabel = $stepItem['label'] ?? '';
      @endphp
      <div class="tw-flex tw-flex-1 tw-flex-col tw-items-center tw-gap-1">
        @if($isActive)
          {{-- Bước active/completed: nền xanh đậm, text trắng --}}
          <div
            class="tw-h-10 tw-w-10 tw-rounded-full tw-bg-[#567A63] tw-text-white tw-flex tw-items-center tw-justify-center tw-font-bold">
            {{ $stepNumber }}
          </div>
        @else
          {{-- Bước locked/inactive: viền xanh nhạt, text xanh nhạt --}}
          <div
            class="tw-h-10 tw-w-10 tw-rounded-full tw-border tw-border-[#8AA79A] tw-text-[#8AA79A] tw-flex tw-items-center tw-justify-center tw-font-bold">
            {{ $stepNumber }}
          </div>
        @endif
        <p class="tw-text-xs tw-text-gray-600 tw-text-center leading-tight">
          {{ $stepLabel }}
        </p>
      </div>
    @endforeach
  </div>
</div>
