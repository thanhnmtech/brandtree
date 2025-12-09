<x-app-layout>
    <main class="tw-flex tw-flex-col md:tw-rounded-[10px] md:tw-bg-[#F3F7F5] md:tw-mt-[36px] md:tw-mx-[71px] tw-p-10">
        <!-- Header -->
        <x-breadcrumb :items="[
            ['label' => 'Trang chủ', 'url' => route('dashboard')],
            ['label' => $brand->name, 'url' => route('brands.show', $brand)],
        ]" />
        <div class="tw-mb-6">
            <h1 class="tw-text-2xl tw-font-bold tw-text-gray-800">Chi tiết thanh toán</h1>
        </div>

        <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-gap-6">
            <!-- Payment Status -->
            <div class="tw-bg-white tw-rounded-2xl tw-shadow-sm tw-p-6">
                <div class="tw-flex tw-items-center tw-justify-between tw-mb-6">
                    <h3 class="tw-text-lg tw-font-semibold tw-text-gray-800">Trạng thái</h3>
                    @if ($payment->isPending())
                        <span
                            class="tw-px-3 tw-py-1 tw-bg-yellow-100 tw-text-yellow-700 tw-rounded-full tw-text-sm tw-font-medium">Chờ
                            thanh toán</span>
                    @elseif($payment->isCompleted())
                        <span
                            class="tw-px-3 tw-py-1 tw-bg-green-100 tw-text-green-700 tw-rounded-full tw-text-sm tw-font-medium">Đã
                            thanh toán</span>
                    @else
                        <span
                            class="tw-px-3 tw-py-1 tw-bg-red-100 tw-text-red-700 tw-rounded-full tw-text-sm tw-font-medium">Thất
                            bại</span>
                    @endif
                </div>

                <div class="tw-space-y-4">
                    <div class="tw-flex tw-justify-between">
                        <span class="tw-text-gray-600">Mã giao dịch</span>
                        <span class="tw-font-medium tw-text-gray-800">{{ $payment->transaction_id }}</span>
                    </div>
                    <div class="tw-flex tw-justify-between">
                        <span class="tw-text-gray-600">Gói dịch vụ</span>
                        <span class="tw-font-medium tw-text-gray-800">{{ $payment->subscription->plan->name }}</span>
                    </div>
                    <div class="tw-flex tw-justify-between">
                        <span class="tw-text-gray-600">Số tiền</span>
                        <span class="tw-font-bold tw-text-[#16a249]">{{ $payment->formatted_amount }}</span>
                    </div>
                    <div class="tw-flex tw-justify-between">
                        <span class="tw-text-gray-600">Ngày tạo</span>
                        <span
                            class="tw-font-medium tw-text-gray-800">{{ $payment->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                    @if ($payment->paid_at)
                        <div class="tw-flex tw-justify-between">
                            <span class="tw-text-gray-600">Ngày thanh toán</span>
                            <span
                                class="tw-font-medium tw-text-gray-800">{{ $payment->paid_at->format('d/m/Y H:i') }}</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Bank Transfer Info -->
            @if ($payment->isPending() && $bankInfo)
                <div class="tw-bg-white tw-rounded-2xl tw-shadow-sm tw-p-6">
                    <h3 class="tw-text-lg tw-font-semibold tw-text-gray-800 tw-mb-4">Thông tin chuyển khoản</h3>

                    <!-- QR Code -->
                    <div class="tw-flex tw-justify-center tw-mb-4">
                        <img src="{{ $bankInfo['qr_url'] }}" alt="QR Code"
                            class="tw-w-48 tw-h-48 tw-rounded-lg tw-border tw-border-gray-200">
                    </div>
                    <p class="tw-text-center tw-text-sm tw-text-gray-500 tw-mb-4">Quét mã QR để thanh toán nhanh</p>

                    <div class="tw-bg-gray-50 tw-rounded-xl tw-p-4 tw-mb-4">
                        <div class="tw-space-y-3 tw-text-sm">
                            <div>
                                <span class="tw-text-gray-500">Ngân hàng:</span>
                                <p class="tw-font-medium tw-text-gray-800">{{ $bankInfo['bank_name'] }}</p>
                            </div>
                            <div>
                                <span class="tw-text-gray-500">Số tài khoản:</span>
                                <p class="tw-font-medium tw-text-gray-800">{{ $bankInfo['account_number'] }}</p>
                            </div>
                            <div>
                                <span class="tw-text-gray-500">Chủ tài khoản:</span>
                                <p class="tw-font-medium tw-text-gray-800">{{ $bankInfo['account_name'] }}</p>
                            </div>
                            <div>
                                <span class="tw-text-gray-500">Nội dung chuyển khoản:</span>
                                <p class="tw-font-medium tw-text-[#16a249]">{{ $bankInfo['content'] }}</p>
                            </div>
                            <div>
                                <span class="tw-text-gray-500">Số tiền:</span>
                                <p class="tw-font-bold tw-text-lg tw-text-gray-800">
                                    {{ number_format($bankInfo['amount']) }}đ</p>
                            </div>
                        </div>
                    </div>

                    <div class="tw-bg-yellow-50 tw-border tw-border-yellow-200 tw-rounded-xl tw-p-4 tw-mb-4">
                        <p class="tw-text-sm tw-text-yellow-700">
                            <strong>Lưu ý:</strong> Vui lòng ghi đúng nội dung chuyển khoản. Gói dịch vụ sẽ được kích
                            hoạt tự động sau khi xác nhận thanh toán (thường trong vòng 5 phút).
                        </p>
                    </div>

                    <!-- Check Status Button -->
                    <form action="{{ route('brands.payments.check', [$brand, $payment]) }}" method="POST">
                        @csrf
                        <button type="submit"
                            class="tw-w-full tw-px-4 tw-py-3 tw-bg-[#16a249] tw-text-white tw-rounded-lg tw-font-medium hover:tw-bg-[#138a3e] tw-transition">
                            Tôi đã thanh toán - Kiểm tra trạng thái
                        </button>
                    </form>
                </div>
            @else
                <div
                    class="tw-bg-white tw-rounded-2xl tw-shadow-sm tw-p-6 tw-flex tw-flex-col tw-items-center tw-justify-center">
                    @if ($payment->isCompleted())
                        <div
                            class="tw-w-16 tw-h-16 tw-bg-green-100 tw-rounded-full tw-flex tw-items-center tw-justify-center tw-mb-4">
                            <svg class="tw-w-8 tw-h-8 tw-text-green-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <h3 class="tw-text-lg tw-font-semibold tw-text-gray-800 tw-mb-2">Thanh toán thành công!</h3>
                        <p class="tw-text-gray-600 tw-text-center tw-mb-4">Gói dịch vụ đã được kích hoạt.</p>
                        <a href="{{ route('brands.subscription.show', $brand) }}"
                            class="tw-px-4 tw-py-2 tw-bg-[#16a249] tw-text-white tw-rounded-lg tw-font-medium hover:tw-bg-[#138a3e] tw-transition">
                            Xem gói dịch vụ
                        </a>
                    @endif
                </div>
            @endif
        </div>

        <div class="tw-mt-6 tw-text-center">
            <a href="{{ route('brands.subscription.show', $brand) }}" class="tw-text-gray-600 hover:tw-text-gray-800">←
                Quay lại gói dịch vụ</a>
        </div>
    </main>
</x-app-layout>
