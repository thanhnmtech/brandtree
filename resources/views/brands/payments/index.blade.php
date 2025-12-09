<x-app-layout>
    <style>
        [x-cloak] { display: none !important; }
    </style>

    <main class="tw-mt-[36px] tw-flex tw-flex-col tw-gap-5">
        <div class="tw-px-8">
            <x-breadcrumb :items="[
                ['label' => 'Trang chủ', 'url' => route('dashboard')],
                ['label' => $brand->name, 'url' => route('brands.show', $brand)]
            ]" />

            <div class="tw-flex tw-flex-col md:tw-flex-row tw-justify-between tw-mb-3">
                <div>
                    <h1 class="tw-text-[22px] tw-font-bold">Lịch sử thanh toán</h1>
                </div>
            </div>
        </div>

        <!-- ================= PAYMENT LIST ================= -->
        <section class="tw-px-8">
            <div class="tw-bg-white tw-border tw-border-[#E0EAE6] tw-rounded-[12px] tw-shadow-sm tw-w-full">
                @if($payments->count() > 0)
                    <!-- Header -->
                    <div class="tw-hidden md:tw-grid tw-grid-cols-7 tw-font-semibold tw-text-[14px] tw-border-b tw-border-[#E0EAE6] tw-bg-[#F7F8F9] tw-p-4">
                        <div>Mã giao dịch</div>
                        <div>Gói dịch vụ</div>
                        <div class="tw-text-right">Số tiền</div>
                        <div class="tw-text-center">Trạng thái</div>
                        <div>Ngày tạo</div>
                        <div>Ngày thanh toán</div>
                        <div class="tw-text-right">Hành động</div>
                    </div>

                    <!-- Payment List -->
                    <div class="tw-divide-y tw-divide-[#E0EAE6]">
                        @foreach($payments as $payment)
                        <div class="tw-grid md:tw-grid-cols-7 tw-items-center tw-p-4 hover:tw-bg-gray-50">
                            <!-- Mobile Layout -->
                            <div class="md:tw-hidden tw-space-y-2">
                                <div class="tw-flex tw-justify-between tw-items-start">
                                    <div>
                                        <p class="tw-font-bold tw-text-lg">{{ $payment->subscription->plan->name ?? '-' }}</p>
                                        <p class="tw-text-sm tw-text-[#6F7C7A] tw-font-mono">{{ $payment->transaction_id }}</p>
                                    </div>
                                    @if($payment->status === 'completed')
                                        <span class="tw-px-2 tw-py-1 tw-text-xs tw-rounded-full tw-bg-green-100 tw-text-green-700 tw-font-medium">Đã thanh toán</span>
                                    @elseif($payment->status === 'pending')
                                        <span class="tw-px-2 tw-py-1 tw-text-xs tw-rounded-full tw-bg-yellow-100 tw-text-yellow-700 tw-font-medium">Chờ thanh toán</span>
                                    @else
                                        <span class="tw-px-2 tw-py-1 tw-text-xs tw-rounded-full tw-bg-red-100 tw-text-red-700 tw-font-medium">Thất bại</span>
                                    @endif
                                </div>
                                <div class="tw-flex tw-justify-between tw-items-center">
                                    <span class="tw-text-lg tw-font-bold tw-text-[#1AA24C]">{{ $payment->formatted_amount }}</span>
                                    <span class="tw-text-sm tw-text-[#6F7C7A]">{{ $payment->created_at->format('d/m/Y H:i') }}</span>
                                </div>
                                @if($payment->paid_at)
                                    <p class="tw-text-sm tw-text-[#6F7C7A]">TT: {{ $payment->paid_at->format('d/m/Y H:i') }}</p>
                                @endif
                                <div class="tw-flex tw-gap-2 tw-mt-2">
                                    <a href="{{ route('brands.payments.show', [$brand, $payment]) }}"
                                        class="tw-flex-1 tw-text-center tw-bg-[#F3F7F5] tw-border tw-border-[#E2EBE7] tw-py-2 tw-px-4 tw-rounded-lg tw-text-sm tw-font-medium tw-text-black hover:tw-bg-[#E8EEE9]">
                                        Chi tiết
                                    </a>
                                </div>
                            </div>

                            <!-- Desktop Columns -->
                            <div class="tw-hidden md:tw-block">
                                <p class="tw-font-mono tw-text-sm tw-text-[#6F7C7A]">{{ $payment->transaction_id }}</p>
                            </div>

                            <div class="tw-hidden md:tw-block">
                                <p class="tw-text-sm tw-text-gray-800">{{ $payment->subscription->plan->name ?? '-' }}</p>
                            </div>

                            <div class="tw-hidden md:tw-block tw-text-right">
                                <p class="tw-text-sm tw-font-bold tw-text-[#1AA24C]">{{ $payment->formatted_amount }}</p>
                            </div>

                            <div class="tw-hidden md:tw-flex tw-justify-center">
                                @if($payment->status === 'completed')
                                    <span class="tw-px-3 tw-py-1 tw-text-xs tw-rounded-full tw-bg-green-100 tw-text-green-700 tw-font-medium">Đã thanh toán</span>
                                @elseif($payment->status === 'pending')
                                    <span class="tw-px-3 tw-py-1 tw-text-xs tw-rounded-full tw-bg-yellow-100 tw-text-yellow-700 tw-font-medium">Chờ thanh toán</span>
                                @else
                                    <span class="tw-px-3 tw-py-1 tw-text-xs tw-rounded-full tw-bg-red-100 tw-text-red-700 tw-font-medium">Thất bại</span>
                                @endif
                            </div>

                            <div class="tw-hidden md:tw-block">
                                <p class="tw-text-sm tw-text-[#6F7C7A]">{{ $payment->created_at->format('d/m/Y H:i') }}</p>
                            </div>

                            <div class="tw-hidden md:tw-block">
                                <p class="tw-text-sm tw-text-[#6F7C7A]">{{ $payment->paid_at?->format('d/m/Y H:i') ?? '-' }}</p>
                            </div>

                            <div class="tw-hidden md:tw-flex tw-justify-end tw-gap-2">
                                <a href="{{ route('brands.payments.show', [$brand, $payment]) }}"
                                    class="tw-bg-[#F3F7F5] tw-border tw-border-[#E2EBE7] tw-py-2 tw-px-4 tw-rounded-lg tw-text-sm tw-font-medium tw-text-black hover:tw-bg-[#E8EEE9] tw-transition">
                                    Chi tiết
                                </a>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    @if($payments->hasPages())
                    <div class="tw-border-t tw-border-[#E0EAE6] tw-p-4">
                        {{ $payments->links() }}
                    </div>
                    @endif
                @else
                    <!-- Empty State -->
                    <div class="tw-flex tw-flex-col tw-items-center tw-justify-center tw-py-16 tw-px-4">
                        <div class="tw-w-16 tw-h-16 tw-bg-gray-100 tw-rounded-full tw-flex tw-items-center tw-justify-center tw-mb-4">
                            <i class="ri-receipt-line tw-text-3xl tw-text-gray-400"></i>
                        </div>
                        <h3 class="tw-text-lg tw-font-semibold tw-text-gray-800 tw-mb-2">Chưa có lịch sử thanh toán</h3>
                        <p class="tw-text-sm tw-text-gray-500 tw-text-center tw-max-w-md">
                            Các giao dịch thanh toán của bạn sẽ được hiển thị tại đây.
                        </p>
                    </div>
                @endif
            </div>
        </section>
    </main>
</x-app-layout>
