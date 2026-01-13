<section class="tw-px-8">
    <div
        class="tw-bg-white tw-border tw-border-[#E0EAE6]
               tw-rounded-[12px] tw-shadow-sm tw-w-full"
    >
        {{-- Header --}}
        <div
            class="tw-hidden md:tw-grid tw-grid-cols-6 tw-font-semibold
                   tw-text-[14px] tw-border-b tw-border-[#E0EAE6]
                   tw-bg-[#F7F8F9] tw-p-4"
        >
            <div>Tên thành viên</div>
            <div>Email</div>
            <div>Vai trò</div>
            <div>Trạng thái</div>
            <div>Ngày mời</div>
            <div>Hành động</div>
        </div>

        {{-- Rows --}}
        <div class="tw-divide-y tw-divide-[#E0EAE6]">
            @forelse ($users as $user)
                @include('user.partials.user-row', ['user' => $user])
            @empty
                <div class="tw-p-6 tw-text-center tw-text-gray-500">
                    Chưa có thành viên nào
                </div>
            @endforelse
        </div>
    </div>
</section>