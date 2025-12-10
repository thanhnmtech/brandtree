<x-app-layout>
    <style>
        @keyframes pulse-ring {
            0% { transform: scale(0.95); opacity: 1; }
            50% { transform: scale(1); opacity: 0.7; }
            100% { transform: scale(0.95); opacity: 1; }
        }

        @keyframes pulse-dot {
            0%, 100% { opacity: 0.2; }
            50% { opacity: 1; }
        }

        @keyframes slide-up {
            from { transform: translateY(10px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        @keyframes shimmer {
            0% { background-position: -1000px 0; }
            100% { background-position: 1000px 0; }
        }

        .pulse-ring {
            animation: pulse-ring 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        .pulse-dot {
            animation: pulse-dot 1.4s ease-in-out infinite;
        }

        .pulse-dot:nth-child(1) { animation-delay: 0s; }
        .pulse-dot:nth-child(2) { animation-delay: 0.2s; }
        .pulse-dot:nth-child(3) { animation-delay: 0.4s; }

        .slide-up {
            animation: slide-up 0.5s ease-out;
        }

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
                if (window.showToast) {
                    window.showToast('Không thể copy. Vui lòng thử lại.', 'error')
                } else {
                    alert('Không thể copy. Vui lòng thử lại.')
                }
            })
        }
    </script>

    <div data-controller="payment-status"
         data-payment-status-status-value="{{ $payment->status }}"
         data-payment-status-check-url-value="{{ route('brands.payments.status', [$brand, $payment]) }}"
         data-payment-status-max-checks-value="60"
         data-payment-status-interval-ms-value="5000">

        <main class="tw-flex tw-flex-col md:tw-rounded-[10px] md:tw-bg-[#F3F7F5] md:tw-mt-[36px] md:tw-mx-[71px] tw-p-4 md:tw-p-10">
            <!-- Header -->
            <x-breadcrumb :items="[
                ['label' => 'Trang chủ', 'url' => route('dashboard')],
                ['label' => $brand->name, 'url' => route('brands.show', $brand)],
                ['label' => 'Thanh toán', 'url' => route('brands.payments.show', [$brand, $payment])],
            ]" />

            <!-- Pending Payment View -->
            <div data-payment-pending class="{{ $payment->status === 'completed' ? 'tw-hidden' : '' }}">
                <div class="slide-up">
                    <!-- Payment Info Header -->
                    <div class="tw-bg-white tw-rounded-2xl tw-shadow-lg tw-p-6 tw-mb-6">
                        <div class="tw-flex tw-flex-col md:tw-flex-row tw-items-start md:tw-items-center tw-justify-between tw-gap-4">
                            <div>
                                <h2 class="tw-text-2xl tw-font-bold tw-text-gray-900 tw-mb-2">Thanh toán đơn hàng</h2>
                                <div class="tw-flex tw-flex-wrap tw-gap-4 tw-text-sm">
                                    <div>
                                        <span class="tw-text-gray-600">Gói dịch vụ:</span>
                                        <span class="tw-font-semibold tw-text-gray-900 tw-ml-1">{{ $payment->subscription->plan->name }}</span>
                                    </div>
                                    <div>
                                        <span class="tw-text-gray-600">Mã GD:</span>
                                        <span class="tw-font-mono tw-font-semibold tw-text-gray-900 tw-ml-1">{{ $payment->transaction_id }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="tw-text-right">
                                <div class="tw-text-sm tw-text-gray-600 tw-mb-1">Tổng thanh toán</div>
                                <div class="tw-text-3xl tw-font-bold tw-text-emerald-600">{{ $payment->formatted_amount }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Main Payment Grid: QR Left, Manual Right -->
                    @if ($bankInfo)
                    <div class="tw-grid tw-grid-cols-1 lg:tw-grid-cols-2 tw-gap-6 tw-mb-6">
                        <!-- LEFT: QR Code -->
                        <div class="tw-bg-white tw-rounded-2xl tw-shadow-lg tw-overflow-hidden">
                            <div class="tw-bg-gradient-to-br tw-from-emerald-50 tw-to-emerald-100 tw-p-6 tw-border-b tw-border-emerald-200">
                                <div class="tw-flex tw-items-center tw-gap-3 tw-mb-4">
                                    <div class="tw-w-10 tw-h-10 tw-rounded-full tw-bg-emerald-600 tw-flex tw-items-center tw-justify-center">
                                        <svg class="tw-w-6 tw-h-6 tw-text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="tw-text-xl tw-font-bold tw-text-gray-900">Quét mã QR</h3>
                                        <p class="tw-text-sm tw-text-gray-700">Cách nhanh nhất để thanh toán</p>
                                    </div>
                                </div>

                                <div class="tw-flex tw-justify-center">
                                    <div class="tw-bg-white tw-p-5 tw-rounded-2xl tw-shadow-2xl">
                                        <img src="{{ $bankInfo['qr_url'] }}" alt="QR Code" class="tw-w-72 tw-h-72">
                                    </div>
                                </div>
                            </div>

                            <div class="tw-p-6 tw-bg-gradient-to-br tw-from-gray-50 tw-to-white">
                                <div class="tw-flex tw-items-start tw-gap-3 tw-text-sm tw-text-gray-700">
                                    <svg class="tw-w-5 tw-h-5 tw-text-emerald-600 tw-flex-shrink-0 tw-mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <div>
                                        <p class="tw-font-semibold tw-text-gray-900 tw-mb-1">Hướng dẫn thanh toán:</p>
                                        <ol class="tw-space-y-1">
                                            <li>1. Mở app ngân hàng của bạn</li>
                                            <li>2. Chọn chức năng quét mã QR</li>
                                            <li>3. Quét mã QR phía trên</li>
                                            <li>4. Xác nhận và hoàn tất thanh toán</li>
                                        </ol>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- RIGHT: Manual Transfer -->
                        <div class="tw-bg-white tw-rounded-2xl tw-shadow-lg tw-overflow-hidden">
                            <div class="tw-bg-gradient-to-br tw-from-blue-50 tw-to-blue-100 tw-p-6 tw-border-b tw-border-blue-200">
                                <div class="tw-flex tw-items-center tw-gap-3">
                                    <div class="tw-w-10 tw-h-10 tw-rounded-full tw-bg-blue-600 tw-flex tw-items-center tw-justify-center">
                                        <svg class="tw-w-6 tw-h-6 tw-text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="tw-text-xl tw-font-bold tw-text-gray-900">Chuyển khoản thủ công</h3>
                                        <p class="tw-text-sm tw-text-gray-700">Nhập thông tin vào app ngân hàng</p>
                                    </div>
                                </div>
                            </div>

                            <div class="tw-p-6 tw-space-y-3">
                                <!-- Bank Name -->
                                <div class="tw-bg-gray-50 tw-rounded-xl tw-p-3 tw-border tw-border-gray-200">
                                    <div class="tw-grid tw-grid-cols-2 tw-gap-4">
                                        <div class="tw-text-sm tw-text-gray-600 tw-font-medium">Ngân hàng</div>
                                        <div class="tw-font-bold tw-text-gray-900 tw-text-sm">{{ $bankInfo['bank_name'] }}</div>
                                    </div>
                                </div>

                                <!-- Account Name -->
                                <div class="tw-bg-gray-50 tw-rounded-xl tw-p-3 tw-border tw-border-gray-200">
                                    <div class="tw-grid tw-grid-cols-2 tw-gap-4">
                                        <div class="tw-text-sm tw-text-gray-600 tw-font-medium">Chủ tài khoản</div>
                                        <div class="tw-font-bold tw-text-gray-900 tw-text-sm">{{ $bankInfo['account_name'] }}</div>
                                    </div>
                                </div>

                                <!-- Account Number with Copy -->
                                <div class="tw-bg-gray-50 tw-rounded-xl tw-p-3 tw-border tw-border-gray-200">
                                    <div class="tw-grid tw-grid-cols-2 tw-gap-4 tw-items-center">
                                        <div class="tw-text-sm tw-text-gray-600 tw-font-medium">Số tài khoản</div>
                                        <div class="tw-flex tw-items-center tw-justify-between tw-gap-2">
                                            <div class="tw-font-bold tw-text-gray-900 tw-text-sm tw-font-mono">{{ $bankInfo['account_number'] }}</div>
                                            <button onclick="copyToClipboard('{{ $bankInfo['account_number'] }}', 'Đã copy số tài khoản')"
                                                    class="tw-flex-shrink-0 tw-p-1.5 tw-rounded-lg tw-bg-blue-100 hover:tw-bg-blue-200 tw-text-blue-600 tw-transition">
                                                <svg class="tw-w-4 tw-h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Transfer Content - IMPORTANT with Copy -->
                                <div class="tw-bg-gradient-to-r tw-from-red-50 tw-to-orange-50 tw-border-2 tw-border-red-300 tw-rounded-xl tw-p-3">
                                    <div class="tw-grid tw-grid-cols-2 tw-gap-4 tw-items-center">
                                        <div class="tw-text-sm tw-text-red-700 tw-font-medium">Nội dung chuyển khoản</div>
                                         <div class="tw-flex tw-items-center tw-justify-between tw-gap-2">
                                            <div class="tw-font-bold tw-text-red-900 tw-text-sm tw-font-mono">{{ $bankInfo['content'] }}</div>
                                            <button onclick="copyToClipboard('{{ $bankInfo['content'] }}', 'Đã copy nội dung chuyển khoản')"
                                                    class="tw-flex-shrink-0 tw-p-1.5 tw-rounded-lg tw-bg-red-100 hover:tw-bg-red-200 tw-text-red-600 tw-transition">
                                                <svg class="tw-w-4 tw-h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                    <p class="tw-text-xs tw-text-red-700 tw-mt-2">
                                        <strong>⚠️ Lưu ý:</strong> Giữ nguyên nội dung này, không thay đổi!
                                    </p>
                                </div>

                                <!-- Amount with Copy -->
                                <div class="tw-bg-gradient-to-r tw-from-emerald-50 tw-to-emerald-100 tw-border-2 tw-border-emerald-300 tw-rounded-xl tw-p-3">
                                    <div class="tw-grid tw-grid-cols-2 tw-gap-4 tw-items-center">
                                        <div class="tw-text-sm tw-text-emerald-700 tw-font-medium">Số tiền cần chuyển</div>
                                        <div class="tw-flex tw-items-center tw-justify-between tw-gap-2">
                                            <div class="tw-font-bold tw-text-emerald-700 tw-text-base">{{ number_format($bankInfo['amount']) }} VNĐ</div>
                                            <button onclick="copyToClipboard('{{ $bankInfo['amount'] }}', 'Đã copy số tiền')"
                                                    class="tw-flex-shrink-0 tw-p-1.5 tw-rounded-lg tw-bg-emerald-100 hover:tw-bg-emerald-200 tw-text-emerald-600 tw-transition">
                                                <svg class="tw-w-4 tw-h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Status Bar at Bottom with Loading -->
                    <div data-payment-status-bar class="tw-bg-white tw-rounded-2xl tw-shadow-lg tw-overflow-hidden tw-transition-opacity tw-duration-300">
                        <div class="tw-bg-gradient-to-r tw-from-emerald-500 tw-to-emerald-600 tw-px-6 tw-py-4">
                            <div class="tw-flex tw-items-center tw-justify-between tw-text-white">
                                <div class="tw-flex tw-items-center tw-gap-3">
                                    <div class="tw-relative tw-w-8 tw-h-8">
                                        <svg class="tw-animate-spin tw-w-8 tw-h-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="tw-opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="tw-opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="tw-font-bold tw-text-lg">Trạng thái: Chờ thanh toán</div>
                                        <div class="tw-text-sm tw-text-emerald-100">
                                            Hệ thống đang tự động kiểm tra thanh toán
                                            <span data-payment-time-container class="tw-hidden">
                                                • Đã chờ: <strong data-payment-time-elapsed></strong>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="tw-flex tw-gap-1">
                                    <span class="tw-w-2 tw-h-2 tw-bg-white tw-rounded-full pulse-dot"></span>
                                    <span class="tw-w-2 tw-h-2 tw-bg-white tw-rounded-full pulse-dot"></span>
                                    <span class="tw-w-2 tw-h-2 tw-bg-white tw-rounded-full pulse-dot"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Progress Bar -->
                        <div class="tw-h-1 tw-bg-emerald-200 tw-overflow-hidden">
                            <div class="tw-h-full tw-bg-white shimmer"></div>
                        </div>

                        <!-- Important Notes -->
                        <div class="tw-p-6 tw-bg-gradient-to-br tw-from-gray-50 tw-to-white">
                            <div class="tw-flex tw-items-start tw-gap-3">
                                <svg class="tw-flex-shrink-0 tw-w-5 tw-h-5 tw-text-amber-600 tw-mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div class="tw-flex-1">
                                    <h4 class="tw-font-bold tw-text-gray-900 tw-mb-2">Lưu ý quan trọng</h4>
                                    <ul class="tw-text-sm tw-text-gray-700 tw-space-y-1">
                                        <li class="tw-flex tw-items-start tw-gap-2">
                                            <span class="tw-text-emerald-600 tw-mt-0.5">•</span>
                                            <span>Gói dịch vụ sẽ <strong class="tw-text-emerald-700">tự động kích hoạt</strong> sau khi nhận được thanh toán (1-5 phút)</span>
                                        </li>
                                        <li class="tw-flex tw-items-start tw-gap-2">
                                            <span class="tw-text-red-600 tw-mt-0.5">•</span>
                                            <span>Không thay đổi nội dung chuyển khoản</span>
                                        </li>
                                        <li class="tw-flex tw-items-start tw-gap-2">
                                            <span class="tw-text-red-600 tw-mt-0.5">•</span>
                                            <span>Chuyển đúng số tiền được yêu cầu</span>
                                        </li>
                                        <li class="tw-flex tw-items-start tw-gap-2">
                                            <span class="tw-text-amber-600 tw-mt-0.5">•</span>
                                            <span>Nếu sau 10 phút vẫn chưa kích hoạt, vui lòng liên hệ support</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Completed Payment View -->
            <div data-payment-completed class="{{ $payment->status === 'pending' ? 'tw-hidden' : '' }}">
                <div class="tw-flex tw-items-center tw-justify-center tw-min-h-[500px]">
                    <div class="tw-bg-white tw-rounded-2xl tw-shadow-xl tw-p-12 tw-text-center tw-max-w-md slide-up">
                        <div class="tw-w-20 tw-h-20 tw-bg-emerald-100 tw-rounded-full tw-flex tw-items-center tw-justify-center tw-mx-auto tw-mb-6 pulse-ring">
                            <svg class="tw-w-10 tw-h-10 tw-text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <h2 class="tw-text-3xl tw-font-bold tw-text-gray-900 tw-mb-3">Thanh toán thành công!</h2>
                        <p class="tw-text-gray-600 tw-mb-6 tw-text-lg">Gói dịch vụ của bạn đã được kích hoạt</p>
                        <a href="{{ route('brands.subscription.show', $brand) }}"
                            class="tw-inline-flex tw-items-center tw-gap-2 tw-px-8 tw-py-4 tw-bg-gradient-to-r tw-from-emerald-500 tw-to-emerald-600 tw-text-white tw-rounded-xl tw-font-semibold hover:tw-from-emerald-600 hover:tw-to-emerald-700 tw-transition tw-shadow-lg hover:tw-shadow-xl tw-transform hover:tw-scale-105">
                            <span>Xem gói dịch vụ</span>
                            <svg class="tw-w-5 tw-h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Back Link -->
            <div class="tw-mt-8 tw-text-center">
                <a href="{{ route('brands.subscription.show', $brand) }}"
                    class="tw-inline-flex tw-items-center tw-gap-2 tw-text-gray-600 hover:tw-text-gray-900 tw-transition tw-font-medium">
                    <svg class="tw-w-5 tw-h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Quay lại trang gói dịch vụ
                </a>
            </div>
        </main>
    </div>
</x-app-layout>
