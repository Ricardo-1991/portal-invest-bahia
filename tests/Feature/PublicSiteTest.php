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
}
