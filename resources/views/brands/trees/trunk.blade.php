<x-app-layout>
    @php
      $trunkData = $brand->trunk_data ?? [];
      $initialData = [
          'trunk1' => $trunkData['trunk1'] ?? '',
          'trunk2' => $trunkData['trunk2'] ?? '',
      ];
  @endphp

  <main class="tw-mt-[36px]"
      data-controller="result-modal"
      data-result-modal-brand-slug-value="{{ $brand->slug }}"
      data-result-modal-url-value="{{ route('brands.trunk.show', $brand->slug) }}"
      data-result-modal-data-value='@json($initialData)'
  >
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
      <div class="tw-flex-1 tw-space-y-6" data-result-modal-target="stepsContainer">
        @include('brands.trees.partials.trunk_steps')
      </div>

      <!-- RIGHT COLUMN -->
      <aside class="tw-w-[350px] tw-flex tw-flex-col tw-gap-6">
        <!-- PROGRESS BLOCK -->
        <div data-result-modal-target="progressContainer">
            @include('brands.trees.partials.progress_card', [
                'phaseTitle' => 'Thân Cây',
                'steps' => $trunkSteps,
                'stepPrefix' => 'T'
            ])
        </div>

        <!-- NEXT STEP WIDGET -->
        <div data-result-modal-target="nextStepContainer">
            @include('brands.trees.partials.next_step_card')
        </div>



        <!-- ROOT FOUNDATION WIDGET -->
        <div id="root_foundation_card"></div>
      </aside>
    </div>
  </div>

  {{-- Result Modal (View Only) --}}
  <x-result-modal :brand="$brand" />
</main>
</x-app-layout>
