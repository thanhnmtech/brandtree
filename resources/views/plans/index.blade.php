<x-app-layout>
    <div class="tw-max-w-6xl tw-mx-auto" x-data="{ billingCycle: 'monthly' }">
        <!-- Header -->
        <div class="tw-text-center tw-mb-8">
            <h1 class="tw-text-3xl md:tw-text-4xl tw-font-bold tw-text-gray-800 tw-mb-4">Bảng giá dịch vụ</h1>
            <p class="tw-text-gray-600 tw-text-lg tw-mb-6">Chọn gói phù hợp để phát triển thương hiệu của bạn</p>

            <!-- Billing Toggle -->
            <div class="tw-inline-flex tw-items-center tw-bg-gray-100 tw-rounded-full tw-p-1">
                <button
                    @click="billingCycle = 'monthly'"
                    :class="billingCycle === 'monthly' ? 'tw-bg-white tw-shadow-sm tw-text-gray-800' : 'tw-text-gray-500 hover:tw-text-gray-700'"
                    class="tw-px-5 tw-py-2 tw-rounded-full tw-text-sm tw-font-medium tw-transition"
                >
                    Thanh toán tháng
                </button>
                <button
                    @click="billingCycle = 'yearly'"
                    :class="billingCycle === 'yearly' ? 'tw-bg-white tw-shadow-sm tw-text-gray-800' : 'tw-text-gray-500 hover:tw-text-gray-700'"
                    class="tw-px-5 tw-py-2 tw-rounded-full tw-text-sm tw-font-medium tw-transition tw-flex tw-items-center tw-gap-2"
                >
                    Thanh toán năm
                    <span class="tw-bg-[#16a249] tw-text-white tw-text-xs tw-px-2 tw-py-0.5 tw-rounded-full">-17%</span>
                </button>
            </div>
        </div>

        <!-- Monthly Plans -->
        <x-modal-transition type="fade" x-show="billingCycle === 'monthly'">
            <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-3 tw-gap-6">
                @foreach($monthlyPlans as $plan)
                    @include('plans.partials.plan-card', ['plan' => $plan, 'isYearly' => false])
                @endforeach
            </div>
        </x-modal-transition>

        <!-- Yearly Plans -->
        <x-modal-transition type="fade" x-show="billingCycle === 'yearly'">
            <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-gap-6 tw-max-w-3xl tw-mx-auto">
                @foreach($yearlyPlans as $plan)
                    @include('plans.partials.plan-card', ['plan' => $plan, 'isYearly' => true])
                @endforeach
            </div>
        </x-modal-transition>

        <!-- Credit Packages -->
        @if($creditPackages->count() > 0)
            <div class="tw-mt-16">
                <div class="tw-text-center tw-mb-8">
                    <h2 class="tw-text-2xl tw-font-bold tw-text-gray-800 tw-mb-2">Nạp thêm Credits</h2>
                    <p class="tw-text-gray-600">Bổ sung credits khi cần, không cần đổi gói</p>
                </div>

                <div class="tw-grid tw-grid-cols-1 sm:tw-grid-cols-2 lg:tw-grid-cols-4 tw-gap-4">
                    @foreach($creditPackages as $package)
                        <div class="tw-bg-white tw-rounded-xl tw-shadow-sm tw-p-5 tw-text-center tw-relative {{ $package->is_popular ? 'tw-ring-2 tw-ring-[#16a249]' : '' }}">
                            @if($package->is_popular)
                                <div class="tw-absolute tw-top-0 tw-right-0 tw-bg-[#16a249] tw-text-white tw-text-xs tw-font-medium tw-px-2 tw-py-0.5 tw-rounded-bl-lg tw-rounded-tr-xl">
                                    Phổ biến
                                </div>
                            @endif
                            @if($package->hasDiscount())
                                <div class="tw-absolute tw-top-0 tw-left-0 tw-bg-orange-500 tw-text-white tw-text-xs tw-font-medium tw-px-2 tw-py-0.5 tw-rounded-br-lg tw-rounded-tl-xl">
                                    -{{ $package->discount_percent }}%
                                </div>
                            @endif

                            <div class="tw-text-2xl tw-font-bold tw-text-[#16a249] tw-mb-1">
                                {{ number_format($package->credits) }}
                            </div>
                            <div class="tw-text-gray-500 tw-text-sm tw-mb-3">credits</div>

                            <div class="tw-mb-4">
                                @if($package->hasDiscount())
                                    <span class="tw-text-gray-400 tw-line-through tw-text-sm">{{ $package->formatted_original_price }}</span>
                                @endif
                                <div class="tw-text-xl tw-font-bold tw-text-gray-800">{{ $package->formatted_price }}</div>
                            </div>

                            @auth
                                <a href="{{ route('dashboard') }}" class="tw-block tw-w-full tw-py-2 tw-text-center tw-rounded-lg tw-text-sm tw-font-medium tw-border tw-border-gray-300 tw-text-gray-700 hover:tw-bg-gray-50 tw-transition">
                                    Mua ngay
                                </a>
                            @else
                                <a href="{{ route('register') }}" class="tw-block tw-w-full tw-py-2 tw-text-center tw-rounded-lg tw-text-sm tw-font-medium tw-border tw-border-gray-300 tw-text-gray-700 hover:tw-bg-gray-50 tw-transition">
                                    Mua ngay
                                </a>
                            @endauth
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- FAQ or Notes -->
        <div class="tw-mt-12 tw-bg-white tw-rounded-2xl tw-shadow-sm tw-p-6">
            <h3 class="tw-text-lg tw-font-semibold tw-text-gray-800 tw-mb-4">Lưu ý</h3>
            <ul class="tw-space-y-2 tw-text-gray-600">
                <li class="tw-flex tw-items-start tw-gap-2">
                    <span class="tw-text-[#16a249]">•</span>
                    <span>Credits được reset hàng tháng (gói tháng) hoặc hàng năm (gói năm) vào ngày đăng ký.</span>
                </li>
                <li class="tw-flex tw-items-start tw-gap-2">
                    <span class="tw-text-[#16a249]">•</span>
                    <span>Gói dùng thử chỉ được sử dụng 1 lần cho mỗi thương hiệu.</span>
                </li>
                <li class="tw-flex tw-items-start tw-gap-2">
                    <span class="tw-text-[#16a249]">•</span>
                    <span>Gói nạp thêm credits yêu cầu có subscription đang hoạt động.</span>
                </li>
                <li class="tw-flex tw-items-start tw-gap-2">
                    <span class="tw-text-[#16a249]">•</span>
                    <span>Có thể nâng cấp hoặc hạ cấp gói bất kỳ lúc nào.</span>
                </li>
            </ul>
        </div>
    </div>
</x-app-layout>
