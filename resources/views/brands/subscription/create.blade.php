<x-app-layout>
    <div x-data="{ billingCycle: 'monthly' }">
        <!-- Header -->
        <div class="tw-mb-6">
            <nav class="tw-flex tw-mb-4" aria-label="Breadcrumb">
                <ol class="tw-inline-flex tw-items-center tw-space-x-1 md:tw-space-x-3">
                    <li><a href="{{ route('dashboard') }}" class="tw-text-gray-500 hover:tw-text-gray-700">Dashboard</a></li>
                    <li class="tw-flex tw-items-center">
                        <svg class="tw-w-4 tw-h-4 tw-text-gray-400 tw-mx-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                        <a href="{{ route('brands.show', $brand) }}" class="tw-text-gray-500 hover:tw-text-gray-700">{{ $brand->name }}</a>
                    </li>
                    <li class="tw-flex tw-items-center">
                        <svg class="tw-w-4 tw-h-4 tw-text-gray-400 tw-mx-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                        <a href="{{ route('brands.subscription.show', $brand) }}" class="tw-text-gray-500 hover:tw-text-gray-700">Gói dịch vụ</a>
                    </li>
                    <li class="tw-flex tw-items-center">
                        <svg class="tw-w-4 tw-h-4 tw-text-gray-400 tw-mx-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                        <span class="tw-text-gray-700">Nâng cấp</span>
                    </li>
                </ol>
            </nav>
            <h1 class="tw-text-2xl tw-font-bold tw-text-gray-800">Chọn gói dịch vụ</h1>
        </div>

        @if($errors->any())
            <div class="tw-mb-6 tw-bg-red-100 tw-border tw-border-red-400 tw-text-red-700 tw-px-4 tw-py-3 tw-rounded-lg">
                @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
            </div>
        @endif

        @if($currentSubscription)
            <div class="tw-mb-6 tw-bg-blue-50 tw-border tw-border-blue-200 tw-text-blue-700 tw-px-4 tw-py-3 tw-rounded-lg">
                <p>Bạn đang sử dụng gói <strong>{{ $currentSubscription->plan->name }}</strong>. Khi chọn gói mới, gói hiện tại sẽ được thay thế.</p>
            </div>
        @endif

        <!-- Billing Toggle -->
        <div class="tw-flex tw-justify-center tw-mb-8">
            <div class="tw-inline-flex tw-items-center tw-bg-gray-100 tw-rounded-full tw-p-1">
                <button
                    type="button"
                    @click="billingCycle = 'monthly'"
                    :class="billingCycle === 'monthly' ? 'tw-bg-white tw-shadow-sm tw-text-gray-800' : 'tw-text-gray-500 hover:tw-text-gray-700'"
                    class="tw-px-4 tw-py-2 tw-rounded-full tw-text-sm tw-font-medium tw-transition-all tw-duration-200">
                    Thanh toán tháng
                </button>
                <button
                    type="button"
                    @click="billingCycle = 'yearly'"
                    :class="billingCycle === 'yearly' ? 'tw-bg-white tw-shadow-sm tw-text-gray-800' : 'tw-text-gray-500 hover:tw-text-gray-700'"
                    class="tw-px-4 tw-py-2 tw-rounded-full tw-text-sm tw-font-medium tw-transition-all tw-duration-200">
                    Thanh toán năm
                    {{-- <span class="tw-ml-1 tw-text-xs tw-text-orange-500 tw-font-semibold">-17%</span> --}}
                </button>
            </div>
        </div>

        <!-- Monthly Plans -->
        <div x-show="billingCycle === 'monthly'" x-transition:enter="tw-transition tw-ease-out tw-duration-200" x-transition:enter-start="tw-opacity-0" x-transition:enter-end="tw-opacity-100">
            <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-3 tw-gap-6">
                @foreach($monthlyPlans as $plan)
                    @include('brands.subscription.partials.plan-card', ['plan' => $plan, 'brand' => $brand, 'currentSubscription' => $currentSubscription, 'isYearly' => false])
                @endforeach
            </div>
        </div>

        <!-- Yearly Plans -->
        <div x-show="billingCycle === 'yearly'" x-transition:enter="tw-transition tw-ease-out tw-duration-200" x-transition:enter-start="tw-opacity-0" x-transition:enter-end="tw-opacity-100">
            <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-gap-6 tw-max-w-3xl tw-mx-auto">
                @foreach($yearlyPlans as $plan)
                    @include('brands.subscription.partials.plan-card', ['plan' => $plan, 'brand' => $brand, 'currentSubscription' => $currentSubscription, 'isYearly' => true])
                @endforeach
            </div>
        </div>

        <!-- Credit Packages -->
        @if($creditPackages->count() > 0)
            <div class="tw-mt-12 tw-pt-8 tw-border-t tw-border-gray-200">
                <div class="tw-text-center tw-mb-6">
                    <h2 class="tw-text-xl tw-font-bold tw-text-gray-800 tw-mb-2">Nạp thêm Credits</h2>
                    <p class="tw-text-gray-500 tw-text-sm">Bổ sung thêm credits khi cần thiết</p>
                </div>

                <div class="tw-grid tw-grid-cols-2 md:tw-grid-cols-4 tw-gap-4">
                    @foreach($creditPackages as $package)
                        <form action="{{ route('brands.subscription.store', $brand) }}" method="POST">
                            @csrf
                            <input type="hidden" name="plan_id" value="{{ $package->id }}">
                            <div class="tw-bg-white tw-rounded-xl tw-shadow-sm tw-p-4 tw-text-center tw-border tw-border-gray-100 hover:tw-border-[#16a249] tw-transition">
                                <div class="tw-text-2xl tw-font-bold tw-text-[#16a249] tw-mb-1">{{ number_format($package->credits) }}</div>
                                <div class="tw-text-xs tw-text-gray-500 tw-mb-3">credits</div>
                                <div class="tw-text-lg tw-font-semibold tw-text-gray-800 tw-mb-3">{{ $package->formatted_price }}</div>
                                <button type="submit" class="tw-w-full tw-py-2 tw-text-sm tw-rounded-lg tw-font-medium tw-border tw-border-gray-300 tw-text-gray-700 hover:tw-bg-gray-50 tw-transition">
                                    Mua ngay
                                </button>
                            </div>
                        </form>
                    @endforeach
                </div>

                <p class="tw-text-center tw-text-xs tw-text-gray-400 tw-mt-4">
                    * Gói nạp thêm credits yêu cầu có subscription đang hoạt động.
                </p>
            </div>
        @endif

        <div class="tw-mt-8 tw-text-center">
            <a href="{{ route('brands.subscription.show', $brand) }}" class="tw-text-gray-600 hover:tw-text-gray-800">← Quay lại</a>
        </div>
    </div>
</x-app-layout>
