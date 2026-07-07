<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Listing;
use App\Models\PageContent;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class PublicController extends Controller
{
    public function home(string $locale): View
    {
        return view('public.home', [
            'page' => PageContent::forKey('home'),
            'featured' => Listing::published()->latest()->take(6)->get(),
            'events' => Event::published()->orderBy('order')->get(),
        ]);
    }

    public function category(Request $request, string $locale, string $category): View|Response
    {
        // with('user'): o card horizontal exibe o WhatsApp do corretor.
        $query = Listing::published()->category($category)->with('user');

        // Busca textual (em qualquer idioma) sobre título e descrição.
        if ($term = trim((string) $request->query('q'))) {
            $like = '%'.$term.'%';
            $query->where(function ($q) use ($like) {
                $q->whereRaw('title::text ilike ?', [$like])
                    ->orWhereRaw('description::text ilike ?', [$like]);
            });
        }

        // Filtro por região.
        if ($region = trim((string) $request->query('region'))) {
            $query->where('region', $region);
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
            'regions' => Listing::published()->category($category)
                ->whereNotNull('region')->distinct()->orderBy('region')->pluck('region'),
            'filters' => ['q' => $request->query('q'), 'region' => $request->query('region')],
        ]);
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
