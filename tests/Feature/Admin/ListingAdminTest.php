<?php

namespace Tests\Feature\Admin;

use App\Filament\Resources\Events\Pages\CreateEvent;
use App\Filament\Resources\Events\Pages\EditEvent;
use App\Filament\Resources\Events\Pages\ListEvents;
use App\Filament\Resources\Listings\Pages\CreateListing;
use App\Filament\Resources\Listings\Pages\EditListing;
use App\Filament\Resources\Listings\Pages\ListListings;
use App\Filament\Resources\PageContents\PageContentResource;
use App\Filament\Resources\PageContents\Pages\EditPageContent;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\UserResource;
use App\Models\Event;
use App\Models\Listing;
use App\Models\PageContent;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Livewire\Livewire;
use ReflectionMethod;
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

    public function test_every_edit_save_action_requires_confirmation(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin);

        $listing = Listing::create([
            'user_id' => $admin->id,
            'category' => 'fazenda',
            'status' => 'draft',
            'title' => $this->allLocales('Fazenda'),
            'description' => $this->allLocales('Descrição'),
        ]);
        $event = Event::create([
            'user_id' => $admin->id,
            'title' => $this->allLocales('Evento'),
        ]);
        $home = PageContent::create([
            'key' => 'home',
            'title' => ['pt' => 'Início'],
        ]);

        $editPages = [
            [EditListing::class, $listing],
            [EditEvent::class, $event],
            [EditPageContent::class, $home],
            [EditUser::class, $admin],
        ];

        foreach ($editPages as [$pageClass, $record]) {
            $livewire = Livewire::test($pageClass, ['record' => $record->getKey()]);
            $component = $livewire->instance();
            $action = collect($component->getSchema('content')->getFlatComponents())
                ->first(fn ($component): bool => $component instanceof Action && $component->getName() === 'save');

            $this->assertInstanceOf(Action::class, $action);
            $this->assertFalse($action->canSubmitForm());
            $this->assertNotNull($action->getActionFunction());
            $this->assertTrue($action->isConfirmationRequired());
            $this->assertSame('Confirmar alterações', $action->getModalHeading());
            $this->assertSame('Sim, guardar alterações', $action->getModalSubmitActionLabel());

            $livewire->call('mountAction', 'save', [], $action->getContext())
                ->assertSet('mountedActions.0.name', 'save');

            $this->assertStringContainsString(
                'Confirmar alterações',
                $livewire->getMountedActionModalHtml(),
            );
        }
    }

    public function test_listing_and_event_creation_actions_require_confirmation(): void
    {
        $this->actingAs($this->admin());

        foreach ([CreateListing::class, CreateEvent::class] as $pageClass) {
            $livewire = Livewire::test($pageClass);
            $component = $livewire->instance();

            $method = new ReflectionMethod($component, 'getCreateFormAction');
            $action = $method->invoke($component);

            $this->assertInstanceOf(Action::class, $action);
            $this->assertNotNull($action->getActionFunction());
            $this->assertTrue($action->isConfirmationRequired());
            $this->assertSame('Confirmar criação', $action->getModalHeading());
            $this->assertSame('Sim, criar registro', $action->getModalSubmitActionLabel());
            $this->assertFalse($component->canCreateAnother());

            $notificationMethod = new ReflectionMethod($component, 'getCreatedNotification');
            $notification = $notificationMethod->invoke($component);

            $this->assertInstanceOf(Notification::class, $notification);
            $this->assertSame('filament.notifications.creation-success', $notification->getView());
            $this->assertStringContainsString('criado com sucesso!', $notification->getTitle());
            $this->assertStringContainsString('role="dialog"', $notification->toHtml());
            $this->assertStringContainsString('place-items: center', $notification->toHtml());

            $restoredNotification = Notification::fromArray($notification->toArray());
            $this->assertSame('filament.notifications.creation-success', $restoredNotification->getView());

            $action = collect($component->getSchema('content')->getFlatComponents())
                ->first(fn ($component): bool => $component instanceof Action && $component->getName() === 'create');

            $this->assertInstanceOf(Action::class, $action);
            $livewire->call('mountAction', 'create', [], $action->getContext())
                ->assertSet('mountedActions.0.name', 'create');

            $this->assertStringContainsString(
                'Confirmar criação',
                $livewire->getMountedActionModalHtml(),
            );
        }
    }

    public function test_listing_numeric_limits_show_validation_instead_of_database_error(): void
    {
        $this->actingAs($this->broker());

        $livewire = Livewire::test(CreateListing::class)
            ->fillForm([
                'category' => 'fazenda',
                'status' => 'draft',
                'price' => '10000000000000',
                'area' => '12312312312',
                'title' => ['pt' => 'Fazenda teste'],
                'description' => ['pt' => 'Descrição da fazenda teste.'],
            ]);

        $component = $livewire->instance();
        $action = collect($component->getSchema('content')->getFlatComponents())
            ->first(fn ($component): bool => $component instanceof Action && $component->getName() === 'create');

        $this->assertInstanceOf(Action::class, $action);

        $livewire
            ->call('mountAction', 'create', [], $action->getContext())
            ->assertSet('mountedActions.0.name', 'create')
            ->call('callMountedAction')
            ->assertSet('mountedActions', [])
            ->assertHasFormErrors([
                'price' => 'max',
                'area' => 'max',
            ])
            ->assertSee('O preço não pode ser maior que R$ 9.999.999.999.999,99.')
            ->assertSee('A área não pode ser maior que 9.999.999.999,99 hectares.');

        $this->assertDatabaseCount('listings', 0);
    }

    public function test_duplicate_listing_slug_shows_validation_instead_of_database_error(): void
    {
        $broker = $this->broker();
        $this->actingAs($broker);

        Listing::create([
            'user_id' => $broker->id,
            'category' => 'fazenda',
            'status' => 'draft',
            'slug' => 'fazenda-teste-5',
            'title' => ['pt' => 'Fazenda existente'],
            'description' => ['pt' => 'Descrição da fazenda existente.'],
        ]);

        $livewire = Livewire::test(CreateListing::class)
            ->fillForm([
                'category' => 'fazenda',
                'status' => 'draft',
                'slug' => 'fazenda-teste-5',
                'title' => ['pt' => 'Fazenda Teste 5'],
                'description' => ['pt' => 'Descrição da nova fazenda.'],
            ]);

        $component = $livewire->instance();
        $action = collect($component->getSchema('content')->getFlatComponents())
            ->first(fn ($component): bool => $component instanceof Action && $component->getName() === 'create');

        $this->assertInstanceOf(Action::class, $action);

        $livewire
            ->call('mountAction', 'create', [], $action->getContext())
            ->call('callMountedAction')
            ->assertHasFormErrors(['slug' => 'unique'])
            ->assertSet('data.slug', 'fazenda-teste-5');

        $errorModal = collect(
            session('filament.claimed_notifications') ?? session('filament.notifications', []),
        )
            ->firstWhere('title', 'Não foi possível concluir o cadastro');

        $this->assertNotNull($errorModal);
        $this->assertSame('danger', $errorModal['status']);
        $this->assertSame('Já existe um classificado com este slug. Escolha outro.', $errorModal['body']);
        $this->assertSame('filament.notifications.creation-success', $errorModal['view']);

        $livewire->assertNotified('Não foi possível concluir o cadastro');

        $this->assertDatabaseCount('listings', 1);
    }

    public function test_listing_model_generates_a_unique_slug_when_slug_is_blank(): void
    {
        $broker = $this->broker();

        foreach (range(1, 2) as $index) {
            Listing::create([
                'user_id' => $broker->id,
                'category' => 'fazenda',
                'status' => 'draft',
                'title' => ['pt' => 'Fazenda Teste 5'],
                'description' => ['pt' => "Descrição {$index}."],
            ]);
        }

        $this->assertSame(
            ['fazenda-teste-5', 'fazenda-teste-5-1'],
            Listing::query()->orderBy('id')->pluck('slug')->all(),
        );
    }

    public function test_listing_model_rejects_a_filled_duplicate_slug_without_renaming_it(): void
    {
        $broker = $this->broker();
        $data = [
            'user_id' => $broker->id,
            'category' => 'fazenda',
            'status' => 'draft',
            'slug' => 'fazenda-teste-5',
            'title' => ['pt' => 'Fazenda Teste 5'],
            'description' => ['pt' => 'Descrição da fazenda.'],
        ];

        Listing::create($data);

        try {
            Listing::create($data);
            $this->fail('O modelo permitiu a criação de um slug preenchido e duplicado.');
        } catch (ValidationException $exception) {
            $this->assertSame(
                ['Já existe um classificado com este slug. Escolha outro.'],
                $exception->errors()['data.slug'],
            );
        }

        $this->assertDatabaseCount('listings', 1);
        $this->assertSame(['fazenda-teste-5'], Listing::pluck('slug')->all());
    }

    public function test_confirming_creation_saves_listing_and_event_with_centered_success_feedback(): void
    {
        $this->actingAs($this->broker());

        $cases = [
            [
                'page' => CreateListing::class,
                'data' => [
                    'category' => 'fazenda',
                    'status' => 'draft',
                    'region' => 'Chapada Diamantina',
                    'price' => '950000',
                    'area' => '120',
                    'slug' => 'fazenda-confirmada',
                    'title' => ['pt' => 'Fazenda confirmada'],
                    'subtitle' => ['pt' => 'Pronta para produção'],
                    'description' => ['pt' => 'Descrição da fazenda confirmada.'],
                ],
                'table' => 'listings',
                'notification' => 'Classificado criado com sucesso!',
                'reset' => [
                    'category' => null,
                    'status' => 'draft',
                    'region' => null,
                    'price' => null,
                    'area' => null,
                    'slug' => null,
                    'title.pt' => null,
                    'subtitle.pt' => null,
                    'description.pt' => null,
                ],
            ],
            [
                'page' => CreateEvent::class,
                'data' => [
                    'title' => ['pt' => 'Evento confirmado'],
                    'subtitle' => ['pt' => 'Encontro de investidores'],
                    'description' => ['pt' => 'Descrição do evento confirmado.'],
                    'is_published' => true,
                    'order' => 8,
                ],
                'table' => 'events',
                'notification' => 'Evento criado com sucesso!',
                'reset' => [
                    'title.pt' => null,
                    'subtitle.pt' => null,
                    'description.pt' => null,
                    'is_published' => true,
                    'order' => 0,
                ],
            ],
        ];

        foreach ($cases as $case) {
            $livewire = Livewire::test($case['page'])->fillForm($case['data']);
            $component = $livewire->instance();
            $action = collect($component->getSchema('content')->getFlatComponents())
                ->first(fn ($component): bool => $component instanceof Action && $component->getName() === 'create');

            $this->assertInstanceOf(Action::class, $action);

            $livewire
                ->call('mountAction', 'create', [], $action->getContext())
                ->call('callMountedAction')
                ->assertHasNoFormErrors()
                ->assertNotified($case['notification'])
                ->assertSet('record', null)
                ->assertNoRedirect();

            foreach ($case['reset'] as $field => $value) {
                $livewire->assertSet("data.{$field}", $value);
            }

            $this->assertDatabaseCount($case['table'], 1);
        }
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
