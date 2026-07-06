<?php

namespace Tests\Feature\Admin;

use App\Filament\Resources\Events\Pages\CreateEvent;
use App\Filament\Resources\Events\Pages\EditEvent;
use App\Filament\Resources\Events\Pages\ListEvents;
use App\Filament\Resources\Listings\Pages\CreateListing;
use App\Filament\Resources\Listings\Pages\EditListing;
use App\Filament\Resources\Listings\Pages\ListListings;
use App\Filament\Resources\PageContents\PageContentResource;
use App\Filament\Resources\Users\UserResource;
use App\Models\Event;
use App\Models\Listing;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ListingAdminTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'broker']);

        Filament::setCurrentPanel('admin');
    }

    private function broker(string $email = 'b1@pib.com.br'): User
    {
        $user = User::factory()->create(['email' => $email]);
        $user->assignRole('broker');

        return $user;
    }

    private function admin(): User
    {
        $user = User::factory()->create(['email' => 'admin@pib.com.br']);
        $user->assignRole('admin');

        return $user;
    }

    /** Conteúdo traduzido para todos os idiomas de um campo. */
    private function allLocales(string $prefix): array
    {
        return [
            'pt' => "{$prefix} PT",
            'en' => "{$prefix} EN",
            'es' => "{$prefix} ES",
            'it' => "{$prefix} IT",
        ];
    }

    public function test_broker_only_sees_own_listings(): void
    {
        $b1 = $this->broker('b1@pib.com.br');
        $b2 = $this->broker('b2@pib.com.br');

        $mine = Listing::create([
            'user_id' => $b1->id, 'category' => 'fazenda', 'status' => 'published',
            'title' => $this->allLocales('Minha'), 'description' => $this->allLocales('Desc'),
        ]);
        $other = Listing::create([
            'user_id' => $b2->id, 'category' => 'fazenda', 'status' => 'published',
            'title' => $this->allLocales('Outra'), 'description' => $this->allLocales('Desc'),
        ]);

        $this->actingAs($b1);

        Livewire::test(ListListings::class)
            ->assertCanSeeTableRecords([$mine])
            ->assertCanNotSeeTableRecords([$other]);
    }

    public function test_cannot_publish_listing_with_no_locale_fully_filled(): void
    {
        $broker = $this->broker();
        $this->actingAs($broker);

        Livewire::test(CreateListing::class)
            ->fillForm([
                'category' => 'fazenda',
                'status' => 'published',
                // Título preenchido mas descrição vazia em todos os idiomas:
                // nenhum idioma fica "completo".
                'title' => ['pt' => 'Só título', 'en' => '', 'es' => '', 'it' => ''],
                'description' => ['pt' => '', 'en' => '', 'es' => '', 'it' => ''],
            ])
            ->call('create');

        // Halt impede a criação: nenhum registro persistido.
        $this->assertDatabaseCount('listings', 0);
    }

    public function test_can_publish_with_only_one_language_filled(): void
    {
        // Corretor estrangeiro (ex.: italiano) publica apenas no próprio idioma.
        $broker = $this->broker();
        $this->actingAs($broker);

        Livewire::test(CreateListing::class)
            ->fillForm([
                'category' => 'fazenda',
                'status' => 'published',
                'title' => ['pt' => '', 'en' => '', 'es' => '', 'it' => 'Fattoria di Cacao'],
                'description' => ['pt' => '', 'en' => '', 'es' => '', 'it' => 'Descrizione completa in italiano.'],
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $listing = Listing::first();
        $this->assertNotNull($listing);
        $this->assertSame('published', $listing->status);
        $this->assertSame('Fattoria di Cacao', $listing->getTranslation('title', 'it'));
        $this->assertNotEmpty($listing->slug);
    }

    public function test_can_publish_and_translations_round_trip(): void
    {
        $broker = $this->broker();
        $this->actingAs($broker);

        Livewire::test(CreateListing::class)
            ->fillForm([
                'category' => 'ativo',
                'status' => 'published',
                'title' => $this->allLocales('Título'),
                'description' => $this->allLocales('Descrição'),
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $listing = Listing::first();
        $this->assertNotNull($listing);
        $this->assertSame('published', $listing->status);
        $this->assertSame($broker->id, $listing->user_id);
        $this->assertSame('Título EN', $listing->getTranslation('title', 'en'));
        $this->assertSame('Título IT', $listing->getTranslation('title', 'it'));
        $this->assertNotEmpty($listing->slug);
    }

    public function test_page_content_and_user_resources_are_admin_only(): void
    {
        $this->actingAs($this->broker());
        $this->assertFalse(UserResource::canAccess());
        $this->assertFalse(PageContentResource::canAccess());

        $this->actingAs($this->admin());
        $this->assertTrue(UserResource::canAccess());
        $this->assertTrue(PageContentResource::canAccess());
    }

    public function test_broker_only_sees_own_events(): void
    {
        $b1 = $this->broker('b1@pib.com.br');
        $b2 = $this->broker('b2@pib.com.br');

        $mine = Event::create([
            'user_id' => $b1->id, 'is_published' => true,
            'title' => $this->allLocales('Meu Evento'),
        ]);
        $other = Event::create([
            'user_id' => $b2->id, 'is_published' => true,
            'title' => $this->allLocales('Outro Evento'),
        ]);

        $this->actingAs($b1);

        Livewire::test(ListEvents::class)
            ->assertCanSeeTableRecords([$mine])
            ->assertCanNotSeeTableRecords([$other]);
    }

    public function test_broker_created_event_defaults_to_own_id(): void
    {
        $broker = $this->broker();
        $this->actingAs($broker);

        Livewire::test(CreateEvent::class)
            ->fillForm(['title' => $this->allLocales('Feira de Investidores')])
            ->call('create')
            ->assertHasNoFormErrors();

        $event = Event::first();
        $this->assertNotNull($event);
        $this->assertSame($broker->id, $event->user_id);
    }

    public function test_broker_cannot_reassign_event_to_another_user_on_edit(): void
    {
        $broker = $this->broker('b1@pib.com.br');
        $otherBroker = $this->broker('b2@pib.com.br');

        $event = Event::create([
            'user_id' => $broker->id, 'is_published' => true,
            'title' => $this->allLocales('Meu Evento'),
        ]);

        $this->actingAs($broker);

        Livewire::test(EditEvent::class, ['record' => $event->getKey()])
            ->fillForm(['user_id' => $otherBroker->id])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame($broker->id, $event->fresh()->user_id);
    }

    public function test_broker_cannot_reassign_listing_to_another_user_on_edit(): void
    {
        $broker = $this->broker('b1@pib.com.br');
        $otherBroker = $this->broker('b2@pib.com.br');

        $listing = Listing::create([
            'user_id' => $broker->id, 'category' => 'fazenda', 'status' => 'draft',
            'title' => $this->allLocales('Minha'), 'description' => $this->allLocales('Desc'),
        ]);

        $this->actingAs($broker);

        // O campo user_id é oculto para o corretor, mas simulamos um payload
        // adulterado (ex.: via devtools) tentando reatribuir o anúncio.
        Livewire::test(EditListing::class, ['record' => $listing->getKey()])
            ->fillForm(['user_id' => $otherBroker->id])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame($broker->id, $listing->fresh()->user_id);
    }
}
