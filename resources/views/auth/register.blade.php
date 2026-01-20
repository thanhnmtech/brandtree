<x-auth-layout>
    <x-slot name="title">{{ __('auth.register_title') }}</x-slot>
    <x-slot name="heroDescription">{{ __('auth.hero_register_description') }}</x-slot>
    <x-slot name="heroTitle">{{ __('auth.hero_register_title') }}</x-slot>
    <x-slot name="heroText">{{ __('auth.hero_register_text') }}</x-slot>

    <form method="POST" action="{{ route('register') }}" class="tw-space-y-6">
        @csrf

        <!-- Full Name -->
        <div class="tw-space-y-2">
            <label for="name" class="tw-text-base md:tw-text-lg tw-font-medium">{{ __('auth.full_name_label') }}</label>
            <input
                type="text"
                id="name"
                name="name"
                value="{{ old('name') }}"
                placeholder="{{ __('auth.full_name_placeholder') }}"
                class="tw-w-full tw-h-[44px] md:tw-h-[50px] tw-border tw-border-gray-300 tw-rounded-full tw-px-5 tw-text-gray-600 tw-text-sm md:tw-text-lg focus:tw-border-green-600 focus:tw-shadow-[0_0_0_3px_rgba(22,162,73,0.15)]"
                required
                autofocus
            />
            @error('name')
                <p class="tw-text-red-600 tw-text-sm tw-mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Email -->
        <div class="tw-space-y-2">
            <label for="email" class="tw-text-base md:tw-text-lg tw-font-medium">{{ __('auth.email_label') }}</label>
            <input
                type="email"
                id="email"
                name="email"
                value="{{ old('email') }}"
                placeholder="{{ __('auth.email_placeholder') }}"
                class="tw-w-full tw-h-[44px] md:tw-h-[50px] tw-border tw-border-gray-300 tw-rounded-full tw-px-5 tw-text-gray-600 tw-text-sm md:tw-text-lg focus:tw-border-green-600 focus:tw-shadow-[0_0_0_3px_rgba(22,162,73,0.15)]"
                required
            />
            @error('email')
                <p class="tw-text-red-600 tw-text-sm tw-mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Phone -->
        <div class="tw-space-y-2">
            <label for="phone" class="tw-text-base md:tw-text-lg tw-font-medium">{{ __('auth.phone_label') }}</label>
            <input
                type="tel"
                id="phone"
                name="phone"
                value="{{ old('phone') }}"
                placeholder="{{ __('auth.phone_placeholder') }}"
                class="tw-w-full tw-h-[44px] md:tw-h-[50px] tw-border tw-border-gray-300 tw-rounded-full tw-px-5 tw-text-gray-600 tw-text-sm md:tw-text-lg focus:tw-border-green-600 focus:tw-shadow-[0_0_0_3px_rgba(22,162,73,0.15)]"
                required
            />
            @error('phone')
                <p class="tw-text-red-600 tw-text-sm tw-mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password -->
        <div class="tw-space-y-2">
            <label for="password" class="tw-text-base md:tw-text-lg tw-font-medium">{{ __('auth.password_label') }}</label>
            <input
                type="password"
                id="password"
                name="password"
                placeholder="{{ __('auth.password_placeholder') }}"
                class="tw-w-full tw-h-[44px] md:tw-h-[50px] tw-border tw-border-gray-300 tw-rounded-full tw-px-5 tw-text-gray-600 tw-text-sm md:tw-text-lg focus:tw-border-green-600 focus:tw-shadow-[0_0_0_3px_rgba(22,162,73,0.15)]"
                required
            />
            @error('password')
                <p class="tw-text-red-600 tw-text-sm tw-mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Confirm Password -->
        <div class="tw-space-y-2">
            <label for="password_confirmation" class="tw-text-base md:tw-text-lg tw-font-medium">{{ __('auth.confirm_password_label') }}</label>
            <input
                type="password"
                id="password_confirmation"
                name="password_confirmation"
                placeholder="{{ __('auth.confirm_password_placeholder') }}"
                class="tw-w-full tw-h-[44px] md:tw-h-[50px] tw-border tw-border-gray-300 tw-rounded-full tw-px-5 tw-text-gray-600 tw-text-sm md:tw-text-lg focus:tw-border-green-600 focus:tw-shadow-[0_0_0_3px_rgba(22,162,73,0.15)]"
                required
            />
            @error('password_confirmation')
                <p class="tw-text-red-600 tw-text-sm tw-mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Terms -->
        <label class="tw-flex tw-items-start tw-gap-3 tw-text-sm md:tw-text-base">
            <input
                type="checkbox"
                name="terms"
                class="tw-w-5 tw-h-5 tw-mt-1 tw-accent-green-600"
                required
            />
            <span>
                {{ __('auth.agree_terms') }}
                <a href="#" class="tw-text-[#16a249] hover:tw-underline">{{ __('auth.terms') }}</a>
                {{ __('auth.and') }}
                <a href="#" class="tw-text-[#16a249] hover:tw-underline">{{ __('auth.privacy_policy') }}</a>.
            </span>
        </label>

        <!-- Submit -->
        <button
            type="submit"
            class="tw-w-full tw-h-[44px] md:tw-h-[50px] tw-bg-[linear-gradient(180deg,_#34b269_0%,_#78d29e_100%)] tw-text-white tw-rounded-lg tw-font-semibold tw-transform tw-transition hover:tw-scale-[1.03] hover:tw-shadow-lg"
        >
            {{ __('auth.register') }}
        </button>

        <!-- Divider -->
        <div class="tw-flex tw-items-center tw-gap-3 tw-my-4 md:tw-my-6">
            <div class="tw-flex-1 tw-h-px tw-bg-gray-300"></div>
            <span class="tw-text-gray-500 tw-text-xs md:tw-text-sm">
                {{ __('auth.or_register_with_google') }}
            </span>
            <div class="tw-flex-1 tw-h-px tw-bg-gray-300"></div>
        </div>

        <!-- Google -->
        <a
            href="{{ route('auth.google') }}"
            class="tw-w-full tw-h-[44px] md:tw-h-[54px] tw-bg-[#f3f7f5] tw-rounded-lg tw-flex tw-items-center tw-justify-center tw-gap-3 tw-font-semibold tw-text-sm md:tw-text-base tw-transition hover:tw-scale-[1.03] hover:tw-shadow-md hover:tw-bg-[#e8f4f0]"
        >
            <img src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/auth/google.svg" class="tw-w-5 tw-h-5" />
            <span>{{ __('auth.continue_with_google') }}</span>
        </a>

        <!-- Sign In Link -->
        <p class="tw-text-center tw-text-sm md:tw-text-base tw-mt-4">
            {{ __('auth.has_account') }}
            <a href="{{ route('login') }}" class="tw-text-[#16a249] tw-font-semibold hover:tw-underline">{{ __('auth.login') }}</a>
        </p>
    </form>
</x-auth-layout>
