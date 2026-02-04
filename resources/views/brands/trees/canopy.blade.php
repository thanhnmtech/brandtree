<x-app-layout>
  <div class="tw-w-full tw-max-w-7xl tw-mx-auto tw-mt-6 tw-px-4">

    <!-- Main Content -->
    <main class="tw-p-4" x-data="{ openCreateModal: false, openTemplateModal: false, openEditModal: false }">

      <!-- Title -->
      <div class="tw-text-center tw-mt-4">
        <h1 class="tw-text-3xl tw-font-bold tw-text-gray-800">
          Bản Đồ Hành Trình <span class="tw-text-vlbcgreen">Tán Cây</span>
        </h1>
        <p class="tw-text-gray-600 tw-mt-2">
          AI sẽ đánh giá mức độ sẵn sàng và đề xuất chiến lược truyền thông, quảng cáo.
        </p>
      </div>

      <!-- Completion Status -->
      <div class="tw-mx-auto tw-mt-8 tw-flex tw-justify-center">
        <div
          class="tw-w-40 tw-h-40 tw-rounded-full tw-border-8 tw-border-green-300 tw-flex tw-items-center tw-justify-center">
          <span class="tw-text-3xl tw-font-bold tw-text-vlbcgreen">53%</span>
        </div>
      </div>

      <!-- Brand Garden -->
      <section class="tw-mt-12">
        <h2 class="tw-text-2xl tw-font-bold">{{ $brand->name }} Brand Garden</h2>
        <p class="tw-text-gray-600 tw-mt-1">Đang triển khai bản đồ truyền thông toàn hành trình</p>
      </section>

      <!-- Nền tảng chiến lược -->
      <section class="tw-mt-8">
        <h3 class="tw-font-semibold tw-text-gray-700 tw-mb-3">Nền tảng Chiến Lược</h3>

        <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-3 tw-gap-4">
          <div class="tw-bg-white tw-border tw-p-4 tw-rounded-lg">Giá trị Cốt lõi</div>
          <div class="tw-bg-white tw-border tw-p-4 tw-rounded-lg">Chiến lược Định vị</div>
          <div class="tw-bg-white tw-border tw-p-4 tw-rounded-lg">Định vị Thương hiệu</div>
        </div>
      </section>

      <!-- AI Agents -->
      <section class="tw-mt-12" x-data="{ search: '' }">
        <div class="tw-flex tw-flex-col md:tw-flex-row tw-justify-between md:tw-items-center tw-mb-3 tw-gap-4">
          <div>
            <h3 class="tw-font-bold tw-text-xl tw-text-gray-900">AI Agents Thư viện</h3>
            <p class="tw-text-sm tw-text-gray-500 tw-mt-1">Danh sách các Agents mặc định của hệ thống</p>
          </div>

          <div class="tw-flex tw-flex-col sm:tw-flex-row tw-gap-3 tw-w-full md:tw-w-auto">
            <div class="tw-relative tw-w-full sm:tw-w-auto">
              <input type="text" x-model="search" placeholder="Tìm kiếm Agent..."
                class="tw-pl-10 tw-pr-4 tw-py-2 tw-border tw-border-gray-200 tw-rounded-lg tw-text-sm tw-w-full sm:tw-w-[300px] focus:tw-outline-none focus:tw-ring-2 focus:tw-ring-green-500">
              <i class="ri-search-line tw-absolute tw-left-3 tw-top-1/2 -tw-translate-y-1/2 tw-text-gray-400"></i>
            </div>

            <div class="tw-flex tw-gap-2">
              <button @click="openCreateModal = true"
                class="tw-flex-1 sm:tw-flex-none tw-px-4 tw-py-2 tw-bg-vlbcgreen tw-text-white tw-rounded-lg tw-text-sm hover:tw-bg-green-700 tw-transition tw-flex tw-items-center tw-justify-center tw-gap-2 tw-whitespace-nowrap">
                <i class="ri-add-line"></i> Tạo Agent mới
              </button>

              <button @click="openTemplateModal = true"
                class="tw-flex-1 sm:tw-flex-none tw-px-4 tw-py-2 tw-bg-vlbcgreen tw-text-white tw-rounded-lg tw-text-sm hover:tw-bg-green-700 tw-transition tw-flex tw-items-center tw-justify-center tw-gap-2 tw-whitespace-nowrap">
                <i class="ri-add-line"></i> Tạo từ mẫu
              </button>
            </div>
          </div>
        </div>

        <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-3 tw-gap-6">
          @foreach($agents as $agent)
            <div x-show="search === '' || '{{ strtolower($agent->name) }}'.includes(search.toLowerCase())"
              class="tw-bg-white tw-border tw-border-gray-200 tw-rounded-xl tw-p-6 tw-shadow-sm hover:tw-shadow-md tw-transition tw-relative">

              <span
                class="tw-absolute tw-top-4 tw-right-4 tw-bg-blue-50 tw-text-blue-600 tw-text-xs tw-font-semibold tw-px-2.5 tw-py-1 tw-rounded-full">Content</span>

              <div
                class="tw-w-12 tw-h-12 tw-bg-[#E6F6EC] tw-rounded-lg tw-flex tw-items-center tw-justify-center tw-mb-4 tw-text-vlbcgreen">
                <i class="ri-robot-2-line tw-text-2xl"></i>
              </div>

              <a href="{{ route('chat', ['brand' => $brand->slug, 'agentType' => 'canopy', 'agentId' => $agent->id, 'convId' => 'new']) }}"
                class="tw-block hover:tw-underline">
                <h4 class="tw-font-bold tw-text-gray-900 tw-text-lg">{{ $agent->name }}</h4>
              </a>
              <p class="tw-text-sm tw-text-gray-500 tw-mt-2 tw-line-clamp-2">
                {{ $agent->instruction ?? 'Chưa có mô tả chi tiết cho agent này.' }}
              </p>

              <div class="tw-mt-6 tw-flex tw-items-center tw-justify-between">
                <a href="{{ route('chat', ['brand' => $brand->slug, 'agentType' => 'canopy', 'agentId' => $agent->id, 'convId' => 'new']) }}"
                  class="tw-inline-flex tw-items-center tw-gap-2 tw-px-4 tw-py-2 tw-border tw-border-gray-200 tw-rounded-full tw-text-sm tw-font-bold tw-text-green-700 hover:tw-bg-green-50 tw-transition">
                  <i class="ri-shining-fill"></i> Sử dụng Agent
                </a>

                <div class="tw-flex tw-items-center tw-gap-2">
                  <button type="button"
                    onclick="openEditAgent({{ $agent->id }}, '{{ addslashes($agent->name) }}', {{ $agent->is_include ? 'true' : 'false' }}, `{{ addslashes($agent->instruction ?? '') }}`)"
                    class="tw-p-2 tw-text-gray-400 hover:tw-text-blue-600 tw-transition" title="Chỉnh sửa">
                    <i class="ri-edit-line tw-text-lg"></i>
                  </button>
                  <button type="button"
                    onclick="deleteAgent({{ $agent->id }}, '{{ addslashes($agent->name) }}', '{{ $brand->slug }}')"
                    class="tw-p-2 tw-text-gray-400 hover:tw-text-red-600 tw-transition" title="Xóa">
                    <i class="ri-delete-bin-line tw-text-lg"></i>
                  </button>
                </div>
              </div>
            </div>
          @endforeach

          @if($agents->isEmpty())
            <div class="tw-col-span-3 tw-text-center tw-py-12 tw-text-gray-500">
              Chưa có Agent nào. Hãy tạo mới!
            </div>
          @endif

        </div>
      </section>

      <!-- Recommended -->
      <section
        class="tw-bg-gradient-to-r tw-from-green-500 tw-to-green-600 tw-text-white tw-rounded-xl tw-p-6 tw-mt-12">
        <h3 class="tw-font-semibold tw-text-lg tw-mb-2">Bước tiếp theo được đề xuất</h3>
        <ul class="tw-text-sm tw-leading-relaxed">
          <li>- Tối ưu bộ nhận diện trước khi chạy ads</li>
          <li>- Chuẩn hóa thông điệp xuyên suốt hành trình</li>
          <li>- Tạo bộ nội dung onboarding khách hàng</li>
        </ul>
        <button class="tw-mt-4 tw-px-4 tw-py-2 tw-bg-white tw-text-green-700 tw-rounded-lg tw-text-sm tw-font-semibold">
          Xem đề xuất ngay
        </button>
      </section>

      <!-- Input Bar -->
      <div id="inputbar" class="tw-mt-10"></div>

      <!-- Create Agent Modal -->
      @include('brands.trees.partials.create-agent-modal')

      <!-- Create From Template Modal -->
      @include('brands.trees.partials.create-from-template-modal')

      <!-- Edit Agent Modal -->
      @include('brands.trees.partials.edit-agent-modal')

    </main>
  </div>

  <script>
    async function deleteAgent(agentId, agentName, brandSlug) {
      if (!confirm('Bạn có chắc muốn xóa Agent "' + agentName + '" không?')) {
        return;
      }

      try {
        const response = await fetch('/brands/' + brandSlug + '/agents/' + agentId, {
          method: 'DELETE',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
          }
        });

        const result = await response.json();

        if (result.status === 'success') {
          window.location.reload();
        } else {
          alert(result.message || 'Có lỗi xảy ra');
        }
      } catch (error) {
        console.error(error);
        alert('Lỗi kết nối');
      }
    }

    function openEditAgent(id, name, hasKnowledge, instruction) {
      window.dispatchEvent(new CustomEvent('open-edit-modal', {
        detail: {
          id: id,
          name: name,
          hasKnowledge: hasKnowledge,
          instruction: instruction
        }
      }));
    }
  </script>
</x-app-layout>