<x-app-layout>
    <main class="tw-flex tw-flex-col md:tw-rounded-[10px] md:tw-bg-[#F3F7F5] md:tw-mt-[36px] md:tw-mx-[71px] tw-p-4 md:tw-p-10">
        <!-- Header -->
        <div class="tw-flex tw-items-center tw-justify-between tw-mb-8">
            <div>
                <h1 class="tw-text-2xl md:tw-text-3xl tw-font-bold tw-text-gray-900">Hồ sơ cá nhân</h1>
                <p class="tw-text-sm tw-text-gray-600 tw-mt-1">Quản lý thông tin tài khoản và bảo mật</p>
            </div>
        </div>

        <div class="tw-space-y-6">
            <!-- Update Profile Information -->
            <div class="tw-bg-white tw-rounded-2xl tw-shadow-lg tw-border-2 tw-border-[#E2EBE7] tw-p-6 md:tw-p-8">
                @include('profile.partials.update-profile-information-form')
            </div>

            <!-- Update Password -->
            <div class="tw-bg-white tw-rounded-2xl tw-shadow-lg tw-border-2 tw-border-[#E2EBE7] tw-p-6 md:tw-p-8">
                @include('profile.partials.update-password-form')
            </div>

            {{-- Delete User Form - commented out
            <div class="tw-bg-white tw-rounded-2xl tw-shadow-lg tw-border-2 tw-border-[#E2EBE7] tw-p-6 md:tw-p-8">
                @include('profile.partials.delete-user-form')
            </div>
            --}}
        </div>
    </main>
</x-app-layout>
