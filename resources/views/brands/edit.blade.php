<x-app-layout>
    <x-slot name="header">
        <div class="tw-flex tw-items-center tw-gap-4">
            <a href="{{ route('brands.show', $brand) }}" class="tw-text-gray-500 hover:tw-text-gray-700 tw-transition">
                <svg class="tw-w-6 tw-h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
            <h2 class="tw-text-2xl md:tw-text-3xl tw-font-bold tw-text-gray-800">
                Chỉnh sửa thương hiệu
            </h2>
        </div>
    </x-slot>

    <div class="tw-bg-white tw-rounded-2xl tw-shadow-sm tw-p-6 md:tw-p-8">
        <form action="{{ route('brands.update', $brand) }}" method="POST" enctype="multipart/form-data" class="tw-max-w-2xl">
            @csrf
            @method('PUT')

            <!-- Logo -->
            <div class="tw-mb-6">
                <label class="tw-block tw-text-sm tw-font-medium tw-text-gray-700 tw-mb-2">Logo</label>
                <div class="tw-flex tw-items-center tw-gap-4">
                    <div id="logo-preview" class="tw-w-20 tw-h-20 tw-bg-gray-100 tw-rounded-full tw-flex tw-items-center tw-justify-center tw-overflow-hidden">
                        @if($brand->logo_path)
                            <img src="{{ asset('storage/' . $brand->logo_path) }}" class="tw-w-full tw-h-full tw-object-cover">
                        @else
                            <svg class="tw-w-8 tw-h-8 tw-text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        @endif
                    </div>
                    <div>
                        <input type="file" name="logo" id="logo" accept="image/*" class="tw-hidden" onchange="previewLogo(this)">
                        <label for="logo" class="tw-cursor-pointer tw-inline-flex tw-items-center tw-px-4 tw-py-2 tw-border tw-border-gray-300 tw-rounded-lg tw-text-sm tw-font-medium tw-text-gray-700 hover:tw-bg-gray-50 tw-transition">
                            Đổi ảnh
                        </label>
                        <p class="tw-text-xs tw-text-gray-500 tw-mt-1">PNG, JPG, GIF tối đa 2MB</p>
                    </div>
                </div>
                @error('logo')
                    <p class="tw-text-red-500 tw-text-sm tw-mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Name -->
            <div class="tw-mb-6">
                <label for="name" class="tw-block tw-text-sm tw-font-medium tw-text-gray-700 tw-mb-2">Tên thương hiệu <span class="tw-text-red-500">*</span></label>
                <input type="text" name="name" id="name" value="{{ old('name', $brand->name) }}" required
                       class="tw-w-full tw-px-4 tw-py-3 tw-border tw-border-gray-300 tw-rounded-lg focus:tw-ring-2 focus:tw-ring-[#16a249] focus:tw-border-transparent tw-transition">
                @error('name')
                    <p class="tw-text-red-500 tw-text-sm tw-mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Industry -->
            <div class="tw-mb-6">
                <label for="industry" class="tw-block tw-text-sm tw-font-medium tw-text-gray-700 tw-mb-2">Ngành nghề</label>
                <input type="text" name="industry" id="industry" value="{{ old('industry', $brand->industry) }}"
                       class="tw-w-full tw-px-4 tw-py-3 tw-border tw-border-gray-300 tw-rounded-lg focus:tw-ring-2 focus:tw-ring-[#16a249] focus:tw-border-transparent tw-transition">
                @error('industry')
                    <p class="tw-text-red-500 tw-text-sm tw-mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Target Market -->
            <div class="tw-mb-6">
                <label for="target_market" class="tw-block tw-text-sm tw-font-medium tw-text-gray-700 tw-mb-2">Thị trường mục tiêu</label>
                <input type="text" name="target_market" id="target_market" value="{{ old('target_market', $brand->target_market) }}"
                       class="tw-w-full tw-px-4 tw-py-3 tw-border tw-border-gray-300 tw-rounded-lg focus:tw-ring-2 focus:tw-ring-[#16a249] focus:tw-border-transparent tw-transition">
                @error('target_market')
                    <p class="tw-text-red-500 tw-text-sm tw-mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Founded Year -->
            <div class="tw-mb-6">
                <label for="founded_year" class="tw-block tw-text-sm tw-font-medium tw-text-gray-700 tw-mb-2">Năm thành lập</label>
                <input type="number" name="founded_year" id="founded_year" value="{{ old('founded_year', $brand->founded_year) }}"
                       min="1900" max="{{ date('Y') }}"
                       class="tw-w-full tw-px-4 tw-py-3 tw-border tw-border-gray-300 tw-rounded-lg focus:tw-ring-2 focus:tw-ring-[#16a249] focus:tw-border-transparent tw-transition">
                @error('founded_year')
                    <p class="tw-text-red-500 tw-text-sm tw-mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Description -->
            <div class="tw-mb-6">
                <label for="description" class="tw-block tw-text-sm tw-font-medium tw-text-gray-700 tw-mb-2">Mô tả</label>
                <textarea name="description" id="description" rows="4"
                          class="tw-w-full tw-px-4 tw-py-3 tw-border tw-border-gray-300 tw-rounded-lg focus:tw-ring-2 focus:tw-ring-[#16a249] focus:tw-border-transparent tw-transition tw-resize-none">{{ old('description', $brand->description) }}</textarea>
                @error('description')
                    <p class="tw-text-red-500 tw-text-sm tw-mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit -->
            <div class="tw-flex tw-items-center tw-gap-4">
                <button type="submit" class="tw-px-6 tw-py-3 tw-bg-[#16a249] tw-text-white tw-rounded-lg tw-font-medium hover:tw-bg-[#138a3e] tw-transition">
                    Lưu thay đổi
                </button>
                <a href="{{ route('brands.show', $brand) }}" class="tw-px-6 tw-py-3 tw-border tw-border-gray-300 tw-text-gray-700 tw-rounded-lg tw-font-medium hover:tw-bg-gray-50 tw-transition">
                    Hủy
                </a>
            </div>
        </form>
    </div>

    <!-- Delete Section -->
    @can('delete', $brand)
        <div class="tw-bg-white tw-rounded-2xl tw-shadow-sm tw-p-6 md:tw-p-8 tw-mt-6">
            <h3 class="tw-text-lg tw-font-semibold tw-text-red-600 tw-mb-2">Xóa thương hiệu</h3>
            <p class="tw-text-gray-600 tw-mb-4">Khi bạn xóa thương hiệu, tất cả dữ liệu liên quan sẽ bị xóa vĩnh viễn.</p>
            <form action="{{ route('brands.destroy', $brand) }}" method="POST" onsubmit="return confirm('Bạn có chắc muốn xóa thương hiệu này?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="tw-px-6 tw-py-3 tw-bg-red-600 tw-text-white tw-rounded-lg tw-font-medium hover:tw-bg-red-700 tw-transition">
                    Xóa thương hiệu
                </button>
            </form>
        </div>
    @endcan

    <script>
        function previewLogo(input) {
            const preview = document.getElementById('logo-preview');
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.innerHTML = `<img src="${e.target.result}" class="tw-w-full tw-h-full tw-object-cover">`;
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</x-app-layout>

