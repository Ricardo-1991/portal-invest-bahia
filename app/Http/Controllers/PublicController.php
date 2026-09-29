<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Listing;
use App\Models\PageContent;
use App\Support\AreaUnits;
use App\Support\MoneyInput;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PublicController extends Controller
{
    public function home(string $locale): View
    {
        return view('public.home', [
            'page' => PageContent::forKey('home'),
            'featured' => Listing::published()->latest()->take(6)->get(),
            'events' => Event::published()->orderBy('order')->get(),
            'regionsByCategory' => $this->regionsByCategory(),
            'filters' => [
                'category' => 'fazenda',
                'q' => null,
                'region' => null,
                'max_price' => null,
                'price_currency' => 'BRL',
                'min_area' => null,
                'area_unit' => 'ha',
            ],
        ]);
    }

    /** Valida a categoria escolhida na home e encaminha para o catálogo correto. */
    public function search(Request $request, string $locale): RedirectResponse
    {
        $this->normalizePriceFilter($request);
        $filters = $request->validate($this->filterRules(includeCategory: true));
        $category = $filters['category'];
        unset($filters['category']);
        if (blank($filters['max_price'] ?? null)) {
            unset($filters['price_currency']);
        }
        if (blank($filters['min_area'] ?? null)) {
            unset($filters['area_unit']);
        }

        $query = array_filter($filters, static fn ($value): bool => filled($value));

        return redirect()->route('public.'.$category, [
            'locale' => $locale,
            ...$query,
        ]);
    }

    public function category(Request $request, string $locale, string $category): View|Response
    {
        $this->normalizePriceFilter($request);
        $filters = $request->validate($this->filterRules());

        // with('user'): o card horizontal exibe o WhatsApp do corretor.
        $query = Listing::published()->with('user');

        if ($category !== 'all') {
            $query->category($category);
        }

        // Busca textual (em qualquer idioma) sobre os textos exibidos no card.
        if ($term = trim((string) ($filters['q'] ?? ''))) {
            $like = '%'.$term.'%';
            $query->where(function ($q) use ($like) {
                $q->whereRaw('title::text ilike ?', [$like])
                    ->orWhereRaw('subtitle::text ilike ?', [$like])
                    ->orWhereRaw('description::text ilike ?', [$like]);
            });
        }

        // Filtro por região.
        if ($region = trim((string) ($filters['region'] ?? ''))) {
            $query->where('region', $region);
        }

        if (filled($filters['max_price'] ?? null)) {
            $query->where('currency', $filters['price_currency'] ?? 'BRL')
                ->whereNotNull('price')
                ->where('price', '<=', $filters['max_price']);
        }

        if (filled($filters['min_area'] ?? null)) {
            $query->whereNotNull('area_sqm')
                ->where('area_sqm', '>=', AreaUnits::toSquareMeters(
                    $filters['min_area'],
                    $filters['area_unit'] ?? 'ha',
                ));
        }

        $listings = $query->latest()->paginate(12)->withQueryString();

        // Busca ao vivo (fetch do Alpine): devolve só o fragmento de resultados.
        if ($request->header('X-PIB-Partial') === 'true') {
            return response(view('public.partials.category-results', compact('listings')));
        }

        return view('public.category', [
            'category' => $category,
            'page' => $category === 'all' ? null : PageContent::forKey($category),
            'listings' => $listings,
            'regionsByCategory' => $this->regionsByCategory(),
            'filters' => [
                'category' => $category,
                'q' => $filters['q'] ?? null,
                'region' => $filters['region'] ?? null,
                'max_price' => $filters['max_price'] ?? null,
                'price_currency' => $filters['price_currency'] ?? 'BRL',
                'min_area' => $filters['min_area'] ?? null,
                'area_unit' => $filters['area_unit'] ?? 'ha',
            ],
        ]);
    }

    private function normalizePriceFilter(Request $request): void
    {
        $value = $request->input('max_price');

        if (is_string($value) && str_contains($value, ',')) {
            $request->merge(['max_price' => MoneyInput::toDecimal($value)]);
        }
    }

    private function filterRules(bool $includeCategory = false): array
    {
        return [
            ...($includeCategory ? ['category' => ['required', Rule::in(['all', ...Listing::CATEGORIES])]] : []),
            'q' => ['nullable', 'string', 'max:255'],
            'region' => ['nullable', 'string', 'max:255'],
            'max_price' => ['nullable', 'numeric', 'min:0', 'max:9999999999999.99'],
            'price_currency' => ['nullable', Rule::in(['BRL', 'USD'])],
            'min_area' => ['nullable', 'numeric', 'min:0', 'max:9999999999.99'],
            'area_unit' => ['nullable', Rule::in(array_keys(AreaUnits::SQUARE_METERS))],
        ];
    }

    /** Regiões publicadas, separadas por categoria para os selects dependentes. */
    private function regionsByCategory(): array
    {
        $regions = Listing::published()
            ->whereNotNull('region')
            ->where('region', '<>', '')
            ->select(['category', 'region'])
            ->distinct()
            ->orderBy('category')
            ->orderBy('region')
            ->get()
            ->groupBy('category')
            ->map(fn ($items) => $items->pluck('region')->values()->all())
            ->all();

        $regions = array_replace(array_fill_keys(Listing::CATEGORIES, []), $regions);

        return [
            'all' => collect($regions)->flatten()->unique()->sort()->values()->all(),
            ...$regions,
        ];
    }

    public function informacoes(string $locale): View
    {
        return view('public.informacoes', [
            'page' => PageContent::forKey('informacoes'),
            'events' => Event::published()->orderBy('order')->get(),
        ]);
    }

    public function contatos(string $locale): View
    {
        return view('public.contatos', [
            'page' => PageContent::forKey('contatos'),
        ]);
    }

    public function show(string $locale, string $slug): View
    {
        $listing = Listing::published()->where('slug', $slug)->firstOrFail();

        return view('public.listing', [
            'listing' => $listing,
            'gallery' => $listing->getMedia('gallery'),
        ]);
    }
}
