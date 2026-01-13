<div class="tw-flex tw-flex-col">
  <div
    class="tw-px-3 tw-py-3 tw-border-b tw-border-gray-100 tw-flex tw-items-center tw-gap-3"
  >
    <div id="logo-sidebar">
      <img
        src="./assets/img/logo-sidebar.svg"
        class="tw-w-[38px] tw-h-[38px] tw-object-contain"
      />
    </div>
    <div id="content-sidebar" class="tw-flex-1 tw-min-w-0">
      <div
        class="tw-font-bold tw-truncate tw-overflow-hidden tw-whitespace-nowrap"
      >
        VLBC Brand Garden
      </div>
      <div
        class="tw-text-sm tw-text-gray-500 tw-truncate tw-overflow-hidden tw-whitespace-nowrap"
      >
        Brand Tree System
      </div>
    </div>
    <div id="button-sidebar" class="tw-flex tw-items-center">
      <button onclick="toggleSidebar()">
        <img
          src="./assets/img/sidebar-toggle.svg"
          class="tw-w-[20px] tw-h-[20px] tw-object-contain"
        />
      </button>
    </div>
  </div>

  <nav
    id="dataPlatformSection"
    class="tw-px-3 tw-py-3 tw-border-b tw-border-gray-100 tw-flex tw-flex-col tw-items-center tw-gap-2"
  >
    <button
      onclick="toggleMenu('dataPlatformMenu', 'dataArrow')"
      class="tw-w-full tw-px-3 tw-py-2 tw-bg-[linear-gradient(90deg,#0E642D_0%,#16A048_100%)] tw-rounded-md tw-flex tw-items-center tw-gap-3 tw-text-left"
    >
      <div>
        <img
          src="./assets/img/logo-nenTangDuLieu.svg"
          class="tw-w-[38px] tw-h-[38px] tw-object-contain"
        />
      </div>

      <div class="tw-flex-1 tw-min-w-0">
        <div
          class="tw-font-semibold tw-text-white tw-truncate tw-overflow-hidden tw-whitespace-nowrap"
        >
          Nền Tảng Dữ Liệu
        </div>
        <div
          class="tw-text-sm tw-text-white tw-truncate tw-overflow-hidden tw-whitespace-nowrap"
        >
          Kiến thức nền tảng của thương hiệu
        </div>
      </div>

      <div class="tw-flex tw-items-center">
        <img
          id="dataArrow"
          src="./assets/img/dropdown-button-white.svg"
          class="tw-object-contain tw-rotate-[-90deg] tw-transition tw-duration-500"
        />
      </div>
    </button>

    <ul
      id="dataPlatformMenu"
      class="tw-hidden tw-w-full tw-space-y-2 tw-text-sm"
    >
      <li
        class="tw-px-3 tw-py-1 tw-rounded-md tw-bg-[#D9F2E2] tw-flex tw-items-center tw-gap-2"
      >
        <span class="tw-font-semibold tw-text-gray-500"
          >Phân tích thổ nhưỡng</span
        >
      </li>

      <li
        class="tw-px-3 tw-py-1 tw-rounded-md hover:tw-bg-gray-50 tw-cursor-pointer tw-flex tw-items-center tw-gap-2"
      >
        <span class="tw-font-semibold tw-text-gray-500"
          >Định vị Giá trị Giải pháp</span
        >
      </li>

      <li
        class="tw-px-3 tw-py-1 tw-rounded-md hover:tw-bg-gray-50 tw-cursor-pointer tw-flex tw-items-center tw-gap-2"
      >
        <span class="tw-font-semibold tw-text-gray-500"
          >Thiết kế Văn hóa Dịch vụ</span
        >
      </li>

      <li
        class="tw-px-3 tw-py-1 tw-rounded-md hover:tw-bg-gray-50 tw-cursor-pointer tw-flex tw-items-center tw-gap-2"
      >
        <span class="tw-font-semibold tw-text-gray-500"
          >Định vị thương hiệu</span
        >
      </li>
    </ul>
  </nav>

  <div
    id="chatHistorySection"
    class="tw-px-3 tw-py-3 tw-border-b tw-border-gray-100 tw-flex tw-flex-col tw-gap-3"
  >
    <button
      onclick="toggleMenu('chatHistoryMenu', 'chatArrow')"
      class="tw-w-full tw-px-3 tw-py-2 tw-flex tw-items-center tw-gap-3 tw-text-left tw-bg-transparent tw-border-none"
    >
      <div>
        <img
          src="./assets/img/icon-chatHistory.svg"
          class="tw-w-[38px] tw-h-[38px] tw-object-contain"
        />
      </div>

      <div class="tw-flex-1 tw-min-w-0">
        <div
          class="tw-font-semibold tw-truncate tw-overflow-hidden tw-whitespace-nowrap"
        >
          Lịch sử chat
        </div>
      </div>

      <div class="tw-flex tw-items-center">
        <img
          id="chatArrow"
          src="./assets/img/dropdown-button-black.svg"
          class="tw-w-[12px] tw-h-[7px] tw-object-contain tw-rotate-[-90deg] tw-transition tw-duration-500"
        />
      </div>
    </button>

    <ul
      id="chatHistoryMenu"
      class="tw-hidden tw-w-full tw-space-y-2 tw-text-sm"
    >
      <li
        class="tw-px-3 tw-py-1 tw-rounded-md hover:tw-bg-gray-50 tw-cursor-pointer tw-flex tw-items-center tw-gap-2"
      >
        <span class="tw-font-semibold tw-text-gray-500"
          >Nhận diện ngôn ngữ</span
        >
      </li>
    </ul>
  </div>
</div>
