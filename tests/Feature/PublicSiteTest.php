<?php

namespace Tests\Feature;

use App\Models\Listing;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PublicSiteTest extends TestCase
{
    use RefreshDatabase;

    private function publishedListing(array $overrides = []): Listing
    {
        Role::firstOrCreate(['name' => 'broker']);
        $broker = User::factory()->create(['whatsapp' => '5573999998888', 'email_public' => 'b@pib.com.br']);
        $broker->assignRole('broker');

        return Listing::create(array_merge([
            'user_id' => $broker->id,
            'category' => 'fazenda',
            'status' => 'published',
            'region' => 'Ilhéus',
            'title' => ['pt' => 'Fazenda de Cacau', 'en' => 'Cocoa Farm', 'es' => 'Hacienda', 'it' => 'Tenuta'],
            'description' => ['pt' => 'Desc PT', 'en' => 'Desc EN', 'es' => 'Desc ES', 'it' => 'Desc IT'],
        ], $overrides));
    }

    public function test_root_redirects_to_a_locale(): void
    {
        $this->get('/')->assertRedirect();
    }

    public function test_card_shows_area_and_only_the_listing_price(): void
    {
        $this->publishedListing(['price' => 2660000, 'area' => 133]);

        $this->get('/pt/fazendas')
            ->assertOk()
            ->assertSee('133 ha')
            ->assertSee('R$ 2.660.000,00')
            ->assertDontSee('Hectare:')
            ->assertSee('wa.me/5573999998888', false);
    }

    public function test_price_can_be_dollars_and_area_keeps_its_selected_unit(): void
    {
        $listing = $this->publishedListing([
            'category' => 'apartamento',
            'currency' => 'USD',
            'price' => 65000000,
            'area' => 240,
            'area_unit' => 'm2',
        ]);

        $this->assertSame('240.00', $listing->area_sqm);

        $this->get("/pt/anuncio/{$listing->slug}")
            ->assertOk()
            ->assertSee('US$ 65.000.000,00')
            ->assertSee('240 m²')
            ->assertDontSee('Hectare:');
    }

    public function test_exact_location_map_is_conditional_and_loaded_on_request(): void
    {
        $withoutCoordinates = $this->publishedListing(['slug' => 'sem-coordenadas']);
        $withCoordinates = $this->publishedListing([
            'slug' => 'com-coordenadas',
            'latitude' => '-14.7880000',
            'longitude' => '-39.2780000',
        ]);

        $this->get("/pt/anuncio/{$withoutCoordinates->slug}")
            ->assertOk()
            ->assertDontSee('openstreetmap.org/export/embed.html')
            ->assertDontSee('Carregar mapa');

        $this->get("/pt/anuncio/{$withCoordinates->slug}")
            ->assertOk()
            ->assertSee('Carregar mapa')
            ->assertSee('openstreetmap.org/export/embed.html')
            ->assertSee('openstreetmap.org/?mlat=', false)
            ->assertSee('<template x-if="mapOpen">', false);
    }

    public function test_category_page_renders_listing_in_selected_language(): void
    {
        $this->publishedListing();

        $this->get('/pt/fazendas')->assertOk()->assertSee('Fazenda de Cacau')->assertSee('Fazendas');
        $this->get('/en/fazendas')->assertOk()->assertSee('Cocoa Farm')->assertSee('Farms');
    }

    public function test_search_filters_listings(): void
    {
        $this->publishedListing();

        $this->get('/pt/fazendas?q=Cacau')->assertOk()->assertSee('Fazenda de Cacau');
        $this->get('/pt/fazendas?q=inexistente-xyz')->assertOk()->assertDontSee('Fazenda de Cacau');
    }

    public function test_home_exposes_shared_filter_and_search_redirects_to_selected_category(): void
    {
        $this->get('/pt')
            ->assertOk()
            ->assertSee('name="category"', false)
            ->assertSee('<option value="all"', false)
            ->assertSee('Todas')
            ->assertSee('name="q"', false)
            ->assertSee('name="region"', false)
            ->assertSee('name="max_price"', false)
            ->assertSee('name="min_area"', false);

        $this->get('/pt/buscar?category=ativo&q=cacau&max_price=900000&min_area=30')
            ->assertRedirect('/pt/ativos?q=cacau&max_price=900000&min_area=30');

        $this->get('/pt/buscar?category=all&q=cacau&max_price=900000&min_area=30')
            ->assertRedirect('/pt/oportunidades?q=cacau&max_price=900000&min_area=30');

        $this->from('/pt')->get('/pt/buscar?category=invalida')
            ->assertRedirect('/pt')
            ->assertSessionHasErrors('category');
    }

    public function test_all_opportunities_catalog_combines_every_listing_category(): void
    {
        $this->publishedListing([
            'slug' => 'fazenda-todas',
            'category' => 'fazenda',
            'title' => ['pt' => 'Fazenda em Todas'],
        ]);
        $this->publishedListing([
            'slug' => 'ativo-todos',
            'category' => 'ativo',
            'region' => 'Salvador',
            'title' => ['pt' => 'Ativo em Todas'],
        ]);
        $this->publishedListing([
            'slug' => 'servico-todos',
            'category' => 'servico',
            'region' => 'Barreiras',
            'title' => ['pt' => 'Serviço em Todas'],
        ]);

        $this->get('/pt/oportunidades')
            ->assertOk()
            ->assertSee('Todas as oportunidades')
            ->assertSee('Fazenda em Todas')
            ->assertSee('Ativo em Todas')
            ->assertSee('Serviço em Todas')
            ->assertSee('Ilhéus')
            ->assertSee('Salvador')
            ->assertSee('Barreiras');
    }

    public function test_price_and_area_filters_work_alone_and_together(): void
    {
        $this->publishedListing([
            'slug' => 'fazenda-menor',
            'price' => 500000,
            'area' => 40,
            'title' => ['pt' => 'Fazenda Menor'],
        ]);
        $this->publishedListing([
            'slug' => 'fazenda-maior',
            'price' => 1500000,
            'area' => 180,
            'title' => ['pt' => 'Fazenda Maior'],
        ]);

        $this->get('/pt/fazendas?max_price=700000')
            ->assertOk()->assertSee('Fazenda Menor')->assertDontSee('Fazenda Maior');

        $this->get('/pt/fazendas?min_area=100')
            ->assertOk()->assertSee('Fazenda Maior')->assertDontSee('Fazenda Menor');

        $this->get('/pt/fazendas?region=Ilh%C3%A9us&max_price=1600000&min_area=100')
            ->assertOk()->assertSee('Fazenda Maior')->assertDontSee('Fazenda Menor');
    }

    public function test_new_categories_have_public_pages_in_all_languages(): void
    {
        foreach (['apartamento' => 'apartamentos', 'casa' => 'casas', 'sitio' => 'sitios'] as $category => $path) {
            $this->publishedListing([
                'slug' => 'teste-'.$category,
                'category' => $category,
                'title' => ['pt' => 'Anúncio '.$category],
            ]);

            foreach (Listing::LOCALES as $locale) {
                $this->get("/{$locale}/{$path}")
                    ->assertOk()
                    ->assertSee('Anúncio '.$category);
            }
        }
    }

    public function test_area_filter_converts_units_and_price_filter_keeps_currencies_separate(): void
    {
        $this->publishedListing([
            'slug' => 'casa-em-reais',
            'category' => 'casa',
            'currency' => 'BRL',
            'price' => 800000,
            'area' => 10000,
            'area_unit' => 'm2',
            'title' => ['pt' => 'Casa em reais'],
        ]);
        $this->publishedListing([
            'slug' => 'casa-em-dolares',
            'category' => 'casa',
            'currency' => 'USD',
            'price' => 800000,
            'area' => 1,
            'area_unit' => 'ha',
            'title' => ['pt' => 'Casa em dólares'],
        ]);

        $this->get('/pt/casas?max_price=900000&price_currency=USD&min_area=1&area_unit=ha')
            ->assertOk()->assertSee('Casa em dólares')->assertDontSee('Casa em reais');

        $this->get('/pt/casas?max_price=900000&min_area=10000&area_unit=m2')
            ->assertOk()->assertSee('Casa em reais')->assertDontSee('Casa em dólares');

        $this->get('/pt/casas?min_area=1&area_unit=alq_baiano')
            ->assertOk()->assertDontSee('Casa em reais')->assertDontSee('Casa em dólares');
    }

    public function test_search_accepts_masked_price_without_changing_its_magnitude(): void
    {
        $this->publishedListing(['price' => 65000000]);

        $this->get('/pt/fazendas?max_price=65.000.000%2C00')
            ->assertOk()->assertSee('Fazenda de Cacau');

        $this->get('/pt/fazendas?max_price=650000')
            ->assertOk()->assertDontSee('Fazenda de Cacau');
    }

    public function test_legacy_decimal_price_url_keeps_its_numeric_meaning(): void
    {
        $this->publishedListing(['price' => 100]);

        $this->get('/pt/fazendas?max_price=65.000')
            ->assertOk()->assertDontSee('Fazenda de Cacau');

        $this->get('/pt/fazendas?max_price=100.000')
            ->assertOk()->assertSee('Fazenda de Cacau');
    }

    public function test_header_uses_country_flags_without_email_and_contact_page_keeps_email(): void
    {
        config()->set('pib.contact.email', 'admin@pib.com.br');
        config()->set('pib.contact.whatsapp', '(73) 99999-8888');
        config()->set('pib.contact.phones', ['(73) 99999-8888', '(71) 98888-7777']);

        $this->get('/pt')
            ->assertOk()
            ->assertDontSee('admin@pib.com.br')
            ->assertSee('aria-label="Brasil"', false)
            ->assertSee('aria-label="Estados Unidos"', false)
            ->assertSee('aria-label="Espanha"', false)
            ->assertSee('aria-label="Itália"', false)
            ->assertSee('href="http://localhost:8080/en"', false);

        $this->get('/pt/contatos')
            ->assertOk()
            ->assertSee('admin@pib.com.br')
            ->assertSee('mailto:admin@pib.com.br', false)
            ->assertSee('https://wa.me/73999998888', false)
            ->assertSee('(73) 99999-8888')
            ->assertSee('(71) 98888-7777')
            ->assertSee('Falar no WhatsApp')
            ->assertSee('data-contact-button-icon="whatsapp"', false)
            ->assertSee('data-contact-button-icon="email"', false);
    }

    public function test_detail_page_shows_translation_and_hreflang(): void
    {
        $listing = $this->publishedListing();

        $this->get("/it/anuncio/{$listing->slug}")
            ->assertOk()
            ->assertSee('Tenuta')
            ->assertSee('hreflang="pt"', false)
            ->assertSee('hreflang="it"', false);
    }

    public function test_listing_with_only_one_language_falls_back_gracefully(): void
    {
        // Corretor estrangeiro (ex.: italiano) publicou apenas em italiano.
        $listing = $this->publishedListing([
            'slug' => 'tenuta-solo-italiano',
            'title' => ['pt' => '', 'en' => '', 'es' => '', 'it' => 'Tenuta Esclusiva'],
            'description' => ['pt' => '', 'en' => '', 'es' => '', 'it' => 'Descrizione in italiano.'],
        ]);

        // Visitante em PT ou EN ainda vê o conteúdo (em italiano), nunca em branco.
        $this->get('/pt/fazendas')->assertOk()->assertSee('Tenuta Esclusiva');
        $this->get('/en/fazendas')->assertOk()->assertSee('Tenuta Esclusiva');
        $this->get("/pt/anuncio/{$listing->slug}")->assertOk()->assertSee('Tenuta Esclusiva');
    }

    public function test_draft_listing_is_not_publicly_visible(): void
    {
        $listing = $this->publishedListing(['status' => 'draft', 'title' => ['pt' => 'Rascunho', 'en' => 'Draft', 'es' => 'B', 'it' => 'B'], 'slug' => 'rascunho-x']);

        $this->get('/pt/fazendas')->assertOk()->assertDontSee('Rascunho');
        $this->get("/pt/anuncio/{$listing->slug}")->assertNotFound();
    }

    public function test_live_search_partial_returns_only_the_results_fragment(): void
    {
        $this->publishedListing();

        $response = $this->get('/pt/fazendas?q=Cacau', ['X-PIB-Partial' => 'true']);

        $response->assertOk()->assertSee('Fazenda de Cacau');
        $response->assertDontSee('<html', false);
        $response->assertDontSee('name="q"', false);
    }

    public function test_live_search_partial_shows_no_results_message(): void
    {
        $this->publishedListing();

        $this->get('/pt/fazendas?q=inexistente-xyz', ['X-PIB-Partial' => 'true'])
            ->assertOk()
            ->assertSee('Nenhum anúncio encontrado')
            ->assertDontSee('Fazenda de Cacau');
    }

    public function test_live_search_partial_applies_price_and_area_limits(): void
    {
        $this->publishedListing(['price' => 800000, 'area' => 75]);

        $this->get('/pt/fazendas?max_price=700000', ['X-PIB-Partial' => 'true'])
            ->assertOk()
            ->assertDontSee('Fazenda de Cacau');

        $this->get('/pt/fazendas?max_price=900000&min_area=50', ['X-PIB-Partial' => 'true'])
            ->assertOk()
            ->assertSee('Fazenda de Cacau')
            ->assertDontSee('<html', false);
    }
}
