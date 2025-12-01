@php
    $currentRoute = Route::currentRouteName();
@endphp

<div class="tw-bg-white tw-rounded-2xl tw-shadow-sm tw-overflow-hidden">
    <!-- Brand Header -->
    <div class="tw-p-4 tw-border-b tw-border-gray-100">
        <div class="tw-flex tw-items-center tw-gap-3">
            @if($brand->logo_path)
                <img src="{{ asset('storage/' . $brand->logo_path) }}" alt="{{ $brand->name }}" class="tw-w-12 tw-h-12 tw-object-contain tw-rounded-lg tw-bg-gray-100 tw-p-1">
            @else
                <div class="tw-w-12 tw-h-12 tw-bg-gradient-to-br tw-from-[#16a249] tw-to-[#138a3e] tw-rounded-lg tw-flex tw-items-center tw-justify-center">
                    <span class="tw-text-lg tw-font-bold tw-text-white">{{ strtoupper(substr($brand->name, 0, 2)) }}</span>
                </div>
            @endif
            <div class="tw-flex-1 tw-min-w-0">
                <h3 class="tw-font-semibold tw-text-gray-800 tw-truncate">{{ $brand->name }}</h3>
                @if($brand->activeSubscription)
                    <span class="tw-text-xs tw-text-green-600">{{ $brand->activeSubscription->plan->name }}</span>
                @endif
            </div>
        </div>
    </div>

    <!-- Navigation Menu -->
    <nav class="tw-p-2">
        <ul class="tw-space-y-1">
            <!-- Tổng quan -->
            <li>
                <a href="{{ route('brands.show', $brand) }}"
                   class="tw-flex tw-items-center tw-gap-3 tw-px-3 tw-py-2.5 tw-rounded-lg tw-text-sm tw-font-medium tw-transition
                          {{ $currentRoute === 'brands.show' ? 'tw-bg-[#16a249] tw-text-white' : 'tw-text-gray-700 hover:tw-bg-gray-100' }}">
                    <svg class="tw-w-5 tw-h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    Tổng quan
                </a>
            </li>

            <!-- Thành viên -->
            <li>
                <a href="{{ route('brands.members.index', $brand) }}"
                   class="tw-flex tw-items-center tw-gap-3 tw-px-3 tw-py-2.5 tw-rounded-lg tw-text-sm tw-font-medium tw-transition
                          {{ str_starts_with($currentRoute, 'brands.members') ? 'tw-bg-[#16a249] tw-text-white' : 'tw-text-gray-700 hover:tw-bg-gray-100' }}">
                    <svg class="tw-w-5 tw-h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                    </svg>
                    Thành viên
                    <span class="tw-ml-auto tw-px-2 tw-py-0.5 tw-text-xs tw-rounded-full {{ str_starts_with($currentRoute, 'brands.members') ? 'tw-bg-white/20 tw-text-white' : 'tw-bg-gray-100 tw-text-gray-600' }}">
                        {{ $brand->members->count() }}
                    </span>
                </a>
            </li>

            <!-- Gói dịch vụ -->
            <li>
                <a href="{{ route('brands.subscription.show', $brand) }}"
                   class="tw-flex tw-items-center tw-gap-3 tw-px-3 tw-py-2.5 tw-rounded-lg tw-text-sm tw-font-medium tw-transition
                          {{ str_starts_with($currentRoute, 'brands.subscription') ? 'tw-bg-[#16a249] tw-text-white' : 'tw-text-gray-700 hover:tw-bg-gray-100' }}">
                    <svg class="tw-w-5 tw-h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                    Gói dịch vụ
                </a>
            </li>

            <!-- Lịch sử thanh toán -->
            <li>
                <a href="{{ route('brands.payments.index', $brand) }}"
                   class="tw-flex tw-items-center tw-gap-3 tw-px-3 tw-py-2.5 tw-rounded-lg tw-text-sm tw-font-medium tw-transition
                          {{ str_starts_with($currentRoute, 'brands.payments') ? 'tw-bg-[#16a249] tw-text-white' : 'tw-text-gray-700 hover:tw-bg-gray-100' }}">
                    <svg class="tw-w-5 tw-h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                    </svg>
                    Lịch sử thanh toán
                </a>
            </li>

            <!-- Lịch sử Credit -->
            <li>
                <a href="{{ route('brands.credits.index', $brand) }}"
                   class="tw-flex tw-items-center tw-gap-3 tw-px-3 tw-py-2.5 tw-rounded-lg tw-text-sm tw-font-medium tw-transition
                          {{ str_starts_with($currentRoute, 'brands.credits') ? 'tw-bg-[#16a249] tw-text-white' : 'tw-text-gray-700 hover:tw-bg-gray-100' }}">
                    <svg class="tw-w-5 tw-h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    Lịch sử Credit
                </a>
            </li>
        </ul>
    </nav>

    <!-- Quick Stats -->
    @if($brand->activeSubscription)
        <div class="tw-p-4 tw-border-t tw-border-gray-100 tw-bg-gray-50">
            <div class="tw-text-xs tw-text-gray-500 tw-mb-2">Credits còn lại</div>
            <div class="tw-flex tw-items-center tw-gap-2">
                <div class="tw-flex-1 tw-bg-gray-200 tw-rounded-full tw-h-2">
                    <div class="tw-bg-[#16a249] tw-h-2 tw-rounded-full" style="width: {{ 100 - $brand->activeSubscription->credit_usage_percent }}%"></div>
                </div>
                <span class="tw-text-sm tw-font-semibold tw-text-gray-700">{{ number_format($brand->activeSubscription->credits_remaining) }}</span>
            </div>
        </div>
    @endif
</div>

