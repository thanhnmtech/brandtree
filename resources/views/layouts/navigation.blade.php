<header class="tw-h-[60px]">
    <div
        class="tw-fixed tw-top-0 tw-left-0 tw-w-full tw-h-[60px] tw-bg-[#F3F7F5] tw-flex tw-items-center tw-justify-between tw-px-0 md:tw-px-8 tw-z-[999]">
        <!-- Menu icon -->
        <button class="tw-p-4 tw-text-gray-800 tw-text-2xl md:tw-hidden" onclick="toggleSidebarMobile()">
            <i class="ri-menu-line"></i>
        </button>
        <!-- Logo + Title -->
        <a href="{{ route('dashboard') }}" class="tw-flex tw-items-center tw-gap-[14px]">
            <img src="{{ asset('assets/img/ccf8fd54d8a10b4ab49d514622f1efb57099e1a4.svg') }}"
                class="tw-w-[28px] tw-h-[28px] md:tw-w-[34px] md:tw-h-[34px] tw-object-contain" alt="logo" />
            <div class="dashboard-page__title">
                <h1 class="tw-text md:tw-text-lg tw-font-bold">
                    Vườn Cây Thương Hiệu
                </h1>
                <p class="tw-hidden md:tw-block md:tw-text-sm tw-text-gray-500 tw--mt-0">
                    Quản lý danh mục thương hiệu của bạn
                </p>
            </div>
        </a>

        <!-- ==== MENU DESKTOP (>=1024px) ==== -->
        @if(!empty($currentBrand))
            <div class="tw-hidden lg:tw-flex tw-flex-1 tw-justify-center tw-items-center tw-gap-[20px] lg:tw-gap-[40px]">
                <a href="{{ route('brands.show', $currentBrand) }}"
                    class="{{ request()->routeIs('brands.show') ? 'tw-bg-vlbcgreen tw-text-white' : 'tw-bg-transparent tw-text-black hover:tw-text-vlbcgreen' }} tw-text-[14px] tw-font-semibold tw-cursor-pointer tw-px-4 tw-py-1 tw-rounded-full tw-transition-colors tw-duration-200">
                    <i class="ri-home-line tw-transition"></i> Trang chủ
                </a>
                {{-- Gốc cây với dropdown --}}
                <div class="tw-relative tw-group">
                    <a href="{{ route('brands.root.show', $currentBrand) }}"
                        class="{{ request()->routeIs('brands.root.show') || request()->is('*/chat/root*') ? 'tw-bg-vlbcgreen tw-text-white tw-px-4 tw-py-1 tw-rounded-full' : 'tw-text-black hover:tw-text-vlbcgreen' }} tw-text-[14px] tw-font-semibold tw-cursor-pointer tw-transition-colors tw-duration-200">
                        <i class="ri-plant-line tw-transition"></i> Gốc cây
                    </a>
                    {{-- Dropdown menu --}}
                    <div class="tw-absolute tw-hidden group-hover:tw-block tw-w-[246px] tw-top-full tw-left-1/2 tw--translate-x-1/2 tw-pt-2 tw-z-50">
                        <div class="tw-border tw-border-gray-300 tw-bg-white tw-rounded-[4px] tw-shadow-lg tw-overflow-hidden">
                            @php
                                $rootData = $currentBrand->root_data ?? [];
                                $prevStepDone = true; // First step is always ready
                            @endphp
                            @foreach(config('timeline_steps.root') as $key => $step)
                                @php
                                    $isDone = !empty($rootData[$key]);
                                    // Chỉ root1 mở mặc định, các step sau chỉ mở khi step trước đã có data
                                    $isFirstStep = ($key === 'root1');
                                    $isUnlocked = $isFirstStep ? true : $prevStepDone;
                                    
                                    // Kiểm tra xem đang ở trang chat của step này không
                                    // URL: /brands/{slug}/chat/{agentType} => segment(4) = agentType
                                    $currentAgentType = request()->segment(4);
                                    $isActiveChat = (request()->segment(3) === 'chat' && $currentAgentType === $key);
                                    
                                    // Style class: nếu active thì đổi bg, nếu không thì theo trạng thái unlocked/locked
                                    if ($isActiveChat) {
                                        $styleClass = 'tw-text-vlbcgreen tw-bg-[#D9F2E2]';
                                    } else {
                                        $styleClass = $isUnlocked 
                                            ? 'tw-text-vlbcgreen tw-bg-[#F4FCF7]' 
                                            : 'tw-text-[#7B7773] tw-bg-[#e7e5df]';
                                    }
                                @endphp
                                @if($isUnlocked || $isActiveChat)
                                <a href="{{ route('chat', ['brand' => $currentBrand->slug, 'agentType' => $key]) }}"
                                   data-nav-key="{{ $key }}"
                                   class="tw-block tw-h-[28px] tw-px-3 tw-flex tw-items-center tw-text-sm {{ $styleClass }} hover:tw-opacity-80 tw-transition-opacity tw-border-b tw-border-gray-200 last:tw-border-b-0">
                                    {{ $step['label'] }}
                                </a>
                                @else
                                <span data-nav-key="{{ $key }}"
                                   class="tw-block tw-h-[28px] tw-px-3 tw-flex tw-items-center tw-text-sm {{ $styleClass }} tw-cursor-not-allowed tw-border-b tw-border-gray-200 last:tw-border-b-0">
                                    {{ $step['label'] }}
                                </span>
                                @endif
                                @php
                                    $prevStepDone = $isDone;
                                @endphp
                            @endforeach
                        </div>
                    </div>
                </div>
                {{-- Thân cây với dropdown --}}
                <div class="tw-relative tw-group">
                    <a href="{{ route('brands.trunk.show', $currentBrand) }}"
                        class="{{ request()->routeIs('brands.trunk.show') || request()->is('*/chat/trunk*') ? 'tw-bg-vlbcgreen tw-text-white tw-px-4 tw-py-1 tw-rounded-full' : 'tw-text-black hover:tw-text-vlbcgreen' }} tw-text-[14px] tw-font-semibold tw-cursor-pointer tw-transition-colors tw-duration-200">
                        <i class="ri-tree-line tw-transition"></i> Thân cây
                    </a>
                    {{-- Dropdown menu --}}
                    <div class="tw-absolute tw-hidden group-hover:tw-block tw-w-[246px] tw-top-full tw-left-1/2 tw--translate-x-1/2 tw-pt-2 tw-z-50">
                        <div class="tw-border tw-border-gray-300 tw-bg-white tw-rounded-[4px] tw-shadow-lg tw-overflow-hidden">
                            @php
                                $trunkData = $currentBrand->trunk_data ?? [];
                                // Kiểm tra xem tất cả root đã hoàn thành chưa
                                $rootSteps = array_keys(config('timeline_steps.root'));
                                $allRootCompleted = true;
                                foreach ($rootSteps as $rootKey) {
                                    if (empty($rootData[$rootKey])) {
                                        $allRootCompleted = false;
                                        break;
                                    }
                                }
                                $prevTrunkStepDone = $allRootCompleted;
                            @endphp
                            @foreach(config('timeline_steps.trunk') as $key => $step)
                                @php
                                    $isDone = !empty($trunkData[$key]);
                                    // trunk1 mở nếu root xong hết, các step sau mở khi step trước đã có data
                                    $isFirstTrunkStep = ($key === 'trunk1');
                                    $isUnlocked = $isFirstTrunkStep ? $allRootCompleted : $prevTrunkStepDone;
                                    
                                    // Kiểm tra xem đang ở trang chat của step này không
                                    // URL: /brands/{slug}/chat/{agentType} => segment(4) = agentType
                                    $currentAgentType = request()->segment(4);
                                    $isActiveChat = (request()->segment(3) === 'chat' && $currentAgentType === $key);
                                    
                                    // Style class: nếu active thì đổi bg, nếu không thì theo trạng thái unlocked/locked
                                    if ($isActiveChat) {
                                        $styleClass = 'tw-text-vlbcgreen tw-bg-[#D9F2E2]';
                                    } else {
                                        $styleClass = $isUnlocked 
                                            ? 'tw-text-vlbcgreen tw-bg-[#F4FCF7]' 
                                            : 'tw-text-[#7B7773] tw-bg-[#e7e5df]';
                                    }
                                @endphp
                                @if($isUnlocked || $isActiveChat)
                                <a href="{{ route('chat', ['brand' => $currentBrand->slug, 'agentType' => $key]) }}"
                                   data-nav-key="{{ $key }}"
                                   class="tw-block tw-h-[28px] tw-px-3 tw-flex tw-items-center tw-text-sm {{ $styleClass }} hover:tw-opacity-80 tw-transition-opacity tw-border-b tw-border-gray-200 last:tw-border-b-0">
                                    {{ $step['label'] }}
                                </a>
                                @else
                                <span data-nav-key="{{ $key }}"
                                   class="tw-block tw-h-[28px] tw-px-3 tw-flex tw-items-center tw-text-sm {{ $styleClass }} tw-cursor-not-allowed tw-border-b tw-border-gray-200 last:tw-border-b-0">
                                    {{ $step['label'] }}
                                </span>
                                @endif
                                @php
                                    $prevTrunkStepDone = $isDone;
                                @endphp
                            @endforeach
                        </div>
                    </div>
                </div>
                <a href="{{ route('brands.canopy.show', $currentBrand) }}"
                    class="{{ request()->routeIs('brands.canopy.show') || request()->is('*/chat/canopy*') ? 'tw-bg-vlbcgreen tw-text-white tw-px-4 tw-py-1 tw-rounded-full' : 'tw-text-black hover:tw-text-vlbcgreen' }} tw-text-[14px] tw-font-semibold tw-cursor-pointer tw-transition-colors tw-duration-200">
                    <i class="ri-leaf-line tw-transition"></i> Tán cây
                </a>
            </div>
        @endif

        <!-- ==== MENU MOBILE (<1024px) ==== -->
        @if(!empty($currentBrand))
            <div
                class="tw-flex tw-flex-1 lg:tw-hidden tw-items-center tw-justify-evenly tw-text-gray-800 tw-text-xl tw-gap-5">
                <!-- GỐC CÂY -->
                <a href="{{ route('brands.root.show', $currentBrand) }}"
                    class="{{ request()->routeIs('brands.root.show') || request()->is('*/chat/root*') ? 'tw-text-vlbcgreen' : 'tw-text-gray-800 hover:tw-text-green-600' }} tw-transition">
                    <i class="ri-plant-line"></i>
                </a>

                <!-- THÂN CÂY -->
                <a href="{{ route('brands.trunk.show', $currentBrand) }}"
                    class="{{ request()->routeIs('brands.trunk.show') || request()->is('*/chat/trunk*') ? 'tw-text-vlbcgreen' : 'tw-text-gray-800 hover:tw-text-green-600' }} tw-transition">
                    <i class="ri-tree-line"></i>
                </a>

                <!-- TÁN CÂY -->
                <a href="{{ route('brands.canopy.show', $currentBrand) }}"
                    class="{{ request()->routeIs('brands.canopy.show') || request()->is('*/chat/canopy*') ? 'tw-text-vlbcgreen' : 'tw-text-gray-800 hover:tw-text-green-600' }} tw-transition">
                    <i class="ri-leaf-line"></i>
                </a>
            </div>
        @endif

        <div class="tw-hidden md:tw-flex tw-items-center tw-justify-end tw-gap-[14px]">
                             <x-language-switcher />

            <!-- Avatar (ONLY ONE) — CLICK TO OPEN POPUP -->
            <button onclick="toggleAccountPopup()">
                @if(auth()->user()->avatar)
                    <img id="accountBtn" src="{{ Storage::url(auth()->user()->avatar) }}"
                        class="tw-w-[36px] tw-h-[36px] tw-rounded-full tw-object-cover tw-cursor-pointer tw-border tw-border-gray-300"
                        alt="avatar" />
                @else
                    <img id="accountBtn" src="{{ asset('assets/img/default-avatar.svg') }}"
                        class="tw-w-[36px] tw-h-[36px] tw-rounded-full tw-object-cover tw-cursor-pointer tw-border tw-border-gray-300"
                        alt="avatar" />
                @endif
            </button>
        </div>

        <div id="accountBtn"
            class="tw-flex md:tw-flex-1 md:tw-hidden tw-p-4 tw-items-center tw-gap-5 tw-text-gray-800 tw-text-xl">
            <!-- USER -->
            <button onclick="toggleAccountPopup()">
                <i class="ri-user-3-line hover:tw-text-green-600 tw-transition"></i>
            </button>
        </div>
    </div>

    <!-- ============= ACCOUNT POPUP ============= -->
    <div id="accountPopup"
        class="tw-fixed tw-top-[70px] tw-right-5 tw-w-72 md:tw-w-[360px] tw-bg-[#4E634B] tw-text-white tw-rounded-2xl tw-shadow-2xl tw-border tw-border-black/20 tw-hidden tw-transition-all tw-duration-300 tw-z-[9999]">
        <div class="tw-p-5">
            <p class="tw-text-sm tw-opacity-80">Tài khoản</p>
            <div class="tw-flex tw-items-center tw-gap-3 tw-mt-3 tw-py-2">
                {{-- Avatar với icon camera để thay đổi --}}
                <div class="tw-relative tw-group/avatar">
                    @if(auth()->user()->avatar)
                        <img id="popupCurrentAvatar" src="{{ Storage::url(auth()->user()->avatar) }}" alt="{{ auth()->user()->name }}" class="tw-w-12 tw-h-12 tw-rounded-full tw-object-cover">
                    @else
                        <div id="popupCurrentAvatar" class="tw-w-12 tw-h-12 tw-rounded-full tw-bg-white/40 tw-flex tw-items-center tw-justify-center">
                            <i class="ri-user-line tw-text-white/60 tw-text-xl"></i>
                        </div>
                    @endif
                    {{-- Icon camera ở góc dưới-phải --}}
                    <button type="button" onclick="openAvatarPreviewPopup()" 
                        class="tw-absolute tw-bottom-0 tw-right-0 tw-w-5 tw-h-5 tw-bg-white tw-rounded-full tw-flex tw-items-center tw-justify-center tw-shadow-md tw-border tw-border-gray-200 tw-cursor-pointer hover:tw-bg-gray-100 tw-transition-colors"
                        title="Thay đổi ảnh đại diện">
                        <i class="ri-camera-line tw-text-gray-600 tw-text-xs"></i>
                    </button>
                </div>
                <div>
                    <p class="tw-font-semibold">{{ request()->user()->name }}</p>
                    <p class="tw-text-sm tw-opacity-80">{{ request()->user()->email }}</p>
                </div>
            </div>

            <div class="tw-h-px tw-bg-white/30 tw-my-4"></div>

            <div class="tw-space-y-4">
                <a href="{{ route('profile.edit') }}" class="tw-flex tw-items-center tw-gap-3 hover:tw-opacity-80">
                    <i class="ri-settings-3-line tw-text md:tw-text-xl"></i> Cập nhật tài khoản
                </a>
                {{-- <a href="#" class="tw-flex tw-items-center tw-gap-3 hover:tw-opacity-80">
                    <i class="ri-shopping-cart-2-line tw-text md:tw-text-xl"></i>Lịch sử đơn hàng
                </a> --}}
            </div>

            <div class="tw-h-px tw-bg-white/30 tw-my-4"></div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="tw-flex tw-items-center tw-gap-3 hover:tw-opacity-80 tw-bg-transparent tw-border-0 tw-cursor-pointer tw-w-full tw-text-left tw-p-0">
                    <i class="ri-logout-box-r-line tw-text md:tw-text-xl"></i> Thoát
                </button>
            </form>
        </div>
    </div>
    <x-avatar-upload-popup />

    <script>
        const popup = document.getElementById('accountPopup');
        const toggleBtn = document.getElementById('accountBtn');

        toggleBtn?.addEventListener('click', (e) => {
            e.stopPropagation();
            popup.classList.toggle('tw-hidden');
        });
        document.addEventListener('click', (e) => {
            if (!popup.contains(e.target) && !popup.classList.contains('tw-hidden')) {
                popup.classList.add('tw-hidden');
            }
        });
        popup.addEventListener('click', (e) => {
            e.stopPropagation();
        });
    </script>
</header>

