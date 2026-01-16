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
        <section class="tw-px-8">
            <div class="tw-bg-white tw-rounded-xl tw-border tw-border-[#E0EAE6] tw-shadow-sm tw-p-6">
                <div class="tw-flex tw-items-start tw-justify-between">
                    <h2 class="tw-text-sm tw-font-semibold tw-text-gray-500">
                        Gói hiện tại
                        <p class="tw-text-lg tw-font-bold tw-text-black">
                            {{ $brand->activeSubscription->plan->name }}
                            @if($brand->activeSubscription->billing_cycle === 'yearly')
                                <span class="tw-text-sm tw-font-normal tw-text-gray-500">(Thanh toán năm)</span>
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
                                {{ $brand->credits_remaining }}</span>
                            <span class="tw-text-gray-500">/ {{ $brand->total_credits }}</span>
                        </div>
                    </div>

                    <!-- NGÀY HẾT HẠN -->
                    <div class="tw-text-center">
                        <p class="tw-text-sm tw-text-gray-500">Ngày làm mới năng lượng</p>
                        <p class="tw-text-2xl tw-font-bold tw-mt-1">
                            {{ $brand->activeSubscription->credits_reset_at->format('d/m/Y') }}</p>
                    </div>

                    <div class="tw-text-center">
                        <p class="tw-text-sm tw-text-gray-500">Ngày gia hạn</p>
                        <p class="tw-text-2xl tw-font-bold tw-mt-1">
                            {{ $brand->activeSubscription->expires_at->format('d/m/Y') }}</p>
                    </div>
                </div>
            </div>
        </section>

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
                        <form action="{{ route('brands.subscription.store', $brand) }}" method="POST"
                            class="tw-h-full">
                            @csrf
                            <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                            <input type="hidden" name="billing_cycle" class="billing-cycle-input" value="monthly">
                            <div
                                class="tw-bg-white tw-shadow-sm tw-rounded-xl tw-border tw-h-full @if ($plan->is_popular) tw-relative tw-border-[#1AA24C] @else tw-border-[#E4ECE8] @endif tw-p-6 tw-flex tw-flex-col tw-justify-between">
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
                                            {{-- @if ($plan->hasYearlyDiscount())
                                                <span class="tw-text-gray-400 tw-line-through tw-text-sm">{{ $plan->formatted_yearly_original_price }}</span>
                                            @endif --}}
                                            <div class="tw-flex tw-items-baseline tw-gap-2">
                                                <span class="tw-text-3xl tw-font-bold">{{ $plan->formatted_yearly_price }}</span>
                                                <span class="tw-text-sm tw-text-gray-500">/năm</span>
                                            </div>
                                            {{-- <p class="tw-text-sm tw-text-[#1AA24C] tw-mt-1">
                                                ~ {{ $plan->formatted_monthly_from_yearly_price }}/tháng
                                            </p> --}}
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
                                @if ($currentSubscription && $currentSubscription->plan_id === $plan->id)
                                    <button type="button"
                                        class="tw-w-full tw-bg-[#DCE2E0] tw-text-gray-600 tw-font-medium tw-text-sm tw-py-2 tw-rounded-lg tw-mt-6 tw-cursor-default">
                                        Gói hiện tại
                                    </button>
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

            // Update hidden inputs
            billingInputs.forEach(input => {
                input.value = cycle;
            });
        }
    </script>
    @endpush
</x-app-layout>
