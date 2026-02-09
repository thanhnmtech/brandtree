<x-app-layout>
    <div
        data-controller="member-management"
        data-member-management-current-role-value="{{ old('role', 'editor') }}">
    <main class="tw-mt-[36px] tw-flex tw-flex-col tw-gap-5">
        <div class="tw-px-8">
            <x-breadcrumb :items="[
                ['label' => __('messages.dashboard.manage_brand'), 'url' => route('dashboard')],
                ['label' => $brand->name, 'url' => route('brands.show', $brand)]
            ]" />

            <div class="tw-flex tw-flex-col md:tw-flex-row tw-justify-between tw-mb-3">
                <div>
                    <h1 class="tw-text-[22px] tw-font-bold">{{ __('messages.members.title') }}</h1>
                </div>

                @if($isAdmin)
                <div class="tw-relative tw-mt-2">
                    <button data-action="click->member-management#openInvite"
                        class="tw-relative tw-bg-vlbcgreen tw-text-white tw-pl-10 tw-pr-4 tw-py-2 tw-rounded-lg tw-shadow hover:tw-scale-105 tw-transition">
                        <img src="{{ asset('assets/img/add-vector.svg') }}" alt="add"
                            class="tw-absolute tw-left-3 tw-top-1/2 -tw-translate-y-1/2 tw-w-4 tw-h-4 tw-pointer-events-none" />
                        <span>{{ __('messages.members.add_member') }}</span>
                    </button>
                </div>
                @endif
            </div>
        </div>

        <!-- ================= USER LIST ================= -->
        <section class="tw-px-8">
            <div class="tw-bg-white tw-border tw-border-[#E0EAE6] tw-rounded-[12px] tw-shadow-sm tw-w-full">
                <!-- Header -->
                <div
                    class="tw-hidden md:tw-grid tw-grid-cols-6 tw-font-semibold tw-text-[14px] tw-border-b tw-border-[#E0EAE6] tw-bg-[#F7F8F9] tw-p-4">
                    <div>{{ __('messages.members.member_name') }}</div>
                    <div>{{ __('messages.members.email') }}</div>
                    <div>{{ __('messages.members.role') }}</div>
                    <div>{{ __('messages.members.status') }}</div>
                    <div>{{ __('messages.members.joined_date') }}</div>
                    <div>{{ __('messages.members.actions') }}</div>
                </div>

                <!-- Mobile Responsive Card List -->
                <div class="tw-divide-y tw-divide-[#E0EAE6]">
                    @forelse($members as $member)
                    <div class="tw-grid md:tw-grid-cols-6 tw-items-center tw-p-4 hover:tw-bg-gray-50">
                        <!-- Mobile Layout -->
                        <div class="md:tw-hidden">
                            <p class="tw-font-bold tw-text-lg">{{ $member->user->name }}</p>
                            <p class="tw-text-[#6F7C7A]">{{ $member->user->email }}</p>
                        </div>

                        <!-- Desktop Columns -->
                        <div class="tw-font-medium tw-hidden md:tw-block">
                            {{ $member->user->name }}
                        </div>
                        <div class="tw-text-[#6F7C7A] tw-hidden md:tw-block">
                            {{ $member->user->email }}
                        </div>

                        <!-- Role -->
                        <div>
                            @if($member->role === 'admin')
                            <span
                                class="tw-text-[12px] tw-bg-[#E6F6EC] tw-text-[#1AA24C] tw-font-semibold tw-px-3 tw-py-1 tw-rounded-full">
                                {{ __('messages.members.admin') }}</span>
                            @elseif($member->role === 'editor')
                            <span
                                class="tw-text-[12px] tw-bg-[#FFF3D8] tw-text-[#E3A74B] tw-font-semibold tw-px-3 tw-py-1 tw-rounded-full">
                                {{ __('messages.members.editor') }}</span>
                            @else
                            <span
                                class="tw-text-[12px] tw-bg-gray-100 tw-text-gray-700 tw-font-semibold tw-px-3 tw-py-1 tw-rounded-full">
                                {{ __('messages.members.member') }}</span>
                            @endif
                        </div>

                        <!-- Status -->
                        <div>
                            <span
                                class="tw-text-[12px] tw-bg-[#E6F6EC] tw-text-[#1AA24C] tw-font-semibold tw-px-3 tw-py-1 tw-rounded-full">
                                {{ __('messages.members.active') }}</span>
                        </div>

                        <!-- Joined Date -->
                        <div class="tw-text-[#6F7C7A] tw-hidden md:tw-block">
                            {{ $member->joined_at ? $member->joined_at->format('d/m/Y') : '-' }}
                        </div>

                        <!-- Actions -->
                        <div class="tw-flex tw-items-center tw-gap-4">
                            @if($isAdmin && $member->user_id !== auth()->id() && $member->user_id !== $brand->created_by)
                            <button data-action="click->member-management#openEdit"
                                data-member-id="{{ $member->id }}"
                                data-member-role="{{ $member->role }}"
                                class="tw-flex tw-items-center tw-gap-1 tw-font-semibold hover:tw-text-[#1AA24C]">
                                <i class="ri-edit-line"></i> {{ __('messages.members.edit') }}
                            </button>
                            <button data-action="click->member-management#openDelete"
                                data-member-id="{{ $member->id }}"
                                data-member-name="{{ addslashes($member->user->name) }}"
                                class="tw-flex tw-items-center tw-gap-1 tw-font-semibold hover:tw-text-red-500">
                                <i class="ri-user-unfollow-line"></i> {{ __('messages.members.remove') }}
                            </button>
                            @else
                            <span class="tw-text-[#6F7C7A] tw-text-sm">-</span>
                            @endif
                        </div>
                    </div>
                    @empty
                    <div class="tw-p-8 tw-text-center tw-text-gray-500">
                        <i class="ri-user-line tw-text-4xl tw-mb-2"></i>
                        <p>{{ __('messages.members.no_members') }}</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </section>
    </main>

    <!-- ================= INVITE MEMBER POPUP ================= -->
    @if($isAdmin)
    <div data-member-management-target="inviteModal"
        class="tw-hidden tw-fixed tw-inset-0 tw-bg-black/40 tw-backdrop-blur-sm tw-z-[9999]"
        style="display: none;"
        data-action="click->member-management#closeInvite">
        <div class="tw-bg-white tw-rounded-2xl tw-w-[500px] tw-max-w-[100%] tw-max-h-[90dvh] tw-overflow-y-hidden tw-p-6 tw-relative tw-border tw-border-gray-300 tw-mx-auto tw-my-[7.5vh]"
            data-action="click->member-management#stopPropagation">
            <button data-action="click->member-management#closeInvite" type="button"
                class="tw-absolute tw-top-4 tw-right-4 tw-text-gray-400 hover:tw-text-black">
                <i class="ri-close-line tw-text-2xl"></i>
            </button>

            <form action="{{ route('brands.members.store', $brand) }}" method="POST">
                @csrf

                <!-- Hidden field để track modal type -->
                <input type="hidden" name="_modal_type" value="invite">

                <!-- Title -->
                <h2 class="tw-text-[20px] tw-font-bold tw-text-gray-900">
                    {{ __('messages.members.invite_title') }}
                </h2>
                <p class="tw-text-sm tw-text-gray-500 tw-mt-1">
                    {{ __('messages.members.invite_desc') }}
                </p>

                <!-- Email -->
                <label class="tw-block tw-font-medium tw-mt-5 tw-mb-1 tw-text-sm">{{ __('messages.members.member_email') }}</label>
                <input type="email" name="email" id="memberEmail" required
                    class="tw-w-full tw-border {{ $errors->has('email') ? 'tw-border-red-500' : 'tw-border-gray-300' }} tw-rounded-xl tw-px-3 tw-py-2 focus:tw-border-vlbcgreen focus:tw-ring-0"
                    placeholder="Email@company.com" value="{{ old('email') }}" />
                @error('email')
                    <span class="tw-text-red-500 tw-text-sm tw-mt-1">{{ $message }}</span>
                @enderror

                <!-- Roles -->
                <p class="tw-font-medium tw-mt-5 tw-mb-2 tw-text-sm">{{ __('messages.members.select_role') }}</p>

                <div class="tw-space-y-3">
                    <!-- Admin -->
                    <label class="tw-flex tw-items-start tw-gap-3 tw-cursor-pointer tw-select-none">
                        <input type="radio" name="role" value="admin"
                            {{ old('role') === 'admin' ? 'checked' : '' }}
                            class="tw-mt-1 tw-h-4 tw-w-4 tw-text-vlbcgreen focus:tw-ring-vlbcgreen"
                            data-action="change->member-management#updatePermissions" />
                        <div>
                            <p class="tw-font-semibold tw-text-gray-800">{{ __('messages.members.admin') }}</p>
                            <p class="tw-text-sm tw-text-gray-500">
                                {{ __('messages.members.admin_desc') }}
                            </p>
                        </div>
                    </label>

                    <!-- Editor -->
                    <label class="tw-flex tw-items-start tw-gap-3 tw-cursor-pointer tw-select-none">
                        <input type="radio" name="role" value="editor"
                            {{ old('role', 'editor') === 'editor' ? 'checked' : '' }}
                            class="tw-mt-1 tw-h-4 tw-w-4 tw-text-vlbcgreen focus:tw-ring-vlbcgreen"
                            data-action="change->member-management#updatePermissions" />
                        <div>
                            <p class="tw-font-semibold tw-text-gray-800">
                                {{ __('messages.members.editor') }}
                            </p>
                            <p class="tw-text-sm tw-text-gray-500">
                                {{ __('messages.members.editor_desc') }}
                            </p>
                        </div>
                    </label>
                </div>

                <!-- Permission Area -->
                <div class="tw-bg-[#F8F9F7] tw-border tw-border-[#E0EAE6] tw-rounded-xl tw-p-4 tw-mt-5">
                    <p class="tw-font-semibold tw-mb-3 tw-text-gray-800 tw-text-sm">
                        {{ __('messages.members.brand_tree_access') }}
                    </p>

                    <div class="tw-space-y-4">
                        <!-- Root -->
                        <div class="tw-flex tw-items-center tw-justify-between">
                            <div class="tw-flex tw-items-center tw-gap-2">
                                <div
                                    class="tw-h-8 tw-w-8 tw-rounded-full tw-bg-[#E6F6EC] tw-text-vlbcgreen tw-flex tw-items-center tw-justify-center">
                                    <i class="ri-seedling-fill"></i>
                                </div>
                                <span class="tw-text-gray-800 tw-font-medium tw-text-sm">{{ __('messages.members.root_foundation') }}</span>
                            </div>
                            <span data-member-management-target="rootPerm" class="tw-text-xs tw-font-medium tw-px-2 tw-py-1 tw-rounded-full tw-bg-green-100 tw-text-green-700">{{ __('messages.members.full_access') }}</span>
                        </div>

                        <!-- Stem -->
                        <div class="tw-flex tw-items-center tw-justify-between">
                            <div class="tw-flex tw-items-center tw-gap-2">
                                <div
                                    class="tw-h-8 tw-w-8 tw-rounded-full tw-bg-[#FFF3D8] tw-text-[#E3A74B] tw-flex tw-items-center tw-justify-center">
                                    <i class="ri-focus-2-line"></i>
                                </div>
                                <span class="tw-text-gray-800 tw-font-medium tw-text-sm">{{ __('messages.members.trunk_strategy') }}</span>
                            </div>
                            <span data-member-management-target="stemPerm" class="tw-text-xs tw-font-medium tw-px-2 tw-py-1 tw-rounded-full tw-bg-green-100 tw-text-green-700">{{ __('messages.members.full_access') }}</span>
                        </div>

                        <!-- Crown -->
                        <div class="tw-flex tw-items-center tw-justify-between">
                            <div class="tw-flex tw-items-center tw-gap-2">
                                <div
                                    class="tw-h-8 tw-w-8 tw-rounded-full tw-bg-[#E6F6EC] tw-text-vlbcgreen tw-flex tw-items-center tw-justify-center">
                                    <i class="ri-tree-line"></i>
                                </div>
                                <span class="tw-text-gray-800 tw-font-medium tw-text-sm">{{ __('messages.members.canopy_execution') }}</span>
                            </div>
                            <span data-member-management-target="crownPerm" class="tw-text-xs tw-font-medium tw-px-2 tw-py-1 tw-rounded-full tw-bg-green-100 tw-text-green-700">{{ __('messages.members.full_access') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Footer buttons -->
                <div class="tw-flex tw-justify-end tw-gap-3 tw-mt-6">
                    <button type="button" data-action="click->member-management#closeInvite"
                        class="tw-bg-gray-100 tw-text-gray-700 tw-rounded-lg tw-px-5 tw-py-2 tw-font-medium hover:tw-bg-gray-200">
                        {{ __('messages.members.cancel') }}
                    </button>

                    <button type="submit"
                        class="tw-bg-vlbcgreen tw-text-white tw-rounded-lg tw-px-5 tw-py-2 tw-font-semibold hover:tw-opacity-90">
                        {{ __('messages.members.add_member') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- ================= EDIT ROLE POPUP ================= -->
    @if($isAdmin)
    <div data-member-management-target="editModal"
        class="tw-hidden tw-fixed tw-inset-0 tw-bg-black/40 tw-backdrop-blur-sm tw-z-[9999]"
        style="display: none;"
        data-action="click->member-management#closeEdit">
        <div class="tw-bg-white tw-rounded-2xl tw-w-[420px] tw-max-w-[90%] tw-p-6 tw-relative tw-border tw-border-gray-300 tw-mx-auto tw-my-[7.5vh]"
            data-action="click->member-management#stopPropagation">
            <button type="button" data-action="click->member-management#closeEdit"
                class="tw-absolute tw-top-4 tw-right-4 tw-text-gray-400 hover:tw-text-black">
                <i class="ri-close-line tw-text-2xl"></i>
            </button>

            <form data-member-management-target="editForm" action="{{ route('brands.members.index', $brand) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Hidden fields để track modal và member ID -->
                <input type="hidden" name="_modal_type" value="edit">
                <input type="hidden" data-member-management-target="editMemberIdInput" name="_member_id" value="">

                <h2 class="tw-text-[20px] tw-font-bold tw-text-gray-900">{{ __('messages.members.change_role') }}</h2>
                <p class="tw-text-sm tw-text-gray-500 tw-mt-1">{{ __('messages.members.change_role_desc') }}</p>

                <div class="tw-mt-5 tw-space-y-3">
                    <label class="tw-flex tw-items-start tw-gap-3 tw-cursor-pointer tw-select-none">
                        <input type="radio" data-member-management-target="editRoleAdmin" name="role" value="admin"
                            class="tw-mt-1 tw-h-4 tw-w-4" />
                        <div>
                            <p class="tw-font-semibold">{{ __('messages.members.admin') }}</p>
                            <p class="tw-text-sm tw-text-gray-500">{{ __('messages.members.admin_full_access') }}</p>
                        </div>
                    </label>

                    <label class="tw-flex tw-items-start tw-gap-3 tw-cursor-pointer tw-select-none">
                        <input type="radio" data-member-management-target="editRoleEditor" name="role" value="editor"
                            class="tw-mt-1 tw-h-4 tw-w-4" />
                        <div>
                            <p class="tw-font-semibold">{{ __('messages.members.editor') }}</p>
                            <p class="tw-text-sm tw-text-gray-500">{{ __('messages.members.editor_full_trunk') }}</p>
                        </div>
                    </label>
                </div>

                @error('role')
                    <p class="tw-text-red-500 tw-text-sm tw-mt-2">{{ $message }}</p>
                @enderror

                <div class="tw-flex tw-justify-end tw-gap-3 tw-mt-6">
                    <button type="button" data-action="click->member-management#closeEdit"
                        class="tw-bg-gray-100 tw-text-gray-700 tw-rounded-lg tw-px-5 tw-py-2 tw-font-medium hover:tw-bg-gray-200">
                        {{ __('messages.members.cancel') }}
                    </button>
                    <button type="submit"
                        class="tw-bg-vlbcgreen tw-text-white tw-rounded-lg tw-px-5 tw-py-2 tw-font-semibold hover:tw-opacity-90">
                        {{ __('messages.members.update') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- ================= DELETE CONFIRMATION POPUP ================= -->
    @if($isAdmin)
    <div data-member-management-target="deleteModal"
        class="tw-hidden tw-fixed tw-inset-0 tw-bg-black/40 tw-backdrop-blur-sm tw-z-[9999]"
        style="display: none;"
        data-action="click->member-management#closeDelete">
        <div class="tw-bg-white tw-rounded-2xl tw-w-[400px] tw-max-w-[90%] tw-p-6 tw-relative tw-border tw-border-gray-300 tw-mx-auto tw-my-[7.5vh]"
            data-action="click->member-management#stopPropagation">
            <div class="tw-text-center">
                <div
                    class="tw-mx-auto tw-flex tw-items-center tw-justify-center tw-h-12 tw-w-12 tw-rounded-full tw-bg-red-100">
                    <i class="ri-error-warning-line tw-text-2xl tw-text-red-600"></i>
                </div>
                <h3 class="tw-mt-4 tw-text-lg tw-font-bold tw-text-gray-900">{{ __('messages.members.remove_member') }}</h3>
                <p class="tw-mt-2 tw-text-sm tw-text-gray-500">
                    {{ __('messages.members.remove_confirm') }} <span data-member-management-target="deleteMemberName"
                        class="tw-font-semibold tw-text-gray-900"></span> {{ __('messages.members.from_brand') }}
                </p>

                <form data-member-management-target="deleteForm" action="{{ route('brands.members.index', $brand) }}" method="POST" class="tw-mt-6">
                    @csrf
                    @method('DELETE')

                    <div class="tw-flex tw-gap-3">
                        <button type="button" data-action="click->member-management#closeDelete"
                            class="tw-flex-1 tw-bg-gray-100 tw-text-gray-700 tw-rounded-lg tw-px-4 tw-py-2 tw-font-medium hover:tw-bg-gray-200">
                            {{ __('messages.members.cancel') }}
                        </button>
                        <button type="submit"
                            class="tw-flex-1 tw-bg-red-600 tw-text-white tw-rounded-lg tw-px-4 tw-py-2 tw-font-semibold hover:tw-bg-red-700">
                            {{ __('messages.members.remove') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
    </div>
</x-app-layout>
