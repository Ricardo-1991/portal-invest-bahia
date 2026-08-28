<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Listing;
use App\Models\PageContent;
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
                'min_area' => null,
            ],
        ]);
    }

    /** Valida a categoria escolhida na home e encaminha para o catálogo correto. */
    public function search(Request $request, string $locale): RedirectResponse
    {
        $filters = $request->validate($this->filterRules(includeCategory: true));
        $category = $filters['category'];
        unset($filters['category']);

        $query = array_filter($filters, static fn ($value): bool => filled($value));

        return redirect()->route('public.'.$category, [
            'locale' => $locale,
            ...$query,
        ]);
    }

    public function category(Request $request, string $locale, string $category): View|Response
    {
        $filters = $request->validate($this->filterRules());

        // with('user'): o card horizontal exibe o WhatsApp do corretor.
        $query = Listing::published()->category($category)->with('user');

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
            $query->whereNotNull('price')->where('price', '<=', $filters['max_price']);
        }

        if (filled($filters['min_area'] ?? null)) {
            $query->whereNotNull('area')->where('area', '>=', $filters['min_area']);
        }

        $listings = $query->latest()->paginate(12)->withQueryString();

        // Busca ao vivo (fetch do Alpine): devolve só o fragmento de resultados.
        if ($request->header('X-PIB-Partial') === 'true') {
            return response(view('public.partials.category-results', compact('listings')));
        }

        return view('public.category', [
            'category' => $category,
            'page' => PageContent::forKey($category),
            'listings' => $listings,
            'regionsByCategory' => $this->regionsByCategory(),
            'filters' => [
                'category' => $category,
                'q' => $filters['q'] ?? null,
                'region' => $filters['region'] ?? null,
                'max_price' => $filters['max_price'] ?? null,
                'min_area' => $filters['min_area'] ?? null,
            ],
        ]);
    }

    private function filterRules(bool $includeCategory = false): array
    {
        return [
            ...($includeCategory ? ['category' => ['required', Rule::in(Listing::CATEGORIES)]] : []),
            'q' => ['nullable', 'string', 'max:255'],
            'region' => ['nullable', 'string', 'max:255'],
            'max_price' => ['nullable', 'numeric', 'min:0'],
            'min_area' => ['nullable', 'numeric', 'min:0'],
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

        return array_replace(array_fill_keys(Listing::CATEGORIES, []), $regions);
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
