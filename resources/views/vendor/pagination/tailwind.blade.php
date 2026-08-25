{{-- Paginação do PIB (baseada na view "tailwind" do Laravel).
     Paleta CLARA: este bloco é renderizado sobre linen-50/branco, na página de categoria. --}}
@if ($paginator->hasPages())
    @php
        $strong = fn ($value) => '<span class="font-semibold text-onyx-900 tabular">'.e($value).'</span>';

        $boxBase = 'grid h-10 w-10 place-items-center rounded-card border text-sm transition duration-200 ease-pib';
        $boxIdle = $boxBase.' border-linen-300 bg-white text-onyx-700 hover:border-ouro-500 hover:text-ouro-700 active:scale-95';
        $boxOff = $boxBase.' border-linen-200 bg-linen-100/60 text-onyx-400';

        $pillBase = 'rounded-pill border px-4 py-2 text-sm font-semibold transition duration-200 ease-pib';
        $pillIdle = $pillBase.' border-linen-300 bg-white text-onyx-800 hover:border-ouro-500 hover:text-ouro-700 active:scale-95';
        $pillOff = $pillBase.' border-linen-200 bg-linen-100/60 text-onyx-400';
    @endphp

    <nav role="navigation" aria-label="{{ __('pagination.nav_label') }}" class="mt-10 flex items-center justify-between gap-4">
        {{-- Mobile: só anterior/próximo --}}
        <div class="flex-1 sm:hidden">
            @if ($paginator->onFirstPage())
                <span class="{{ $pillOff }}">{!! __('pagination.previous') !!}</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="{{ $pillIdle }}">{!! __('pagination.previous') !!}</a>
            @endif
        </div>
        <div class="flex-1 text-right sm:hidden">
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="{{ $pillIdle }}">{!! __('pagination.next') !!}</a>
            @else
                <span class="{{ $pillOff }}">{!! __('pagination.next') !!}</span>
            @endif
        </div>

        {{-- Desktop: contador + números --}}
        <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
            <p class="text-sm text-onyx-600">
                {!! __('pagination.showing', [
                    'first' => $strong($paginator->firstItem() ?? $paginator->count()),
                    'last' => $strong($paginator->lastItem() ?? $paginator->count()),
                    'total' => $strong($paginator->total()),
                ]) !!}
            </p>

            <div class="flex items-center gap-1.5">
                @if ($paginator->onFirstPage())
                    <span class="{{ $boxOff }}" aria-hidden="true">&lsaquo;</span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="{{ __('site.a11y.prev') }}"
                       class="{{ $boxIdle }}">&lsaquo;</a>
                @endif

                @foreach ($elements as $element)
                    @if (is_string($element))
                        <span class="grid h-10 w-10 place-items-center text-sm text-onyx-400">{{ $element }}</span>
                    @endif

                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span aria-current="page"
                                      class="grid h-10 w-10 place-items-center rounded-card border border-ouro-500 bg-ouro-400 text-sm font-semibold text-onyx-950 tabular">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}" class="{{ $boxIdle }} tabular">{{ $page }}</a>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="{{ __('site.a11y.next') }}"
                       class="{{ $boxIdle }}">&rsaquo;</a>
                @else
                    <span class="{{ $boxOff }}" aria-hidden="true">&rsaquo;</span>
                @endif
            </div>
        </div>
    </nav>
@endif
