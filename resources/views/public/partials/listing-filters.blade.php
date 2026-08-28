@php
    $filterMode = $filterMode ?? 'home';
    $l = app()->getLocale();
    $selectedCategory = $filters['category'] ?? 'fazenda';
    $categoryRoutes = [
        'fazenda' => route('public.fazenda', $l),
        'ativo' => route('public.ativo', $l),
        'servico' => route('public.servico', $l),
    ];
    $fieldId = $filterMode === 'home' ? 'home-filter' : 'category-filter';
@endphp

<form method="GET"
      action="{{ $filterMode === 'home' ? route('public.search', $l) : $categoryRoutes[$selectedCategory] }}"
      @if ($filterMode === 'category') @submit.prevent="search()" @endif
      class="rounded-panel border border-linen-200 bg-white p-5 shadow-panel sm:p-6 lg:p-7">
    <h2 class="mb-5 text-center text-base font-bold text-onyx-800">@lang('site.search.title')</h2>

    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
        <div class="lg:col-span-2">
            <label for="{{ $fieldId }}-q" class="mb-2 block text-xs font-semibold text-onyx-600">@lang('site.search.placeholder')</label>
            <input id="{{ $fieldId }}-q" type="search" name="q" x-model="q"
                   @if ($filterMode === 'category') @input.debounce.400ms="search()" @endif
                   value="{{ $filters['q'] ?? '' }}" placeholder="@lang('site.search.home_placeholder')"
                   class="min-h-13 w-full rounded-card border border-linen-300 bg-linen-50 px-4 text-onyx-900 shadow-inner transition duration-200 placeholder:text-onyx-400 focus:border-ouro-500 focus:outline-none focus:ring-2 focus:ring-ouro-200">
        </div>

        <div>
            <label for="{{ $fieldId }}-category" class="mb-2 block text-xs font-semibold text-onyx-600">@lang('site.search.category')</label>
            <select id="{{ $fieldId }}-category" name="category" x-model="category"
                    @change="{{ $filterMode === 'category' ? 'changeCategory()' : 'syncRegion()' }}"
                    class="min-h-13 w-full rounded-card border border-linen-300 bg-linen-50 px-4 text-onyx-900 shadow-inner transition duration-200 focus:border-ouro-500 focus:outline-none focus:ring-2 focus:ring-ouro-200">
                @foreach ($categoryRoutes as $key => $url)
                    <option value="{{ $key }}" @selected($selectedCategory === $key)>{{ __('site.nav.'.$key) }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="{{ $fieldId }}-region" class="mb-2 block text-xs font-semibold text-onyx-600">@lang('site.listing.region')</label>
            <select id="{{ $fieldId }}-region" name="region" x-model="region"
                    @if ($filterMode === 'category') @change="search()" @endif
                    class="min-h-13 w-full rounded-card border border-linen-300 bg-linen-50 px-4 text-onyx-900 shadow-inner transition duration-200 focus:border-ouro-500 focus:outline-none focus:ring-2 focus:ring-ouro-200">
                <option value="">@lang('site.search.region_all')</option>
                <template x-for="option in regions" :key="option">
                    <option :value="option" x-text="option"></option>
                </template>
            </select>
        </div>

        <div>
            <label for="{{ $fieldId }}-max-price" class="mb-2 block text-xs font-semibold text-onyx-600">@lang('site.search.max_price')</label>
            <div class="relative">
                <span class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-sm text-onyx-500">R$</span>
                <input id="{{ $fieldId }}-max-price" type="number" name="max_price" min="0" step="0.01" inputmode="decimal"
                       x-model="maxPrice" @if ($filterMode === 'category') @input.debounce.400ms="search()" @endif
                       value="{{ $filters['max_price'] ?? '' }}"
                       class="min-h-13 w-full rounded-card border border-linen-300 bg-linen-50 pl-12 pr-4 text-onyx-900 shadow-inner transition duration-200 focus:border-ouro-500 focus:outline-none focus:ring-2 focus:ring-ouro-200">
            </div>
        </div>

        <div>
            <label for="{{ $fieldId }}-min-area" class="mb-2 block text-xs font-semibold text-onyx-600">@lang('site.search.min_area')</label>
            <div class="relative">
                <input id="{{ $fieldId }}-min-area" type="number" name="min_area" min="0" step="0.01" inputmode="decimal"
                       x-model="minArea" @if ($filterMode === 'category') @input.debounce.400ms="search()" @endif
                       value="{{ $filters['min_area'] ?? '' }}"
                       class="min-h-13 w-full rounded-card border border-linen-300 bg-linen-50 px-4 pr-12 text-onyx-900 shadow-inner transition duration-200 focus:border-ouro-500 focus:outline-none focus:ring-2 focus:ring-ouro-200">
                <span class="pointer-events-none absolute inset-y-0 right-4 flex items-center text-sm text-onyx-500">ha</span>
            </div>
        </div>

        <button type="submit"
                class="min-h-13 rounded-card bg-onyx-950 px-8 py-3 font-semibold text-white transition duration-300 hover:bg-ouro-600 active:scale-[0.98] md:col-span-2">
            <span class="inline-flex items-center justify-center gap-2">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path stroke-linecap="round" d="m20 20-4-4"/></svg>
                @lang('site.search.button')
            </span>
        </button>
    </div>
</form>
