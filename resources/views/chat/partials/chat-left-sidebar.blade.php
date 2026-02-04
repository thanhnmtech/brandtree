<div class="tw-flex tw-flex-col tw-h-full">
  <div class="tw-px-3 tw-py-3 tw-border-b tw-border-gray-100 tw-flex tw-items-center tw-gap-3">
    <div id="logo-sidebar">
      <img src="{{ asset('assets/img/logo-sidebar.svg') }}" class="tw-w-[38px] tw-h-[38px] tw-object-contain" />
    </div>
    <div id="content-sidebar" class="tw-flex-1 tw-min-w-0">
      <div class="tw-font-bold tw-truncate tw-overflow-hidden tw-whitespace-nowrap">
        {{ $brand->name }}
      </div>
      <div class="tw-text-sm tw-text-gray-500 tw-truncate tw-overflow-hidden tw-whitespace-nowrap">
        Brand Tree System
      </div>
    </div>
    <div id="button-sidebar" class="tw-flex tw-items-center">
      <button onclick="toggleLeftSidebar()">
        <img src="{{ asset('assets/img/sidebar-toggle.svg') }}" class="tw-w-[20px] tw-h-[20px] tw-object-contain" />
      </button>
    </div>
  </div>

  <nav id="dataPlatformSection"
    class="tw-px-3 tw-py-3 tw-border-b tw-border-gray-100 tw-flex tw-flex-col tw-items-center tw-gap-2">
    <button onclick="toggleMenu('dataPlatformMenu', 'dataArrow')"
      class="tw-w-full tw-px-3 tw-py-2 tw-bg-[linear-gradient(90deg,#0E642D_0%,#16A048_100%)] tw-rounded-md tw-flex tw-items-center tw-gap-3 tw-text-left">
      <div>
        <img src="{{ asset('assets/img/logo-nenTangDuLieu.svg') }}" class="tw-w-[38px] tw-h-[38px] tw-object-contain" />
      </div>

      <div class="tw-flex-1 tw-min-w-0">
        <div class="tw-font-semibold tw-text-white tw-truncate tw-overflow-hidden tw-whitespace-nowrap">
          Nền Tảng Dữ Liệu
        </div>
        <div class="tw-text-sm tw-text-white tw-truncate tw-overflow-hidden tw-whitespace-nowrap">
          Kiến thức nền tảng của thương hiệu
        </div>
      </div>

      <div class="tw-flex tw-items-center">
        <img id="dataArrow" src="{{ asset('assets/img/dropdown-button-white.svg') }}"
          class="tw-object-contain tw-rotate-[-90deg] tw-transition tw-duration-500" />
      </div>
    </button>

    <ul id="dataPlatformMenu" class="tw-hidden tw-w-full tw-space-y-2 tw-text-sm">
      <li class="tw-px-3 tw-py-1 tw-rounded-md tw-bg-[#D9F2E2] tw-flex tw-items-center tw-gap-2">
        <span class="tw-font-semibold tw-text-gray-500">Phân tích thổ nhưỡng</span>
      </li>

      <li class="tw-px-3 tw-py-1 tw-rounded-md hover:tw-bg-gray-50 tw-cursor-pointer tw-flex tw-items-center tw-gap-2">
        <span class="tw-font-semibold tw-text-gray-500">Định vị Giá trị Giải pháp</span>
      </li>

      <li class="tw-px-3 tw-py-1 tw-rounded-md hover:tw-bg-gray-50 tw-cursor-pointer tw-flex tw-items-center tw-gap-2">
        <span class="tw-font-semibold tw-text-gray-500">Thiết kế Văn hóa Dịch vụ</span>
      </li>

      <li class="tw-px-3 tw-py-1 tw-rounded-md hover:tw-bg-gray-50 tw-cursor-pointer tw-flex tw-items-center tw-gap-2">
        <span class="tw-font-semibold tw-text-gray-500">Định vị thương hiệu</span>
      </li>
    </ul>
  </nav>

  <!-- Chat History Section with Alpine.js -->
  <div id="chatHistorySection" class="tw-flex tw-flex-col tw-gap-3 tw-overflow-hidden tw-flex-1" x-data="chatHistorySidebar({
         brandId: '{{ $brand->id }}',
         brandSlug: '{{ $brand->slug }}',
         agentId: '{{ $agentId }}',
         agentType: '{{ $agentType }}'
       })">

    <div class="tw-px-3 tw-py-3 tw-border-b tw-border-gray-100 tw-flex tw-flex-col tw-flex-1 tw-min-h-0">
      <a href="/brands/{{ $brand->slug }}/chat/{{ $agentType }}/{{ $agentId }}/new"
        class="tw-w-full tw-px-3 tw-py-2 tw-flex tw-items-center tw-gap-3 tw-text-left tw-bg-transparent hover:tw-bg-gray-50 tw-rounded-md tw-shrink-0 tw-mb-2">
        <div class="tw-flex tw-items-center tw-justify-center tw-w-[38px] tw-h-[38px]">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
            stroke-linecap="round" stroke-linejoin="round" class="tw-text-gray-600">
            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
          </svg>
        </div>

        <div class="tw-flex-1 tw-min-w-0">
          <div class="tw-font-semibold tw-truncate tw-overflow-hidden tw-whitespace-nowrap">
            Đoạn chat mới
          </div>
        </div>
      </a>

      <button onclick="toggleMenu('chatHistoryMenu', 'chatArrow')"
        class="tw-w-full tw-px-3 tw-py-2 tw-flex tw-items-center tw-gap-3 tw-text-left tw-bg-transparent tw-border-none tw-shrink-0">
        <div>
          <img src="{{ asset('assets/img/icon-chatHistory.svg') }}" class="tw-w-[38px] tw-h-[38px] tw-object-contain" />
        </div>

        <div class="tw-flex-1 tw-min-w-0">
          <div class="tw-font-semibold tw-truncate tw-overflow-hidden tw-whitespace-nowrap">
            Lịch sử chat
          </div>
        </div>

        <div class="tw-flex tw-items-center">
          <img id="chatArrow" src="{{ asset('assets/img/dropdown-button-black.svg') }}"
            class="tw-w-[12px] tw-h-[7px] tw-object-contain tw-transition tw-duration-500" />
        </div>
      </button>

      <ul id="chatHistoryMenu" class="tw-w-full tw-space-y-2 tw-text-sm tw-mt-2 tw-flex-1 tw-overflow-y-auto tw-min-h-0"
        @scroll="handleScroll">

        <template x-for="chat in chats" :key="chat.id">
          <li
            class="tw-group tw-px-3 tw-py-2 tw-rounded-md hover:tw-bg-gray-50 tw-cursor-pointer tw-flex tw-items-center tw-gap-2"
            @mouseenter="hoveredChatId = chat.id" @mouseleave="hoveredChatId = null">

            <!-- Chế độ xem bình thường -->
            <template x-if="editingChatId !== chat.id">
              <div class="tw-flex tw-items-center tw-gap-2 tw-w-full">
                <a :href="getChatLink(chat)" class="tw-block tw-flex-1 tw-min-w-0">
                  <span
                    class="tw-font-semibold tw-text-gray-500 hover:tw-text-gray-800 tw-transition-colors tw-block tw-truncate"
                    x-text="chat.title"></span>
                  <div class="tw-text-xs tw-text-gray-400" x-text="formatDate(chat.created_at)"></div>
                </a>

                <!-- Nút chỉnh sửa - hiện khi hover -->
                <button x-show="hoveredChatId === chat.id"
                  x-transition:enter="tw-transition tw-ease-out tw-duration-150" x-transition:enter-start="tw-opacity-0"
                  x-transition:enter-end="tw-opacity-100" @click.prevent="startEdit(chat)"
                  class="tw-p-1 tw-rounded hover:tw-bg-gray-200 tw-transition-colors tw-flex-shrink-0" title="Đổi tên">
                  <svg class="tw-w-4 tw-h-4 tw-text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z">
                    </path>
                  </svg>
                </button>
              </div>
            </template>

            <!-- Chế độ chỉnh sửa -->
            <template x-if="editingChatId === chat.id">
              <div class="tw-w-full">
                <input type="text" x-model="editingTitle" x-ref="editInput" @keydown.enter.prevent="saveEdit(chat)"
                  @keydown.escape.prevent="cancelEdit()" @blur="saveEdit(chat)"
                  class="tw-w-full tw-px-2 tw-py-1 tw-text-sm tw-border tw-border-green-500 tw-rounded tw-outline-none tw-ring-2 tw-ring-green-200"
                  placeholder="Nhập tên mới..." />
                <div class="tw-text-xs tw-text-gray-400 tw-mt-1">Nhấn Enter để lưu, Esc để hủy</div>
              </div>
            </template>
          </li>
        </template>

        <li x-show="loading" class="tw-px-3 tw-py-2 tw-text-center tw-text-gray-400">
          <span class="tw-animate-pulse">Đang tải...</span>
        </li>

        <li x-show="!loading && chats.length === 0" class="tw-px-3 tw-py-2 tw-text-center tw-text-gray-400">
          Chưa có lịch sử chat
        </li>
      </ul>
    </div>
  </div>
