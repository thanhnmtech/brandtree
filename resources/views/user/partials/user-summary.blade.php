<section class="tw-px-8">
    <div class="tw-bg-white tw-border tw-border-[#E0EAE6] tw-rounded-[12px] tw-shadow-sm tw-p-6">
        <div class="tw-flex tw-flex-col md:tw-flex-row tw-justify-between tw-mb-3">
            <div>
                <h2 class="tw-text-[22px] tw-font-bold">
                    Quản lý Thành viên
                </h2>

                <p class="tw-text-[14px] tw-text-[#6F7C7A]">
                    {{ $planName ?? 'Gói chuyên nghiệp – 5 dự án thương hiệu' }}
                </p>

                <p class="tw-text-[14px] tw-text-[#6F7C7A]">
                    {{ $activeCount ?? 0 }} đang hoạt động,
                    {{ $pendingCount ?? 0 }} chờ xác nhận
                </p>
            </div>

            <div class="tw-relative tw-mt-2">
                <button
                    type="button"
                    data-open-modal="invite-member"
                    class="tw-relative tw-bg-vlbcgreen tw-text-white
                        tw-pl-6 tw-pr-4 tw-py-2 tw-rounded-lg
                        tw-shadow hover:tw-scale-105 tw-transition">
                    Mời thành viên
                </button>
            </div>
        </div>
        
        {{-- Progress --}}
        <div class="tw-w-full tw-h-[14px] tw-bg-[#EFECE7] tw-rounded-full tw-overflow-hidden">
            <div
                class="tw-h-full tw-bg-gradient-to-r tw-from-[#34B26A] tw-to-[#78D29E]"
                style="width: {{ $usagePercent ?? 0 }}%"
            ></div>
        </div>

        <p class="tw-text-[14px] tw-text-right tw-text-[#6F7C7A] tw-mt-1">
            {{ $usedSlots ?? 0 }}/{{ $maxSlots ?? 0 }} chỗ đang sử dụng
        </p>
    </div>
</section>