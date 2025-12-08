<x-app-layout>
    <style>
        [x-cloak] { display: none !important; }
    </style>
    <div x-data="{
        inviteModal: false,
        editModal: false,
        deleteModal: false,
        currentRole: 'editor',
        editMemberId: null,
        editMemberRole: '',
        deleteMemberId: null,
        deleteMemberName: '',

        openInvite() {
            this.inviteModal = true;
            this.currentRole = 'editor';
        },

        openEdit(memberId, role) {
            this.editMemberId = memberId;
            this.editMemberRole = role;
            this.editModal = true;
        },

        openDelete(memberId, name) {
            this.deleteMemberId = memberId;
            this.deleteMemberName = name;
            this.deleteModal = true;
        },

        updatePermissionDisplay(role) {
            this.currentRole = role;
        },

        getPermClass(part, role) {
            // Admin: Toàn quyền tất cả
            if (role === 'admin') {
                return 'tw-text-sm tw-rounded-full tw-px-4 tw-py-1 tw-bg-vlbcgreen tw-text-white tw-font-medium';
            }
            // Editor: Toàn quyền Tán Cây (crown), chỉ xem Gốc (root) và Thân (stem)
            else if (role === 'editor' && part === 'crown') {
                return 'tw-text-sm tw-rounded-full tw-px-4 tw-py-1 tw-bg-vlbcgreen tw-text-white tw-font-medium';
            }
            return 'tw-text-sm tw-border tw-border-gray-400 tw-rounded-full tw-px-4 tw-py-1 tw-text-gray-700';
        },

        getPermText(part, role) {
            // Admin: Toàn quyền tất cả
            if (role === 'admin') {
                return 'Toàn quyền';
            }
            // Editor: Toàn quyền Tán Cây (crown), chỉ xem Gốc (root) và Thân (stem)
            else if (role === 'editor' && part === 'crown') {
                return 'Toàn quyền';
            }
            return 'Chỉ xem';
        }
    }">
    <main class="tw-mt-[36px] tw-flex tw-flex-col tw-gap-5">
        <div class="tw-px-8">
            <x-breadcrumb :items="[
                ['label' => 'Trang chủ', 'url' => route('dashboard')],
                ['label' => $brand->name, 'url' => route('brands.show', $brand)]
            ]" />

            <div class="tw-flex tw-flex-col md:tw-flex-row tw-justify-between tw-mb-3">
                <div>
                    <h1 class="tw-text-[22px] tw-font-bold">Quản lý Thành viên</h1>
                </div>

                @if($isAdmin)
                <div class="tw-relative tw-mt-2">
                    <button @click="openInvite()"
                        class="tw-relative tw-bg-vlbcgreen tw-text-white tw-pl-10 tw-pr-4 tw-py-2 tw-rounded-lg tw-shadow hover:tw-scale-105 tw-transition">
                        <img src="{{ asset('assets/img/add-vector.svg') }}" alt="add"
                            class="tw-absolute tw-left-3 tw-top-1/2 -tw-translate-y-1/2 tw-w-4 tw-h-4 tw-pointer-events-none" />
                        <span>Thêm thành viên</span>
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
                    <div>Tên thành viên</div>
                    <div>Email</div>
                    <div>Vai trò</div>
                    <div>Trạng thái</div>
                    <div>Ngày tham gia</div>
                    <div>Hành động</div>
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
                                Quản trị viên</span>
                            @elseif($member->role === 'editor')
                            <span
                                class="tw-text-[12px] tw-bg-[#FFF3D8] tw-text-[#E3A74B] tw-font-semibold tw-px-3 tw-py-1 tw-rounded-full">
                                Nhà thực thi / Marketing</span>
                            @else
                            <span
                                class="tw-text-[12px] tw-bg-gray-100 tw-text-gray-700 tw-font-semibold tw-px-3 tw-py-1 tw-rounded-full">
                                Thành viên</span>
                            @endif
                        </div>

                        <!-- Status -->
                        <div>
                            <span
                                class="tw-text-[12px] tw-bg-[#E6F6EC] tw-text-[#1AA24C] tw-font-semibold tw-px-3 tw-py-1 tw-rounded-full">
                                Đang hoạt động</span>
                        </div>

                        <!-- Joined Date -->
                        <div class="tw-text-[#6F7C7A] tw-hidden md:tw-block">
                            {{ $member->joined_at ? $member->joined_at->format('d/m/Y') : '-' }}
                        </div>

                        <!-- Actions -->
                        <div class="tw-flex tw-items-center tw-gap-4">
                            @if($isAdmin && $member->user_id !== auth()->id() && $member->user_id !== $brand->created_by)
                            <button @click="openEdit({{ $member->id }}, '{{ $member->role }}')"
                                class="tw-flex tw-items-center tw-gap-1 tw-font-semibold hover:tw-text-[#1AA24C]">
                                <i class="ri-edit-line"></i> Sửa
                            </button>
                            <button @click="openDelete({{ $member->id }}, '{{ addslashes($member->user->name) }}')"
                                class="tw-flex tw-items-center tw-gap-1 tw-font-semibold hover:tw-text-red-500">
                                <i class="ri-user-unfollow-line"></i> Gỡ bỏ
                            </button>
                            @else
                            <span class="tw-text-[#6F7C7A] tw-text-sm">-</span>
                            @endif
                        </div>
                    </div>
                    @empty
                    <div class="tw-p-8 tw-text-center tw-text-gray-500">
                        <i class="ri-user-line tw-text-4xl tw-mb-2"></i>
                        <p>Chưa có thành viên nào</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </section>
    </main>

    <!-- ================= INVITE MEMBER POPUP ================= -->
    @if($isAdmin)
    <div x-show="inviteModal" x-cloak
        @click.self="inviteModal = false"
        class="tw-fixed tw-inset-0 tw-bg-black/40 tw-backdrop-blur-sm tw-flex tw-items-center tw-justify-center tw-z-[9999]"
        x-transition:enter="tw-transition tw-ease-out tw-duration-200"
        x-transition:enter-start="tw-opacity-0"
        x-transition:enter-end="tw-opacity-100"
        x-transition:leave="tw-transition tw-ease-in tw-duration-150"
        x-transition:leave-start="tw-opacity-100"
        x-transition:leave-end="tw-opacity-0">
        <div
            class="tw-bg-white tw-rounded-2xl tw-w-[420px] tw-max-w-[90%] tw-max-h-[85dvh] tw-overflow-y-auto tw-p-6 tw-relative tw-border tw-border-gray-300"
            x-transition:enter="tw-transition tw-ease-out tw-duration-200 tw-transform"
            x-transition:enter-start="tw-opacity-0 tw-scale-95"
            x-transition:enter-end="tw-opacity-100 tw-scale-100"
            x-transition:leave="tw-transition tw-ease-in tw-duration-150 tw-transform"
            x-transition:leave-start="tw-opacity-100 tw-scale-100"
            x-transition:leave-end="tw-opacity-0 tw-scale-95"
            @click.away="inviteModal = false">
            <button @click="inviteModal = false" type="button"
                class="tw-absolute tw-top-4 tw-right-4 tw-text-gray-400 hover:tw-text-black">
                <i class="ri-close-line tw-text-2xl"></i>
            </button>

            <form action="{{ route('brands.members.store', $brand) }}" method="POST">
                @csrf

                <!-- Title -->
                <h2 class="tw-text-[20px] tw-font-bold tw-text-gray-900">
                    Mời thành viên mới
                </h2>
                <p class="tw-text-sm tw-text-gray-500 tw-mt-1">
                    Chọn vai trò phù hợp để phân quyền truy cập và tương tác với AI Agents
                </p>

                <!-- Email -->
                <label class="tw-block tw-font-medium tw-mt-5 tw-mb-1 tw-text-sm">Email thành viên</label>
                <input type="email" name="email" id="memberEmail" required
                    class="tw-w-full tw-border tw-border-gray-300 tw-rounded-xl tw-px-3 tw-py-2 focus:tw-border-vlbcgreen focus:tw-ring-0"
                    placeholder="Email@company.com" value="{{ old('email') }}" />
                <span id="emailError" class="tw-text-red-500 tw-text-sm tw-mt-1 tw-hidden"></span>

                <!-- Roles -->
                <p class="tw-font-medium tw-mt-5 tw-mb-2 tw-text-sm">Chọn vai trò</p>

                <div class="tw-space-y-3">
                    <!-- Admin -->
                    <label class="tw-flex tw-items-start tw-gap-3 tw-cursor-pointer tw-select-none">
                        <input type="radio" name="role" value="admin"
                            class="tw-mt-1 tw-h-4 tw-w-4 tw-text-vlbcgreen focus:tw-ring-vlbcgreen"
                            @change="updatePermissionDisplay('admin')" />
                        <div>
                            <p class="tw-font-semibold tw-text-gray-800">Quản trị viên</p>
                            <p class="tw-text-sm tw-text-gray-500">
                                Toàn quyền truy cập tất cả các giai đoạn và với AI Agents
                            </p>
                        </div>
                    </label>

                    <!-- Editor -->
                    <label class="tw-flex tw-items-start tw-gap-3 tw-cursor-pointer tw-select-none">
                        <input type="radio" name="role" value="editor" checked
                            class="tw-mt-1 tw-h-4 tw-w-4 tw-text-vlbcgreen focus:tw-ring-vlbcgreen"
                            @change="updatePermissionDisplay('editor')" />
                        <div>
                            <p class="tw-font-semibold tw-text-gray-800">
                                Nhà Thực thi / Marketing
                            </p>
                            <p class="tw-text-sm tw-text-gray-500">
                                Toàn quyền Tán Cây, chỉ xem Gốc và Thân Cây
                            </p>
                        </div>
                    </label>
                </div>

                <!-- Permission Area -->
                <div class="tw-bg-[#F8F9F7] tw-border tw-border-[#E0EAE6] tw-rounded-xl tw-p-4 tw-mt-5">
                    <p class="tw-font-semibold tw-mb-3 tw-text-gray-800 tw-text-sm">
                        Quyền truy cập Cây Thương hiệu
                    </p>

                    <div class="tw-space-y-4">
                        <!-- Root -->
                        <div class="tw-flex tw-items-center tw-justify-between">
                            <div class="tw-flex tw-items-center tw-gap-2">
                                <div
                                    class="tw-h-8 tw-w-8 tw-rounded-full tw-bg-[#E6F6EC] tw-text-vlbcgreen tw-flex tw-items-center tw-justify-center">
                                    <i class="ri-seedling-fill"></i>
                                </div>
                                <span class="tw-text-gray-800 tw-font-medium tw-text-sm">Gốc Cây (Nền tảng)</span>
                            </div>
                            <span :class="getPermClass('root', currentRole)" x-text="getPermText('root', currentRole)"></span>
                        </div>

                        <!-- Stem -->
                        <div class="tw-flex tw-items-center tw-justify-between">
                            <div class="tw-flex tw-items-center tw-gap-2">
                                <div
                                    class="tw-h-8 tw-w-8 tw-rounded-full tw-bg-[#FFF3D8] tw-text-[#E3A74B] tw-flex tw-items-center tw-justify-center">
                                    <i class="ri-focus-2-line"></i>
                                </div>
                                <span class="tw-text-gray-800 tw-font-medium tw-text-sm">Thân Cây (Chiến lược)</span>
                            </div>
                            <span :class="getPermClass('stem', currentRole)" x-text="getPermText('stem', currentRole)"></span>
                        </div>

                        <!-- Crown -->
                        <div class="tw-flex tw-items-center tw-justify-between">
                            <div class="tw-flex tw-items-center tw-gap-2">
                                <div
                                    class="tw-h-8 tw-w-8 tw-rounded-full tw-bg-[#E6F6EC] tw-text-vlbcgreen tw-flex tw-items-center tw-justify-center">
                                    <i class="ri-tree-line"></i>
                                </div>
                                <span class="tw-text-gray-800 tw-font-medium tw-text-sm">Tán Cây (Triển khai)</span>
                            </div>
                            <span :class="getPermClass('crown', currentRole)" x-text="getPermText('crown', currentRole)"></span>
                        </div>
                    </div>
                </div>

                <!-- Footer buttons -->
                <div class="tw-flex tw-justify-end tw-gap-3 tw-mt-6">
                    <button type="button" @click="inviteModal = false"
                        class="tw-bg-gray-100 tw-text-gray-700 tw-rounded-lg tw-px-5 tw-py-2 tw-font-medium hover:tw-bg-gray-200">
                        Hủy
                    </button>

                    <button type="submit"
                        class="tw-bg-vlbcgreen tw-text-white tw-rounded-lg tw-px-5 tw-py-2 tw-font-semibold hover:tw-opacity-90">
                        Thêm thành viên
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- ================= EDIT ROLE POPUP ================= -->
    @if($isAdmin)
    <div x-show="editModal" x-cloak
        @click.self="editModal = false"
        class="tw-fixed tw-inset-0 tw-bg-black/40 tw-backdrop-blur-sm tw-flex tw-items-center tw-justify-center tw-z-[9999]"
        x-transition>
        <div
            class="tw-bg-white tw-rounded-2xl tw-w-[420px] tw-max-w-[90%] tw-p-6 tw-relative tw-border tw-border-gray-300"
            @click.away="editModal = false"
            x-transition:enter="tw-transition tw-ease-out tw-duration-200 tw-transform"
            x-transition:enter-start="tw-opacity-0 tw-scale-95"
            x-transition:enter-end="tw-opacity-100 tw-scale-100">
            <button type="button" @click="editModal = false"
                class="tw-absolute tw-top-4 tw-right-4 tw-text-gray-400 hover:tw-text-black">
                <i class="ri-close-line tw-text-2xl"></i>
            </button>

            <form :action="`{{ route('brands.members.index', $brand) }}/${editMemberId}`" method="POST">
                @csrf
                @method('PUT')

                <h2 class="tw-text-[20px] tw-font-bold tw-text-gray-900">Thay đổi vai trò</h2>
                <p class="tw-text-sm tw-text-gray-500 tw-mt-1">Chọn vai trò mới cho thành viên</p>

                <div class="tw-mt-5 tw-space-y-3">
                    <label class="tw-flex tw-items-start tw-gap-3 tw-cursor-pointer tw-select-none">
                        <input type="radio" name="role" value="admin"
                            :checked="editMemberRole === 'admin'"
                            class="tw-mt-1 tw-h-4 tw-w-4" />
                        <div>
                            <p class="tw-font-semibold">Quản trị viên</p>
                            <p class="tw-text-sm tw-text-gray-500">Toàn quyền truy cập</p>
                        </div>
                    </label>

                    <label class="tw-flex tw-items-start tw-gap-3 tw-cursor-pointer tw-select-none">
                        <input type="radio" name="role" value="editor"
                            :checked="editMemberRole === 'editor'"
                            class="tw-mt-1 tw-h-4 tw-w-4" />
                        <div>
                            <p class="tw-font-semibold">Nhà thực thi / Marketing</p>
                            <p class="tw-text-sm tw-text-gray-500">Toàn quyền Thân Cây</p>
                        </div>
                    </label>
                </div>

                <div class="tw-flex tw-justify-end tw-gap-3 tw-mt-6">
                    <button type="button" @click="editModal = false"
                        class="tw-bg-gray-100 tw-text-gray-700 tw-rounded-lg tw-px-5 tw-py-2 tw-font-medium hover:tw-bg-gray-200">
                        Hủy
                    </button>
                    <button type="submit"
                        class="tw-bg-vlbcgreen tw-text-white tw-rounded-lg tw-px-5 tw-py-2 tw-font-semibold hover:tw-opacity-90">
                        Cập nhật
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- ================= DELETE CONFIRMATION POPUP ================= -->
    @if($isAdmin)
    <div x-show="deleteModal" x-cloak
        @click.self="deleteModal = false"
        class="tw-fixed tw-inset-0 tw-bg-black/40 tw-backdrop-blur-sm tw-flex tw-items-center tw-justify-center tw-z-[9999]"
        x-transition>
        <div
            class="tw-bg-white tw-rounded-2xl tw-w-[400px] tw-max-w-[90%] tw-p-6 tw-relative tw-border tw-border-gray-300"
            @click.away="deleteModal = false"
            x-transition:enter="tw-transition tw-ease-out tw-duration-200 tw-transform"
            x-transition:enter-start="tw-opacity-0 tw-scale-95"
            x-transition:enter-end="tw-opacity-100 tw-scale-100">
            <div class="tw-text-center">
                <div
                    class="tw-mx-auto tw-flex tw-items-center tw-justify-center tw-h-12 tw-w-12 tw-rounded-full tw-bg-red-100">
                    <i class="ri-error-warning-line tw-text-2xl tw-text-red-600"></i>
                </div>
                <h3 class="tw-mt-4 tw-text-lg tw-font-bold tw-text-gray-900">Gỡ bỏ thành viên</h3>
                <p class="tw-mt-2 tw-text-sm tw-text-gray-500">
                    Bạn có chắc chắn muốn gỡ bỏ <span x-text="deleteMemberName"
                        class="tw-font-semibold tw-text-gray-900"></span> khỏi thương hiệu?
                </p>

                <form :action="`{{ route('brands.members.index', $brand) }}/${deleteMemberId}`" method="POST" class="tw-mt-6">
                    @csrf
                    @method('DELETE')

                    <div class="tw-flex tw-gap-3">
                        <button type="button" @click="deleteModal = false"
                            class="tw-flex-1 tw-bg-gray-100 tw-text-gray-700 tw-rounded-lg tw-px-4 tw-py-2 tw-font-medium hover:tw-bg-gray-200">
                            Hủy
                        </button>
                        <button type="submit"
                            class="tw-flex-1 tw-bg-red-600 tw-text-white tw-rounded-lg tw-px-4 tw-py-2 tw-font-semibold hover:tw-bg-red-700">
                            Gỡ bỏ
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
    </div>
</x-app-layout>
