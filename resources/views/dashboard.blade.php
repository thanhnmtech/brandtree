<x-app-layout>
    <!-- Stats Grid -->
    <div class="tw-grid tw-grid-cols-2 md:tw-grid-cols-4 tw-gap-4 md:tw-gap-6 tw-mb-6">
        <div class="tw-bg-white tw-rounded-2xl tw-shadow-sm tw-p-4 md:tw-p-6">
            <div class="tw-flex tw-items-center tw-justify-between">
                <div>
                    <p class="tw-text-xs md:tw-text-sm tw-text-gray-600 tw-mb-1">Tổng thương hiệu</p>
                    <h4 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-gray-800">{{ $totalBrands }}</h4>
                </div>
                <div class="tw-w-10 tw-h-10 md:tw-w-12 md:tw-h-12 tw-bg-blue-100 tw-rounded-lg tw-flex tw-items-center tw-justify-center">
                    <svg class="tw-w-5 tw-h-5 md:tw-w-6 md:tw-h-6 tw-text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="tw-bg-white tw-rounded-2xl tw-shadow-sm tw-p-4 md:tw-p-6">
            <div class="tw-flex tw-items-center tw-justify-between">
                <div>
                    <p class="tw-text-xs md:tw-text-sm tw-text-gray-600 tw-mb-1">Đang hoạt động</p>
                    <h4 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-gray-800">{{ $activeBrands }}</h4>
                </div>
                <div class="tw-w-10 tw-h-10 md:tw-w-12 md:tw-h-12 tw-bg-green-100 tw-rounded-lg tw-flex tw-items-center tw-justify-center">
                    <svg class="tw-w-5 tw-h-5 md:tw-w-6 md:tw-h-6 tw-text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="tw-bg-white tw-rounded-2xl tw-shadow-sm tw-p-4 md:tw-p-6">
            <div class="tw-flex tw-items-center tw-justify-between">
                <div>
                    <p class="tw-text-xs md:tw-text-sm tw-text-gray-600 tw-mb-1">Cần chăm sóc</p>
                    <h4 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-gray-800">0</h4>
                </div>
                <div class="tw-w-10 tw-h-10 md:tw-w-12 md:tw-h-12 tw-bg-orange-100 tw-rounded-lg tw-flex tw-items-center tw-justify-center">
                    <svg class="tw-w-5 tw-h-5 md:tw-w-6 md:tw-h-6 tw-text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="tw-bg-white tw-rounded-2xl tw-shadow-sm tw-p-4 md:tw-p-6">
            <div class="tw-flex tw-items-center tw-justify-between">
                <div>
                    <p class="tw-text-xs md:tw-text-sm tw-text-gray-600 tw-mb-1">Đã hoàn thiện</p>
                    <h4 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-gray-800">0</h4>
                </div>
                <div class="tw-w-10 tw-h-10 md:tw-w-12 md:tw-h-12 tw-bg-purple-100 tw-rounded-lg tw-flex tw-items-center tw-justify-center">
                    <svg class="tw-w-5 tw-h-5 md:tw-w-6 md:tw-h-6 tw-text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Search & Create -->
    <div class="tw-bg-white tw-rounded-2xl tw-shadow-sm tw-p-4 md:tw-p-6 tw-mb-6">
        <div class="tw-flex tw-flex-col sm:tw-flex-row tw-gap-4 tw-justify-between tw-items-start sm:tw-items-center">
            <form action="{{ route('dashboard') }}" method="GET" class="tw-w-full sm:tw-w-auto sm:tw-flex-1 sm:tw-max-w-md">
                <div class="tw-relative">
                    <input type="text" name="search" value="{{ $search }}"
                           placeholder="Tìm kiếm thương hiệu..."
                           class="tw-w-full tw-pl-10 tw-pr-4 tw-py-2.5 tw-border tw-border-gray-300 tw-rounded-lg focus:tw-ring-2 focus:tw-ring-[#16a249] focus:tw-border-transparent tw-transition">
                    <svg class="tw-w-5 tw-h-5 tw-text-gray-400 tw-absolute tw-left-3 tw-top-1/2 tw--translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    @if($search)
                        <a href="{{ route('dashboard') }}" class="tw-absolute tw-right-3 tw-top-1/2 tw--translate-y-1/2 tw-text-gray-400 hover:tw-text-gray-600">
                            <svg class="tw-w-5 tw-h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </a>
                    @endif
                </div>
            </form>
            <a href="{{ route('brands.create') }}"
               class="tw-w-full sm:tw-w-auto tw-inline-flex tw-items-center tw-justify-center tw-gap-2 tw-px-5 tw-py-2.5 tw-bg-[#16a249] tw-text-white tw-rounded-lg tw-font-medium hover:tw-bg-[#138a3e] tw-transition">
                <svg class="tw-w-5 tw-h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Tạo thương hiệu
            </a>
        </div>
    </div>

    <!-- Brands List -->
    @if($brands->isEmpty())
        <div class="tw-bg-white tw-rounded-2xl tw-shadow-sm tw-p-8 tw-text-center">
            <div class="tw-w-20 tw-h-20 tw-bg-gray-100 tw-rounded-full tw-flex tw-items-center tw-justify-center tw-mx-auto tw-mb-4">
                <svg class="tw-w-10 tw-h-10 tw-text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
            </div>
            @if($search)
                <h3 class="tw-text-lg tw-font-semibold tw-text-gray-800 tw-mb-2">Không tìm thấy kết quả</h3>
                <p class="tw-text-gray-600 tw-mb-6">Không có thương hiệu nào phù hợp với "{{ $search }}"</p>
                <a href="{{ route('dashboard') }}"
                   class="tw-inline-flex tw-items-center tw-gap-2 tw-px-6 tw-py-3 tw-border tw-border-gray-300 tw-text-gray-700 tw-rounded-lg tw-font-medium hover:tw-bg-gray-50 tw-transition">
                    Xóa bộ lọc
                </a>
            @else
                <h3 class="tw-text-lg tw-font-semibold tw-text-gray-800 tw-mb-2">Chưa có thương hiệu nào</h3>
                <p class="tw-text-gray-600 tw-mb-6">Bắt đầu bằng việc tạo thương hiệu đầu tiên của bạn.</p>
                <a href="{{ route('brands.create') }}"
                   class="tw-inline-flex tw-items-center tw-gap-2 tw-px-6 tw-py-3 tw-bg-[#16a249] tw-text-white tw-rounded-lg tw-font-medium hover:tw-bg-[#138a3e] tw-transition">
                    <svg class="tw-w-5 tw-h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Tạo thương hiệu mới
                </a>
            @endif
        </div>
    @else
        <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 lg:tw-grid-cols-3 tw-gap-4 md:tw-gap-6">
            @foreach($brands as $brand)
                <a href="{{ route('brands.show', $brand) }}"
                   class="tw-bg-white tw-rounded-2xl tw-shadow-sm tw-overflow-hidden hover:tw-shadow-md tw-transition tw-block">
                    <div class="tw-h-28 md:tw-h-32 tw-bg-gradient-to-br tw-from-[#16a249] tw-to-[#138a3e] tw-flex tw-items-center tw-justify-center">
                        @if($brand->logo_path)
                            <img src="{{ asset('storage/' . $brand->logo_path) }}" alt="{{ $brand->name }}" class="tw-h-16 tw-w-16 md:tw-h-20 md:tw-w-20 tw-object-contain tw-rounded-full tw-bg-white tw-p-2">
                        @else
                            <div class="tw-w-16 tw-h-16 md:tw-w-20 md:tw-h-20 tw-bg-white tw-rounded-full tw-flex tw-items-center tw-justify-center">
                                <span class="tw-text-xl md:tw-text-2xl tw-font-bold tw-text-[#16a249]">{{ strtoupper(substr($brand->name, 0, 2)) }}</span>
                            </div>
                        @endif
                    </div>
                    <div class="tw-p-4 md:tw-p-5">
                        <h3 class="tw-text-base md:tw-text-lg tw-font-semibold tw-text-gray-800 tw-mb-1">{{ $brand->name }}</h3>
                        @if($brand->industry)
                            <p class="tw-text-sm tw-text-gray-500 tw-mb-3">{{ $brand->industry }}</p>
                        @endif

                        @if($brand->activeSubscription)
                            <div class="tw-flex tw-items-center tw-gap-2 tw-text-sm tw-flex-wrap">
                                <span class="tw-px-2 tw-py-1 tw-bg-green-100 tw-text-green-700 tw-rounded-full tw-text-xs tw-font-medium">
                                    {{ $brand->activeSubscription->plan->name }}
                                </span>
                                <span class="tw-text-gray-500">
                                    {{ number_format($brand->activeSubscription->credits_remaining) }} credits
                                </span>
                            </div>
                        @else
                            <span class="tw-px-2 tw-py-1 tw-bg-gray-100 tw-text-gray-600 tw-rounded-full tw-text-xs tw-font-medium">
                                Chưa có gói
                            </span>
                        @endif
                    </div>
                </a>
            @endforeach
        </div>

        <!-- Pagination -->
        @if($brands->hasPages())
            <div class="tw-mt-6">
                {{ $brands->links() }}
            </div>
        @endif
    @endif
</x-app-layout>
