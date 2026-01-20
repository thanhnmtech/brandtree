<x-app-layout>
    <main class="tw-mt-[36px] tw-flex tw-flex-col tw-gap-10 tw-mb-5">
        <div class="tw-mb-6 tw-px-8">
            <x-breadcrumb :items="[
                ['label' => 'Trang chủ', 'url' => route('dashboard')],
                ['label' => $brand->name, 'url' => route('brands.show', $brand)]
            ]" />
            <h1 class="tw-text-2xl tw-font-bold tw-text-gray-800">Gói dịch vụ</h1>
        </div>

        <!-- ====================== GÓI DỊCH VỤ HIỆN TẠI ====================== -->
        @if ($currentSubscription)
        <section class="tw-px-8">
            <div class="tw-bg-white tw-rounded-xl tw-border tw-border-[#E0EAE6] tw-shadow-sm tw-p-6">
                <div class="tw-flex tw-items-start tw-justify-between">
                    <h2 class="tw-text-sm tw-font-semibold tw-text-gray-500">
                        Gói hiện tại
                        <p class="tw-text-lg tw-font-bold tw-text-black">
                            {{ $currentSubscription->plan->name }}
                            @if($currentSubscription->billing_cycle === 'yearly')
                                <span class="tw-text-sm tw-font-normal tw-text-gray-500">(Thanh toán năm)</span>
                            @endif
                            @if($currentSubscription->status !== 'active')
                                <span class="tw-text-sm tw-font-normal tw-text-red-500">(Hết hạn)</span>
                            @endif
                        </p>
                    </h2>
                </div>

                <!-- Gói + Ngày hết hạn -->
                <div class="tw-grid md:tw-grid-cols-3 tw-mt-2 tw-items-center tw-gap-4">
                    <!-- GÓI -->
                    <div>
                        <div class="tw-flex tw-items-center tw-gap-2 tw-mt-1">
                            <i class="ri-flashlight-fill tw-text-vlbcgreen tw-text-lg"></i>
                            <p class="tw-text-sm tw-text-gray-500">Năng Lượng còn lại</p>
                        </div>

                        <div class="tw-mt-1">
                            <span class="tw-text-vlbcgreen tw-font-semibold tw-text-2xl">
                                {{ $currentSubscription->credits_remaining }}</span>
                            <span class="tw-text-gray-500">/ {{ $currentSubscription->plan->credits }}</span>
                        </div>
                    </div>

                    <!-- NGÀY LÀM MỚI -->
                    <div class="tw-text-center">
                        <p class="tw-text-sm tw-text-gray-500">Ngày làm mới năng lượng</p>
                        <p class="tw-text-2xl tw-font-bold tw-mt-1">
                            {{ $currentSubscription->credits_reset_at?->format('d/m/Y') ?? '-' }}</p>
                    </div>

                    <!-- NGÀY HẾT HẠN -->
                    <div class="tw-text-center">
                        <p class="tw-text-sm tw-text-gray-500">Ngày hết hạn</p>
                        <p class="tw-text-2xl tw-font-bold tw-mt-1 {{ $currentSubscription->expires_at->isPast() ? 'tw-text-red-500' : '' }}">
                            {{ $currentSubscription->expires_at->format('d/m/Y') }}</p>

                        {{-- Chỉ hiện nút Gia hạn nếu không phải gói trial và gói có giá > 0 --}}
                        @if (!$currentSubscription->plan->is_trial && $currentSubscription->plan->price > 0)
                            <form action="{{ route('brands.subscription.renew', $brand) }}" method="POST" class="tw-mt-2">
                                @csrf
                                <button type="submit"
                                    class="tw-text-sm tw-text-[#1AA24C] hover:tw-text-[#158f3f] tw-font-medium tw-flex tw-items-center tw-gap-1 tw-mx-auto">
                                    <i class="ri-refresh-line"></i>
                                    Gia hạn ngay
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </section>
        @endif

        <!-- ====================== CHỌN GÓI DỊCH VỤ ====================== -->
        <section class="tw-px-8">
            <div class="tw-w-full tw-flex tw-flex-col tw-gap-6">
                <div class="tw-flex tw-flex-col tw-items-center tw-gap-4">
                    <h2 class="tw-text-xl tw-font-bold tw-flex tw-items-center tw-gap-2">
                        <i class="ri-flashlight-line tw-text-[#1AA24C]"></i>
                        Chọn gói dịch vụ
                    </h2>

                    <!-- Billing Toggle -->
                    <div class="tw-inline-flex tw-items-center tw-bg-gray-100 tw-rounded-full tw-p-1">
                        <button
                            type="button"
                            id="btn-monthly"
                            onclick="setBillingCycle('monthly')"
                            class="tw-px-5 tw-py-2 tw-rounded-full tw-text-sm tw-font-medium tw-transition tw-cursor-pointer tw-bg-white tw-shadow-sm tw-text-gray-800"
                        >
                            Thanh toán tháng
                        </button>
                        @php
                            // Tính % giảm giá trung bình từ các gói có yearly option
                            $avgDiscount = $plans->filter(fn($p) => $p->hasYearlyDiscount())->avg('yearly_discount_percent');
                        @endphp
                        <button
                            type="button"
                            id="btn-yearly"
                            onclick="setBillingCycle('yearly')"
                            class="tw-px-5 tw-py-2 tw-rounded-full tw-text-sm tw-font-medium tw-transition tw-flex tw-items-center tw-gap-2 tw-cursor-pointer tw-text-gray-500 hover:tw-text-gray-700"
                        >
                            Thanh toán năm
                            @if($avgDiscount > 0)
                                <span class="tw-bg-[#1AA24C] tw-text-white tw-text-xs tw-px-2 tw-py-0.5 tw-rounded-full">-{{ round($avgDiscount) }}%</span>
                            @endif
                        </button>
                    </div>
                </div>

                <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-4 tw-gap-6">
                    @foreach ($plans as $plan)
                        @php
                            $isCurrentPlan = $currentSubscription && $currentSubscription->plan_id === $plan->id && $currentSubscription->status === 'active';
                        @endphp
                        <form action="{{ route('brands.subscription.store', $brand) }}" method="POST"
                            class="tw-h-full">
                            @csrf
                            <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                            <input type="hidden" name="billing_cycle" class="billing-cycle-input" value="monthly">
                            <div
                                class="tw-bg-white tw-shadow-sm tw-rounded-xl tw-h-full tw-relative tw-p-6 tw-flex tw-flex-col tw-justify-between @if ($isCurrentPlan) tw-border-2 tw-border-blue-500 @elseif ($plan->is_popular) tw-border tw-border-[#1AA24C] @else tw-border tw-border-[#E4ECE8] @endif">
                                @if ($isCurrentPlan)
                                    <span
                                        class="tw-absolute tw-top-[-10px] tw-left-3 tw-bg-blue-500 tw-text-white tw-text-[10px] tw-font-semibold tw-px-2 tw-py-1 tw-rounded-full">
                                        Gói hiện tại
                                    </span>
                                @endif
                                @if ($plan->is_popular)
                                    <span
                                        class="tw-absolute tw-top-[-10px] tw-right-3 tw-bg-[#1AA24C] tw-text-white tw-text-[10px] tw-font-semibold tw-px-2 tw-py-1 tw-rounded-full">
                                        Phổ biến nhất
                                    </span>
                                @endif
                                <div>
                                    <p class="tw-font-bold tw-text-lg">{{ $plan->name }}</p>

                                    <!-- Monthly Price -->
                                    <div class="price-monthly tw-mt-4">
                                        <div class="tw-flex tw-items-baseline tw-gap-2">
                                            <span class="tw-text-3xl tw-font-bold">{{ $plan->formatted_price }}</span>
                                            @if (!$plan->is_trial && $plan->price > 0)
                                                <span class="tw-text-sm tw-text-gray-500">/tháng</span>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Yearly Price -->
                                    <div class="price-yearly tw-mt-4 tw-hidden">
                                        @if ($plan->hasYearlyOption())
                                            <div class="tw-flex tw-items-baseline tw-gap-2">
                                                <span class="tw-text-3xl tw-font-bold">{{ $plan->formatted_yearly_price }}</span>
                                                <span class="tw-text-sm tw-text-gray-500">/năm</span>
                                            </div>
                                        @else
                                            <div class="tw-flex tw-items-baseline tw-gap-2">
                                                <span class="tw-text-3xl tw-font-bold">{{ $plan->formatted_price }}</span>
                                                @if (!$plan->is_trial && $plan->price > 0)
                                                    <span class="tw-text-sm tw-text-gray-500">/tháng</span>
                                                @endif
                                            </div>
                                        @endif
                                    </div>

                                    <ul class="tw-mt-5 tw-space-y-2 tw-text-sm tw-text-gray-700">
                                        <li>✔ {{ number_format($plan->credits) }} năng lượng/tháng</li>
                                        <li>✔ {{ count($plan->models_allowed) }} AI Model</li>
                                    </ul>
                                </div>

                                {{-- Button logic --}}
                                @php
                                    $isCurrentPlan = $currentSubscription && $currentSubscription->plan_id === $plan->id;
                                    $isCurrentMonthly = $isCurrentPlan && $currentSubscription->billing_cycle === 'monthly';
                                @endphp

                                @if ($isCurrentPlan)
                                    <div class="tw-mt-6 tw-space-y-2">
                                        {{-- Gói hiện tại (monthly) --}}
                                        {{-- <button type="button"
                                            class="btn-current-monthly tw-w-full tw-bg-[#DCE2E0] tw-text-gray-600 tw-font-medium tw-text-sm tw-py-2 tw-rounded-lg tw-cursor-default {{ $isCurrentMonthly ? '' : 'tw-hidden' }}">
                                            Gói hiện tại
                                        </button> --}}

                                        {{-- Nếu đang gói tháng, hiện nút đăng ký gói năm --}}
                                        @if ($isCurrentMonthly && $plan->hasYearlyOption())
                                            <button type="submit"
                                                class="btn-upgrade-yearly tw-hidden tw-w-full tw-bg-[#1AA24C] tw-text-white tw-font-medium tw-text-sm tw-py-2 tw-rounded-lg hover:tw-opacity-90">
                                                Đăng ký gói năm
                                            </button>
                                        @endif

                                        {{-- Gói hiện tại (yearly) --}}
                                        {{-- <button type="button"
                                            class="btn-current-yearly tw-w-full tw-bg-[#DCE2E0] tw-text-gray-600 tw-font-medium tw-text-sm tw-py-2 tw-rounded-lg tw-cursor-default {{ $isCurrentMonthly ? 'tw-hidden' : '' }}">
                                            Gói hiện tại
                                        </button> --}}
                                    </div>
                                @elseif (!$plan->is_trial)
                                    <button type="submit"
                                        class="tw-w-full tw-bg-[#1AA24C] tw-text-white tw-font-medium tw-text-sm tw-py-2 tw-rounded-lg tw-mt-6 hover:tw-opacity-90">
                                        Đăng ký
                                    </button>
                                @endif
                            </div>
                        </form>
                    @endforeach
                    <div
                        class="tw-bg-white tw-shadow-sm tw-rounded-xl tw-border tw-border-[#E4ECE8] tw-p-6 tw-flex tw-flex-col tw-justify-between">
                        <div>
                            <p class="tw-font-bold tw-text-lg">Gói Tối Ưu (Premium)</p>
                            <p class="tw-text-3xl tw-font-bold tw-mt-6">Liên hệ</p>

                            <ul class="tw-mt-5 tw-space-y-2 tw-text-sm tw-text-gray-700">
                                <li>✔ 20.000 Credits/tháng</li>
                                <li>✔ 5 AI Agents</li>
                                <li>✔ Tạo nội dung không giới hạn</li>
                                <li>✔ Hỗ trợ 24/7 ưu tiên + tư vấn chiến lược</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    @push('scripts')
    <script>
        function setBillingCycle(cycle) {
            const btnMonthly = document.getElementById('btn-monthly');
            const btnYearly = document.getElementById('btn-yearly');
            const priceMonthly = document.querySelectorAll('.price-monthly');
            const priceYearly = document.querySelectorAll('.price-yearly');
            const billingInputs = document.querySelectorAll('.billing-cycle-input');

            // Buttons for current plan toggle
            const btnCurrentMonthly = document.querySelectorAll('.btn-current-monthly');
            const btnCurrentYearly = document.querySelectorAll('.btn-current-yearly');
            const btnUpgradeYearly = document.querySelectorAll('.btn-upgrade-yearly');

            // Update button styles
            if (cycle === 'monthly') {
                btnMonthly.className = 'tw-px-5 tw-py-2 tw-rounded-full tw-text-sm tw-font-medium tw-transition tw-cursor-pointer tw-bg-white tw-shadow-sm tw-text-gray-800';
                btnYearly.className = 'tw-px-5 tw-py-2 tw-rounded-full tw-text-sm tw-font-medium tw-transition tw-flex tw-items-center tw-gap-2 tw-cursor-pointer tw-text-gray-500 hover:tw-text-gray-700';
            } else {
                btnMonthly.className = 'tw-px-5 tw-py-2 tw-rounded-full tw-text-sm tw-font-medium tw-transition tw-cursor-pointer tw-text-gray-500 hover:tw-text-gray-700';
                btnYearly.className = 'tw-px-5 tw-py-2 tw-rounded-full tw-text-sm tw-font-medium tw-transition tw-flex tw-items-center tw-gap-2 tw-cursor-pointer tw-bg-white tw-shadow-sm tw-text-gray-800';
            }

            // Toggle price display
            priceMonthly.forEach(el => {
                el.classList.toggle('tw-hidden', cycle !== 'monthly');
            });
            priceYearly.forEach(el => {
                el.classList.toggle('tw-hidden', cycle !== 'yearly');
            });

            // Toggle current plan buttons
            btnCurrentMonthly.forEach(el => {
                el.classList.toggle('tw-hidden', cycle !== 'monthly');
            });
            btnCurrentYearly.forEach(el => {
                el.classList.toggle('tw-hidden', cycle !== 'yearly');
            });
            btnUpgradeYearly.forEach(el => {
                el.classList.toggle('tw-hidden', cycle !== 'yearly');
            });

            // Update hidden inputs
            billingInputs.forEach(input => {
                input.value = cycle;
            });
        }
    </script>
    @endpush
</x-app-layout>
