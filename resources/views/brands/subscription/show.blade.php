<x-app-layout>
    <main class="tw-mt-[36px] tw-flex tw-flex-col tw-gap-10 tw-mb-5">
        <div class="tw-mb-6 tw-px-8">
            <nav class="tw-flex tw-mb-4" aria-label="Breadcrumb">
                <ol class="tw-inline-flex tw-items-center tw-space-x-1 md:tw-space-x-3">
                    <li><a href="{{ route('dashboard') }}" class="tw-text-gray-500 hover:tw-text-gray-700">Trang chủ</a>
                    </li>
                    <li class="tw-flex tw-items-center">
                        <svg class="tw-w-4 tw-h-4 tw-text-gray-400 tw-mx-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                clip-rule="evenodd"></path>
                        </svg>
                        <a href="{{ route('brands.show', $brand) }}"
                            class="tw-text-gray-500 hover:tw-text-gray-700">{{ $brand->name }}</a>
                    </li>
                    <li class="tw-flex tw-items-center">
                        <svg class="tw-w-4 tw-h-4 tw-text-gray-400 tw-mx-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                clip-rule="evenodd"></path>
                        </svg>
                        <span class="tw-text-gray-700">Gói dịch vụ</span>
                    </li>
                </ol>
            </nav>
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
                <h2 class="tw-text-xl tw-font-bold tw-flex tw-items-center tw-gap-2">
                    <i class="ri-flashlight-line tw-text-[#1AA24C]"></i>
                    Chọn gói dịch vụ
                </h2>

                <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-4 tw-gap-6">
                    @foreach ($monthlyPlans as $plan)
                        <form action="{{ route('brands.subscription.store', $brand) }}" method="POST"
                            class="tw-h-full">
                            @csrf
                            <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                            <div
                                class="tw-bg-white tw-shadow-sm tw-rounded-xl tw-border @if ($plan->is_popular) tw-relative tw-border-[#1AA24C] @else tw-border-[#E4ECE8] @endif tw-p-6 tw-flex tw-flex-col tw-justify-between">
                                <span
                                    class="tw-absolute tw-top-[-10px] tw-right-3 tw-bg-[#1AA24C] tw-text-white tw-text-[10px] tw-font-semibold tw-px-2 tw-py-1 tw-rounded-full">
                                    Phổ biến nhất
                                </span>
                                <div>
                                    <p class="tw-font-bold tw-text-lg">{{ $plan->name }}</p>

                                    <div class="tw-mt-4 tw-flex tw-items-baseline tw-gap-4">
                                        <span class="tw-text-3xl tw-font-bold">{{ $plan->formatted_price }}</span>
                                        @if (!$plan->is_trial && $plan->price > 0)
                                            <span
                                                class="tw-text-sm tw-text-gray-500">/{{ $plan->isYearly() ?? false ? 'năm' : 'tháng' }}</span>
                                        @endif
                                    </div>
                                    <ul class="tw-mt-5 tw-space-y-2 tw-text-sm tw-text-gray-700">
                                        <li>✔ {{ number_format($plan->credits) }} năng
                                            lượng/{{ $plan->isYearly() ?? false ? 'năm' : 'tháng' }}</li>
                                        <li>✔ {{ count($plan->models_allowed) }} AI Model</li>
                                    </ul>
                                </div>
                                @if ($currentSubscription && $currentSubscription->plan_id === $plan->id)
                                    <button
                                        class="tw-w-full tw-bg-[#DCE2E0] tw-text-gray-600 tw-font-medium tw-text-sm tw-py-2 tw-rounded-lg tw-mt-6">
                                        Gói hiện tại
                                    </button>
                                @endif
                                @if (!$plan->is_trial)
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
</x-app-layout>
