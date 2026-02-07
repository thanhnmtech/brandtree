<x-app-layout>
    <main class="tw-mt-[36px]">
    <section class="tw-bg-[#e5e5df] tw-py-10 tw-px-6 tw-border-b tw-border-gray-200">
        <div class="tw-max-w-5xl tw-mx-auto tw-text-center">
            <p class="tw-text-xs tw-font-medium tw-text-gray-600 tw-uppercase">
                GIAI ĐOẠN CHIẾN LƯỢC KHƠI MỞ BẢN SẮC THƯƠNG HIỆU
            </p>

            <h1 class="tw-text-4xl tw-font-bold tw-mt-3">
                Bản Đồ Hành Trình <span class="tw-text-vlbcgreen">Thân Cây</span>
            </h1>

            <p class="tw-text-base tw-text-gray-600 tw-mt-3 tw-max-w-2xl tw-mx-auto">
                Xây dựng nền tảng chiến lược vững chắc để thương hiệu tỏa sáng giá
                trị...
            </p>
        </div>
    </section>

        <!-- MAIN CONTENT GRID -->
    <div class="tw-mx-auto tw-px-8 tw-pt-10 tw-flex tw-flex-col tw-gap-10">
        <!-- ===== HEADER BLOCK ===== -->
        <div
            class="tw-w-full tw-bg-[#faf9f6] tw-rounded-xl tw-border tw-border-gray-200 tw-py-6 tw-px-8 tw-flex tw-items-center tw-justify-between">
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
                <div
                    class="tw-h-20 tw-w-20 tw-rounded-full tw-border-[6px] tw-border-green-300 tw-bg-white tw-flex tw-flex-col tw-items-center tw-justify-center tw-shadow-sm">
                    <span class="tw-text-green-600 tw-font-semibold tw-text-xl">53%</span>
                    <span class="tw-text-gray-500 tw-text-[10px] tw-mt-0.5">
                    Hoàn thành
                    </span>
                </div>
            </div>
        </div>

    <!-- ===== MAIN CONTENT AREA ===== -->
    <div class="tw-w-full tw-flex tw-gap-10">
      <!-- LEFT COLUMN -->
      <div class="tw-flex-1 tw-space-y-6">
        @include('brands.trees.partials.trunk_steps')
      </div>

      <!-- RIGHT COLUMN -->
      <aside class="tw-w-[350px] tw-flex tw-flex-col tw-gap-6">
        <!-- PROGRESS BLOCK -->
        <div class="tw-bg-white tw-rounded-xl tw-border tw-border-gray-200 tw-shadow-sm tw-p-6 tw-space-y-5">
          <div class="tw-flex tw-items-start tw-justify-between">
            <h3 class="tw-text-xl tw-font-semibold tw-text-gray-900 leading-tight">
              Tiến Độ Giai Đoạn<br />Thân Cây
            </h3>

              <div class="tw-flex-col tw-text-right">
                <p class="tw-text-[#829B99] tw-font-semibold tw-text-md">
                  0/2 bước
                </p>
                <p
                  class="tw-text-[#829B99] tw-font-semibold tw-text-md tw--mt-1"
                >
                  hoàn thành
                </p>
              </div>
          </div>

          <div class="tw-w-full tw-h-3 tw-rounded-full tw-bg-gray-200"></div>

          <div class="tw-flex tw-items-center tw-justify-between tw-text-sm tw-text-gray-600">
            <span class="tw-flex-1 tw-text-left">Bắt Đầu</span>
            <span class="tw-flex-1 tw-text-center">0%</span>
            <span class="tw-flex-1 tw-text-right">Hoàn Thành</span>
          </div>

          <div class="tw-flex tw-items-start tw-justify-between tw-pt-1">
            <div class="tw-flex tw-flex-1 tw-flex-col tw-items-center tw-gap-1">
              <div
                class="tw-h-10 tw-w-10 tw-rounded-full tw-bg-[#567A63] tw-text-white tw-flex tw-items-center tw-justify-center tw-font-bold">
                T1
              </div>
              <p class="tw-text-xs tw-text-gray-600 tw-text-center leading-tight">
                Định vị Thương hiệu
              </p>
            </div>

            <div class="tw-flex tw-flex-1 tw-flex-col tw-items-center tw-gap-1">
            </div>

            <div class="tw-flex tw-flex-1 tw-flex-col tw-items-center tw-gap-1">
              <div
                class="tw-h-10 tw-w-10 tw-rounded-full tw-border tw-border-[#8AA79A] tw-text-[#8AA79A] tw-flex tw-items-center tw-justify-center tw-font-bold">
                T2
              </div>
              <p class="tw-text-xs tw-text-gray-600 tw-text-center leading-tight">
                Nhận diện Ngôn ngữ
              </p>
            </div>
          </div>
        </div>

        <!-- NEXT STEP WIDGET -->
        <div
          class="tw-bg-white tw-rounded-xl tw-border tw-border-gray-200 tw-shadow-sm tw-p-6 tw-relative tw-overflow-hidden"
          style="
              background: linear-gradient(135deg, #ffffff 0%, #f9f5ee 100%);
            ">
          <div class="tw-flex tw-items-center tw-justify-between">
            <div class="tw-flex tw-items-center tw-gap-2">
              <div class="tw-h-9 tw-w-9 tw-rounded-full tw-bg-[#e8f7eb] tw-flex tw-items-center tw-justify-center">
                <i class="ri-seedling-fill tw-text-vlbcgreen tw-text-lg"></i>
              </div>

              <h4 class="tw-font-semibold tw-text-gray-800 tw-text-base">
                Bước Tiếp Theo
              </h4>
            </div>

            <span
              class="tw-flex tw-items-center tw-gap-1 tw-bg-gray-100 tw-text-gray-600 tw-text-xs tw-py-1 tw-px-3 tw-rounded-full">
              <i class="ri-robot-line"></i>
              AI Điều Phối
            </span>
          </div>

          <p class="tw-text-sm tw-text-gray-700 tw-mt-3 tw-leading-relaxed">
            Tuyệt vời! Hãy tiếp tục với G1: Thiết kế Văn hoá Dịch vụ
          </p>

          <p class="tw-text-sm tw-text-vlbcgreen tw-mt-4 tw-font-medium">
            Được hướng dẫn bởi Orchestrator Agent
          </p>

          <button
            class="tw-mt-3 tw-bg-vlbcgreen tw-text-white tw-text-sm tw-font-medium tw-py-2 tw-px-5 tw-rounded-lg tw-flex tw-items-center tw-gap-2">
            Bắt Đầu Phân Tích
            <i class="ri-arrow-right-line tw-text-base"></i>
          </button>
        </div>

        <!-- ROOT FOUNDATION WIDGET -->
        <div id="root_foundation_card"></div>
      </aside>
    </div>
  </div>
</main>
</x-app-layout>
