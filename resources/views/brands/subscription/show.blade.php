<x-app-layout>
    <x-slot name="header">
        <div class="tw-flex tw-items-center tw-justify-between">
            <div class="tw-flex tw-items-center tw-gap-4">
                <a href="{{ route('brands.index') }}" class="tw-text-gray-500 hover:tw-text-gray-700 tw-transition">
                    <svg class="tw-w-6 tw-h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </a>
                <div>
                    <h2 class="tw-text-2xl md:tw-text-3xl tw-font-bold tw-text-gray-800">Gói dịch vụ</h2>
                    <p class="tw-text-sm tw-text-gray-500">{{ $brand->name }}</p>
                </div>
            </div>
            @can('update', $brand)
                <a href="{{ route('brands.subscription.create', $brand) }}" class="tw-inline-flex tw-items-center tw-gap-2 tw-px-4 tw-py-2 tw-bg-[#16a249] tw-text-white tw-rounded-lg tw-font-medium hover:tw-bg-[#138a3e] tw-transition">
                    <svg class="tw-w-5 tw-h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg>
                    Nâng cấp
                </a>
            @endcan
        </div>
    </x-slot>

    <div class="tw-flex tw-flex-col lg:tw-flex-row tw-gap-6">
        <!-- Sidebar -->
        <div class="lg:tw-w-64 tw-flex-shrink-0">
            @include('brands.partials.sidebar', ['brand' => $brand])
        </div>

        <!-- Main Content -->
        <div class="tw-flex-1 tw-min-w-0">
    @if($subscription)
        <!-- Current Subscription -->
        <div class="tw-bg-white tw-rounded-2xl tw-shadow-sm tw-p-6 tw-mb-6">
            <div class="tw-flex tw-items-start tw-justify-between tw-mb-6">
                <div>
                    <h3 class="tw-text-lg tw-font-semibold tw-text-gray-800">Gói hiện tại</h3>
                    <p class="tw-text-gray-500">{{ $subscription->plan->name }}</p>
                </div>
                <span class="tw-px-3 tw-py-1 tw-text-sm tw-font-medium tw-rounded-full {{ $subscription->isActive() ? 'tw-bg-green-100 tw-text-green-700' : 'tw-bg-red-100 tw-text-red-700' }}">
                    {{ $subscription->isActive() ? 'Đang hoạt động' : 'Hết hạn' }}
                </span>
            </div>

            <div class="tw-grid tw-grid-cols-2 md:tw-grid-cols-4 tw-gap-4 tw-mb-6">
                <div class="tw-bg-gray-50 tw-rounded-lg tw-p-4">
                    <p class="tw-text-sm tw-text-gray-500 tw-mb-1">Credits còn lại</p>
                    <p class="tw-text-2xl tw-font-bold tw-text-gray-800">{{ number_format($subscription->credits_remaining) }}</p>
                </div>
                <div class="tw-bg-gray-50 tw-rounded-lg tw-p-4">
                    <p class="tw-text-sm tw-text-gray-500 tw-mb-1">Tổng credits</p>
                    <p class="tw-text-2xl tw-font-bold tw-text-gray-800">{{ number_format($subscription->plan->credits) }}</p>
                </div>
                <div class="tw-bg-gray-50 tw-rounded-lg tw-p-4">
                    <p class="tw-text-sm tw-text-gray-500 tw-mb-1">Ngày hết hạn</p>
                    <p class="tw-text-lg tw-font-semibold tw-text-gray-800">{{ $subscription->expires_at?->format('d/m/Y') ?? 'Không giới hạn' }}</p>
                </div>
                <div class="tw-bg-gray-50 tw-rounded-lg tw-p-4">
                    <p class="tw-text-sm tw-text-gray-500 tw-mb-1">Còn lại</p>
                    <p class="tw-text-lg tw-font-semibold tw-text-gray-800">{{ $subscription->days_remaining ?? '∞' }} ngày</p>
                </div>
            </div>

            <!-- Credit Usage Bar -->
            <div class="tw-mb-4">
                <div class="tw-flex tw-justify-between tw-text-sm tw-text-gray-600 tw-mb-2">
                    <span>Đã sử dụng</span>
                    <span>{{ $subscription->credit_usage_percent }}%</span>
                </div>
                <div class="tw-w-full tw-bg-gray-200 tw-rounded-full tw-h-2.5">
                    <div class="tw-bg-[#16a249] tw-h-2.5 tw-rounded-full" style="width: {{ $subscription->credit_usage_percent }}%"></div>
                </div>
            </div>

            @can('update', $brand)
                @if(!$subscription->plan->is_trial)
                    <form action="{{ route('brands.subscription.destroy', $brand) }}" method="POST" onsubmit="return confirm('Bạn có chắc muốn hủy gói này?')" class="tw-mt-4">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="tw-text-red-600 tw-text-sm hover:tw-underline">Hủy gói</button>
                    </form>
                @endif
            @endcan
        </div>
    @else
        <!-- No Subscription -->
        <div class="tw-bg-white tw-rounded-2xl tw-shadow-sm tw-p-8 tw-text-center">
            <div class="tw-w-16 tw-h-16 tw-bg-gray-100 tw-rounded-full tw-flex tw-items-center tw-justify-center tw-mx-auto tw-mb-4">
                <svg class="tw-w-8 tw-h-8 tw-text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <h3 class="tw-text-lg tw-font-semibold tw-text-gray-800 tw-mb-2">Chưa có gói dịch vụ</h3>
            <p class="tw-text-gray-600 tw-mb-6">Chọn một gói để bắt đầu sử dụng các tính năng của BrandTree.</p>
            @can('update', $brand)
                <a href="{{ route('brands.subscription.create', $brand) }}" class="tw-inline-flex tw-items-center tw-gap-2 tw-px-6 tw-py-3 tw-bg-[#16a249] tw-text-white tw-rounded-lg tw-font-medium hover:tw-bg-[#138a3e] tw-transition">
                    Chọn gói ngay
                </a>
            @endcan
        </div>
    @endif

    <!-- Available Plans -->
    <div class="tw-bg-white tw-rounded-2xl tw-shadow-sm tw-p-6">
        <h3 class="tw-text-lg tw-font-semibold tw-text-gray-800 tw-mb-4">Các gói dịch vụ</h3>
        <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-3 tw-gap-4">
            @foreach($plans as $plan)
                <div class="tw-border tw-rounded-xl tw-p-4 {{ $subscription && $subscription->plan_id === $plan->id ? 'tw-border-[#16a249] tw-bg-green-50' : 'tw-border-gray-200' }}">
                    <h4 class="tw-font-semibold tw-text-gray-800">{{ $plan->name }}</h4>
                    <p class="tw-text-2xl tw-font-bold tw-text-gray-800 tw-my-2">{{ $plan->formatted_price }}</p>
                    <p class="tw-text-sm tw-text-gray-500">{{ number_format($plan->credits) }} credits/tháng</p>
                    @if($subscription && $subscription->plan_id === $plan->id)
                        <span class="tw-inline-block tw-mt-3 tw-text-sm tw-text-[#16a249] tw-font-medium">Gói hiện tại</span>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
        </div>
    </div>
</x-app-layout>
