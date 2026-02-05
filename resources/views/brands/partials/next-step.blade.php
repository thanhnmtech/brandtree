@php
    $nextUrl = '#';
    $nextStepText = __('messages.brand_show.start_now');
    
    // Priority: Root -> Trunk -> Canopy
    // Check Root first
    if (($phases['root']['status'] ?? '') !== 'completed') {
        $nextUrl = $phases['root']['url'] ?? '#';
        // Nếu đã có progress > 0 thì là continue, ngược lại là start_now
         if (($phases['root']['progress'] ?? 0) > 0) {
            $nextStepText = __('messages.brand_show.continue');
        } else {
            $nextStepText = __('messages.brand_show.start_now');
        }
    } 
    // If Root completed, check Trunk
    elseif (($phases['trunk']['status'] ?? '') !== 'completed') {
        $nextUrl = $phases['trunk']['url'] ?? '#';
        $nextStepText = __('messages.brand_show.continue');
    } 
    // If Trunk completed, go to Canopy
    else {
        $nextUrl = $phases['canopy']['url'] ?? '#';
        $nextStepText = __('messages.brand_show.continue');
    }
@endphp

<section class="tw-px-8">
    <div
        class="tw-w-full tw-bg-[linear-gradient(273deg,#FDFAF4_0.32%,#F5FBF7_99.68%)] tw-border tw-border-[#E0EAE6] tw-rounded-xl tw-p-8 tw-shadow-sm">
        <!-- Header -->
        <div class="tw-flex tw-items-center tw-gap-3">
            <div
                class="tw-h-12 tw-w-12 tw-rounded-full tw-bg-[linear-gradient(324deg,#69CB92_15.2%,#41B873_81.83%)] tw-flex tw-items-center tw-justify-center">
                <img src="{{ asset('assets/img/icon-star-white.svg') }}" alt="ok"
                    class="tw-w-[24px] tw-h-[24px]" />
            </div>

            <div>
                <h2 class="tw-text-2xl tw-font-bold tw-text-gray-800">
                    {{ __('messages.brand_show.next_step') }}
                </h2>
                <p class="tw-text-sm tw-text-gray-500 tw-mt-1">
                    {{ __('messages.brand_show.next_step_desc') }}
                </p>
            </div>
        </div>

        <!-- Description Box -->
        <div
            class="tw-bg-white tw-border tw-border-[#E0EAE6] tw-rounded-lg tw-p-4 tw-mt-4 tw-text-gray-700 tw-text-sm">
            {{ __('messages.brand_show.next_step_detail') }}
        </div>

        <!-- Action Button -->
        <div class="tw-mt-5">
            <a href="{{ $nextUrl }}"
                class="tw-w-full tw-bg-gradient-to-r tw-from-[#34B26A] tw-to-[#78D29E] tw-text-white tw-font-medium tw-text-base tw-py-1 tw-rounded-lg tw-flex tw-items-center tw-justify-center tw-gap-4 tw-shadow-sm hover:tw-scale-[1.01] tw-transition">
                {{ $nextStepText }}
                <i class="ri-arrow-right-line tw-text-lg"></i>
            </a>
        </div>

        <!-- Hint -->
        <div class="tw-text-xs tw-text-gray-500 tw-flex tw-items-center tw-justify-center tw-gap-1 tw-mt-4">
            <i class="ri-sparkling-2-line tw-text-[#F4C56A]"></i>
            {{ __('messages.brand_show.tip') }}
        </div>
    </div>
</section>
