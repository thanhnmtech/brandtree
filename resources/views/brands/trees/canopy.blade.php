<x-app-layout>
  <!-- Main Content -->
  <main class="tw-mt-[36px]" x-data="{ openCreateModal: false, openTemplateModal: false, openEditModal: false }">

      <!-- Title -->
    <section class="tw-bg-[#e5e5df] tw-py-10 tw-px-6 tw-border-b tw-border-gray-200">
      <div class="tw-max-w-5xl tw-mx-auto tw-text-center">
        <p class="tw-text-xs tw-font-medium tw-text-gray-600 tw-uppercase">
          GIAI ĐOẠN TÁN CÂY ĐANG HOẠT ĐỘNG
        </p>

        <h1 class="tw-text-4xl tw-font-bold tw-mt-3">
          Bản Đồ Hành Trình <span class="tw-text-vlbcgreen">Tán Cây</span>
        </h1>

        <p class="tw-text-base tw-text-gray-600 tw-mt-3 tw-max-w-2xl tw-mx-auto">
          AI đã biến chiến lược nền tảng thành bộ nhận diện thương hiệu hoàn chỉnh, sẵn sàng để triển khai.
        </p>
      </div>
    </section>

    <div class="tw-mx-auto tw-px-8 tw-pt-10 tw-flex tw-flex-col tw-gap-10">
      <!-- ===== HEADER BLOCK ===== -->
      <div class="tw-w-full tw-bg-[#faf9f6] tw-rounded-xl tw-border tw-border-gray-200 tw-py-6 tw-px-8 tw-flex tw-items-center tw-justify-between">
        <div>
          <div class="tw-flex tw-items-center tw-gap-2">
            <h2 class="tw-text-xl tw-font-semibold tw-text-gray-900">
              <a href="{{ route('brands.show', $brand) }}" class="hover:tw-text-vlbcgreen tw-transition-colors">
                {{ $brand->name }}
              </a>
            </h2>
            <i class="ri-settings-3-line tw-text-gray-600 tw-text-lg"></i>
            <i class="ri-arrow-down-s-line tw-text-gray-500 tw-text-lg"></i>
          </div>

          <p class="tw-text-sm tw-text-gray-500 tw-mt-1">
            Xây dựng và phát triển thương hiệu dựa trên cốt lõi của bạn
          </p>
        </div>

        <div class="tw-flex tw-items-center">
          <div class="tw-h-20 tw-w-20 tw-rounded-full tw-border-[6px] tw-border-green-300 tw-bg-white tw-flex tw-flex-col tw-items-center tw-justify-center tw-shadow-sm">
            <span class="tw-text-green-600 tw-font-semibold tw-text-xl">53%</span>
            <span class="tw-text-gray-500 tw-text-[10px] tw-mt-0.5">Hoàn thành</span>
          </div>
        </div>
      </div>

      <!-- Nền tảng chiến lược -->
      <section class="tw-space-y-6">
        <h2 class="tw-text-2xl tw-font-bold tw-text-gray-800">
          Nền Tảng Chiến Lược
        </h2>

        <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-3 tw-gap-5">
          <!-- CARD 1 -->
          <div class="tw-bg-white tw-rounded-[10px] tw-border-2 tw-border-[#E0EAE6] tw-shadow-[0_4px_4px_rgba(0,0,0,0.05)] tw-p-6 tw-transition">
            <div class="tw-flex tw-items-center tw-gap-3">
              <div class="tw-h-10 tw-w-10 tw-bg-[#E6F6EC] tw-rounded-full tw-flex tw-items-center tw-justify-center">
                <img
                  src="{{ asset('assets/img/icon-target-green.svg') }}"
                  class="tw-w-[24px] tw-h-[24px]"
                />
              </div>

              <div class="tw-flex-1">
                <h3 class="tw-text-lg tw-font-semibold tw-text-gray-800">
                  Giá trị Cốt Lõi
                </h3>
                <p class="tw-text-sm tw-font-semibold tw-text-[#829B99] tw-mt-1">
                  Chất lượng Đổi mới, Trách nhiệm
                </p>
              </div>
            </div>
          </div>

          <!-- CARD 2 -->
          <div class="tw-bg-white tw-rounded-[10px] tw-border-2 tw-border-[#E0EAE6] tw-shadow-[0_4px_4px_rgba(0,0,0,0.05)] tw-p-6 tw-transition">
            <div class="tw-flex tw-items-center tw-gap-3">
              <div class="tw-h-10 tw-w-10 tw-rounded-full tw-bg-[#E6F6EC] tw-flex tw-items-center tw-justify-center">
                <img
                  src="{{ asset('assets/img/icon-human-green.svg') }}"
                  class="tw-w-[20px] tw-h-[20px]"
                />
              </div>

              <div class="tw-flex-1">
                <h3 class="tw-text-lg tw-font-semibold tw-text-gray-800">
                  Chân dung Khách hàng
                </h3>
                <p class="tw-text-sm tw-font-semibold tw-text-[#829B99] tw-mt-1">
                  SME Việt Nam, 30-45 tuổi, tìm kiếm tăng trưởng
                </p>
              </div>
            </div>
          </div>

          <!-- CARD 3 -->
          <div class="tw-bg-white tw-rounded-[10px] tw-border-2 tw-border-[#E0EAE6] tw-shadow-[0_4px_4px_rgba(0,0,0,0.05)] tw-p-6 tw-transition">
            <div class="tw-flex tw-items-center tw-gap-3">
              <div class="tw-h-10 tw-w-10 tw-rounded-full tw-bg-[#E9F3EF] tw-flex tw-items-center tw-justify-center">
                <img
                  src="{{ asset('assets/img/icon-star-green.svg') }}"
                  class="tw-w-[20px] tw-h-[20px]"
                />
              </div>

              <div class="tw-flex-1">
                <h3 class="tw-text-lg tw-font-semibold tw-text-gray-800">
                  Định vị Thương hiệu
                </h3>
                <p class="tw-text-sm tw-font-semibold tw-text-[#829B99] tw-mt-1">
                  Giải pháp marketing AI-powered cho SME
                </p>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- AI Agents -->
      <section class="tw-space-y-6" x-data="{ search: '' }">
        <div class="tw-flex tw-flex-col md:tw-flex-row tw-justify-between md:tw-items-center tw-mb-3 tw-gap-4">
          <div>
            <h3 class="tw-font-bold tw-text-2xl tw-text-gray-800">AI Agents Thư viện</h3>
            <p class="tw-text tw-text-[#829B99] tw-font-semibold">Danh sách các Agents mặc định của hệ thống</p>
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
      <section class="tw-flex tw-flex-col tw-rounded-[10px] tw-bg-[linear-gradient(270deg,#45C974_-4.54%,#2A9150_71.65%)] tw-shadow-[0_4px_4px_rgba(0,0,0,0.05)] tw-p-6 md:tw-p-10 tw-gap-4">
        <div class="tw-text-white">
          <div class="tw-flex tw-items-center tw-gap-4">
            <div class="tw-h-12 tw-w-12 tw-bg-[#FFFFFF] tw-rounded-full tw-flex tw-items-center tw-justify-center">
              <img src="{{ asset('assets/img/icon-star-green.svg') }}" class="tw-w-[24px] tw-h-[24px]" />
            </div>
            <h3 class="tw-flex-1 tw-text-lg tw-font-semibold">
              Bước tiếp theo được đề xuất
            </h3>
          </div>

          <ul class="tw-mt-2 tw-text-sm tw-leading-6">
            <li>
              • Tối ưu nhận diện trước khi chạy quảng cáo.
            </li>
            <li>
              • Chuẩn hóa thông điệp xuyên suốt hành trình khách hàng.
            </li>
            <li>• Tạo bộ nội dung onboarding tự động.</li>
          </ul>
        </div>

        <div class="tw-flex tw-flex-col md:tw-flex-row tw-gap-4 tw-gap-4">
          <button class="tw-bg-white tw-text-[#16a249] tw-px-4 tw-py-2 tw-rounded-lg tw-font-semibold hover:tw-opacity-90">
            Bắt đầu ngay →
          </button>

          <button class="tw-bg-[#33B468] tw-text-white tw-px-4 tw-py-2 tw-rounded-lg tw-font-semibold hover:tw-opacity-90">
            Tùy chỉnh
          </button>
        </div>
      </section>

      <!-- Input Bar -->
      <div id="inputbar"></div>

      <!-- Create Agent Modal -->
      @include('brands.trees.partials.create-agent-modal')

      <!-- Create From Template Modal -->
      @include('brands.trees.partials.create-from-template-modal')

      <!-- Edit Agent Modal -->
      @include('brands.trees.partials.edit-agent-modal')

    </div>
  </main>

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