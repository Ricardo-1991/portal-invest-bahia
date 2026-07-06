{{-- Paginação customizada para o tema onyx/ouro do PIB (baseada na view "tailwind" do Laravel). --}}
@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex items-center justify-between gap-4">
        <div class="flex-1 sm:hidden">
            @if ($paginator->onFirstPage())
                <span class="rounded-lg border border-onyx-700 px-4 py-2 text-sm text-onyx-500">{!! __('pagination.previous') !!}</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="rounded-lg border border-onyx-700 px-4 py-2 text-sm text-onyx-100 transition hover:border-ouro-500 hover:text-ouro-400">{!! __('pagination.previous') !!}</a>
            @endif
        </div>
        <div class="flex-1 text-right sm:hidden">
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="rounded-lg border border-onyx-700 px-4 py-2 text-sm text-onyx-100 transition hover:border-ouro-500 hover:text-ouro-400">{!! __('pagination.next') !!}</a>
            @else
                <span class="rounded-lg border border-onyx-700 px-4 py-2 text-sm text-onyx-500">{!! __('pagination.next') !!}</span>
            @endif
        </div>

        <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
            <p class="text-sm text-onyx-400">
                {!! __('Showing') !!}
                <span class="font-medium text-onyx-100">{{ $paginator->firstItem() ?? $paginator->count() }}</span>
                {!! __('to') !!}
                <span class="font-medium text-onyx-100">{{ $paginator->lastItem() ?? $paginator->count() }}</span>
                {!! __('of') !!}
                <span class="font-medium text-onyx-100">{{ $paginator->total() }}</span>
                {!! __('results') !!}
            </p>

            <div class="flex items-center gap-1">
                @if ($paginator->onFirstPage())
                    <span class="grid h-9 w-9 place-items-center rounded-lg border border-onyx-700 text-onyx-600" aria-hidden="true">&lsaquo;</span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="{{ __('pagination.previous') }}"
                       class="grid h-9 w-9 place-items-center rounded-lg border border-onyx-700 text-onyx-100 transition hover:border-ouro-500 hover:text-ouro-400">&lsaquo;</a>
                @endif

                @foreach ($elements as $element)
                    @if (is_string($element))
                        <span class="grid h-9 w-9 place-items-center text-onyx-500">{{ $element }}</span>
                    @endif

                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span aria-current="page" class="grid h-9 w-9 place-items-center rounded-lg bg-ouro-400 font-semibold text-onyx-950">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}" class="grid h-9 w-9 place-items-center rounded-lg text-onyx-100 transition hover:bg-onyx-800 hover:text-ouro-400">{{ $page }}</a>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="{{ __('pagination.next') }}"
                       class="grid h-9 w-9 place-items-center rounded-lg border border-onyx-700 text-onyx-100 transition hover:border-ouro-500 hover:text-ouro-400">&rsaquo;</a>
                @else
                    <span class="grid h-9 w-9 place-items-center rounded-lg border border-onyx-700 text-onyx-600" aria-hidden="true">&rsaquo;</span>
                @endif
            </div>
        </div>
    </nav>
@endif
