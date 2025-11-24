<x-app-layout>
    <x-slot name="header">
        <h2 class="tw-text-2xl md:tw-text-3xl tw-font-bold tw-text-gray-800">
            Hồ sơ cá nhân
        </h2>
    </x-slot>

    <div class="tw-space-y-6">
        <div class="tw-bg-white tw-rounded-2xl tw-shadow-sm tw-p-6 md:tw-p-8">
            <div class="tw-max-w-2xl">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        <div class="tw-bg-white tw-rounded-2xl tw-shadow-sm tw-p-6 md:tw-p-8">
            <div class="tw-max-w-2xl">
                @include('profile.partials.update-password-form')
            </div>
        </div>

        <div class="tw-bg-white tw-rounded-2xl tw-shadow-sm tw-p-6 md:tw-p-8">
            <div class="tw-max-w-2xl">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</x-app-layout>
