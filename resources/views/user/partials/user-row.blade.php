<div
    class="tw-grid md:tw-grid-cols-6 tw-items-center tw-p-4 hover:tw-bg-gray-50"
>
    {{-- Mobile --}}
    <div class="md:tw-hidden">
        <p class="tw-font-bold tw-text-lg">
            {{ $user['name'] }}
        </p>
        <p class="tw-text-[#6F7C7A]">
            {{ $user['email'] }}
        </p>
    </div>

    {{-- Desktop --}}
    <div class="tw-font-medium tw-hidden md:tw-block">
        {{ $user['name'] }}
    </div>

    <div class="tw-text-[#6F7C7A] tw-hidden md:tw-block">
        {{ $user['email'] }}
    </div>

    <div>
        <span class="tw-text-[12px] tw-bg-[#E6F6EC] tw-text-[#1AA24C]
                     tw-font-semibold tw-px-3 tw-py-1 tw-rounded-full">
            {{ $user['role'] }}
        </span>
    </div>

    <div>
        <span class="tw-text-[12px] tw-bg-[#E6F6EC] tw-text-[#1AA24C]
                     tw-font-semibold tw-px-3 tw-py-1 tw-rounded-full">
            {{ $user['status'] }}
        </span>
    </div>

    <div class="tw-text-[#6F7C7A] tw-hidden md:tw-block">
        {{ $user['invited_at'] }}
    </div>

    <div class="tw-flex tw-items-center tw-gap-4">
        <button class="tw-flex tw-items-center tw-gap-1 tw-font-semibold hover:tw-text-[#1AA24C]">
            <i class="ri-edit-line"></i> Sửa
        </button>

        <button class="tw-flex tw-items-center tw-gap-1 tw-font-semibold hover:tw-text-red-500">
            <i class="ri-user-unfollow-line"></i> Vô hiệu hóa
        </button>
    </div>
</div>