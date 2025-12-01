<nav x-data="{ open: false }" class="tw-bg-white tw-shadow-sm tw-sticky tw-top-0 tw-z-50">
    <!-- Primary Navigation Menu -->
    <div class="tw-max-w-7xl tw-mx-auto tw-px-4 sm:tw-px-6 lg:tw-px-8">
        <div class="tw-flex tw-justify-between tw-h-16 md:tw-h-[70px]">
            <div class="tw-flex tw-items-center">
                <!-- Logo -->
                <div class="tw-flex tw-items-center tw-gap-3">
            <img src="{{ asset('html/assets/img/ccf8fd54d8a10b4ab49d514622f1efb57099e1a4.svg') }}" class="tw-w-[42px] tw-h-[42px] tw-object-contain" />
            <div class="tw-leading-tight">
                <h1 class="tw-text-base md:tw-text-lg tw-font-bold">AI Cây Thương Hiệu</h1>
                <p class="tw-text-[10px] md:tw-text-xs tw-text-gray-500 tw--mt-1">By VLBC</p>
            </div>
        </div>
            </div>

            <!-- Navigation Links -->
            <div class="tw-hidden sm:tw-flex sm:tw-items-center sm:tw-ml-8 tw-space-x-6">
                <a href="{{ route('dashboard') }}"
                   class="tw-text-sm tw-font-medium {{ request()->routeIs('dashboard') ? 'tw-text-[#16a249]' : 'tw-text-gray-600 hover:tw-text-gray-800' }} tw-transition">
                    Trang chủ
                </a>
                {{-- <a href="{{ route('brands.index') }}"
                   class="tw-text-sm tw-font-medium {{ request()->routeIs('brands.*') ? 'tw-text-[#16a249]' : 'tw-text-gray-600 hover:tw-text-gray-800' }} tw-transition">
                    Thông b
                </a> --}}
            </div>

            <!-- Settings Dropdown -->
            <div class="tw-hidden sm:tw-flex sm:tw-items-center sm:tw-ml-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="tw-inline-flex tw-items-center tw-px-3 tw-py-2 tw-border tw-border-transparent tw-text-sm tw-leading-4 tw-font-medium tw-rounded-md tw-text-gray-600 hover:tw-text-gray-800 focus:tw-outline-none tw-transition tw-ease-in-out tw-duration-150">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="tw-ml-1">
                                <svg class="tw-fill-current tw-h-4 tw-w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            Hồ sơ
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                Đăng xuất
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="tw--mr-2 tw-flex tw-items-center sm:tw-hidden">
                <button @click="open = ! open" class="tw-inline-flex tw-items-center tw-justify-center tw-p-2 tw-rounded-md tw-text-gray-400 hover:tw-text-gray-600 hover:tw-bg-gray-100 focus:tw-outline-none focus:tw-bg-gray-100 focus:tw-text-gray-600 tw-transition tw-duration-150 tw-ease-in-out">
                    <svg class="tw-h-6 tw-w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'tw-hidden': open, 'tw-inline-flex': ! open }" class="tw-inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'tw-hidden': ! open, 'tw-inline-flex': open }" class="tw-hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'tw-block': open, 'tw-hidden': ! open}" class="tw-hidden sm:tw-hidden">
        <div class="tw-pt-2 tw-pb-3 tw-space-y-1">
            <a href="{{ route('dashboard') }}"
               class="tw-block tw-pl-3 tw-pr-4 tw-py-2 tw-border-l-4 tw-text-base tw-font-medium {{ request()->routeIs('dashboard') ? 'tw-border-[#16a249] tw-text-[#16a249] tw-bg-green-50' : 'tw-border-transparent tw-text-gray-600 hover:tw-text-gray-800 hover:tw-bg-gray-50 hover:tw-border-gray-300' }} tw-transition">
                Dashboard
            </a>
            <a href="{{ route('brands.index') }}"
               class="tw-block tw-pl-3 tw-pr-4 tw-py-2 tw-border-l-4 tw-text-base tw-font-medium {{ request()->routeIs('brands.*') ? 'tw-border-[#16a249] tw-text-[#16a249] tw-bg-green-50' : 'tw-border-transparent tw-text-gray-600 hover:tw-text-gray-800 hover:tw-bg-gray-50 hover:tw-border-gray-300' }} tw-transition">
                Thương hiệu
            </a>
        </div>

        <!-- Responsive Settings Options -->
        <div class="tw-pt-4 tw-pb-1 tw-border-t tw-border-gray-200">
            <div class="tw-px-4">
                <div class="tw-font-medium tw-text-base tw-text-gray-800">{{ Auth::user()->name }}</div>
                <div class="tw-font-medium tw-text-sm tw-text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="tw-mt-3 tw-space-y-1">
                <a href="{{ route('profile.edit') }}"
                   class="tw-block tw-pl-3 tw-pr-4 tw-py-2 tw-border-l-4 tw-border-transparent tw-text-base tw-font-medium tw-text-gray-600 hover:tw-text-gray-800 hover:tw-bg-gray-50 hover:tw-border-gray-300 tw-transition">
                    Hồ sơ
                </a>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <a href="{{ route('logout') }}"
                       onclick="event.preventDefault(); this.closest('form').submit();"
                       class="tw-block tw-pl-3 tw-pr-4 tw-py-2 tw-border-l-4 tw-border-transparent tw-text-base tw-font-medium tw-text-gray-600 hover:tw-text-gray-800 hover:tw-bg-gray-50 hover:tw-border-gray-300 tw-transition">
                        Đăng xuất
                    </a>
                </form>
            </div>
        </div>
    </div>
</nav>
