<x-auth-layout>
    {{-- Tiêu đề trang --}}
    <x-slot name="title">{{ __('auth.account_type_title') }}</x-slot>
    <x-slot name="heroDescription">{{ __('auth.hero_register_description') }}</x-slot>
    <x-slot name="heroTitle">{{ __('auth.account_type_hero_title') }}</x-slot>
    <x-slot name="heroText">{{ __('auth.account_type_hero_text') }}</x-slot>

    {{-- Form chọn loại tài khoản --}}
    <form method="POST" action="{{ route('account-type.store') }}" class="tw-space-y-8" id="account-type-form">
        @csrf

        {{-- Tiêu đề --}}
        <div class="tw-text-center tw-space-y-2">
            <h2 class="tw-text-2xl md:tw-text-3xl tw-font-bold tw-text-[#1a1a1a]">
                {{ __('auth.account_type_heading') }}
            </h2>
            <p class="tw-text-sm md:tw-text-base tw-text-gray-500">
                {{ __('auth.account_type_subheading') }}
            </p>
        </div>

        {{-- Radio button cards --}}
        <div class="tw-space-y-4">
            {{-- Card: Sinh Viên --}}
            <label for="type_student"
                class="tw-group tw-flex tw-items-center tw-gap-4 tw-p-5 md:tw-p-6 tw-border-2 tw-rounded-2xl tw-cursor-pointer tw-transition-all tw-duration-300
                       tw-border-gray-200 hover:tw-border-[#16a249] hover:tw-shadow-[0_0_0_3px_rgba(22,162,73,0.12)]
                       has-[:checked]:tw-border-[#16a249] has-[:checked]:tw-bg-[#f0fdf4] has-[:checked]:tw-shadow-[0_0_0_3px_rgba(22,162,73,0.15)]"
                id="card-student"
            >
                {{-- Radio input --}}
                <input
                    type="radio"
                    id="type_student"
                    name="account_type"
                    value="student"
                    class="tw-hidden"
                    {{ old('account_type') === 'student' ? 'checked' : '' }}
                />

                {{-- Icon --}}
                <div class="tw-flex-shrink-0 tw-w-14 tw-h-14 md:tw-w-16 md:tw-h-16 tw-rounded-2xl tw-bg-gradient-to-br tw-from-blue-100 tw-to-blue-50 tw-flex tw-items-center tw-justify-center tw-text-3xl md:tw-text-4xl tw-transition-transform tw-duration-300 group-hover:tw-scale-110">
                    🎓
                </div>

                {{-- Nội dung --}}
                <div class="tw-flex-1 tw-min-w-0">
                    <h3 class="tw-text-base md:tw-text-lg tw-font-semibold tw-text-[#1a1a1a]">
                        {{ __('auth.student') }}
                    </h3>
                    <p class="tw-text-xs md:tw-text-sm tw-text-gray-500 tw-mt-0.5 tw-leading-relaxed">
                        {{ __('auth.student_desc') }}
                    </p>
                </div>

                {{-- Checkmark indicator --}}
                <div class="tw-flex-shrink-0 tw-w-6 tw-h-6 tw-rounded-full tw-border-2 tw-border-gray-300 tw-flex tw-items-center tw-justify-center tw-transition-all tw-duration-300"
                     id="check-student">
                    <svg class="tw-w-3.5 tw-h-3.5 tw-text-white tw-opacity-0 tw-transition-opacity tw-duration-300" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                </div>
            </label>

            {{-- Card: Doanh Nghiệp --}}
            <label for="type_business"
                class="tw-group tw-flex tw-items-center tw-gap-4 tw-p-5 md:tw-p-6 tw-border-2 tw-rounded-2xl tw-cursor-pointer tw-transition-all tw-duration-300
                       tw-border-gray-200 hover:tw-border-[#16a249] hover:tw-shadow-[0_0_0_3px_rgba(22,162,73,0.12)]
                       has-[:checked]:tw-border-[#16a249] has-[:checked]:tw-bg-[#f0fdf4] has-[:checked]:tw-shadow-[0_0_0_3px_rgba(22,162,73,0.15)]"
                id="card-business"
            >
                {{-- Radio input --}}
                <input
                    type="radio"
                    id="type_business"
                    name="account_type"
                    value="business"
                    class="tw-hidden"
                    {{ old('account_type') === 'business' ? 'checked' : '' }}
                />

                {{-- Icon --}}
                <div class="tw-flex-shrink-0 tw-w-14 tw-h-14 md:tw-w-16 md:tw-h-16 tw-rounded-2xl tw-bg-gradient-to-br tw-from-amber-100 tw-to-amber-50 tw-flex tw-items-center tw-justify-center tw-text-3xl md:tw-text-4xl tw-transition-transform tw-duration-300 group-hover:tw-scale-110">
                    🏢
                </div>

                {{-- Nội dung --}}
                <div class="tw-flex-1 tw-min-w-0">
                    <h3 class="tw-text-base md:tw-text-lg tw-font-semibold tw-text-[#1a1a1a]">
                        {{ __('auth.business') }}
                    </h3>
                    <p class="tw-text-xs md:tw-text-sm tw-text-gray-500 tw-mt-0.5 tw-leading-relaxed">
                        {{ __('auth.business_desc') }}
                    </p>
                </div>

                {{-- Checkmark indicator --}}
                <div class="tw-flex-shrink-0 tw-w-6 tw-h-6 tw-rounded-full tw-border-2 tw-border-gray-300 tw-flex tw-items-center tw-justify-center tw-transition-all tw-duration-300"
                     id="check-business">
                    <svg class="tw-w-3.5 tw-h-3.5 tw-text-white tw-opacity-0 tw-transition-opacity tw-duration-300" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                </div>
            </label>
        </div>

        {{-- Hiển thị lỗi validation --}}
        @error('account_type')
            <p class="tw-text-red-600 tw-text-sm tw-text-center">{{ $message }}</p>
        @enderror

        {{-- Nút tiếp tục --}}
        <button
            type="submit"
            id="btn-continue"
            class="tw-w-full tw-h-[44px] md:tw-h-[50px] tw-rounded-lg tw-font-semibold tw-text-white tw-transform tw-transition-all tw-duration-300
                   tw-bg-gray-300 tw-cursor-not-allowed"
            disabled
        >
            {{ __('auth.continue_button') }}
        </button>
    </form>

    {{-- JavaScript xử lý UI tương tác --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const radios = document.querySelectorAll('input[name="account_type"]');
            const btnContinue = document.getElementById('btn-continue');

            // Các phần tử check indicator
            const checkStudent = document.getElementById('check-student');
            const checkBusiness = document.getElementById('check-business');

            /**
             * Cập nhật trạng thái giao diện khi chọn radio
             */
            function updateUI() {
                const selected = document.querySelector('input[name="account_type"]:checked');

                // Bật/tắt nút tiếp tục
                if (selected) {
                    btnContinue.disabled = false;
                    btnContinue.classList.remove('tw-bg-gray-300', 'tw-cursor-not-allowed');
                    btnContinue.classList.add(
                        'tw-bg-[linear-gradient(180deg,_#34b269_0%,_#78d29e_100%)]',
                        'tw-cursor-pointer',
                        'hover:tw-scale-[1.03]',
                        'hover:tw-shadow-lg'
                    );
                }

                // Cập nhật checkmark indicators
                [checkStudent, checkBusiness].forEach(function(el) {
                    el.classList.remove('tw-bg-[#16a249]', 'tw-border-[#16a249]');
                    el.classList.add('tw-border-gray-300');
                    el.querySelector('svg').classList.add('tw-opacity-0');
                    el.querySelector('svg').classList.remove('tw-opacity-100');
                });

                if (selected) {
                    const activeCheck = selected.value === 'student' ? checkStudent : checkBusiness;
                    activeCheck.classList.remove('tw-border-gray-300');
                    activeCheck.classList.add('tw-bg-[#16a249]', 'tw-border-[#16a249]');
                    activeCheck.querySelector('svg').classList.remove('tw-opacity-0');
                    activeCheck.querySelector('svg').classList.add('tw-opacity-100');
                }
            }

            // Gắn sự kiện cho từng radio
            radios.forEach(function(radio) {
                radio.addEventListener('change', updateUI);
            });

            // Khởi tạo UI nếu đã có giá trị old
            updateUI();
        });
    </script>

</x-auth-layout>
