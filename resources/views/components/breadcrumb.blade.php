@props(['items' => []])

<nav class="tw-flex tw-mb-4" aria-label="Breadcrumb">
    <ol class="tw-inline-flex tw-items-center tw-space-x-1 md:tw-space-x-3">
        @foreach($items as $index => $item)
            <li class="tw-flex tw-items-center">
                @if($index > 0)
                    <svg class="tw-w-4 tw-h-4 tw-text-gray-400 tw-mx-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                            clip-rule="evenodd"></path>
                    </svg>
                @endif

                @if(isset($item['url']))
                    <a href="{{ $item['url'] }}" class="tw-text-gray-500 hover:tw-text-gray-700">
                        {{ $item['label'] }}
                    </a>
                @else
                    <span class="{{ $loop->last ? 'tw-text-gray-700 tw-font-medium' : 'tw-text-gray-500' }}">
                        {{ $item['label'] }}
                    </span>
                @endif
            </li>
        @endforeach
    </ol>
</nav>
