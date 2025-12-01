<x-app-layout>
    <x-slot name="header">
        <div class="tw-flex tw-items-center tw-gap-4">
            <a href="{{ route('brands.index') }}" class="tw-text-gray-500 hover:tw-text-gray-700 tw-transition">
                <svg class="tw-w-6 tw-h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
            <div>
                <h2 class="tw-text-2xl md:tw-text-3xl tw-font-bold tw-text-gray-800">Lịch sử thanh toán</h2>
                <p class="tw-text-sm tw-text-gray-500">{{ $brand->name }}</p>
            </div>
        </div>
    </x-slot>

    <div class="tw-flex tw-flex-col lg:tw-flex-row tw-gap-6">
        <!-- Sidebar -->
        <div class="lg:tw-w-64 tw-flex-shrink-0">
            @include('brands.partials.sidebar', ['brand' => $brand])
        </div>

        <!-- Main Content -->
        <div class="tw-flex-1 tw-min-w-0">
            <!-- Payments Table -->
    <div class="tw-bg-white tw-rounded-xl tw-shadow-sm tw-overflow-hidden">
        @if($payments->count() > 0)
            <div class="tw-overflow-x-auto">
                <table class="tw-w-full">
                    <thead class="tw-bg-gray-50">
                        <tr>
                            <th class="tw-px-6 tw-py-3 tw-text-left tw-text-xs tw-font-medium tw-text-gray-500 tw-uppercase">Mã GD</th>
                            <th class="tw-px-6 tw-py-3 tw-text-left tw-text-xs tw-font-medium tw-text-gray-500 tw-uppercase">Gói dịch vụ</th>
                            <th class="tw-px-6 tw-py-3 tw-text-right tw-text-xs tw-font-medium tw-text-gray-500 tw-uppercase">Số tiền</th>
                            <th class="tw-px-6 tw-py-3 tw-text-center tw-text-xs tw-font-medium tw-text-gray-500 tw-uppercase">Trạng thái</th>
                            <th class="tw-px-6 tw-py-3 tw-text-left tw-text-xs tw-font-medium tw-text-gray-500 tw-uppercase">Ngày tạo</th>
                            <th class="tw-px-6 tw-py-3 tw-text-left tw-text-xs tw-font-medium tw-text-gray-500 tw-uppercase">Ngày TT</th>
                            <th class="tw-px-6 tw-py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="tw-divide-y tw-divide-gray-100">
                        @foreach($payments as $payment)
                            <tr>
                                <td class="tw-px-6 tw-py-4 tw-text-sm tw-font-mono tw-text-gray-600">{{ $payment->transaction_id }}</td>
                                <td class="tw-px-6 tw-py-4 tw-text-sm tw-text-gray-800">{{ $payment->subscription->plan->name ?? '-' }}</td>
                                <td class="tw-px-6 tw-py-4 tw-text-sm tw-text-right tw-font-medium tw-text-gray-800">{{ $payment->formatted_amount }}</td>
                                <td class="tw-px-6 tw-py-4 tw-text-center">
                                    @if($payment->status === 'completed')
                                        <span class="tw-px-2 tw-py-1 tw-text-xs tw-rounded-full tw-bg-green-100 tw-text-green-700">Đã thanh toán</span>
                                    @elseif($payment->status === 'pending')
                                        <span class="tw-px-2 tw-py-1 tw-text-xs tw-rounded-full tw-bg-yellow-100 tw-text-yellow-700">Chờ thanh toán</span>
                                    @else
                                        <span class="tw-px-2 tw-py-1 tw-text-xs tw-rounded-full tw-bg-red-100 tw-text-red-700">Thất bại</span>
                                    @endif
                                </td>
                                <td class="tw-px-6 tw-py-4 tw-text-sm tw-text-gray-600">{{ $payment->created_at->format('d/m/Y H:i') }}</td>
                                <td class="tw-px-6 tw-py-4 tw-text-sm tw-text-gray-600">{{ $payment->paid_at?->format('d/m/Y H:i') ?? '-' }}</td>
                                <td class="tw-px-6 tw-py-4 tw-text-right">
                                    <a href="{{ route('brands.payments.show', [$brand, $payment]) }}" class="tw-text-[#16a249] hover:tw-underline tw-text-sm">Chi tiết</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="tw-px-6 tw-py-4 tw-border-t tw-border-gray-100">
                {{ $payments->links() }}
            </div>
        @else
            <div class="tw-px-6 tw-py-12 tw-text-center tw-text-gray-500">
                Chưa có lịch sử thanh toán.
            </div>
        @endif
    </div>
        </div>
    </div>
</x-app-layout>
