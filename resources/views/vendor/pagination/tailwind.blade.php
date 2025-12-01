@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="tw-flex tw-items-center tw-justify-between tw-bg-white tw-rounded-2xl tw-shadow-sm tw-p-4">
        <div class="tw-flex tw-justify-between tw-flex-1 sm:tw-hidden">
            @if ($paginator->onFirstPage())
                <span class="tw-relative tw-inline-flex tw-items-center tw-px-4 tw-py-2 tw-text-sm tw-font-medium tw-text-gray-400 tw-bg-gray-100 tw-border tw-border-gray-200 tw-cursor-default tw-rounded-lg">
                    Trước
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="tw-relative tw-inline-flex tw-items-center tw-px-4 tw-py-2 tw-text-sm tw-font-medium tw-text-gray-700 tw-bg-white tw-border tw-border-gray-300 tw-rounded-lg hover:tw-bg-gray-50 tw-transition">
                    Trước
                </a>
            @endif

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="tw-relative tw-inline-flex tw-items-center tw-px-4 tw-py-2 tw-ml-3 tw-text-sm tw-font-medium tw-text-gray-700 tw-bg-white tw-border tw-border-gray-300 tw-rounded-lg hover:tw-bg-gray-50 tw-transition">
                    Sau
                </a>
            @else
                <span class="tw-relative tw-inline-flex tw-items-center tw-px-4 tw-py-2 tw-ml-3 tw-text-sm tw-font-medium tw-text-gray-400 tw-bg-gray-100 tw-border tw-border-gray-200 tw-cursor-default tw-rounded-lg">
                    Sau
                </span>
            @endif
        </div>

        <div class="tw-hidden sm:tw-flex-1 sm:tw-flex sm:tw-items-center sm:tw-justify-between">
            <div>
                <p class="tw-text-sm tw-text-gray-600">
                    Hiển thị
                    @if ($paginator->firstItem())
                        <span class="tw-font-medium">{{ $paginator->firstItem() }}</span>
                        đến
                        <span class="tw-font-medium">{{ $paginator->lastItem() }}</span>
                    @else
                        {{ $paginator->count() }}
                    @endif
                    trong
                    <span class="tw-font-medium">{{ $paginator->total() }}</span>
                    kết quả
                </p>
            </div>

            <div>
                <span class="tw-relative tw-z-0 tw-inline-flex tw-rounded-lg tw-overflow-hidden">
                    {{-- Previous Page Link --}}
                    @if ($paginator->onFirstPage())
                        <span aria-disabled="true" aria-label="Trang trước">
                            <span class="tw-relative tw-inline-flex tw-items-center tw-px-3 tw-py-2 tw-text-sm tw-font-medium tw-text-gray-400 tw-bg-gray-100 tw-border tw-border-gray-200 tw-cursor-default" aria-hidden="true">
                                <svg class="tw-w-5 tw-h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                            </span>
                        </span>
                    @else
                        <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="tw-relative tw-inline-flex tw-items-center tw-px-3 tw-py-2 tw-text-sm tw-font-medium tw-text-gray-600 tw-bg-white tw-border tw-border-gray-300 hover:tw-bg-gray-50 tw-transition" aria-label="Trang trước">
                            <svg class="tw-w-5 tw-h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                        </a>
                    @endif

                    {{-- Pagination Elements --}}
                    @foreach ($elements as $element)
                        {{-- "Three Dots" Separator --}}
                        @if (is_string($element))
                            <span aria-disabled="true">
                                <span class="tw-relative tw-inline-flex tw-items-center tw-px-4 tw-py-2 tw--ml-px tw-text-sm tw-font-medium tw-text-gray-700 tw-bg-white tw-border tw-border-gray-300 tw-cursor-default">{{ $element }}</span>
                            </span>
                        @endif

                        {{-- Array Of Links --}}
                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                    <span aria-current="page">
                                        <span class="tw-relative tw-inline-flex tw-items-center tw-px-4 tw-py-2 tw--ml-px tw-text-sm tw-font-medium tw-text-white tw-bg-[#16a249] tw-border tw-border-[#16a249] tw-cursor-default">{{ $page }}</span>
                                    </span>
                                @else
                                    <a href="{{ $url }}" class="tw-relative tw-inline-flex tw-items-center tw-px-4 tw-py-2 tw--ml-px tw-text-sm tw-font-medium tw-text-gray-700 tw-bg-white tw-border tw-border-gray-300 hover:tw-bg-gray-50 tw-transition" aria-label="Đi tới trang {{ $page }}">
                                        {{ $page }}
                                    </a>
                                @endif
                            @endforeach
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($paginator->hasMorePages())
                        <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="tw-relative tw-inline-flex tw-items-center tw-px-3 tw-py-2 tw--ml-px tw-text-sm tw-font-medium tw-text-gray-600 tw-bg-white tw-border tw-border-gray-300 hover:tw-bg-gray-50 tw-transition" aria-label="Trang sau">
                            <svg class="tw-w-5 tw-h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                            </svg>
                        </a>
                    @else
                        <span aria-disabled="true" aria-label="Trang sau">
                            <span class="tw-relative tw-inline-flex tw-items-center tw-px-3 tw-py-2 tw--ml-px tw-text-sm tw-font-medium tw-text-gray-400 tw-bg-gray-100 tw-border tw-border-gray-200 tw-cursor-default" aria-hidden="true">
                                <svg class="tw-w-5 tw-h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                </svg>
                            </span>
                        </span>
                    @endif
                </span>
            </div>
        </div>
    </nav>
@endif
