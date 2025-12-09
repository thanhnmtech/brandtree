<x-app-layout>
    <style>
        [x-cloak] { display: none !important; }

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

    <div x-data="{
        paymentStatus: '{{ $payment->status }}',
        isChecking: false,
        pollingInterval: null,
        checkCount: 0,
        maxChecks: 60,
        timeElapsed: 0,

        async checkPaymentStatus() {
            if (this.paymentStatus !== 'pending') return;

            this.isChecking = true;
            try {
                const response = await fetch('{{ route('brands.payments.status', [$brand, $payment]) }}', {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                const data = await response.json();

                if (data.completed) {
                    this.paymentStatus = data.status;
                    this.stopPolling();

                    if (window.showNotification) {
                        window.showNotification(data.message, 'success');
                    }

                    setTimeout(() => {
                        window.location.href = data.redirect;
                    }, 2000);
                }
            } catch (error) {
                console.error('Error checking payment status:', error);
            } finally {
                setTimeout(() => {
                    this.isChecking = false;
                }, 500);
            }
        },

        startPolling() {
            // Prevent multiple intervals
            if (this.pollingInterval) {
                return;
            }

            this.checkPaymentStatus();

            this.pollingInterval = setInterval(() => {
                this.checkCount++;
                this.timeElapsed = this.checkCount * 5;

                if (this.checkCount >= this.maxChecks) {
                    this.stopPolling();
                    return;
                }

                this.checkPaymentStatus();
            }, 5000);
        },

        stopPolling() {
            if (this.pollingInterval) {
                clearInterval(this.pollingInterval);
                this.pollingInterval = null;
            }
        },

        formatTime(seconds) {
            const mins = Math.floor(seconds / 60);
            const secs = seconds % 60;
            return mins > 0 ? `${mins}m ${secs}s` : `${secs}s`;
        },

        init() {
            if (this.paymentStatus === 'pending') {
                this.startPolling();
            }

            document.addEventListener('visibilitychange', () => {
                if (document.hidden) {
                    this.stopPolling();
                } else if (this.paymentStatus === 'pending') {
                    this.checkCount = 0;
                    this.timeElapsed = 0;
                    this.startPolling();
                }
            });

            window.addEventListener('beforeunload', () => {
                this.stopPolling();
            });
        }
    }" x-init="init()">
        <main class="tw-flex tw-flex-col md:tw-rounded-[10px] md:tw-bg-[#F3F7F5] md:tw-mt-[36px] md:tw-mx-[71px] tw-p-4 md:tw-p-10">
            <!-- Header -->
            <x-breadcrumb :items="[
                ['label' => 'Trang chủ', 'url' => route('dashboard')],
                ['label' => $brand->name, 'url' => route('brands.show', $brand)],
                ['label' => 'Thanh toán', 'url' => route('brands.payments.show', [$brand, $payment])],
            ]" />

            <!-- Pending Payment View -->
            <template x-if="paymentStatus === 'pending'">
                <div class="slide-up">
                    <!-- Auto-checking Status Bar - LUÔN HIỂN THỊ -->
                    <div class="tw-bg-gradient-to-r tw-from-emerald-50 tw-to-blue-50 tw-border-2 tw-border-emerald-200 tw-rounded-2xl tw-p-5 tw-mb-6">
                        <div class="tw-flex tw-items-center tw-gap-4">
                            <div class="tw-relative">
                                <svg class="tw-animate-spin tw-h-10 tw-w-10 tw-text-emerald-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="tw-opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="tw-opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </div>
                            <div class="tw-flex-1">
                                <div class="tw-flex tw-items-center tw-gap-2 tw-mb-1">
                                    <h3 class="tw-text-lg tw-font-bold tw-text-gray-800">Đang tự động kiểm tra thanh toán</h3>
                                    <span class="tw-flex tw-gap-1">
                                        <span class="tw-w-2 tw-h-2 tw-bg-emerald-600 tw-rounded-full pulse-dot" style="animation: pulse-dot 1.4s ease-in-out infinite;"></span>
                                        <span class="tw-w-2 tw-h-2 tw-bg-emerald-600 tw-rounded-full pulse-dot" style="animation: pulse-dot 1.4s ease-in-out infinite;"></span>
                                        <span class="tw-w-2 tw-h-2 tw-bg-emerald-600 tw-rounded-full pulse-dot" style="animation: pulse-dot 1.4s ease-in-out infinite;"></span>
                                    </span>
                                </div>
                                <p class="tw-text-sm tw-text-gray-700">
                                    Hệ thống kiểm tra mỗi <strong class="tw-text-emerald-700">5 giây</strong>
                                    <span x-show="timeElapsed > 0" x-cloak>
                                        • Đã chờ: <strong class="tw-text-emerald-700" x-text="formatTime(timeElapsed)"></strong>
                                    </span>
                                </p>
                                <div class="tw-mt-2 tw-bg-white/60 tw-rounded-full tw-h-2 tw-overflow-hidden">
                                    <div class="tw-h-full tw-bg-gradient-to-r tw-from-emerald-500 tw-to-blue-500 shimmer"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tw-grid tw-grid-cols-1 lg:tw-grid-cols-2 tw-gap-6">
                        <!-- Left: QR Code & Bank Info -->
                        @if ($bankInfo)
                            <div class="tw-bg-white tw-rounded-2xl tw-shadow-lg tw-overflow-hidden">
                                <!-- QR Code Section -->
                                <div class="tw-bg-gradient-to-br tw-from-gray-50 tw-to-gray-100 tw-p-6 tw-border-b tw-border-gray-200">
                                    <h3 class="tw-text-lg tw-font-bold tw-text-gray-800 tw-mb-4 tw-text-center">
                                        <svg class="tw-inline-block tw-w-5 tw-h-5 tw-mr-2 tw-text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                                        </svg>
                                        Quét mã QR để thanh toán
                                    </h3>
                                    <div class="tw-flex tw-justify-center tw-mb-3">
                                        <div class="tw-bg-white tw-p-4 tw-rounded-2xl tw-shadow-xl">
                                            <img src="{{ $bankInfo['qr_url'] }}" alt="QR Code" class="tw-w-64 tw-h-64">
                                        </div>
                                    </div>
                                    <p class="tw-text-center tw-text-sm tw-text-gray-600 tw-font-medium">
                                        Sử dụng app ngân hàng để quét mã
                                    </p>
                                </div>

                                <!-- Bank Details -->
                                <div class="tw-p-6">
                                    <h4 class="tw-font-bold tw-text-gray-800 tw-mb-4 tw-flex tw-items-center tw-gap-2">
                                        <svg class="tw-w-5 tw-h-5 tw-text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                        </svg>
                                        Hoặc chuyển khoản thủ công
                                    </h4>
                                    <div class="tw-space-y-4">
                                        <div class="tw-bg-gray-50 tw-rounded-xl tw-p-4">
                                            <div class="tw-text-xs tw-text-gray-500 tw-mb-1">Ngân hàng</div>
                                            <div class="tw-font-bold tw-text-gray-900 tw-text-lg">{{ $bankInfo['bank_name'] }}</div>
                                        </div>
                                        <div class="tw-bg-gray-50 tw-rounded-xl tw-p-4">
                                            <div class="tw-text-xs tw-text-gray-500 tw-mb-1">Số tài khoản</div>
                                            <div class="tw-font-bold tw-text-gray-900 tw-text-lg tw-font-mono">{{ $bankInfo['account_number'] }}</div>
                                        </div>
                                        <div class="tw-bg-gray-50 tw-rounded-xl tw-p-4">
                                            <div class="tw-text-xs tw-text-gray-500 tw-mb-1">Chủ tài khoản</div>
                                            <div class="tw-font-bold tw-text-gray-900">{{ $bankInfo['account_name'] }}</div>
                                        </div>
                                        <div class="tw-bg-gradient-to-r tw-from-blue-50 tw-to-emerald-50 tw-border-2 tw-border-blue-200 tw-rounded-xl tw-p-4">
                                            <div class="tw-text-xs tw-text-blue-700 tw-mb-1 tw-font-semibold tw-flex tw-items-center tw-gap-1">
                                                <svg class="tw-w-4 tw-h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                Nội dung chuyển khoản (BẮT BUỘC)
                                            </div>
                                            <div class="tw-font-bold tw-text-blue-900 tw-text-xl tw-font-mono tw-tracking-wider">{{ $bankInfo['content'] }}</div>
                                        </div>
                                        <div class="tw-bg-emerald-50 tw-rounded-xl tw-p-4">
                                            <div class="tw-text-xs tw-text-emerald-700 tw-mb-1">Số tiền cần chuyển</div>
                                            <div class="tw-font-bold tw-text-emerald-700 tw-text-2xl">{{ number_format($bankInfo['amount']) }} VNĐ</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Right: Instructions & Payment Info -->
                        <div class="tw-space-y-6">
                            <!-- Instructions -->
                            <div class="tw-bg-white tw-rounded-2xl tw-shadow-lg tw-p-6">
                                <h3 class="tw-text-lg tw-font-bold tw-text-gray-800 tw-mb-4 tw-flex tw-items-center tw-gap-2">
                                    <svg class="tw-w-5 tw-h-5 tw-text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    Hướng dẫn thanh toán
                                </h3>
                                <ol class="tw-space-y-3 tw-text-sm">
                                    <li class="tw-flex tw-gap-3">
                                        <span class="tw-flex-shrink-0 tw-w-6 tw-h-6 tw-bg-blue-100 tw-text-blue-700 tw-rounded-full tw-flex tw-items-center tw-justify-center tw-font-bold tw-text-xs">1</span>
                                        <span class="tw-text-gray-700">Mở ứng dụng ngân hàng và quét mã QR hoặc nhập thông tin thủ công</span>
                                    </li>
                                    <li class="tw-flex tw-gap-3">
                                        <span class="tw-flex-shrink-0 tw-w-6 tw-h-6 tw-bg-blue-100 tw-text-blue-700 tw-rounded-full tw-flex tw-items-center tw-justify-center tw-font-bold tw-text-xs">2</span>
                                        <span class="tw-text-gray-700"><strong class="tw-text-red-600">Quan trọng:</strong> Giữ nguyên nội dung chuyển khoản <strong class="tw-font-mono tw-text-blue-700">{{ $bankInfo['content'] ?? '' }}</strong></span>
                                    </li>
                                    <li class="tw-flex tw-gap-3">
                                        <span class="tw-flex-shrink-0 tw-w-6 tw-h-6 tw-bg-blue-100 tw-text-blue-700 tw-rounded-full tw-flex tw-items-center tw-justify-center tw-font-bold tw-text-xs">3</span>
                                        <span class="tw-text-gray-700">Xác nhận và hoàn tất giao dịch</span>
                                    </li>
                                    <li class="tw-flex tw-gap-3">
                                        <span class="tw-flex-shrink-0 tw-w-6 tw-h-6 tw-bg-emerald-100 tw-text-emerald-700 tw-rounded-full tw-flex tw-items-center tw-justify-center tw-font-bold tw-text-xs">4</span>
                                        <span class="tw-text-gray-700">Hệ thống sẽ <strong class="tw-text-emerald-700">tự động xác nhận</strong> và kích hoạt gói dịch vụ sau khi nhận được thanh toán (thường trong 1-5 phút)</span>
                                    </li>
                                </ol>
                            </div>

                            <!-- Payment Details -->
                            <div class="tw-bg-white tw-rounded-2xl tw-shadow-lg tw-p-6">
                                <h3 class="tw-text-lg tw-font-bold tw-text-gray-800 tw-mb-4 tw-flex tw-items-center tw-gap-2">
                                    <svg class="tw-w-5 tw-h-5 tw-text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                    </svg>
                                    Thông tin đơn hàng
                                </h3>
                                <div class="tw-space-y-3 tw-text-sm">
                                    <div class="tw-flex tw-justify-between tw-py-2 tw-border-b tw-border-gray-100">
                                        <span class="tw-text-gray-600">Gói dịch vụ</span>
                                        <span class="tw-font-bold tw-text-gray-900">{{ $payment->subscription->plan->name }}</span>
                                    </div>
                                    <div class="tw-flex tw-justify-between tw-py-2 tw-border-b tw-border-gray-100">
                                        <span class="tw-text-gray-600">Thương hiệu</span>
                                        <span class="tw-font-medium tw-text-gray-900">{{ $brand->name }}</span>
                                    </div>
                                    <div class="tw-flex tw-justify-between tw-py-2 tw-border-b tw-border-gray-100">
                                        <span class="tw-text-gray-600">Mã giao dịch</span>
                                        <span class="tw-font-mono tw-font-medium tw-text-gray-900">{{ $payment->transaction_id }}</span>
                                    </div>
                                    <div class="tw-flex tw-justify-between tw-py-2 tw-border-b tw-border-gray-100">
                                        <span class="tw-text-gray-600">Ngày tạo</span>
                                        <span class="tw-font-medium tw-text-gray-900">{{ $payment->created_at->format('d/m/Y H:i') }}</span>
                                    </div>
                                    <div class="tw-flex tw-justify-between tw-py-3 tw-bg-emerald-50 tw-rounded-lg tw-px-3 tw-mt-2">
                                        <span class="tw-text-emerald-700 tw-font-semibold">Tổng thanh toán</span>
                                        <span class="tw-font-bold tw-text-2xl tw-text-emerald-700">{{ $payment->formatted_amount }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Important Notes -->
                            <div class="tw-bg-amber-50 tw-border-2 tw-border-amber-200 tw-rounded-2xl tw-p-5">
                                <div class="tw-flex tw-gap-3">
                                    <svg class="tw-flex-shrink-0 tw-w-6 tw-h-6 tw-text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                    </svg>
                                    <div>
                                        <h4 class="tw-font-bold tw-text-amber-900 tw-mb-2">Lưu ý quan trọng</h4>
                                        <ul class="tw-text-sm tw-text-amber-800 tw-space-y-1">
                                            <li>• Không thay đổi nội dung chuyển khoản</li>
                                            <li>• Chuyển đúng số tiền được yêu cầu</li>
                                            <li>• Gói dịch vụ sẽ tự động kích hoạt sau khi thanh toán thành công</li>
                                            <li>• Nếu sau 10 phút vẫn chưa kích hoạt, vui lòng liên hệ support</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </template>

            <!-- Completed Payment View -->
            <template x-if="paymentStatus === 'completed'">
                <div class="tw-flex tw-items-center tw-justify-center tw-min-h-[500px]">
                    <div class="tw-bg-white tw-rounded-2xl tw-shadow-xl tw-p-12 tw-text-center tw-max-w-md slide-up">
                        <div class="tw-w-20 tw-h-20 tw-bg-emerald-100 tw-rounded-full tw-flex tw-items-center tw-justify-center tw-mx-auto tw-mb-6">
                            <svg class="tw-w-10 tw-h-10 tw-text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <h2 class="tw-text-2xl tw-font-bold tw-text-gray-800 tw-mb-3">Thanh toán thành công!</h2>
                        <p class="tw-text-gray-600 tw-mb-6">Gói dịch vụ của bạn đã được kích hoạt</p>
                        <a href="{{ route('brands.subscription.show', $brand) }}"
                            class="tw-inline-flex tw-items-center tw-gap-2 tw-px-6 tw-py-3 tw-bg-gradient-to-r tw-from-emerald-500 tw-to-emerald-600 tw-text-white tw-rounded-xl tw-font-medium hover:tw-from-emerald-600 hover:tw-to-emerald-700 tw-transition tw-shadow-lg">
                            <svg class="tw-w-5 tw-h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                            Xem gói dịch vụ
                        </a>
                    </div>
                </div>
            </template>

            <!-- Back Link -->
            <div class="tw-mt-8 tw-text-center">
                <a href="{{ route('brands.subscription.show', $brand) }}"
                    class="tw-inline-flex tw-items-center tw-gap-2 tw-text-gray-600 hover:tw-text-gray-800 tw-transition">
                    <svg class="tw-w-5 tw-h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Quay lại trang gói dịch vụ
                </a>
            </div>
        </main>
    </div>
</x-app-layout>
