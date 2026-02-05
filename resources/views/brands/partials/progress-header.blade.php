{{-- 
    Partial view cho Progress Header
    Hiển thị 3 card tiến trình: Root, Trunk, Canopy
    Được sử dụng trong show.blade.php và reload qua AJAX
--}}
<div
    class="tw-w-full tw-bg-[linear-gradient(180deg,rgba(207,240,255,0.5)_0%,rgba(255,255,247,0.5)_100%)] tw-from-[#F4FAF7] tw-to-[#EEF6F2] tw-rounded-xl tw-border tw-border-[#E0EAE6] tw-p-8 shadow-[0_18px_40px_-8px_rgba(0,0,0,0.15)]">
    <div class="tw-text-center tw-mb-8">
        <h2
            class="tw-text-2xl tw-font-bold tw-bg-[linear-gradient(90deg,#33B45E_0%,#ABB354_100%)] tw-bg-clip-text tw-text-transparent">
            {{ __('messages.brand_show.brand_journey') }}
        </h2>
        <p class="tw-text-gray-600 tw-text-sm tw-mt-1">
            {{ __('messages.brand_show.track_progress') }}
        </p>
    </div>

    <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-3 tw-gap-6 tw-mt-6">
        {{-- Root Card --}}
        <x-progress-phase-card 
            phase="root" 
            :status="$phases['root']['status']"
            :progress="$phases['root']['progress']"
            :url="$phases['root']['url']"
        />

        {{-- Trunk Card --}}
        <x-progress-phase-card 
            phase="trunk" 
            :status="$phases['trunk']['status']"
            :progress="$phases['trunk']['progress']"
            :url="$phases['trunk']['url']"
        />

        {{-- Canopy Card --}}
        <x-progress-phase-card 
            phase="canopy" 
            :status="$phases['canopy']['status']"
            :progress="$phases['canopy']['progress']"
            :url="$phases['canopy']['url']"
        />
    </div>
</div>
