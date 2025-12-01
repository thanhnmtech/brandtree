<x-app-layout>
    <x-slot name="header">
        <div class="tw-flex tw-items-center tw-justify-between">
            <h2 class="tw-text-2xl md:tw-text-3xl tw-font-bold tw-text-gray-800">
                Thương hiệu của tôi
            </h2>
            <a href="{{ route('brands.create') }}" 
               class="tw-inline-flex tw-items-center tw-gap-2 tw-px-4 tw-py-2 tw-bg-[#16a249] tw-text-white tw-rounded-lg tw-font-medium hover:tw-bg-[#138a3e] tw-transition">
                <svg class="tw-w-5 tw-h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Tạo thương hiệu
            </a>
        </div>
    </x-slot>

    @if(session('success'))
        <div class="tw-mb-6 tw-bg-green-100 tw-border tw-border-green-400 tw-text-green-700 tw-px-4 tw-py-3 tw-rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    @if($brands->isEmpty())
        <div class="tw-bg-white tw-rounded-2xl tw-shadow-sm tw-p-8 tw-text-center">
            <div class="tw-w-20 tw-h-20 tw-bg-gray-100 tw-rounded-full tw-flex tw-items-center tw-justify-center tw-mx-auto tw-mb-4">
                <svg class="tw-w-10 tw-h-10 tw-text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
            </div>
            <h3 class="tw-text-lg tw-font-semibold tw-text-gray-800 tw-mb-2">Chưa có thương hiệu nào</h3>
            <p class="tw-text-gray-600 tw-mb-6">Bắt đầu bằng việc tạo thương hiệu đầu tiên của bạn.</p>
            <a href="{{ route('brands.create') }}" 
               class="tw-inline-flex tw-items-center tw-gap-2 tw-px-6 tw-py-3 tw-bg-[#16a249] tw-text-white tw-rounded-lg tw-font-medium hover:tw-bg-[#138a3e] tw-transition">
                <svg class="tw-w-5 tw-h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Tạo thương hiệu mới
            </a>
        </div>
    @else
        <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 lg:tw-grid-cols-3 tw-gap-6">
            @foreach($brands as $brand)
                <div class="tw-bg-white tw-rounded-2xl tw-shadow-sm tw-overflow-hidden hover:tw-shadow-md tw-transition">
                    <a href="{{ route('brands.show', $brand) }}" class="tw-block">
                        <div class="tw-h-32 tw-bg-gradient-to-br tw-from-[#16a249] tw-to-[#138a3e] tw-flex tw-items-center tw-justify-center">
                            @if($brand->logo_path)
                                <img src="{{ asset('storage/' . $brand->logo_path) }}" alt="{{ $brand->name }}" class="tw-h-20 tw-w-20 tw-object-contain tw-rounded-full tw-bg-white tw-p-2">
                            @else
                                <div class="tw-w-20 tw-h-20 tw-bg-white tw-rounded-full tw-flex tw-items-center tw-justify-center">
                                    <span class="tw-text-2xl tw-font-bold tw-text-[#16a249]">{{ strtoupper(substr($brand->name, 0, 2)) }}</span>
                                </div>
                            @endif
                        </div>
                        <div class="tw-p-5">
                            <h3 class="tw-text-lg tw-font-semibold tw-text-gray-800 tw-mb-1">{{ $brand->name }}</h3>
                            @if($brand->industry)
                                <p class="tw-text-sm tw-text-gray-500 tw-mb-3">{{ $brand->industry }}</p>
                            @endif
                            
                            @if($brand->activeSubscription)
                                <div class="tw-flex tw-items-center tw-gap-2 tw-text-sm">
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
                </div>
            @endforeach
        </div>
    @endif
</x-app-layout>

