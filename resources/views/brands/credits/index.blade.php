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

            <div class="tw-flex tw-flex-col md:tw-flex-row tw-justify-between tw-mb-3 tw-items-start md:tw-items-center tw-gap-3">
                <div>
                    <h1 class="tw-text-[22px] tw-font-bold">Thống kê năng lượng</h1>
                </div>

                <!-- Period Filter -->
                <div class="tw-flex tw-items-center tw-gap-2 tw-flex-wrap" x-data="{
                    showCustom: {{ request('period') === 'custom' ? 'true' : 'false' }},
                    startDate: '{{ request('start_date', '') }}',
                    endDate: '{{ request('end_date', '') }}'
                }">
                    <select onchange="if(this.value === 'custom') { document.querySelector('[x-data]').__x.$data.showCustom = true; } else { window.location.href = this.value; }"
                        class="tw-px-4 tw-py-2 tw-border-2 tw-border-[#E2EBE7] tw-rounded-lg tw-text-sm tw-bg-white hover:tw-border-vlbcgreen tw-transition">
                        <option value="{{ route('brands.credits.stats', ['brand' => $brand, 'period' => '7days']) }}" {{ $period === '7days' ? 'selected' : '' }}>7 Ngày</option>
                        <option value="{{ route('brands.credits.stats', ['brand' => $brand, 'period' => '30days']) }}" {{ $period === '30days' ? 'selected' : '' }}>30 Ngày</option>
                        <option value="{{ route('brands.credits.stats', ['brand' => $brand, 'period' => '60days']) }}" {{ $period === '60days' ? 'selected' : '' }}>60 Ngày</option>
                        <option value="{{ route('brands.credits.stats', ['brand' => $brand, 'period' => 'all']) }}" {{ $period === 'all' ? 'selected' : '' }}>Tất cả</option>
                        <option value="custom" {{ $period === 'custom' ? 'selected' : '' }}>Tùy chỉnh</option>
                    </select>

                    <!-- Custom Date Range -->
                    <div x-show="showCustom" x-cloak class="tw-flex tw-items-center tw-gap-2 tw-flex-wrap">
                        <input type="date" x-model="startDate"
                            class="tw-px-3 tw-py-2 tw-border-2 tw-border-[#E2EBE7] tw-rounded-lg tw-text-sm tw-bg-white focus:tw-border-vlbcgreen tw-outline-none">
                        <span class="tw-text-sm tw-text-[#6F7C7A]">đến</span>
                        <input type="date" x-model="endDate"
                            class="tw-px-3 tw-py-2 tw-border-2 tw-border-[#E2EBE7] tw-rounded-lg tw-text-sm tw-bg-white focus:tw-border-vlbcgreen tw-outline-none">
                        <button @click="window.location.href = '{{ route('brands.credits.stats', ['brand' => $brand, 'period' => 'custom']) }}' + '&start_date=' + startDate + '&end_date=' + endDate"
                            class="tw-px-4 tw-py-2 tw-bg-[#1AA24C] tw-text-white tw-rounded-lg tw-text-sm hover:tw-bg-[#148a3f] tw-transition">
                            Áp dụng
                        </button>
                        <button @click="showCustom = false; window.location.href = '{{ route('brands.credits.stats', ['brand' => $brand, 'period' => '30days']) }}'"
                            class="tw-px-4 tw-py-2 tw-bg-gray-200 tw-text-gray-700 tw-rounded-lg tw-text-sm hover:tw-bg-gray-300 tw-transition">
                            Hủy
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- ================= STATS CARDS ================= -->
        <section class="tw-px-8">
            <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 lg:tw-grid-cols-4 tw-gap-4">
                <!-- Credits còn lại -->
                <div class="tw-bg-white tw-border tw-border-[#E0EAE6] tw-rounded-[12px] tw-shadow-sm tw-p-5">
                    <div class="tw-flex tw-items-center tw-justify-between tw-mb-2">
                        <p class="tw-text-sm tw-text-[#6F7C7A] tw-font-medium">Năng lượng còn lại</p>
                        <div class="tw-w-10 tw-h-10 tw-bg-green-100 tw-rounded-full tw-flex tw-items-center tw-justify-center">
                            <i class="ri-flashlight-line tw-text-xl tw-text-green-600"></i>
                        </div>
                    </div>
                    <p class="tw-text-3xl tw-font-bold tw-text-[#1AA24C]">{{ $subscription ? number_format($subscription->credits_remaining) : 0 }}</p>
                    <p class="tw-text-xs tw-text-[#6F7C7A] tw-mt-2">
                        Trong {{ $subscription ? number_format($subscription->plan->credits) : 0 }} credits
                    </p>
                </div>

                <!-- Đã dùng -->
                <div class="tw-bg-white tw-border tw-border-[#E0EAE6] tw-rounded-[12px] tw-shadow-sm tw-p-5">
                    <div class="tw-flex tw-items-center tw-justify-between tw-mb-2">
                        <p class="tw-text-sm tw-text-[#6F7C7A] tw-font-medium">Đã sử dụng</p>
                        <div class="tw-w-10 tw-h-10 tw-bg-red-100 tw-rounded-full tw-flex tw-items-center tw-justify-center">
                            <i class="ri-arrow-down-line tw-text-xl tw-text-red-600"></i>
                        </div>
                    </div>
                    <p class="tw-text-3xl tw-font-bold tw-text-gray-800">{{ number_format($stats['total_used']) }}</p>
                    <p class="tw-text-xs tw-text-[#6F7C7A] tw-mt-2">
                        {{ ucfirst($period === 'week' ? 'Tuần này' : ($period === 'month' ? 'Tháng này' : ($period === 'year' ? 'Năm nay' : 'Tổng cộng'))) }}
                    </p>
                </div>

                <!-- Trung bình/ngày -->
                <div class="tw-bg-white tw-border tw-border-[#E0EAE6] tw-rounded-[12px] tw-shadow-sm tw-p-5">
                    <div class="tw-flex tw-items-center tw-justify-between tw-mb-2">
                        <p class="tw-text-sm tw-text-[#6F7C7A] tw-font-medium">Trung bình/ngày</p>
                        <div class="tw-w-10 tw-h-10 tw-bg-blue-100 tw-rounded-full tw-flex tw-items-center tw-justify-center">
                            <i class="ri-line-chart-line tw-text-xl tw-text-blue-600"></i>
                        </div>
                    </div>
                    <p class="tw-text-3xl tw-font-bold tw-text-gray-800">
                        {{ count($dailyUsage) > 0 ? number_format(array_sum($dailyUsage) / count($dailyUsage), 0) : 0 }}
                    </p>
                    <p class="tw-text-xs tw-text-[#6F7C7A] tw-mt-2">Credits mỗi ngày</p>
                </div>

                <!-- Reset tiếp theo -->
                <div class="tw-bg-white tw-border tw-border-[#E0EAE6] tw-rounded-[12px] tw-shadow-sm tw-p-5">
                    <div class="tw-flex tw-items-center tw-justify-between tw-mb-2">
                        <p class="tw-text-sm tw-text-[#6F7C7A] tw-font-medium">Reset tiếp theo</p>
                        <div class="tw-w-10 tw-h-10 tw-bg-orange-100 tw-rounded-full tw-flex tw-items-center tw-justify-center">
                            <i class="ri-refresh-line tw-text-xl tw-text-orange-600"></i>
                        </div>
                    </div>
                    <p class="tw-text-2xl tw-font-bold tw-text-gray-800">
                        {{ $subscription?->credits_reset_at?->format('d/m/Y') ?? '-' }}
                    </p>
                    <p class="tw-text-xs tw-text-[#6F7C7A] tw-mt-2">
                        {{ $subscription?->credits_reset_at?->diffForHumans() ?? 'Chưa có gói' }}
                    </p>
                </div>
            </div>
        </section>

        <!-- ================= CHART ================= -->
        <section class="tw-px-8">
            <div class="tw-bg-white tw-border tw-border-[#E0EAE6] tw-rounded-[12px] tw-shadow-sm tw-p-6">
                <h3 class="tw-font-bold tw-text-lg tw-text-gray-800 tw-mb-4">Biểu đồ sử dụng theo ngày</h3>
                <div class="tw-h-80">
                    <canvas id="usageChart"></canvas>
                </div>
            </div>
        </section>

        <!-- ================= USAGE HISTORY ================= -->
        <section class="tw-px-8">
            <div class="tw-bg-white tw-border tw-border-[#E0EAE6] tw-rounded-[12px] tw-shadow-sm tw-w-full">
                @if($usages->count() > 0)
                    <!-- Header -->
                    <div class="tw-hidden md:tw-grid tw-grid-cols-6 tw-font-semibold tw-text-[14px] tw-border-b tw-border-[#E0EAE6] tw-bg-[#F7F8F9] tw-p-4">
                        <div>Thời gian</div>
                        <div>Người dùng</div>
                        <div>Loại hành động</div>
                        <div>Model AI</div>
                        <div>Mô tả</div>
                        <div class="tw-text-right">Credits</div>
                    </div>

                    <!-- Usage List -->
                    <div class="tw-divide-y tw-divide-[#E0EAE6]">
                        @foreach($usages as $usage)
                        <div class="tw-grid md:tw-grid-cols-6 tw-items-center tw-p-4 hover:tw-bg-gray-50">
                            <!-- Mobile Layout -->
                            <div class="md:tw-hidden tw-space-y-2">
                                <div class="tw-flex tw-justify-between tw-items-start">
                                    <div>
                                        <p class="tw-font-bold tw-text-base">{{ $usage->user->name }}</p>
                                        <p class="tw-text-sm tw-text-[#6F7C7A]">{{ $usage->created_at->format('d/m/Y H:i') }}</p>
                                    </div>
                                    <span class="tw-px-3 tw-py-1 tw-text-xs tw-rounded-full tw-font-medium {{ $usage->amount > 0 ? 'tw-bg-red-100 tw-text-red-700' : 'tw-bg-green-100 tw-text-green-700' }}">
                                        {{ $usage->amount > 0 ? '-' : '+' }}{{ number_format(abs($usage->amount)) }}
                                    </span>
                                </div>
                                <div class="tw-flex tw-gap-2 tw-flex-wrap">
                                    <span class="tw-px-2 tw-py-1 tw-bg-gray-100 tw-text-gray-700 tw-text-xs tw-rounded">
                                        {{ ucfirst(str_replace('_', ' ', $usage->action_type)) }}
                                    </span>
                                    @if($usage->model_used)
                                        <span class="tw-px-2 tw-py-1 tw-bg-blue-100 tw-text-blue-700 tw-text-xs tw-rounded">
                                            {{ $usage->model_used }}
                                        </span>
                                    @endif
                                </div>
                                @if($usage->description)
                                    <p class="tw-text-sm tw-text-[#6F7C7A]">{{ $usage->description }}</p>
                                @endif
                            </div>

                            <!-- Desktop Columns -->
                            <div class="tw-hidden md:tw-block">
                                <p class="tw-text-sm tw-text-[#6F7C7A]">{{ $usage->created_at->format('d/m/Y H:i') }}</p>
                            </div>

                            <div class="tw-hidden md:tw-block">
                                <p class="tw-text-sm tw-text-gray-800 tw-font-medium">{{ $usage->user->name }}</p>
                            </div>

                            <div class="tw-hidden md:tw-block">
                                <span class="tw-px-3 tw-py-1 tw-text-xs tw-rounded-full tw-bg-gray-100 tw-text-gray-700 tw-font-medium">
                                    {{ ucfirst(str_replace('_', ' ', $usage->action_type)) }}
                                </span>
                            </div>

                            <div class="tw-hidden md:tw-block">
                                @if($usage->model_used)
                                    <span class="tw-px-3 tw-py-1 tw-text-xs tw-rounded-full tw-bg-blue-100 tw-text-blue-700 tw-font-medium">
                                        {{ $usage->model_used }}
                                    </span>
                                @else
                                    <span class="tw-text-sm tw-text-[#6F7C7A]">-</span>
                                @endif
                            </div>

                            <div class="tw-hidden md:tw-block">
                                <p class="tw-text-sm tw-text-[#6F7C7A] tw-truncate">{{ $usage->description ?? '-' }}</p>
                            </div>

                            <div class="tw-hidden md:tw-flex tw-justify-end">
                                <span class="tw-text-sm tw-font-bold {{ $usage->amount > 0 ? 'tw-text-red-600' : 'tw-text-green-600' }}">
                                    {{ $usage->amount > 0 ? '-' : '+' }}{{ number_format(abs($usage->amount)) }}
                                </span>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    @if($usages->hasPages())
                    <div class="tw-border-t tw-border-[#E0EAE6] tw-p-4">
                        {{ $usages->links() }}
                    </div>
                    @endif
                @else
                    <!-- Empty State -->
                    <div class="tw-flex tw-flex-col tw-items-center tw-justify-center tw-py-16 tw-px-4">
                        <div class="tw-w-16 tw-h-16 tw-bg-gray-100 tw-rounded-full tw-flex tw-items-center tw-justify-center tw-mb-4">
                            <i class="ri-coin-line tw-text-3xl tw-text-gray-400"></i>
                        </div>
                        <h3 class="tw-text-lg tw-font-semibold tw-text-gray-800 tw-mb-2">Chưa có lịch sử sử dụng</h3>
                        <p class="tw-text-sm tw-text-gray-500 tw-text-center tw-max-w-md">
                            Lịch sử sử dụng credits của bạn sẽ được hiển thị tại đây.
                        </p>
                    </div>
                @endif
            </div>
        </section>
    </main>

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
                        backgroundColor: 'rgba(26, 162, 76, 0.6)',
                        borderColor: 'rgba(26, 162, 76, 1)',
                        borderWidth: 2,
                        borderRadius: 6,
                        barThickness: 40,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            padding: 12,
                            titleFont: { size: 14, weight: 'bold' },
                            bodyFont: { size: 13 },
                            cornerRadius: 8,
                            callbacks: {
                                label: function(context) {
                                    return context.parsed.y + ' credits';
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0,
                                font: { size: 12 },
                                color: '#6F7C7A'
                            },
                            grid: {
                                color: '#E0EAE6',
                                drawBorder: false
                            }
                        },
                        x: {
                            ticks: {
                                font: { size: 12 },
                                color: '#6F7C7A'
                            },
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        }
    </script>
    @endpush
</x-app-layout>
