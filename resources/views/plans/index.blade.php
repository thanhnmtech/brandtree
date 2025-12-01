<x-app-layout>
    <div class="tw-max-w-5xl tw-mx-auto">
        <!-- Header -->
        <div class="tw-text-center tw-mb-10">
            <h1 class="tw-text-3xl md:tw-text-4xl tw-font-bold tw-text-gray-800 tw-mb-4">Bảng giá dịch vụ</h1>
            <p class="tw-text-gray-600 tw-text-lg">Chọn gói phù hợp để phát triển thương hiệu của bạn</p>
        </div>

        <!-- Plans Grid -->
        <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-3 tw-gap-6">
            @foreach($plans as $plan)
                <div class="tw-bg-white tw-rounded-2xl tw-shadow-sm tw-overflow-hidden tw-relative {{ $plan->is_popular ? 'tw-ring-2 tw-ring-[#16a249]' : '' }}">
                    @if($plan->is_popular)
                        <div class="tw-absolute tw-top-0 tw-right-0 tw-bg-[#16a249] tw-text-white tw-text-xs tw-font-medium tw-px-3 tw-py-1 tw-rounded-bl-lg">
                            Phổ biến
                        </div>
                    @endif

                    <div class="tw-p-6">
                        <!-- Plan Name -->
                        <h3 class="tw-text-xl tw-font-bold tw-text-gray-800 tw-mb-2">{{ $plan->name }}</h3>
                        @if($plan->description)
                            <p class="tw-text-gray-500 tw-text-sm tw-mb-4">{{ $plan->description }}</p>
                        @endif

                        <!-- Price -->
                        <div class="tw-mb-6">
                            <span class="tw-text-3xl tw-font-bold tw-text-gray-800">{{ $plan->formatted_price }}</span>
                            @if(!$plan->is_trial && $plan->price > 0)
                                <span class="tw-text-gray-500">/tháng</span>
                            @endif
                        </div>

                        <!-- Features -->
                        <ul class="tw-space-y-3 tw-mb-6">
                            <li class="tw-flex tw-items-center tw-gap-2 tw-text-gray-600">
                                <svg class="tw-w-5 tw-h-5 tw-text-[#16a249]" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                <span><strong>{{ number_format($plan->credits) }}</strong> credits/tháng</span>
                            </li>
                            <li class="tw-flex tw-items-center tw-gap-2 tw-text-gray-600">
                                <svg class="tw-w-5 tw-h-5 tw-text-[#16a249]" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                <span>{{ $plan->formatted_duration }}</span>
                            </li>
                            @if($plan->models_allowed)
                                <li class="tw-flex tw-items-start tw-gap-2 tw-text-gray-600">
                                    <svg class="tw-w-5 tw-h-5 tw-text-[#16a249] tw-mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                    <span>{{ count($plan->models_allowed) }} AI models</span>
                                </li>
                            @endif
                        </ul>

                        <!-- CTA Button -->
                        @auth
                            <a href="{{ route('dashboard') }}" class="tw-block tw-w-full tw-py-3 tw-text-center tw-rounded-lg tw-font-medium tw-transition {{ $plan->is_popular ? 'tw-bg-[#16a249] tw-text-white hover:tw-bg-[#138a3e]' : 'tw-border tw-border-gray-300 tw-text-gray-700 hover:tw-bg-gray-50' }}">
                                Chọn gói này
                            </a>
                        @else
                            <a href="{{ route('register') }}" class="tw-block tw-w-full tw-py-3 tw-text-center tw-rounded-lg tw-font-medium tw-transition {{ $plan->is_popular ? 'tw-bg-[#16a249] tw-text-white hover:tw-bg-[#138a3e]' : 'tw-border tw-border-gray-300 tw-text-gray-700 hover:tw-bg-gray-50' }}">
                                Bắt đầu ngay
                            </a>
                        @endauth
                    </div>
                </div>
            @endforeach
        </div>

        <!-- FAQ or Notes -->
        <div class="tw-mt-12 tw-bg-white tw-rounded-2xl tw-shadow-sm tw-p-6">
            <h3 class="tw-text-lg tw-font-semibold tw-text-gray-800 tw-mb-4">Lưu ý</h3>
            <ul class="tw-space-y-2 tw-text-gray-600">
                <li class="tw-flex tw-items-start tw-gap-2">
                    <span class="tw-text-[#16a249]">•</span>
                    <span>Credits được reset hàng tháng vào ngày đăng ký.</span>
                </li>
                <li class="tw-flex tw-items-start tw-gap-2">
                    <span class="tw-text-[#16a249]">•</span>
                    <span>Gói dùng thử chỉ được sử dụng 1 lần cho mỗi thương hiệu.</span>
                </li>
                <li class="tw-flex tw-items-start tw-gap-2">
                    <span class="tw-text-[#16a249]">•</span>
                    <span>Có thể nâng cấp hoặc hạ cấp gói bất kỳ lúc nào.</span>
                </li>
            </ul>
        </div>
    </div>
</x-app-layout>

