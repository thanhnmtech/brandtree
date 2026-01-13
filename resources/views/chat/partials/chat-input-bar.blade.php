<div class="tw-flex-1 tw-space-y-2">
  <div class="tw-flex tw-items-end tw-gap-3 tw-flex-nowrap">
    <button
      type="newchat"
      class="tw-w-12 tw-h-12 tw-bg-vlbcgreen tw-text-white tw-rounded-md tw-flex tw-items-center tw-justify-center"
    >
      <img
        src="./assets/img/icon-plus-white.svg"
        class="tw-w-[20px] tw-h-[20px] tw-object-contain"
      />
    </button>

    <textarea
      id="chat-input"
      rows="1"
      class="tw-flex-1 tw-min-h-12 tw-resize-none tw-overflow-y-auto tw-border tw-border-gray-200 tw-rounded-md tw-px-3 tw-py-2 tw-text-sm focus:tw-outline-none focus:tw-ring-2 focus:tw-ring-[#16a34a]/40"
      placeholder="Ask anything"
    ></textarea>

    <button type="enter">
      <img
        src="./assets/img/enter-button.svg"
        class="tw-w-[48px] tw-h-[48px] tw-object-contain"
      />
    </button>
  </div>

  <div
    class="tw-mt-1 tw-flex tw-items-center tw-justify-between tw-text-[11px] tw-text-gray-500"
  >
    <div class="tw-flex-1 tw-min-w-0">
      <span
        class="tw-hidden md:tw-block tw-truncate tw-overflow-hidden tw-whitespace-nowrap"
        title="Kết quả phân tích sẽ hiển thị ở bên phải"
        >Kết quả phân tích sẽ hiển thị ở bên phải</span
      >
      <span
        class="tw-hidden md:tw-block tw-truncate tw-overflow-hidden tw-whitespace-nowrap"
        title="Nhấn Enter để gửi, Shift + Enter để xuống dòng"
        >Nhấn Enter để gửi, Shift + Enter để xuống dòng</span
      >
    </div>

    <button
      type="submit"
      class="tw-bg-[#16a34a] tw-text-white tw-px-4 tw-py-2 tw-rounded-md tw-text-sm tw-font-medium"
    >
      Xác nhận phân tích
    </button>
  </div>
</div>
