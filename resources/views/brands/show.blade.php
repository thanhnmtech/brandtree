<x-app-layout>
    <x-slot name="header">
        <div class="tw-flex tw-items-center tw-gap-4">
            <a href="{{ route('brands.index') }}" class="tw-text-gray-500 hover:tw-text-gray-700 tw-transition">
                <svg class="tw-w-6 tw-h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
            <h2 class="tw-text-2xl md:tw-text-3xl tw-font-bold tw-text-gray-800">{{ $brand->name }}</h2>
        </div>
    </x-slot>

    <div class="tw-flex tw-flex-col lg:tw-flex-row tw-gap-6">
        <!-- Sidebar -->
        <div class="lg:tw-w-64 tw-flex-shrink-0">
            @include('brands.partials.sidebar', ['brand' => $brand])
        </div>

        <!-- Main Content -->
        <div class="tw-flex-1 tw-min-w-0">

<!-- Brand Info Card -->
<div class="tw-bg-white tw-rounded-2xl tw-shadow-sm tw-p-6 tw-mb-6">
    <div class="tw-flex tw-items-start tw-gap-6">
        @if($brand->logo_path)
            <img src="{{ asset('storage/' . $brand->logo_path) }}" alt="{{ $brand->name }}" class="tw-w-24 tw-h-24 tw-object-contain tw-rounded-xl tw-bg-gray-100 tw-p-2">
        @else
            <div class="tw-w-24 tw-h-24 tw-bg-gradient-to-br tw-from-[#16a249] tw-to-[#138a3e] tw-rounded-xl tw-flex tw-items-center tw-justify-center">
                <span class="tw-text-3xl tw-font-bold tw-text-white">{{ strtoupper(substr($brand->name, 0, 2)) }}</span>
            </div>
        @endif
        <div class="tw-flex-1">
            <div class="tw-flex tw-items-start tw-justify-between">
                <div>
                    <h3 class="tw-text-xl tw-font-semibold tw-text-gray-800 tw-mb-2">{{ $brand->name }}</h3>
                    @if($brand->industry)
                        <p class="tw-text-gray-600 tw-mb-1"><span class="tw-font-medium">Ngành nghề:</span> {{ $brand->industry }}</p>
                    @endif
                    @if($brand->target_market)
                        <p class="tw-text-gray-600 tw-mb-1"><span class="tw-font-medium">Thị trường:</span> {{ $brand->target_market }}</p>
                    @endif
                    @if($brand->founded_year)
                        <p class="tw-text-gray-600"><span class="tw-font-medium">Năm thành lập:</span> {{ $brand->founded_year }}</p>
                    @endif
                </div>
                @can('update', $brand)
                    <a href="{{ route('brands.edit', $brand) }}"
                       class="tw-inline-flex tw-items-center tw-gap-2 tw-px-3 tw-py-1.5 tw-text-sm tw-border tw-border-gray-300 tw-text-gray-700 tw-rounded-lg tw-font-medium hover:tw-bg-gray-50 tw-transition">
                        <svg class="tw-w-4 tw-h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Chỉnh sửa
                    </a>
                @endcan
            </div>
        </div>
    </div>
    @if($brand->description)
        <div class="tw-mt-4 tw-pt-4 tw-border-t tw-border-gray-100">
            <h4 class="tw-font-medium tw-text-gray-800 tw-mb-2">Mô tả</h4>
            <p class="tw-text-gray-600">{{ $brand->description }}</p>
        </div>
    @endif
</div>

