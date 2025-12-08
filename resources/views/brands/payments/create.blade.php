<x-app-layout>
    <main class="tw-flex tw-flex-col md:tw-rounded-[10px] md:tw-bg-[#F3F7F5] md:tw-mt-[36px] md:tw-mx-[71px] tw-p-10">
        <!-- Header -->
        <div class="tw-mb-6">
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
                        <a href="{{ route('brands.show', $brand) }}" class="tw-text-gray-500 hover:tw-text-gray-700">{{ $brand->name }}</a>
                    </li>
                    <li class="tw-flex tw-items-center">
                        <svg class="tw-w-4 tw-h-4 tw-text-gray-400 tw-mx-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                clip-rule="evenodd"></path>
                        </svg>
                        <span class="tw-text-gray-700">Thanh toán</span>
                    </li>
                </ol>
            </nav>
            <h1 class="tw-text-2xl tw-font-bold tw-text-gray-800">Thanh toán đăng ký gói</h1>
        </div>

        @if ($errors->any())
            <div
                class="tw-mb-6 tw-bg-red-100 tw-border tw-border-red-400 tw-text-red-700 tw-px-4 tw-py-3 tw-rounded-lg">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-gap-6">
            <!-- Order Summary -->
            <div class="tw-bg-white tw-rounded-2xl tw-shadow-sm tw-p-6">
                <h3 class="tw-text-lg tw-font-semibold tw-text-gray-800 tw-mb-4">Thông tin đơn hàng</h3>

                <div class="tw-border tw-border-gray-200 tw-rounded-xl tw-p-4 tw-mb-4">
                    <div class="tw-flex tw-justify-between tw-items-center tw-mb-3">
                        <span class="tw-font-medium tw-text-gray-800">{{ $subscription->plan->name }}</span>
                        {{-- <span class="tw-px-2 tw-py-1 tw-bg-yellow-100 tw-text-yellow-700 tw-rounded tw-text-xs">Chờ thanh toán</span> --}}
                    </div>
                    <div class="tw-space-y-2 tw-text-sm tw-text-gray-600">
                        <div class="tw-flex tw-justify-between">
                            <span>Credits</span>
                            <span>{{ number_format($subscription->plan->credits) }} credits/tháng</span>
                        </div>
                        <div class="tw-flex tw-justify-between">
                            <span>Thời hạn</span>
                            <span>{{ $subscription->plan->formatted_duration }}</span>
                        </div>
                    </div>
                </div>

                <div class="tw-border-t tw-border-gray-200 tw-pt-4">
                    <div class="tw-flex tw-justify-between tw-items-center tw-text-lg tw-font-bold">
                        <span class="tw-text-gray-800">Tổng cộng</span>
                        <span class="tw-text-[#16a249]">{{ $subscription->plan->formatted_price }}</span>
                    </div>
                </div>
            </div>

            <!-- Payment Form -->
            <div class="tw-bg-white tw-rounded-2xl tw-shadow-sm tw-p-6">
                <h3 class="tw-text-lg tw-font-semibold tw-text-gray-800 tw-mb-4">Phương thức thanh toán</h3>

                <form action="{{ route('brands.payments.store', $brand) }}" method="POST">
                    @csrf
                    <input type="hidden" name="subscription_id" value="{{ $subscription->id }}">

                    <!-- Payment Method Selection -->
                    <div class="tw-space-y-3 tw-mb-6">
                        <label
                            class="tw-flex tw-items-center tw-p-4 tw-border tw-border-[#16a249] tw-rounded-xl tw-bg-green-50 tw-cursor-pointer">
                            <input type="radio" name="payment_method" value="bank_transfer" checked
                                class="tw-w-4 tw-h-4 tw-text-[#16a249]">
                            <div class="tw-ml-3">
                                <span class="tw-font-medium tw-text-gray-800">Chuyển khoản ngân hàng</span>
                                {{-- <p class="tw-text-sm tw-text-gray-500">Quét QR để thanh toán</p> --}}
                            </div>
                        </label>
                    </div>

                    <button type="submit"
                        class="tw-w-full tw-py-3 tw-bg-[#16a249] tw-text-white tw-rounded-lg tw-font-medium hover:tw-bg-[#138a3e] tw-transition">
                        Tiến hành thanh toán
                    </button>
                </form>

                <p class="tw-text-xs tw-text-gray-500 tw-text-center tw-mt-4">
                    Bằng việc thanh toán, bạn đồng ý với <a href="#"
                        class="tw-text-[#16a249] hover:tw-underline">Điều khoản dịch vụ</a>
                </p>
            </div>
        </div>

        <div class="tw-mt-6 tw-text-center">
            <a href="{{ route('brands.subscription.show', $brand) }}" class="tw-text-gray-600 hover:tw-text-gray-800">←
                Quay lại chọn gói</a>
        </div>
    </main>
</x-app-layout>
