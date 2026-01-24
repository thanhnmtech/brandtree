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
        <!-- ==== MENU DESKTOP (>=1024px) ==== -->
        @if(!empty($currentBrand))
            <div class="tw-hidden lg:tw-flex tw-flex-1 tw-justify-center tw-items-center tw-gap-[20px] lg:tw-gap-[40px]">
                <a href="{{ route('brands.show', $currentBrand) }}"
                    class="{{ request()->routeIs('brands.show') ? 'tw-bg-vlbcgreen tw-text-white' : 'tw-bg-transparent tw-text-black hover:tw-text-vlbcgreen' }} tw-text-[14px] tw-font-semibold tw-cursor-pointer tw-px-4 tw-py-1 tw-rounded-full tw-transition-colors tw-duration-200">
                    <i class="ri-home-line tw-transition"></i> Trang chủ
                </a>
                <a href="{{ route('brands.root.show', $currentBrand) }}"
                    class="{{ request()->routeIs('brands.root.show') ? 'tw-bg-vlbcgreen tw-text-white tw-px-4 tw-py-1 tw-rounded-full' : 'tw-text-black hover:tw-text-vlbcgreen' }} tw-text-[14px] tw-font-semibold tw-cursor-pointer tw-transition-colors tw-duration-200">
                    <i class="ri-plant-line tw-transition"></i> Gốc cây
                </a>
                <a href="{{ route('brands.trunk.show', $currentBrand) }}"
                    class="{{ request()->routeIs('brands.trunk.show') ? 'tw-bg-vlbcgreen tw-text-white tw-px-4 tw-py-1 tw-rounded-full' : 'tw-text-black hover:tw-text-vlbcgreen' }} tw-text-[14px] tw-font-semibold tw-cursor-pointer tw-transition-colors tw-duration-200">
                    <i class="ri-tree-line tw-transition"></i> Thân cây
                </a>
                <a href="{{ route('brands.canopy.show', $currentBrand) }}"
                    class="{{ request()->routeIs('brands.canopy.show') ? 'tw-bg-vlbcgreen tw-text-white tw-px-4 tw-py-1 tw-rounded-full' : 'tw-text-black hover:tw-text-vlbcgreen' }} tw-text-[14px] tw-font-semibold tw-cursor-pointer tw-transition-colors tw-duration-200">
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
                    class="{{ request()->routeIs('brands.root.show') ? 'tw-text-vlbcgreen' : 'tw-text-gray-800 hover:tw-text-green-600' }} tw-transition">
                    <i class="ri-plant-line"></i>
                </a>

                <!-- THÂN CÂY -->
                <a href="{{ route('brands.trunk.show', $currentBrand) }}"
                    class="{{ request()->routeIs('brands.trunk.show') ? 'tw-text-vlbcgreen' : 'tw-text-gray-800 hover:tw-text-green-600' }} tw-transition">
                    <i class="ri-tree-line"></i>
                </a>

                <!-- TÁN CÂY -->
                <a href="{{ route('brands.canopy.show', $currentBrand) }}"
                    class="{{ request()->routeIs('brands.canopy.show') ? 'tw-text-vlbcgreen' : 'tw-text-gray-800 hover:tw-text-green-600' }} tw-transition">
                    <i class="ri-leaf-line"></i>
                </a>
            </div>
        @endif

        <div class="tw-hidden md:tw-flex tw-items-center tw-justify-end tw-gap-[14px]">
                             <x-language-switcher />

            <!-- Avatar (ONLY ONE) — CLICK TO OPEN POPUP -->
            <button onclick="toggleAccountPopup()">
                <img id="accountBtn" src="{{ asset('assets/img/default-avatar.svg') }}"
                    class="tw-w-[36px] tw-h-[36px] tw-rounded-full tw-object-cover tw-cursor-pointer tw-border tw-border-gray-300"
                    alt="avatar" />
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
                <div class="tw-w-12 tw-h-12 tw-rounded-full tw-bg-white/40"></div>
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
</header>
