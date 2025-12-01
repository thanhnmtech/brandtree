<x-app-layout>
    <x-slot name="header">
        <div class="tw-flex tw-items-center tw-justify-between">
            <div class="tw-flex tw-items-center tw-gap-4">
                <a href="{{ route('brands.index') }}" class="tw-text-gray-500 hover:tw-text-gray-700 tw-transition">
                    <svg class="tw-w-6 tw-h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </a>
                <div>
                    <h2 class="tw-text-2xl md:tw-text-3xl tw-font-bold tw-text-gray-800">Lịch sử Credit</h2>
                    <p class="tw-text-sm tw-text-gray-500">{{ $brand->name }}</p>
                </div>
            </div>
            <div class="tw-flex tw-items-center tw-gap-2">
                <select onchange="window.location.href=this.value" class="tw-px-3 tw-py-2 tw-border tw-border-gray-300 tw-rounded-lg tw-text-sm">
                    <option value="{{ route('brands.credits.index', ['brand' => $brand, 'period' => 'week']) }}" {{ $period === 'week' ? 'selected' : '' }}>Tuần này</option>
                    <option value="{{ route('brands.credits.index', ['brand' => $brand, 'period' => 'month']) }}" {{ $period === 'month' ? 'selected' : '' }}>Tháng này</option>
                    <option value="{{ route('brands.credits.index', ['brand' => $brand, 'period' => 'year']) }}" {{ $period === 'year' ? 'selected' : '' }}>Năm nay</option>
                    <option value="{{ route('brands.credits.index', ['brand' => $brand, 'period' => 'all']) }}" {{ $period === 'all' ? 'selected' : '' }}>Tất cả</option>
                </select>
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
            <!-- Stats Cards -->
    <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-4 tw-gap-4 tw-mb-6">
        <div class="tw-bg-white tw-rounded-xl tw-shadow-sm tw-p-4">
            <p class="tw-text-sm tw-text-gray-500 tw-mb-1">Credits còn lại</p>
            <p class="tw-text-2xl tw-font-bold tw-text-[#16a249]">{{ $subscription ? number_format($subscription->credits_remaining) : 0 }}</p>
        </div>
        <div class="tw-bg-white tw-rounded-xl tw-shadow-sm tw-p-4">
            <p class="tw-text-sm tw-text-gray-500 tw-mb-1">Đã dùng tháng này</p>
            <p class="tw-text-2xl tw-font-bold tw-text-gray-800">{{ number_format($stats['total_used']) }}</p>
        </div>
        <div class="tw-bg-white tw-rounded-xl tw-shadow-sm tw-p-4">
            <p class="tw-text-sm tw-text-gray-500 tw-mb-1">Tổng credits/tháng</p>
            <p class="tw-text-2xl tw-font-bold tw-text-gray-800">{{ $subscription ? number_format($subscription->plan->credits) : 0 }}</p>
        </div>
        <div class="tw-bg-white tw-rounded-xl tw-shadow-sm tw-p-4">
            <p class="tw-text-sm tw-text-gray-500 tw-mb-1">Reset tiếp theo</p>
            <p class="tw-text-lg tw-font-semibold tw-text-gray-800">{{ $subscription?->credits_reset_at?->addMonth()->format('d/m/Y') ?? '-' }}</p>
        </div>
    </div>

    <!-- Chart and Action Stats -->
    <div class=" tw-gap-6 tw-mb-6">
        <!-- Daily Usage Chart -->
        <div class="lg:tw-col-span-1 tw-bg-white tw-rounded-xl tw-shadow-sm tw-p-6">
            <h3 class="tw-font-semibold tw-text-gray-800 tw-mb-4">Biểu đồ sử dụng</h3>
            <div class="tw-h-64">
                <canvas id="usageChart"></canvas>
            </div>
        </div>

        <!-- Usage by Action -->
        {{-- <div class="tw-bg-white tw-rounded-xl tw-shadow-sm tw-p-6">
            <h3 class="tw-font-semibold tw-text-gray-800 tw-mb-4">Theo loại hành động</h3>
            @if(count($stats['by_action']) > 0)
                <div class="tw-space-y-3">
                    @foreach($stats['by_action'] as $action => $total)
                        <div class="tw-flex tw-justify-between tw-items-center">
                            <span class="tw-text-sm tw-text-gray-600">{{ ucfirst(str_replace('_', ' ', $action)) }}</span>
                            <span class="tw-font-semibold tw-text-gray-800">{{ number_format($total) }}</span>
                        </div>
                        <div class="tw-w-full tw-bg-gray-200 tw-rounded-full tw-h-2">
                            <div class="tw-bg-[#16a249] tw-h-2 tw-rounded-full" style="width: {{ $stats['total_used'] > 0 ? ($total / $stats['total_used'] * 100) : 0 }}%"></div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="tw-text-gray-500 tw-text-center tw-py-4">Chưa có dữ liệu</p>
            @endif
        </div> --}}
    </div>

    <!-- Usage History Table -->
    <div class="tw-bg-white tw-rounded-xl tw-shadow-sm tw-overflow-hidden">
        <div class="tw-px-6 tw-py-4 tw-border-b tw-border-gray-100">
            <h3 class="tw-font-semibold tw-text-gray-800">Lịch sử chi tiết</h3>
        </div>

        @if($usages->count() > 0)
            <div class="tw-overflow-x-auto">
                <table class="tw-w-full">
                    <thead class="tw-bg-gray-50">
                        <tr>
                            <th class="tw-px-6 tw-py-3 tw-text-left tw-text-xs tw-font-medium tw-text-gray-500 tw-uppercase">Thời gian</th>
                            <th class="tw-px-6 tw-py-3 tw-text-left tw-text-xs tw-font-medium tw-text-gray-500 tw-uppercase">Người dùng</th>
                            <th class="tw-px-6 tw-py-3 tw-text-left tw-text-xs tw-font-medium tw-text-gray-500 tw-uppercase">Loại</th>
                            <th class="tw-px-6 tw-py-3 tw-text-left tw-text-xs tw-font-medium tw-text-gray-500 tw-uppercase">Model</th>
                            <th class="tw-px-6 tw-py-3 tw-text-right tw-text-xs tw-font-medium tw-text-gray-500 tw-uppercase">Credits</th>
                        </tr>
                    </thead>
                    <tbody class="tw-divide-y tw-divide-gray-100">
                        @foreach($usages as $usage)
                            <tr>
                                <td class="tw-px-6 tw-py-4 tw-text-sm tw-text-gray-600">{{ $usage->created_at->format('d/m/Y H:i') }}</td>
                                <td class="tw-px-6 tw-py-4 tw-text-sm tw-text-gray-800">{{ $usage->user->name }}</td>
                                <td class="tw-px-6 tw-py-4">
                                    <span class="tw-px-2 tw-py-1 tw-text-xs tw-rounded-full {{ $usage->amount > 0 ? 'tw-bg-red-100 tw-text-red-700' : 'tw-bg-green-100 tw-text-green-700' }}">
                                        {{ ucfirst(str_replace('_', ' ', $usage->action_type)) }}
                                    </span>
                                </td>
                                <td class="tw-px-6 tw-py-4 tw-text-sm tw-text-gray-600">{{ $usage->model_used ?? '-' }}</td>
                                <td class="tw-px-6 tw-py-4 tw-text-sm tw-text-right tw-font-medium {{ $usage->amount > 0 ? 'tw-text-red-600' : 'tw-text-green-600' }}">
                                    {{ $usage->amount > 0 ? '-' : '+' }}{{ number_format(abs($usage->amount)) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="tw-px-6 tw-py-4 tw-border-t tw-border-gray-100">
                {{ $usages->links() }}
            </div>
        @else
            <div class="tw-px-6 tw-py-12 tw-text-center tw-text-gray-500">
                Chưa có lịch sử sử dụng credits.
            </div>
        @endif
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('usageChart');
        if (ctx) {
            const dailyData = @json($dailyUsage);
            const labels = Object.keys(dailyData).map(date => {
                const d = new Date(date);
                return d.getDate() + '/' + (d.getMonth() + 1);
            });
            const values = Object.values(dailyData);

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Credits sử dụng',
                        data: values,
                        backgroundColor: 'rgba(22, 162, 73, 0.5)',
                        borderColor: 'rgba(22, 162, 73, 1)',
                        borderWidth: 1,
                        borderRadius: 4,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { precision: 0 }
                        }
                    }
                }
            });
        }
    </script>
    @endpush
        </div>
    </div>
</x-app-layout>
