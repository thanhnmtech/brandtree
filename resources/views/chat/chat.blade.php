@props([
  'title' => 'Trợ lý ảo',
  'description' => 'Hỗ trợ tra cứu và phân tích dữ liệu.'
])
@php
  $agentType = $agentType ?? request()->route('agentType') ?? 'root1';
  $agentId = $agentId ?? request()->route('agentId') ?? 1;
@endphp
<x-app-layout>
  <div id="sidebar-mobile">
    @include('chat.partials.chat-sidebar-mobile')
  </div>

  <main id="chatContainer" class="tw-flex tw-flex-1 tw-overflow-hidden">
    <!-- LEFT SIDEBAR -->
    <aside id="left-sidebar"
      class="tw-hidden md:tw-flex tw-flex-col tw-w-[280px] tw-min-w-[200px] tw-bg-white tw-border tw-border-gray-200 tw-h-[calc(100vh-60px)] tw-transition-all tw-duration-300 tw-overflow-hidden">
      @include('chat.partials.chat-left-sidebar', ['agentKeywords' => $agentKeywords ?? []])
    </aside>

    <!-- CHAT AREA -->
    <div 
      id="chat-area"
      class="tw-flex-1 md:tw-flex-1 tw-bg-white tw-border tw-border-gray-200 tw-flex tw-flex-col tw-h-[calc(100vh-60px)] tw-transition-all tw-duration-300 tw-min-w-[368px]">
      <livewire:chat :agentType="$agentType" :agentId="$agentId" :convId="$convId" :brandId="$brand->id"
        :brandData="['root1' => !empty($brand->root_data['root1']), 'root2' => !empty($brand->root_data['root2']), 'root3' => !empty($brand->root_data['root3']), 'trunk1' => !empty($brand->trunk_data['trunk1']), 'trunk2' => !empty($brand->trunk_data['trunk2'])]" />
    </div>

    <!-- RESULT PANEL -->
    <aside id="right-sidebar"
      class="tw-hidden md:tw-block tw-w-[300px] tw-min-w-[200px] tw-bg-white tw-border tw-border-gray-200 tw-h-[calc(100vh-60px)] tw-transition-all tw-duration-300 tw-overflow-hidden">
      @include('chat.partials.chat-result-bar')
    </aside>
  </main>
</x-app-layout>
<script>
  function toggleLeftSidebar() {
    const sidebar = document.getElementById("left-sidebar");

    const logoSidebar = document.getElementById("logo-sidebar");
    const contentSidebar = document.getElementById("content-sidebar");
    const dataSection = document.getElementById("dataPlatformSection");
    const chatSection = document.getElementById("chatHistorySection");

    const isCollapsed = sidebar.classList.contains("tw-w-[48px]");

    if (isCollapsed) {
      // ==== MỞ SIDEBAR ====
      sidebar.classList.remove("tw-w-[48px]", "tw-min-w-[48px]");
      sidebar.classList.add("tw-w-[280px]", "tw-min-w-[200px]");

      logoSidebar.classList.remove("tw-hidden");
      contentSidebar.classList.remove("tw-hidden");
      dataSection.classList.remove("tw-hidden");
      chatSection.classList.remove("tw-hidden");
    } else {
      // ==== THU SIDEBAR ====
      sidebar.classList.add("tw-w-[48px]", "tw-min-w-[48px]");
      sidebar.classList.remove("tw-w-[280px]", "tw-min-w-[200px]");

      logoSidebar.classList.add("tw-hidden");
      contentSidebar.classList.add("tw-hidden");
      dataSection.classList.add("tw-hidden");
      chatSection.classList.add("tw-hidden");
    }
  }

  function toggleSidebarMobile() {
    const overlay = document.getElementById("mobileSidebar");
    const panel = document.getElementById("mobileSidebarPanel");

    const isClosed = panel.classList.contains("tw--translate-x-full");

    if (isClosed) {
      // === MỞ SIDE BAR ===
      overlay.classList.remove("tw-hidden");

      // Delay cho CSS apply trước khi animate
      setTimeout(() => {
        panel.classList.remove("tw--translate-x-full");
      }, 20);
    } else {
      // === ĐÓNG SIDE BAR ===
      panel.classList.add("tw--translate-x-full");

      // ĐỢI ANIMATION XONG RỒI MỚI ẨN OVERLAY
      setTimeout(() => {
        overlay.classList.add("tw-hidden");
      }, 300); // match tw-duration-300
    }
  }

  function toggleMenu(menuId, arrowId) {
    const menu = document.getElementById(menuId);
    const arrow = document.getElementById(arrowId);

    if (!menu || !arrow) return;

    const isOpen = !menu.classList.contains("tw-hidden");

    if (isOpen) {
      menu.classList.add("tw-hidden");
      arrow.classList.add("tw-rotate-[-90deg]");
    } else {
      menu.classList.remove("tw-hidden");
      arrow.classList.remove("tw-rotate-[-90deg]");
    }
  }
</script>