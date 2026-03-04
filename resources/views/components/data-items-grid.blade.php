@props([
    'agentType' => '',
    'items' => [],
    'briefItems' => [],
    'showBrief' => false
])

@php
    $displayItems = $showBrief ? $briefItems : $items;
    $hasData = collect($displayItems)->filter(fn($item) => !empty($item))->count() > 0;
@endphp

<div class="tw-space-y-3">
    @forelse($displayItems as $key => $content)
        <div class="tw-group">
            <button type="button"
                @click="openItemModal('{{ $key }}', '{{ $agentType }}')"
                class="tw-w-full tw-text-left tw-px-4 tw-py-3 tw-rounded-lg tw-transition tw-group tw-relative"
                :class="'{{ !empty($content) ? 'tw-bg-[#F9FBF9] tw-border tw-border-[#E8F3EE] hover:tw-bg-[#E6F6EC] hover:tw-border-[#1AA24C] cursor-pointer' : 'tw-bg-gray-100 tw-opacity-50 tw-cursor-not-allowed' }}'">
                
                <div class="tw-flex tw-items-center tw-justify-between">
                    <div class="tw-flex-1">
                        <h4 class="tw-font-semibold {{ !empty($content) ? 'tw-text-gray-700' : 'tw-text-gray-400' }}">
                            {{ $key }}
                        </h4>
                        @if(!empty($content))
                            <p class="tw-text-xs tw-text-gray-500 tw-mt-1 tw-line-clamp-2">
                                {{ Str::limit(strip_tags($content), 100) }}
                            </p>
                        @else
                            <p class="tw-text-xs tw-text-gray-400 tw-mt-1">
                                Chưa có dữ liệu
                            </p>
                        @endif
                    </div>
                    
                    @if(!empty($content))
                        <div class="tw-ml-3 tw-flex-shrink-0">
                            <i class="ri-arrow-right-s-line tw-text-gray-400 group-hover:tw-text-[#1AA24C] tw-transition"></i>
                        </div>
                    @endif
                </div>
            </button>
        </div>
    @empty
        <div class="tw-px-4 tw-py-6 tw-text-center tw-text-gray-400">
            <p class="tw-text-sm">Chưa có dữ liệu để hiển thị</p>
        </div>
    @endforelse
</div>
