@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex items-center justify-between">
        {{-- Total data count (optional, but good for context) --}}
        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
            <div>
                <p class="text-[10px] text-gray-500 font-bold uppercase tracking-wider">
                    Menampilkan <span class="text-gray-900 font-black">{{ $paginator->firstItem() }}</span> - <span
                        class="text-gray-900 font-black">{{ $paginator->lastItem() }}</span> dari <span
                        class="text-gray-900 font-black">{{ $paginator->total() }}</span> data
                </p>
            </div>

            <div class="flex items-center gap-2">
                {{-- Previous Page Link --}}
                @if ($paginator->onFirstPage())
                    <span
                        class="p-2 rounded-lg border border-gray-100 bg-gray-50 text-gray-300 cursor-not-allowed transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" rel="prev"
                        class="p-2 rounded-lg border border-gray-200 bg-white text-gray-600 hover:bg-gray-50 transition-all shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </a>
                @endif

                {{-- Pagination Elements --}}
                <div class="flex items-center gap-1">
                    @foreach ($elements as $element)
                        {{-- "Three Dots" Separator --}}
                        @if (is_string($element))
                            <span class="px-2 text-gray-400 font-bold text-xs" aria-disabled="true">{{ $element }}</span>
                        @endif

                        {{-- Array Of Links --}}
                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                    <span aria-current="page"
                                        class="w-8 h-8 flex items-center justify-center rounded-lg text-xs font-black bg-[#36B2B2] text-white shadow-lg shadow-[#36B2B2]/20 transition-all">
                                        {{ $page }}
                                    </span>
                                @else
                                    <a href="{{ $url }}"
                                        class="w-8 h-8 flex items-center justify-center rounded-lg text-xs font-bold bg-white text-gray-600 border border-gray-200 hover:bg-gray-50 transition-all shadow-sm">
                                        {{ $page }}
                                    </a>
                                @endif
                            @endforeach
                        @endif
                    @endforeach
                </div>

                {{-- Next Page Link --}}
                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" rel="next"
                        class="p-2 rounded-lg border border-gray-200 bg-white text-gray-600 hover:bg-gray-50 transition-all shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                @else
                    <span
                        class="p-2 rounded-lg border border-gray-100 bg-gray-50 text-gray-300 cursor-not-allowed transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </span>
                @endif
            </div>
        </div>

        {{-- Mobile pagination (centered simple arrows) --}}
        <div class="flex sm:hidden items-center justify-between w-full">
            @if ($paginator->onFirstPage())
                <span class="p-3 bg-gray-50 text-gray-300 rounded-xl cursor-not-allowed">Previous</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}"
                    class="p-3 bg-white border border-gray-200 text-gray-600 rounded-xl font-bold text-xs">Previous</a>
            @endif

            <span class="text-xs font-black text-gray-900">
                {{ $paginator->currentPage() }} / {{ $paginator->lastPage() }}
            </span>

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}"
                    class="p-3 bg-white border border-gray-200 text-gray-600 rounded-xl font-bold text-xs">Next</a>
            @else
                <span class="p-3 bg-gray-50 text-gray-300 rounded-xl cursor-not-allowed">Next</span>
            @endif
        </div>
    </nav>
@endif