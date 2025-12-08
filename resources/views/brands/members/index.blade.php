<x-app-layout>
    <main class="tw-mt-[36px] tw-flex tw-flex-col tw-gap-5">
        <div class="tw-px-8">
            <nav class="tw-flex tw-mb-4" aria-label="Breadcrumb">
                <ol class="tw-inline-flex tw-items-center tw-space-x-1 md:tw-space-x-3">
                    <li><a href="{{ route('dashboard') }}" class="tw-text-gray-500 hover:tw-text-gray-700">Trang chủ</a>
                    </li>
                    <li class="tw-flex tw-items-center">
                        <svg class="tw-w-4 tw-h-4 tw-text-gray-400 tw-mx-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                clip-rule="evenodd"></path>
                        </svg>
                        <a href="{{ route('brands.show', $brand) }}"
                            class="tw-text-gray-500 hover:tw-text-gray-700">{{ $brand->name }}</a>
                    </li>
                </ol>
            </nav>
            <div class="tw-flex tw-flex-col md:tw-flex-row tw-justify-between tw-mb-3">
                <div>
                    <h1 class="tw-text-[22px] tw-font-bold">Quản lý Thành viên</h1>
                </div>

                <div class="tw-relative tw-mt-2">
                    <button id="inviteMemberBtn"
                        class="tw-relative tw-bg-vlbcgreen tw-text-white tw-pl-10 tw-pr-4 tw-py-2 tw-rounded-lg tw-shadow hover:tw-scale-105 tw-transition">
                        <img src="{{ asset('assets/img/add-vector.svg') }}" alt="add"
                            class="tw-absolute tw-left-3 tw-top-1/2 -tw-translate-y-1/2 tw-w-4 tw-h-4 tw-pointer-events-none" />
                        <span>Mời thành viên</span>
                    </button>
                </div>
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
                    <div>Ngày mời</div>
                    <div>Hành động</div>
                </div>

                <!-- Mobile Responsive Card List -->
                <div class="tw-divide-y tw-divide-[#E0EAE6]">
                    <!-- ROW TEMPLATE -->
                    <!-- Bạn có thể tách ra widget nếu muốn -->
                    <!-- ROW 1 -->
                    <div class="tw-grid md:tw-grid-cols-6 tw-items-center tw-p-4 hover:tw-bg-gray-50">
                        <!-- Mobile Layout -->
                        <div class="md:tw-hidden">
                            <p class="tw-font-bold tw-text-lg">Nguyễn Văn A</p>
                            <p class="tw-text-[#6F7C7A]">nguyenvana@company.com</p>
                        </div>

                        <!-- Desktop Columns -->
                        <div class="tw-font-medium tw-hidden md:tw-block">
                            Nguyễn Văn A
                        </div>
                        <div class="tw-text-[#6F7C7A] tw-hidden md:tw-block">
                            nguyenvana@company.com
                        </div>

                        <div>
                            <span
                                class="tw-text-[12px] tw-bg-[#E6F6EC] tw-text-[#1AA24C] tw-font-semibold tw-px-3 tw-py-1 tw-rounded-full">
                                Quản trị viên</span>
                        </div>

                        <div>
                            <span
                                class="tw-text-[12px] tw-bg-[#E6F6EC] tw-text-[#1AA24C] tw-font-semibold tw-px-3 tw-py-1 tw-rounded-full">
                                Đang hoạt động</span>
                        </div>

                        <div class="tw-text-[#6F7C7A] tw-hidden md:tw-block">
                            15/01/2025
                        </div>

                        <div class="tw-flex tw-items-center tw-gap-4">
                            <button class="tw-flex tw-items-center tw-gap-1 tw-font-semibold hover:tw-text-[#1AA24C]">
                                <i class="ri-edit-line"></i> Sửa
                            </button>
                            <button class="tw-flex tw-items-center tw-gap-1 tw-font-semibold hover:tw-text-red-500">
                                <i class="ri-user-unfollow-line"></i> Gỡ bỏ
                            </button>
                        </div>
                    </div>
                    <!-- Các ROW 2–4 giữ nguyên như bạn đang dùng -->
                </div>
            </div>
        </section>
    </main>
    <div id="inviteMemberOverlay"
        class="tw-fixed tw-inset-0 tw-bg-black/40 tw-backdrop-blur-sm tw-flex tw-items-center tw-justify-center tw-z-[9999] tw-hidden">
        <!-- POPUP WRAPPER -->
        <div
            class="tw-bg-white tw-rounded-2xl tw-w-[420px] tw-max-w-[90%] tw-max-h-[85dvh] tw-overflow-y-auto tw-p-6 tw-relative tw-border tw-border-gray-300">
            <button id="inviteMemberClose" class="tw-absolute tw-top-4 tw-right-4 tw-text-gray-400 hover:tw-text-black">
                <i class="ri-close-line tw-text-2xl"></i>
            </button>

            <!-- Title -->
            <h2 class="tw-text-[20px] tw-font-bold tw-text-gray-900">
                Mời thành viên mới
            </h2>
            <p class="tw-text-sm tw-text-gray-500 tw-mt-1">
                Chọn vai trò phù hợp để phân quyền truy cập và tương tác với AI Agents
            </p>

            <!-- Email -->
            <label class="tw-block tw-font-medium tw-mt-5 tw-mb-1 tw-text-sm">Email thành viên</label>
            <input type="email"
                class="tw-w-full tw-border tw-border-gray-300 tw-rounded-xl tw-px-3 tw-py-2 focus:tw-border-vlbcgreen focus:tw-ring-0"
                placeholder="Email@company.com" />

            <!-- Roles -->
            <p class="tw-font-medium tw-mt-5 tw-mb-2 tw-text-sm">Chọn vai trò</p>

            <div class="tw-space-y-3">
                <!-- Admin -->
                <label class="tw-flex tw-items-start tw-gap-3 tw-cursor-pointer tw-select-none">
                    <input type="radio" name="role"
                        class="tw-mt-1 tw-h-4 tw-w-4 tw-text-vlbcgreen focus:tw-ring-vlbcgreen" />
                    <div>
                        <p class="tw-font-semibold tw-text-gray-800">Quản trị viên</p>
                        <p class="tw-text-sm tw-text-gray-500">
                            Toàn quyền truy cập tất cả các giai đoạn và với AI Agents
                        </p>
                    </div>
                </label>

                <!-- Executor -->
                <label class="tw-flex tw-items-start tw-gap-3 tw-cursor-pointer tw-select-none">
                    <input type="radio" name="role" checked
                        class="tw-mt-1 tw-h-4 tw-w-4 tw-text-vlbcgreen focus:tw-ring-vlbcgreen" />
                    <div>
                        <p class="tw-font-semibold tw-text-gray-800">
                            Nhà Thực thi / Marketing
                        </p>
                        <p class="tw-text-sm tw-text-gray-500">
                            Toàn quyền Thân Cây, chỉ xem Gốc và Tán Cây
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
                        <button
                            class="tw-text-sm tw-border tw-border-gray-400 tw-rounded-full tw-px-4 tw-py-1 tw-text-gray-700">
                            Chỉ xem
                        </button>
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
                        <button
                            class="tw-text-sm tw-border tw-border-gray-400 tw-rounded-full tw-px-4 tw-py-1 tw-text-gray-700">
                            Chỉ xem
                        </button>
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

                        <button
                            class="tw-text-sm tw-rounded-full tw-px-4 tw-py-1 tw-bg-vlbcgreen tw-text-white tw-font-medium">
                            Toàn quyền
                        </button>
                    </div>
                </div>
            </div>

            <!-- Footer buttons -->
            <div class="tw-flex tw-justify-end tw-gap-3 tw-mt-6">
                <button id="inviteCancel"
                    class="tw-bg-gray-100 tw-text-gray-700 tw-rounded-lg tw-px-5 tw-py-2 tw-font-medium hover:tw-bg-gray-200">
                    Hủy
                </button>

                <button
                    class="tw-bg-vlbcgreen tw-text-white tw-rounded-lg tw-px-5 tw-py-2 tw-font-semibold hover:tw-opacity-90">
                    Gửi lời mời
                </button>
            </div>
        </div>

    </div>
</x-app-layout>