<!-- Quick Stats -->
<div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-3 tw-gap-4 tw-mb-6">
    <!-- Members Count -->
    <div class="tw-bg-white tw-rounded-xl tw-shadow-sm tw-p-4">
        <div class="tw-flex tw-items-center tw-gap-3">
            <div class="tw-w-10 tw-h-10 tw-bg-purple-100 tw-rounded-lg tw-flex tw-items-center tw-justify-center">
                <svg class="tw-w-5 tw-h-5 tw-text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                </svg>
            </div>
            <div>
                <p class="tw-text-2xl tw-font-bold tw-text-gray-800">{{ $brand->members->count() }}</p>
                <p class="tw-text-sm tw-text-gray-500">Thành viên</p>
            </div>
        </div>
    </div>

    <!-- Subscription -->
    <div class="tw-bg-white tw-rounded-xl tw-shadow-sm tw-p-4">
        <div class="tw-flex tw-items-center tw-gap-3">
            <div class="tw-w-10 tw-h-10 tw-bg-green-100 tw-rounded-lg tw-flex tw-items-center tw-justify-center">
                <svg class="tw-w-5 tw-h-5 tw-text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                </svg>
            </div>
            <div>
                <p class="tw-text-lg tw-font-bold tw-text-gray-800">{{ $brand->activeSubscription?->plan->name ?? 'Chưa có' }}</p>
                <p class="tw-text-sm tw-text-gray-500">Gói dịch vụ</p>
            </div>
        </div>
    </div>

    <!-- Credits -->
    <div class="tw-bg-white tw-rounded-xl tw-shadow-sm tw-p-4">
        <div class="tw-flex tw-items-center tw-gap-3">
            <div class="tw-w-10 tw-h-10 tw-bg-blue-100 tw-rounded-lg tw-flex tw-items-center tw-justify-center">
                <svg class="tw-w-5 tw-h-5 tw-text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
            </div>
            <div>
                <p class="tw-text-2xl tw-font-bold tw-text-gray-800">{{ number_format($brand->activeSubscription?->credits_remaining ?? 0) }}</p>
                <p class="tw-text-sm tw-text-gray-500">Credits còn lại</p>
            </div>
        </div>
    </div>
</div>

<!-- Recent Members -->
<div class="tw-bg-white tw-rounded-2xl tw-shadow-sm tw-p-6">
    <div class="tw-flex tw-items-center tw-justify-between tw-mb-4">
        <h3 class="tw-text-lg tw-font-semibold tw-text-gray-800">Thành viên</h3>
        @can('manageMembers', $brand)
            <a href="{{ route('brands.members.create', $brand) }}" class="tw-text-[#16a249] tw-text-sm tw-font-medium hover:tw-underline">
                + Thêm thành viên
            </a>
        @endcan
    </div>
    <div class="tw-space-y-3">
        @foreach($brand->members->take(5) as $member)
            <div class="tw-flex tw-items-center tw-justify-between tw-p-3 tw-bg-gray-50 tw-rounded-lg">
                <div class="tw-flex tw-items-center tw-gap-3">
                    <div class="tw-w-10 tw-h-10 tw-bg-[#16a249] tw-rounded-full tw-flex tw-items-center tw-justify-center">
                        <span class="tw-text-white tw-font-medium">{{ strtoupper(substr($member->user->name, 0, 1)) }}</span>
                    </div>
                    <div>
                        <p class="tw-font-medium tw-text-gray-800">{{ $member->user->name }}</p>
                        <p class="tw-text-sm tw-text-gray-500">{{ $member->user->email }}</p>
                    </div>
                </div>
                <span class="tw-px-2 tw-py-1 tw-text-xs tw-font-medium tw-rounded-full
                    @if($member->role === 'admin') tw-bg-purple-100 tw-text-purple-700
                    @elseif($member->role === 'editor') tw-bg-blue-100 tw-text-blue-700
                    @else tw-bg-gray-100 tw-text-gray-700 @endif">
                    {{ ucfirst($member->role) }}
                </span>
            </div>
        @endforeach
    </div>
    @if($brand->members->count() > 5)
        <a href="{{ route('brands.members.index', $brand) }}" class="tw-mt-4 tw-block tw-text-center tw-text-sm tw-text-[#16a249] hover:tw-underline">
            Xem tất cả {{ $brand->members->count() }} thành viên →
        </a>
    @endif
</div>

        </div>
    </div>
</x-app-layout>
