<section class="tw-space-y-6">
    <header>
        <h2 class="tw-text-xl tw-font-semibold tw-text-gray-800">
            Xóa tài khoản
        </h2>

        <p class="tw-mt-2 tw-text-sm tw-text-gray-600">
            Sau khi tài khoản của bạn bị xóa, tất cả tài nguyên và dữ liệu của nó sẽ bị xóa vĩnh viễn. Trước khi xóa tài khoản, vui lòng tải xuống bất kỳ dữ liệu hoặc thông tin nào bạn muốn giữ lại.
        </p>
    </header>

    <button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        type="button"
        class="tw-h-[44px] md:tw-h-[50px] tw-px-6 md:tw-px-8 tw-bg-red-600 tw-text-white tw-rounded-lg tw-font-semibold tw-text-sm md:tw-text-base tw-transition tw-transform hover:tw-scale-[1.03] hover:tw-shadow-lg hover:tw-bg-red-700">
        Xóa tài khoản
    </button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="tw-p-6">
            @csrf
            @method('delete')

            <h2 class="tw-text-lg tw-font-medium tw-text-gray-900">
                Bạn có chắc chắn muốn xóa tài khoản của mình?
            </h2>

            <p class="tw-mt-1 tw-text-sm tw-text-gray-600">
                @if(Auth::user()->google_id)
                    Sau khi tài khoản của bạn bị xóa, tất cả tài nguyên và dữ liệu của nó sẽ bị xóa vĩnh viễn. Hành động này không thể hoàn tác.
                @else
                    Sau khi tài khoản của bạn bị xóa, tất cả tài nguyên và dữ liệu của nó sẽ bị xóa vĩnh viễn. Vui lòng nhập mật khẩu của bạn để xác nhận bạn muốn xóa vĩnh viễn tài khoản của mình.
                @endif
            </p>

            @if(!Auth::user()->google_id)
            <div class="tw-mt-6">
                <label for="password" class="tw-sr-only">Mật khẩu</label>

                <input
                    id="password"
                    name="password"
                    type="password"
                    placeholder="Mật khẩu"
                    class="tw-w-3/4 tw-h-[44px] md:tw-h-[50px] tw-border tw-border-gray-300 tw-rounded-full tw-px-4 md:tw-px-5 tw-text-gray-600 tw-text-sm md:tw-text-base focus:tw-border-red-600 focus:tw-shadow-[0_0_0_3px_rgba(220,38,38,0.15)] focus:tw-outline-none tw-transition"
                />

                @error('password', 'userDeletion')
                    <p class="tw-text-red-600 tw-text-sm tw-mt-1">{{ $message }}</p>
                @enderror
            </div>
            @endif

            <div class="tw-mt-6 tw-flex tw-justify-end tw-gap-3">
                <button
                    type="button"
                    x-on:click="$dispatch('close')"
                    class="tw-h-[44px] md:tw-h-[50px] tw-px-6 md:tw-px-8 tw-bg-gray-200 tw-text-gray-700 tw-rounded-lg tw-font-semibold tw-text-sm md:tw-text-base tw-transition tw-transform hover:tw-scale-[1.03] hover:tw-shadow-lg hover:tw-bg-gray-300">
                    Hủy
                </button>

                <button
                    type="submit"
                    class="tw-h-[44px] md:tw-h-[50px] tw-px-6 md:tw-px-8 tw-bg-red-600 tw-text-white tw-rounded-lg tw-font-semibold tw-text-sm md:tw-text-base tw-transition tw-transform hover:tw-scale-[1.03] hover:tw-shadow-lg hover:tw-bg-red-700">
                    Xóa tài khoản
                </button>
            </div>
        </form>
    </x-modal>
</section>
