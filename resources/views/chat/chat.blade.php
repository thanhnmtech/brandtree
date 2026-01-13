@props([
    'title' => 'Trợ lý ảo', 
    'description' => 'Hỗ trợ tra cứu và phân tích dữ liệu.'
])
<x-app-layout>
    <main id="chatContainer" class="tw-flex tw-flex-1 tw-overflow-hidden">
      <!-- SIDEBAR -->
      <aside
        id="sidebar"
        class="tw-hidden md:tw-block tw-w-[280px] tw-min-w-[200px] tw-bg-white tw-border tw-border-gray-200 tw-h-[calc(100vh-60px)] tw-transition-all tw-duration-300"
      >
    @include('partials.left-sidebar')
    </aside>

      <!-- CHAT AREA -->
      <div
        id="chat-area"
        class="tw-flex-1 md:tw-flex-1 tw-bg-white tw-border tw-border-gray-200 tw-flex tw-flex-col tw-h-[calc(100vh-60px)] tw-transition-all tw-duration-300"
      >
        <!-- Chat header -->
        <div
          class="tw-hidden md:tw-flex tw-px-3 tw-py-3 tw-border-b tw-border-gray-100 tw-items-center tw-gap-3"
        >
          <div id="logo-chat">
            <img
              src="./assets/img/logo-nenTangDuLieu.svg"
              class="tw-w-[38px] tw-h-[38px] tw-object-contain"
            />
          </div>
          <div class="tw-flex-1 tw-min-w-0">
            <div
              class="tw-font-semibold tw-truncate tw-overflow-hidden tw-whitespace-nowrap"
            >
              {{ $title }} {{-- Truyền title vào đây --}}
            </div>
            <div
              class="tw-text-sm tw-text-gray-500 tw-truncate tw-overflow-hidden tw-whitespace-nowrap"
            >
              {{ $description }} {{-- Truyền description vào đây --}}
            </div>
          </div>
        </div>

        <!-- Chat messages -->
        <section
          id="chat-messages-container"
          class="tw-flex-1 tw-overflow-y-auto tw-px-5 tw-py-4 tw-bg-[#F3F7F5]"
        >
          <div id="chat-messages" class="tw-space-y-2">
            <!-- render tin nhắn ở đây -->
          </div>
        </section>

        <!-- Input bar -->
        <div
          class="tw-w-full tw-bg-white tw-border-t tw-border-gray-200 tw-px-5 tw-py-4 tw-flex tw-flex-col tw-justify-end"
        >
          <div id="inputbar">
            @include('partials.input-bar')
          </div>
        </div>
      </div>

      <!-- RESULT PANEL -->
      <aside
        id="right-sidebar"
        class="tw-hidden md:tw-block tw-w-[280px] tw-min-w-[200px] tw-bg-white tw-border tw-border-gray-200 tw-overflow-y-auto tw-h-full tw-transition-all tw-duration-300"
      >
    </aside>
    </main>
</x-app-layout>