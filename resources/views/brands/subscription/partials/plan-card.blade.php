<form action="{{ route('brands.subscription.store', $brand) }}" method="POST" class="tw-h-full">
    @csrf
    <input type="hidden" name="plan_id" value="{{ $plan->id }}">

    <div class="tw-bg-white tw-rounded-2xl tw-shadow-sm tw-overflow-hidden tw-h-full tw-flex tw-flex-col tw-relative {{ $plan->is_popular ? 'tw-ring-2 tw-ring-[#16a249]' : '' }}">
        @if($plan->is_popular)
            <div class="tw-absolute tw-top-0 tw-right-0 tw-bg-[#16a249] tw-text-white tw-text-xs tw-font-medium tw-px-3 tw-py-1 tw-rounded-bl-lg">
                Phổ biến
            </div>
        @endif

        @if($plan->hasDiscount())
            <div class="tw-absolute tw-top-0 tw-left-0 tw-bg-orange-500 tw-text-white tw-text-xs tw-font-medium tw-px-3 tw-py-1 tw-rounded-br-lg">
                Tiết kiệm {{ $plan->discount_percent }}%
            </div>
        @endif

        <div class="tw-p-6 tw-flex-1">
            <h3 class="tw-text-xl tw-font-bold tw-text-gray-800 tw-mb-2">{{ $plan->name }}</h3>
            @if($plan->description)
                <p class="tw-text-gray-500 tw-text-sm tw-mb-4">{{ $plan->description }}</p>
            @endif

            <div class="tw-mb-6">
                @if($plan->hasDiscount())
                    <div class="tw-text-gray-400 tw-line-through tw-text-sm">{{ $plan->formatted_original_price }}</div>
                @endif
                <span class="tw-text-3xl tw-font-bold tw-text-gray-800">{{ $plan->formatted_price }}</span>
                @if($plan->price > 0)
                    <span class="tw-text-gray-500">/{{ $isYearly ? 'năm' : 'tháng' }}</span>
                @endif
                @if($isYearly && $plan->price > 0)
                    <div class="tw-text-sm tw-text-[#16a249] tw-mt-1">
                        ~ {{ $plan->formatted_monthly_price }}/tháng
                    </div>
                @endif
            </div>

            <ul class="tw-space-y-3 tw-mb-6">
                <li class="tw-flex tw-items-center tw-gap-2 tw-text-gray-600">
                    <svg class="tw-w-5 tw-h-5 tw-text-[#16a249] tw-flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                    <span><strong>{{ number_format($plan->credits) }}</strong> credits/tháng</span>
                </li>
                <li class="tw-flex tw-items-center tw-gap-2 tw-text-gray-600">
                    <svg class="tw-w-5 tw-h-5 tw-text-[#16a249] tw-flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                    <span>{{ $plan->formatted_duration }}</span>
                </li>
                @if($plan->models_allowed)
                    <li class="tw-flex tw-items-start tw-gap-2 tw-text-gray-600">
                        <svg class="tw-w-5 tw-h-5 tw-text-[#16a249] tw-mt-0.5 tw-flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                        <span>{{ count($plan->models_allowed) }} AI models</span>
                    </li>
                @endif
            </ul>
        </div>

        <div class="tw-p-6 tw-pt-0">
            @if($currentSubscription && $currentSubscription->plan_id === $plan->id)
                <span class="tw-block tw-w-full tw-py-3 tw-text-center tw-rounded-lg tw-font-medium tw-bg-green-100 tw-text-green-700">
                    ✓ Gói hiện tại
                </span>
            @elseif($plan->is_trial)
                <span class="tw-block tw-w-full tw-py-3 tw-text-center tw-rounded-lg tw-font-medium tw-bg-gray-100 tw-text-gray-400">
                    Chỉ dùng 1 lần
                </span>
            @else
                <button type="submit" class="tw-w-full tw-py-3 tw-text-center tw-rounded-lg tw-font-medium tw-transition {{ $plan->is_popular ? 'tw-bg-[#16a249] tw-text-white hover:tw-bg-[#138a3e]' : 'tw-border tw-border-gray-300 tw-text-gray-700 hover:tw-bg-gray-50' }}">
                    Đăng ký ngay
                </button>
            @endif
        </div>
    </div>
</form>
