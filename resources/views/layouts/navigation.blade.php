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
            @php
                $rootData = $currentBrand->root_data ?? [];
                $trunkData = $currentBrand->trunk_data ?? [];
                $allSteps = ['root1', 'root2', 'root3', 'trunk1', 'trunk2'];
                $maxReachedIndex = -1;
                foreach ($allSteps as $idx => $sKey) {
                    if (in_array($sKey, ['root1', 'root2', 'root3'])) {
                        if (!empty($rootData[$sKey]) && is_string($rootData[$sKey]) && trim($rootData[$sKey]) !== '') {
                            $maxReachedIndex = max($maxReachedIndex, $idx);
                        }
                    } else {
                        if (!empty($trunkData[$sKey]) && is_string($trunkData[$sKey]) && trim($trunkData[$sKey]) !== '') {
                            $maxReachedIndex = max($maxReachedIndex, $idx);
                        }
                    }
                }
                $currentAgentType = request()->segment(4);
                $currentStepIndex = array_search($currentAgentType, $allSteps);
                if ($currentStepIndex !== false) {
                    $maxReachedIndex = max($maxReachedIndex, $currentStepIndex - 1);
                }
            @endphp
            <div class="tw-hidden lg:tw-flex tw-flex-1 tw-justify-center tw-items-center tw-gap-[20px] lg:tw-gap-[40px]">
                <a href="{{ route('brands.show', $currentBrand) }}"
                    class="{{ request()->routeIs('brands.show') ? 'tw-bg-vlbcgreen tw-text-white' : 'tw-bg-transparent tw-text-black hover:tw-text-vlbcgreen' }} tw-text-[14px] tw-font-semibold tw-cursor-pointer tw-px-4 tw-py-1 tw-rounded-full tw-transition-colors tw-duration-200">
                    Trang chủ
                </a>
                {{-- Gốc cây với dropdown --}}
                <div class="tw-relative tw-group">
                    <a href="{{ route('brands.root.show', $currentBrand) }}"
                        class="{{ request()->routeIs('brands.root.show') || request()->is('*/chat/root*') ? 'tw-bg-vlbcgreen tw-text-white tw-px-4 tw-py-1 tw-rounded-full' : 'tw-text-black hover:tw-text-vlbcgreen' }} tw-text-[14px] tw-font-semibold tw-cursor-pointer tw-transition-colors tw-duration-200">
                        Gốc cây
                    </a>
                    {{-- Dropdown menu --}}
                    <div class="tw-absolute tw-hidden group-hover:tw-block tw-w-[246px] tw-top-full tw-left-1/2 tw--translate-x-1/2 tw-pt-2 tw-z-50">
                        <div class="tw-border tw-border-gray-300 tw-bg-white tw-rounded-[4px] tw-shadow-lg tw-overflow-hidden">
                            @foreach(config('timeline_steps.root') as $key => $step)
                                @php
                                    $isDone = !empty($rootData[$key]) && is_string($rootData[$key]) && trim($rootData[$key]) !== '';
                                    
                                    $stepIndex = array_search($key, $allSteps);
                                    $isUnlocked = ($stepIndex <= $maxReachedIndex + 1);
                                    
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
                            @endforeach
                        </div>
                    </div>
                </div>
                {{-- Thân cây với dropdown --}}
                <div class="tw-relative tw-group">
                    <a href="{{ route('brands.trunk.show', $currentBrand) }}"
                        class="{{ request()->routeIs('brands.trunk.show') || request()->is('*/chat/trunk*') ? 'tw-bg-vlbcgreen tw-text-white tw-px-4 tw-py-1 tw-rounded-full' : 'tw-text-black hover:tw-text-vlbcgreen' }} tw-text-[14px] tw-font-semibold tw-cursor-pointer tw-transition-colors tw-duration-200">
                        Thân cây
                    </a>
                    {{-- Dropdown menu --}}
                    <div class="tw-absolute tw-hidden group-hover:tw-block tw-w-[246px] tw-top-full tw-left-1/2 tw--translate-x-1/2 tw-pt-2 tw-z-50">
                        <div class="tw-border tw-border-gray-300 tw-bg-white tw-rounded-[4px] tw-shadow-lg tw-overflow-hidden">
                            @foreach(config('timeline_steps.trunk') as $key => $step)
                                @php
                                    $isDone = !empty($trunkData[$key]) && is_string($trunkData[$key]) && trim($trunkData[$key]) !== '';
                                    
                                    $stepIndex = array_search($key, $allSteps);
                                    $isUnlocked = ($stepIndex <= $maxReachedIndex + 1);
                                    
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
                            @endforeach
                        </div>
                    </div>
                </div>
                <a href="{{ route('brands.canopy.show', $currentBrand) }}"
                    class="{{ request()->routeIs('brands.canopy.show') || request()->is('*/chat/canopy*') ? 'tw-bg-vlbcgreen tw-text-white tw-px-4 tw-py-1 tw-rounded-full' : 'tw-text-black hover:tw-text-vlbcgreen' }} tw-text-[14px] tw-font-semibold tw-cursor-pointer tw-transition-colors tw-duration-200">
                    Tán cây
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
                    <img src="{{ asset('assets/img/icon-goc-black.svg') }}" class="tw-w-[15px] tw-h-[16px]" />
                </a>

                <!-- THÂN CÂY -->
                <a href="{{ route('brands.trunk.show', $currentBrand) }}"
                    class="{{ request()->routeIs('brands.trunk.show') || request()->is('*/chat/trunk*') ? 'tw-text-vlbcgreen' : 'tw-text-gray-800 hover:tw-text-green-600' }} tw-transition">
                    <img src="{{ asset('assets/img/icon-than-black.svg') }}" class="tw-w-[14px] tw-h-[16px]" />
                </a>

                <!-- TÁN CÂY -->
                <a href="{{ route('brands.canopy.show', $currentBrand) }}"
                    class="{{ request()->routeIs('brands.canopy.show') || request()->is('*/chat/canopy*') ? 'tw-text-vlbcgreen' : 'tw-text-gray-800 hover:tw-text-green-600' }} tw-transition">
                    <img src="{{ asset('assets/img/icon-tan-black.svg') }}" class="tw-w-[16px] tw-h-[16px]" />
                </a>
            </div>
        @endif

        <div class="tw-hidden md:tw-flex tw-items-center tw-justify-end tw-gap-[14px]">
                             <x-language-switcher />

            <!-- Avatar (ONLY ONE) — CLICK TO OPEN POPUP -->
            <button onclick="toggleAccountPopup()">
                @if(auth()->user()->avatar)
                    <img src="{{ Storage::url(auth()->user()->avatar) }}"
                        class="tw-w-[36px] tw-h-[36px] tw-rounded-full tw-object-cover tw-cursor-pointer tw-border tw-border-gray-300"
                        alt="avatar" />
                @else
                    <img src="{{ asset('assets/img/default-avatar.svg') }}"
                        class="tw-w-[36px] tw-h-[36px] tw-rounded-full tw-object-cover tw-cursor-pointer tw-border tw-border-gray-300"
                        alt="avatar" />
                @endif
            </button>
        </div>

        <div id="accountBtnMobile"
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
        // Hàm toggle popup tài khoản — dùng chung cho cả desktop và mobile
        function toggleAccountPopup() {
            const popup = document.getElementById('accountPopup');
            if (popup) {
                popup.classList.toggle('tw-hidden');
            }
        }

        // Đóng popup khi click bên ngoài
        document.addEventListener('click', (e) => {
            const popup = document.getElementById('accountPopup');
            const mobileBtn = document.getElementById('accountBtnMobile');
            if (!popup || popup.classList.contains('tw-hidden')) return;

            // Nếu click nằm trong popup hoặc trong nút mobile → bỏ qua
            const isInsidePopup = popup.contains(e.target);
            const isInsideMobileBtn = mobileBtn && mobileBtn.contains(e.target);
            // Kiểm tra nút desktop (onclick đã gọi toggleAccountPopup)
            const isToggleBtn = e.target.closest('button[onclick="toggleAccountPopup()"]');

            if (!isInsidePopup && !isInsideMobileBtn && !isToggleBtn) {
                popup.classList.add('tw-hidden');
            }
        });
    </script>
</header>

