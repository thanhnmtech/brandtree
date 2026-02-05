{{-- 
    Component: Progress Phase Card
    Hiển thị card cho mỗi giai đoạn (Root/Trunk/Canopy) với 3 trạng thái: completed, ready, locked
    
    Props:
    - $phase: 'root' | 'trunk' | 'canopy'
    - $status: 'completed' | 'ready' | 'locked'
    - $progress: 0-100 (phần trăm hoàn thành)
    - $url: nullable, URL điều hướng khi click (chỉ cho status = ready)
--}}

@props([
    'phase' => 'root',
    'status' => 'locked',
    'progress' => 0,
    'url' => null,
])

@php
    // Cấu hình cho từng phase
    $phaseConfig = [
        'root' => [
            'stage' => __('messages.brand_show.stage_1'),
            'title' => __('messages.brand_show.root'),
            'description' => __('messages.brand_show.brand_foundation'),
            'icon' => 'ri-seedling-fill',
        ],
        'trunk' => [
            'stage' => __('messages.brand_show.stage_2'),
            'title' => __('messages.brand_show.trunk'),
            'description' => __('messages.brand_show.brand_identity'),
            'icon' => 'ri-tree-line',
        ],
        'canopy' => [
            'stage' => __('messages.brand_show.stage_3'),
            'title' => __('messages.brand_show.canopy'),
            'description' => __('messages.brand_show.growth_spread'),
            'icon' => 'ri-sparkling-fill',
        ],
    ];

    // Styles theo trạng thái
    $statusStyles = [
        'completed' => [
            'topBar' => 'tw-bg-[#1AA24C]',
            'iconBg' => 'tw-bg-[#E6F4EC]',
            'iconColor' => 'tw-text-[#1AA24C]',
            'badgeBg' => 'tw-bg-[#F2F8F4]',
            'badgeColor' => 'tw-text-[#1AA24C]',
            'statusBadgeBg' => 'tw-bg-[#E6F6EC]',
            'statusBadgeColor' => 'tw-text-[#1AA24C]',
            'statusIcon' => '✔',
            'statusText' => __('messages.brand_show.stage_completed'),
            'opacity' => '',
            'titleColor' => '',
            'descColor' => 'tw-text-gray-500',
            'showProgress' => true,
        ],
        'ready' => [
            'topBar' => 'tw-bg-[#96C7B0]',
            'iconBg' => 'tw-bg-[#E8F2EE]',
            'iconColor' => 'tw-text-[#489A6D]',
            'badgeBg' => 'tw-bg-[#F0F5F2]',
            'badgeColor' => 'tw-text-[#489A6D]',
            'statusBadgeBg' => 'tw-bg-[#E9F3EF]',
            'statusBadgeColor' => 'tw-text-[#489A6D]',
            'statusIcon' => '⏳',
            'statusText' => __('messages.brand_show.in_progress'),
            'opacity' => '',
            'titleColor' => '',
            'descColor' => 'tw-text-gray-500',
            'showProgress' => true,
        ],
        'locked' => [
            'topBar' => '',
            'iconBg' => 'tw-bg-[#F3F5F4]',
            'iconColor' => 'tw-text-[#A0B5AA]',
            'badgeBg' => 'tw-bg-[#F3F5F4]',
            'badgeColor' => 'tw-text-[#A0B5AA]',
            'statusBadgeBg' => 'tw-bg-[#EDEFEF]',
            'statusBadgeColor' => 'tw-text-[#A0B5AA]',
            'statusIcon' => '',
            'statusText' => __('messages.brand_show.not_unlocked'),
            'opacity' => 'tw-opacity-60',
            'titleColor' => 'tw-text-gray-500',
            'descColor' => 'tw-text-gray-400',
            'showProgress' => false,
        ],
    ];

    $config = $phaseConfig[$phase] ?? $phaseConfig['root'];
    $styles = $statusStyles[$status] ?? $statusStyles['locked'];
    
    // Xác định tag wrapper: <a> nếu có url và status ready, ngược lại <div>
    $isClickable = $status === 'ready' && $url;
@endphp

@if($isClickable)
<a href="{{ $url }}" 
    class="tw-block tw-bg-white tw-rounded-xl tw-border tw-border-[#DDE7E2] tw-p-6 tw-shadow-sm tw-relative tw-overflow-hidden hover:tw-shadow-md tw-transition-shadow tw-cursor-pointer {{ $styles['opacity'] }}">
@else
<div class="tw-bg-white tw-rounded-xl tw-border {{ $status === 'locked' ? 'tw-border-[#E2EAE5]' : 'tw-border-[#DDE7E2]' }} tw-p-6 tw-shadow-sm tw-relative tw-overflow-hidden {{ $styles['opacity'] }}">
@endif

    {{-- Top Bar (chỉ hiển thị khi không phải locked) --}}
    @if($styles['topBar'])
        <div class="tw-h-2 tw-w-full {{ $styles['topBar'] }} tw-absolute tw-top-0 tw-left-0"></div>
    @endif

    <div class="tw-text-center tw-flex tw-flex-col tw-items-center">
        {{-- Icon --}}
        <div class="tw-h-14 tw-w-14 tw-rounded-full {{ $styles['iconBg'] }} tw-flex tw-items-center tw-justify-center {{ $styles['iconColor'] }} tw-text-2xl tw-mb-3">
            <i class="{{ $config['icon'] }}"></i>
        </div>

        {{-- Stage Badge --}}
        <p class="{{ $styles['badgeBg'] }} {{ $styles['badgeColor'] }} tw-text-xs tw-font-bold tw-px-3 tw-py-1 tw-rounded-full">
            {{ $config['stage'] }}
        </p>

        {{-- Title --}}
        <h3 class="tw-text-lg tw-font-semibold {{ $styles['titleColor'] }}">{{ $config['title'] }}</h3>
        
        {{-- Description --}}
        <p class="{{ $styles['descColor'] }} tw-text-sm">{{ $config['description'] }}</p>

        {{-- Progress Bar (Luôn render để giữ layout, dùng invisible để ẩn) --}}
        <div class="tw-mt-4 tw-w-full {{ ($styles['showProgress'] && $phase !== 'canopy') ? '' : 'tw-invisible' }}">
            <div class="tw-text-xs tw-font-medium tw-text-gray-700 tw-mb-1">
                {{ __('messages.brand_show.progress') }}
            </div>
            <div class="tw-w-full tw-h-2 tw-bg-[#E8EDEB] tw-rounded-full tw-overflow-hidden">
                <div class="tw-h-full tw-bg-[#1AA24C]" style="width: {{ $progress }}%"></div>
            </div>
            <div class="tw-text-right tw-text-xs tw-text-gray-700 tw-mt-1">
                {{ $progress }}%
            </div>
        </div>

        {{-- Status Badge --}}
        <div class="tw-mt-5">
            <span class="tw-text-xs tw-font-semibold {{ $styles['statusBadgeBg'] }} {{ $styles['statusBadgeColor'] }} tw-px-3 tw-py-1 tw-rounded-full">
                @if($styles['statusIcon']){{ $styles['statusIcon'] }} @endif{{ $styles['statusText'] }}
            </span>
        </div>
    </div>

@if($isClickable)
</a>
@else
</div>
@endif