</div>

<script>
  function chatHistorySidebar(config) {
    return {
      chats: [],
      page: 1,
      hasMore: true,
      loading: false,
      brandId: config.brandId,
      brandSlug: config.brandSlug,
      agentId: config.agentId,
      agentType: config.agentType,

      // State cho chức năng đổi tên chat
      hoveredChatId: null,      // ID của chat đang được hover
      editingChatId: null,      // ID của chat đang được chỉnh sửa
      editingTitle: '',         // Tên mới đang nhập

      init() {
        this.fetchChats();

        // 🆕 Lắng nghe event khi có chat mới được tạo
        window.addEventListener('chat-created', (e) => {
          // Reset và fetch lại danh sách từ đầu để hiển thị chat mới
          this.chats = [];
          this.page = 1;
          this.hasMore = true;
          this.fetchChats();
        });
      },

      async fetchChats() {
        if (this.loading || !this.hasMore) return;

        this.loading = true;

        try {
          const response = await fetch(`/api/chat/history?brandId=${this.brandId}&agentId=${this.agentId}&agentType=${this.agentType}&page=${this.page}`);
          const json = await response.json();

          if (json.data && json.data.length > 0) {
            this.chats = [...this.chats, ...json.data];
            this.page++;

            if (!json.next_page_url) {
              this.hasMore = false;
            }
          } else {
            this.hasMore = false;
          }
        } catch (error) {
          console.error('Error fetching chat history:', error);
        } finally {
          this.loading = false;
        }
      },

      handleScroll(e) {
        const el = e.target;
        // Check if scrolled near bottom (within 50px)
        if (el.scrollHeight - el.scrollTop - el.clientHeight < 50) {
          this.fetchChats();
        }
      },

      getChatLink(chat) {
        // Link format: /brands/{slug}/chat/{agentType}/{agentId}/{convId}
        // Determine convId. If chat.conversation_id exists use it? No, route uses ID or 'new'.
        // Actually the route is: /brands/{brand}/chat/{agentType?}/{agentId?}/{convId?}
        // So convId should be the ID of the chat record in our usage context (Chat::find($convId)).
        return `/brands/${this.brandSlug}/chat/${this.agentType}/${this.agentId}/${chat.id}`;
      },

      formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('vi-VN', { day: '2-digit', month: '2-digit', hour: '2-digit', minute: '2-digit' });
      },

      // Bắt đầu chỉnh sửa tên chat
      startEdit(chat) {
        this.editingChatId = chat.id;
        this.editingTitle = chat.title;

        // Focus vào input sau khi DOM cập nhật
        this.$nextTick(() => {
          const input = this.$refs.editInput;
          if (input) {
            input.focus();
            input.select();
          }
        });
      },

      // Lưu tên mới
      async saveEdit(chat) {
        // Không làm gì nếu không đang edit chat này
        if (this.editingChatId !== chat.id) return;

        const newTitle = this.editingTitle.trim();

        // Nếu tên rỗng hoặc không thay đổi, hủy edit
        if (!newTitle || newTitle === chat.title) {
          this.cancelEdit();
          return;
        }

        try {
          const response = await fetch(`/api/chat/${chat.id}/rename`, {
            method: 'PUT',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ title: newTitle })
          });

          const result = await response.json();

          if (result.success) {
            // Cập nhật tên trong danh sách chats
            const chatIndex = this.chats.findIndex(c => c.id === chat.id);
            if (chatIndex !== -1) {
              this.chats[chatIndex].title = result.title;
            }
          } else {
            console.error('Lỗi khi đổi tên:', result.message);
            alert('Không thể đổi tên đoạn chat. Vui lòng thử lại.');
          }
        } catch (error) {
          console.error('Error renaming chat:', error);
          alert('Có lỗi xảy ra. Vui lòng thử lại.');
        } finally {
          this.cancelEdit();
        }
      },

      // Hủy chỉnh sửa
      cancelEdit() {
        this.editingChatId = null;
        this.editingTitle = '';
      }
    }
  }
</script>