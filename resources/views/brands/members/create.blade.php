<x-app-layout>
    <!-- Header -->
    <div class="tw-mb-6">
        <nav class="tw-flex tw-mb-4" aria-label="Breadcrumb">
            <ol class="tw-inline-flex tw-items-center tw-space-x-1 md:tw-space-x-3">
                <li><a href="{{ route('dashboard') }}" class="tw-text-gray-500 hover:tw-text-gray-700">Dashboard</a></li>
                <li class="tw-flex tw-items-center">
                    <svg class="tw-w-4 tw-h-4 tw-text-gray-400 tw-mx-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                    <a href="{{ route('brands.show', $brand) }}" class="tw-text-gray-500 hover:tw-text-gray-700">{{ $brand->name }}</a>
                </li>
                <li class="tw-flex tw-items-center">
                    <svg class="tw-w-4 tw-h-4 tw-text-gray-400 tw-mx-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                    <a href="{{ route('brands.members.index', $brand) }}" class="tw-text-gray-500 hover:tw-text-gray-700">Thành viên</a>
                </li>
                <li class="tw-flex tw-items-center">
                    <svg class="tw-w-4 tw-h-4 tw-text-gray-400 tw-mx-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                    <span class="tw-text-gray-700">Thêm thành viên</span>
                </li>
            </ol>
        </nav>
        <h1 class="tw-text-2xl tw-font-bold tw-text-gray-800">Thêm thành viên</h1>
    </div>

    <!-- Form -->
    <div class="tw-bg-white tw-rounded-2xl tw-shadow-sm tw-p-6">
        <form action="{{ route('brands.members.store', $brand) }}" method="POST">
            @csrf

            <!-- Email -->
            <div class="tw-mb-6">
                <label for="email" class="tw-block tw-text-sm tw-font-medium tw-text-gray-700 tw-mb-2">Email người dùng <span class="tw-text-red-500">*</span></label>
                <input type="email" id="email" name="email" value="{{ old('email') }}"
                       class="tw-w-full tw-px-4 tw-py-3 tw-border tw-border-gray-300 tw-rounded-lg focus:tw-ring-2 focus:tw-ring-[#16a249] focus:tw-border-transparent tw-transition @error('email') tw-border-red-500 @enderror"
                       placeholder="email@example.com" required>
                @error('email')
                    <p class="tw-mt-1 tw-text-sm tw-text-red-500">{{ $message }}</p>
                @enderror
                <p class="tw-mt-1 tw-text-sm tw-text-gray-500">Người dùng cần có tài khoản trong hệ thống.</p>
            </div>

            <!-- Role -->
            <div class="tw-mb-6">
                <label for="role" class="tw-block tw-text-sm tw-font-medium tw-text-gray-700 tw-mb-2">Quyền hạn <span class="tw-text-red-500">*</span></label>
                <select id="role" name="role"
                        class="tw-w-full tw-px-4 tw-py-3 tw-border tw-border-gray-300 tw-rounded-lg focus:tw-ring-2 focus:tw-ring-[#16a249] focus:tw-border-transparent tw-transition @error('role') tw-border-red-500 @enderror"
                        required>
                    <option value="member" {{ old('role') === 'member' ? 'selected' : '' }}>Thành viên - Xem và sử dụng thương hiệu</option>
                    <option value="editor" {{ old('role') === 'editor' ? 'selected' : '' }}>Biên tập - Chỉnh sửa nội dung thương hiệu</option>
                    {{-- <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin - Quản lý toàn bộ</option> --}}
                </select>
                @error('role')
                    <p class="tw-mt-1 tw-text-sm tw-text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Role Description -->
            <div class="tw-mb-6 tw-bg-gray-50 tw-rounded-lg tw-p-4">
                <h4 class="tw-font-medium tw-text-gray-800 tw-mb-3">Mô tả quyền hạn:</h4>
                <ul class="tw-space-y-2 tw-text-sm tw-text-gray-600">
                    {{-- <li class="tw-flex tw-items-start tw-gap-2">
                        <span class="tw-px-2 tw-py-0.5 tw-bg-purple-100 tw-text-purple-700 tw-rounded tw-font-medium tw-text-xs">Admin</span>
                        <span>Quản lý thành viên, chỉnh sửa thông tin thương hiệu, xem và sử dụng tất cả tính năng</span>
                    </li> --}}
                    <li class="tw-flex tw-items-start tw-gap-2">
                        <span class="tw-px-2 tw-py-0.5 tw-bg-blue-100 tw-text-blue-700 tw-rounded tw-font-medium tw-text-xs">Editor</span>
                        <span>Chỉnh sửa nội dung, sử dụng credits, không thể quản lý thành viên</span>
                    </li>
                    <li class="tw-flex tw-items-start tw-gap-2">
                        <span class="tw-px-2 tw-py-0.5 tw-bg-gray-100 tw-text-gray-700 tw-rounded tw-font-medium tw-text-xs">Member</span>
                        <span>Xem thông tin, sử dụng credits cơ bản</span>
                    </li>
                </ul>
            </div>

            <!-- Buttons -->
            <div class="tw-flex tw-items-center tw-gap-4">
                <button type="submit" class="tw-px-6 tw-py-3 tw-bg-[#16a249] tw-text-white tw-rounded-lg tw-font-medium hover:tw-bg-[#138a3e] tw-transition">
                    Thêm thành viên
                </button>
                <a href="{{ route('brands.members.index', $brand) }}" class="tw-px-6 tw-py-3 tw-border tw-border-gray-300 tw-text-gray-700 tw-rounded-lg tw-font-medium hover:tw-bg-gray-50 tw-transition">
                    Hủy bỏ
                </a>
            </div>
        </form>
    </div>
</x-app-layout>
