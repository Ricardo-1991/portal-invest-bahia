<?php

namespace Tests\Feature\Admin;

use App\Filament\Resources\Listings\Pages\CreateListing;
use App\Models\Listing;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ListingGalleryUploadTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
        Role::firstOrCreate(['name' => 'broker']);
        Filament::setCurrentPanel('admin');

        $broker = User::factory()->create();
        $broker->assignRole('broker');
        $this->actingAs($broker);
    }

    public function test_broker_can_upload_three_gallery_images_at_once(): void
    {
        $gallery = [
            UploadedFile::fake()->image('galeria-1.jpg', 1200, 800),
            UploadedFile::fake()->image('galeria-2.jpg', 1200, 800),
            UploadedFile::fake()->image('galeria-3.jpg', 1200, 800),
        ];

        $livewire = Livewire::test(CreateListing::class)
            ->fillForm([
                'category' => 'fazenda',
                'status' => 'draft',
                'title' => ['pt' => 'Fazenda com galeria'],
                'description' => ['pt' => 'Descrição da fazenda com três imagens.'],
                'gallery' => $gallery,
            ]);

        $component = $livewire->instance();
        $action = collect($component->getSchema('content')->getFlatComponents())
            ->first(fn ($component): bool => $component instanceof Action && $component->getName() === 'create');

        $this->assertInstanceOf(Action::class, $action);

        $livewire
            ->call('mountAction', 'create', [], $action->getContext())
            ->call('callMountedAction')
            ->assertHasNoFormErrors()
            ->assertSet('record', null)
            ->assertSet('data.gallery', [])
            ->assertNoRedirect();

        $listing = Listing::firstOrFail();

        $this->assertCount(3, $listing->getMedia('gallery'));
        $this->assertSame(
            ['image/jpeg'],
            $listing->getMedia('gallery')->pluck('mime_type')->unique()->values()->all(),
        );
    }
}
