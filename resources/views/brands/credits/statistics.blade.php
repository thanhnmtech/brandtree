<x-app-layout>
    <!-- Header -->
    <div class="tw-mb-6">
        <nav class="tw-flex tw-mb-4" aria-label="Breadcrumb">
            <ol class="tw-inline-flex tw-items-center tw-space-x-1 md:tw-space-x-3">
                <li><a href="{{ route('dashboard') }}" class="tw-text-gray-500 hover:tw-text-gray-700">Dashboard</a></li>
                <li class="tw-flex tw-items-center">
                    <svg class="tw-w-4 tw-h-4 tw-text-gray-400 tw-mx-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                    <a href="{{ route('brands.show', $brand) }}" class="tw-text-gray-500 hover:tw-text-gray-700">{{ $brand->name }}</a>
                </li>
                <li class="tw-flex tw-items-center">
                    <svg class="tw-w-4 tw-h-4 tw-text-gray-400 tw-mx-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                    <span class="tw-text-gray-700">Thống kê Credit</span>
                </li>
            </ol>
        </nav>
        <div class="tw-flex tw-flex-wrap tw-justify-between tw-items-center tw-gap-4">
            <h1 class="tw-text-2xl tw-font-bold tw-text-gray-800">Thống kê sử dụng Credit</h1>
            <select onchange="window.location.href=this.value" class="tw-px-3 tw-py-2 tw-border tw-border-gray-300 tw-rounded-lg tw-text-sm">
                <option value="{{ route('brands.credits.statistics', ['brand' => $brand, 'period' => 'week']) }}" {{ $period === 'week' ? 'selected' : '' }}>Tuần này</option>
                <option value="{{ route('brands.credits.statistics', ['brand' => $brand, 'period' => 'month']) }}" {{ $period === 'month' ? 'selected' : '' }}>Tháng này</option>
                <option value="{{ route('brands.credits.statistics', ['brand' => $brand, 'period' => 'year']) }}" {{ $period === 'year' ? 'selected' : '' }}>Năm nay</option>
            </select>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-4 tw-gap-4 tw-mb-6">
        <div class="tw-bg-white tw-rounded-xl tw-shadow-sm tw-p-6">
            <p class="tw-text-sm tw-text-gray-500 tw-mb-1">Tổng đã sử dụng</p>
            <p class="tw-text-3xl tw-font-bold tw-text-gray-800">{{ number_format($stats['total_used']) }}</p>
        </div>
        <div class="tw-bg-white tw-rounded-xl tw-shadow-sm tw-p-6">
            <p class="tw-text-sm tw-text-gray-500 tw-mb-1">Credits còn lại</p>
            <p class="tw-text-3xl tw-font-bold tw-text-[#16a249]">{{ $subscription ? number_format($subscription->credits_remaining) : 0 }}</p>
        </div>
        <div class="tw-bg-white tw-rounded-xl tw-shadow-sm tw-p-6">
            <p class="tw-text-sm tw-text-gray-500 tw-mb-1">Hạn mức/tháng</p>
            <p class="tw-text-3xl tw-font-bold tw-text-gray-800">{{ $subscription ? number_format($subscription->plan->credits) : 0 }}</p>
        </div>
        <div class="tw-bg-white tw-rounded-xl tw-shadow-sm tw-p-6">
            <p class="tw-text-sm tw-text-gray-500 tw-mb-1">Tỷ lệ sử dụng</p>
            <p class="tw-text-3xl tw-font-bold tw-text-gray-800">{{ $subscription && $subscription->plan->credits > 0 ? round(($stats['total_used'] / $subscription->plan->credits) * 100) : 0 }}%</p>
        </div>
    </div>

    <div class="tw-grid tw-grid-cols-1 lg:tw-grid-cols-2 tw-gap-6 tw-mb-6">
        <!-- Daily Usage Chart -->
        <div class="tw-bg-white tw-rounded-xl tw-shadow-sm tw-p-6">
            <h3 class="tw-font-semibold tw-text-gray-800 tw-mb-4">Sử dụng theo ngày</h3>
            <div class="tw-h-72">
                <canvas id="dailyChart"></canvas>
            </div>
        </div>

        <!-- By Action Chart -->
        <div class="tw-bg-white tw-rounded-xl tw-shadow-sm tw-p-6">
            <h3 class="tw-font-semibold tw-text-gray-800 tw-mb-4">Theo loại hành động</h3>
            <div class="tw-h-72">
                <canvas id="actionChart"></canvas>
            </div>
        </div>
    </div>

    <!-- By Model Chart -->
    @if(count($stats['by_model']) > 0)
    <div class="tw-bg-white tw-rounded-xl tw-shadow-sm tw-p-6 tw-mb-6">
        <h3 class="tw-font-semibold tw-text-gray-800 tw-mb-4">Theo Model AI</h3>
        <div class="tw-h-72">
            <canvas id="modelChart"></canvas>
        </div>
    </div>
    @endif

    <!-- Quick Links -->
    <div class="tw-flex tw-justify-center tw-gap-4">
        <a href="{{ route('brands.credits.index', $brand) }}" class="tw-px-4 tw-py-2 tw-bg-white tw-border tw-border-gray-300 tw-rounded-lg tw-text-sm tw-font-medium hover:tw-bg-gray-50">
            Xem lịch sử chi tiết
        </a>
        <a href="{{ route('brands.credits.export', ['brand' => $brand, 'period' => $period]) }}" class="tw-px-4 tw-py-2 tw-bg-[#16a249] tw-text-white tw-rounded-lg tw-text-sm tw-font-medium hover:tw-bg-[#138a3e]">
            Xuất báo cáo CSV
        </a>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const colors = ['#16a249', '#3b82f6', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899'];

        // Daily Chart
        const dailyData = @json($dailyUsage);
        new Chart(document.getElementById('dailyChart'), {
            type: 'line',
            data: {
                labels: Object.keys(dailyData).map(d => { const date = new Date(d); return date.getDate() + '/' + (date.getMonth() + 1); }),
                datasets: [{
                    label: 'Credits',
                    data: Object.values(dailyData),
                    borderColor: '#16a249',
                    backgroundColor: 'rgba(22, 162, 73, 0.1)',
                    fill: true,
                    tension: 0.3
                }]
            },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { precision: 0 } } } }
        });

        // Action Chart
        const actionData = @json($stats['by_action']);
        new Chart(document.getElementById('actionChart'), {
            type: 'doughnut',
            data: {
                labels: Object.keys(actionData).map(a => a.replace('_', ' ')),
                datasets: [{ data: Object.values(actionData), backgroundColor: colors }]
            },
            options: { responsive: true, maintainAspectRatio: false }
        });

        // Model Chart
        @if(count($stats['by_model']) > 0)
        const modelData = @json($stats['by_model']);
        new Chart(document.getElementById('modelChart'), {
            type: 'bar',
            data: {
                labels: Object.keys(modelData),
                datasets: [{ label: 'Credits', data: Object.values(modelData), backgroundColor: colors }]
            },
            options: { responsive: true, maintainAspectRatio: false, indexAxis: 'y', plugins: { legend: { display: false } } }
        });
        @endif
    </script>
    @endpush
</x-app-layout>

