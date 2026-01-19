@foreach ($brands as $brand)
    @if($brand)
    <article
        class="tw-h-full brand-card tw-bg-white tw-rounded-[7px] tw-border-2 tw-border-[#E2EBE7] tw-shadow-[0_4px_4px_rgba(0,0,0,0.05)] tw-p-[20px] tw-flex tw-flex-col tw-relative tw-transition hover:tw-shadow-[0_8px_12px_rgba(0,0,0,0.1)] hover:tw-border-vlbcgreen">
        @php
            $statusConfig = match($brand->status) {
                'seedling' => ['label' => 'Cần chăm sóc', 'bg' => '#FDEAEA', 'color' => '#DC282E'],
                'growing' => ['label' => 'Đang tăng trưởng', 'bg' => '#FAF4EB', 'color' => '#F59F0A'],
                'completed' => ['label' => 'Đã hoàn thiện', 'bg' => '#E8F5ED', 'color' => '#1AA24C'],
                default => ['label' => 'Chưa xác định', 'bg' => '#F3F7F5', 'color' => '#829B99'],
            };
        @endphp
        <span
            class="badge tw-absolute tw-top-[15px] tw-right-[15px] tw-inline-block tw-py-[6px] tw-px-[12px] tw-rounded-[20px] tw-text-[12px] tw-font-semibold"
            style="background: {{ $statusConfig['bg'] }}; color: {{ $statusConfig['color'] }};">{{ $statusConfig['label'] }}</span>

        <!-- Header: avatar + title -->
        <div class="tw-flex tw-items-center tw-gap-[12px]">
             <img src="{{ Storage::url($brand->logo_path) }}" alt="{{ $brand->name }}" class="tw-h-20 tw-w-20 tw-object-contain tw-rounded-full tw-bg-white tw-p-2">
            <div class="tw-flex tw-flex-col">
                <div class="title tw-text-[20px] tw-font-semibold tw-text-black">
                    <a href="{{ route('brands.show', $brand) }}" class="hover:tw-text-vlbcgreen tw-transition-colors">
                        {{ $brand->name }}
                    </a>
                </div>
                <div class="update-time tw-text-[14px] tw-text-[#829B99]">Cập nhật:
                    {{ $brand->updated_at->diffForHumans() }}</div>
            </div>
        </div>

        <!-- Progress header + small stats -->
        <div class="tw-flex tw-items-center tw-justify-between tw-mt-[12px] tw-gap-4">
            <div>
                <div class="tw-text-[16px] tw-font-semibold tw-text-black">
                    Tiến độ phát triển
                </div>
                <div class="tw-flex tw-gap-4 tw-mt-1">
                    <div class="root-text tw-text-[14px] tw-text-[#829B99]">Gốc
                        {{ $process_root = $brand->getProcessRoot() }}</div>
                    <div class="stem-text tw-text-[14px] tw-text-[#829B99]">Thân
                        {{ $process_trunk = $brand->getProcessTrunk() }}</div>
                </div>
            </div>

            <!-- Vertical progress indicators (right) -->
            <div class="tw-w-[110px] tw-relative tw-flex tw-flex-col tw-items-end tw-gap-3">
                <div class="tw-w-full tw-h-[6px] tw-bg-[#e8eeeb] tw-rounded-full">
                    <div class="progress-root tw-h-full tw-bg-[#269063] tw-rounded-l-full"
                        style="width: {{ $process_root }};"></div>
                </div>
                <div class="tw-w-full tw-h-[6px] tw-bg-[#e8eeeb] tw-rounded-full">
                    <div class="progress-stem tw-h-full tw-bg-[#269063] tw-rounded-l-full"
                        style="width: {{ $process_trunk }};"></div>
                </div>
            </div>
        </div>

        <!-- Description -->
        <div class="tw-text-[16px] tw-font-semibold tw-text-black tw-mt-[12px]">
            Bước tiếp theo
        </div>
        <p class="description tw-text-[14px] tw-text-[#829B99] tw-leading-[1.5] tw-mt-1 tw-flex-grow tw-overflow-hidden tw-line-clamp-3"
            style="display: -webkit-box;-webkit-line-clamp: 3;-webkit-box-orient: vertical;">
            {{ $brand->getNextProcess() }}
        </p>

        <!-- Footer area: action button -->
        <div class="tw-mt-[14px]">
            <a href="{{ route('brands.show', $brand) }}"
                class="tw-w-full tw-bg-[#F3F7F5] tw-border-2 tw-border-[#E2EBE7] tw-py-[10px] tw-px-[16px] tw-rounded-[7px] tw-text-[16px] tw-font-medium tw-text-black tw-flex tw-items-center tw-justify-center tw-gap-2 hover:tw-bg-[#E8EEE9] hover:tw-border-vlbcgreen">
                <span>Quản lý thương hiệu</span>
                <img class="tw-w-[16px] tw-h-[16px]"
                    src="./assets/img/4048a4b29522dad1ba63995de703d70091dcb319.svg" alt="arrow" />
            </a>
        </div>
    </article>
    @endif
@endforeach

@if($brands->isEmpty())
<div class="tw-col-span-full tw-text-center tw-py-10">
    <div class="tw-text-[#829B99] tw-text-lg">Không tìm thấy thương hiệu nào</div>
</div>
@endif
