<x-app-layout>
    <style>
        @keyframes pulse-dot {
            0%, 100% { opacity: 0.2; }
            50% { opacity: 1; }
        }
        @keyframes shimmer {
            0% { background-position: -1000px 0; }
            100% { background-position: 1000px 0; }
        }
        .pulse-dot { animation: pulse-dot 1.4s ease-in-out infinite; }
        .pulse-dot:nth-child(1) { animation-delay: 0s; }
        .pulse-dot:nth-child(2) { animation-delay: 0.2s; }
        .pulse-dot:nth-child(3) { animation-delay: 0.4s; }
        .shimmer {
            background: linear-gradient(90deg, #f0f0f0 0px, #f8f8f8 40px, #f0f0f0 80px);
            background-size: 1000px 100%;
            animation: shimmer 2s infinite linear;
        }
    </style>

    <script>
        function copyToClipboard(text, successMessage) {
            navigator.clipboard.writeText(text).then(() => {
                if (window.showToast) {
                    window.showToast(successMessage, 'success')
                } else {
                    alert(successMessage)
                }
            }).catch(err => {
                console.error('Failed to copy:', err)
            })
        }
    </script>

    <div data-controller="payment-status"
         data-payment-status-status-value="{{ $payment->status }}"
         data-payment-status-check-url-value="{{ route('brands.payments.status', [$brand, $payment]) }}"
         data-payment-status-max-checks-value="60"
         data-payment-status-interval-ms-value="5000">

        <main class="tw-flex tw-flex-col md:tw-rounded-[10px] md:tw-bg-[#F3F7F5] md:tw-mt-4 md:tw-mx-8 lg:tw-mx-16 tw-p-3 md:tw-p-6">
            <!-- Breadcrumb -->
            <x-breadcrumb :items="[
                ['label' => 'Trang chủ', 'url' => route('dashboard')],
                ['label' => $brand->name, 'url' => route('brands.show', $brand)],
                ['label' => 'Thanh toán', 'url' => route('brands.payments.show', [$brand, $payment])],
            ]" />

            <!-- Pending Payment View -->
            <div data-payment-pending class="{{ $payment->status === 'completed' ? 'tw-hidden' : '' }}">
                <!-- Header compact -->
                <div class="tw-bg-white tw-rounded-xl tw-shadow tw-p-4 tw-mb-4">
                    <div class="tw-flex tw-flex-col sm:tw-flex-row tw-items-start sm:tw-items-center tw-justify-between tw-gap-2">
                        <div>
                            <h2 class="tw-text-xl tw-font-bold tw-text-gray-900">Thanh toán đơn hàng</h2>
                            <div class="tw-flex tw-flex-wrap tw-gap-3 tw-text-sm tw-mt-1">
                                <span><span class="tw-text-gray-500">Gói:</span> <strong>{{ $payment->subscription->plan->name }}</strong></span>
                                <span><span class="tw-text-gray-500">Mã:</span> <strong class="tw-font-mono">{{ $payment->transaction_id }}</strong></span>
                            </div>
                        </div>
                        <div class="tw-text-2xl tw-font-bold tw-text-emerald-600">{{ $payment->formatted_amount }}</div>
                    </div>
                </div>

                @if ($bankInfo)
                <!-- Main Grid: QR + Bank Info -->
                <div class="tw-grid tw-grid-cols-1 lg:tw-grid-cols-5 tw-gap-4 tw-mb-4">
                    <!-- LEFT: QR Code (2/5) -->
                    <div class="lg:tw-col-span-2 tw-bg-white tw-rounded-xl tw-shadow tw-p-4">
                        <div class="tw-flex tw-items-center tw-gap-2 tw-mb-3">
                            <div class="tw-w-8 tw-h-8 tw-rounded-full tw-bg-emerald-600 tw-flex tw-items-center tw-justify-center">
                                <svg class="tw-w-4 tw-h-4 tw-text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="tw-font-bold tw-text-gray-900">Quét mã QR</h3>
                                <p class="tw-text-xs tw-text-gray-500">Mở app ngân hàng và quét</p>
                            </div>
                        </div>
                        <div class="tw-flex tw-justify-center">
                            <div class="tw-bg-gray-50 tw-p-3 tw-rounded-xl">
                                <img src="{{ $bankInfo['qr_url'] }}" alt="QR Code" class="tw-w-48 tw-h-48 lg:tw-w-56 lg:tw-h-56">
                            </div>
                        </div>
                        <!-- Status loading inline -->
                        <div data-payment-status-bar class="tw-mt-3 tw-flex tw-items-center tw-justify-center tw-gap-2 tw-text-sm tw-text-emerald-600">
                            <svg class="tw-animate-spin tw-w-4 tw-h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="tw-opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="tw-opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span>Trạng thái: Chờ thanh toán</span>
                            <span class="tw-flex tw-gap-0.5">
                                <span class="tw-w-1 tw-h-1 tw-bg-emerald-500 tw-rounded-full pulse-dot"></span>
                                <span class="tw-w-1 tw-h-1 tw-bg-emerald-500 tw-rounded-full pulse-dot"></span>
                                <span class="tw-w-1 tw-h-1 tw-bg-emerald-500 tw-rounded-full pulse-dot"></span>
                            </span>
                        </div>
                    </div>

                    <!-- RIGHT: Manual Transfer (3/5) -->
                    <div class="lg:tw-col-span-3 tw-bg-white tw-rounded-xl tw-shadow tw-p-4">
                        <div class="tw-flex tw-items-center tw-gap-2 tw-mb-3">
                            <div class="tw-w-8 tw-h-8 tw-rounded-full tw-bg-blue-600 tw-flex tw-items-center tw-justify-center">
                                <svg class="tw-w-4 tw-h-4 tw-text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="tw-font-bold tw-text-gray-900">Chuyển khoản thủ công</h3>
                                <p class="tw-text-xs tw-text-gray-500">Nhập thông tin vào app ngân hàng</p>
                            </div>
                        </div>

                        <div class="tw-space-y-2">
                            <!-- Bank & Account Name - 2 columns -->
                            <div class="tw-grid tw-grid-cols-2 tw-gap-2">
                                <div class="tw-bg-gray-50 tw-rounded-lg tw-p-2.5 tw-border tw-border-gray-200">
                                    <div class="tw-text-xs tw-text-gray-500">Ngân hàng</div>
                                    <div class="tw-font-semibold tw-text-gray-900 tw-text-sm">{{ $bankInfo['bank_name'] }}</div>
                                </div>
                                <div class="tw-bg-gray-50 tw-rounded-lg tw-p-2.5 tw-border tw-border-gray-200">
                                    <div class="tw-text-xs tw-text-gray-500">Chủ TK</div>
                                    <div class="tw-font-semibold tw-text-gray-900 tw-text-sm">{{ $bankInfo['account_name'] }}</div>
                                </div>
                            </div>

                            <!-- Account Number -->
                            <div class="tw-bg-gray-50 tw-rounded-lg tw-p-2.5 tw-border tw-border-gray-200 tw-flex tw-items-center tw-justify-between">
                                <div>
                                    <div class="tw-text-xs tw-text-gray-500">Số tài khoản</div>
                                    <div class="tw-font-semibold tw-text-gray-900 tw-font-mono">{{ $bankInfo['account_number'] }}</div>
                                </div>
                                <button onclick="copyToClipboard('{{ $bankInfo['account_number'] }}', 'Đã copy số tài khoản')"
                                        class="tw-p-1.5 tw-rounded-lg tw-bg-blue-100 hover:tw-bg-blue-200 tw-text-blue-600 tw-transition">
                                    <svg class="tw-w-4 tw-h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                    </svg>
                                </button>
                            </div>

                            <!-- Transfer Content - Important -->
                            <div class="tw-bg-red-50 tw-border-2 tw-border-red-300 tw-rounded-lg tw-p-2.5 tw-flex tw-items-center tw-justify-between">
                                <div>
                                    <div class="tw-text-xs tw-text-red-600 tw-font-medium">Nội dung CK (bắt buộc)</div>
                                    <div class="tw-font-bold tw-text-red-800 tw-font-mono">{{ $bankInfo['content'] }}</div>
                                </div>
                                <button onclick="copyToClipboard('{{ $bankInfo['content'] }}', 'Đã copy nội dung')"
                                        class="tw-p-1.5 tw-rounded-lg tw-bg-red-100 hover:tw-bg-red-200 tw-text-red-600 tw-transition">
                                    <svg class="tw-w-4 tw-h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                    </svg>
                                </button>
                            </div>

                            <!-- Amount -->
                            <div class="tw-bg-emerald-50 tw-border-2 tw-border-emerald-300 tw-rounded-lg tw-p-2.5 tw-flex tw-items-center tw-justify-between">
                                <div>
                                    <div class="tw-text-xs tw-text-emerald-600 tw-font-medium">Số tiền</div>
                                    <div class="tw-font-bold tw-text-emerald-700 tw-text-lg">{{ number_format($bankInfo['amount']) }} VNĐ</div>
                                </div>
                                <button onclick="copyToClipboard('{{ $bankInfo['amount'] }}', 'Đã copy số tiền')"
                                        class="tw-p-1.5 tw-rounded-lg tw-bg-emerald-100 hover:tw-bg-emerald-200 tw-text-emerald-600 tw-transition">
                                    <svg class="tw-w-4 tw-h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Completed Payment View -->
            <div data-payment-completed class="{{ $payment->status === 'pending' ? 'tw-hidden' : '' }}">
                <div class="tw-flex tw-items-center tw-justify-center tw-py-16">
                    <div class="tw-bg-white tw-rounded-xl tw-shadow-lg tw-p-8 tw-text-center tw-max-w-sm">
                        <div class="tw-w-16 tw-h-16 tw-bg-emerald-100 tw-rounded-full tw-flex tw-items-center tw-justify-center tw-mx-auto tw-mb-4">
                            <svg class="tw-w-8 tw-h-8 tw-text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <h2 class="tw-text-2xl tw-font-bold tw-text-gray-900 tw-mb-2">Thanh toán thành công!</h2>
                        <p class="tw-text-gray-600 tw-mb-5">Gói dịch vụ đã được kích hoạt</p>
                        <a href="{{ route('brands.subscription.show', $brand) }}"
                            class="tw-inline-flex tw-items-center tw-gap-2 tw-px-6 tw-py-3 tw-bg-emerald-600 tw-text-white tw-rounded-lg tw-font-semibold hover:tw-bg-emerald-700 tw-transition">
                            Xem gói dịch vụ
                            <svg class="tw-w-4 tw-h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Back Link -->
            <div class="tw-mt-4 tw-text-center">
                <a href="{{ route('brands.subscription.show', $brand) }}"
                    class="tw-inline-flex tw-items-center tw-gap-1 tw-text-gray-500 hover:tw-text-gray-700 tw-transition tw-text-sm">
                    <svg class="tw-w-4 tw-h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Quay lại
                </a>
            </div>
        </main>
    </div>
</x-app-layout>
