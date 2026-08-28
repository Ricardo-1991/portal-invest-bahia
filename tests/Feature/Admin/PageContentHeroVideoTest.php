<?php

namespace Tests\Feature\Admin;

use App\Filament\Resources\PageContents\PageContentResource;
use App\Filament\Resources\PageContents\Pages\EditPageContent;
use App\Filament\Resources\PageContents\Pages\ListPageContents;
use App\Models\PageContent;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Testing\File as TestingFile;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PageContentHeroVideoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
        Role::firstOrCreate(['name' => 'admin']);
        Filament::setCurrentPanel('admin');

        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $this->actingAs($admin);
    }

    /** Cria um arquivo esparso cujo tamanho real sobrevive ao upload temporário do Livewire. */
    private function videoWithRealSize(string $name, int $kilobytes): UploadedFile
    {
        $stream = tmpfile();
        ftruncate($stream, $kilobytes * 1024);

        return (new TestingFile($name, $stream))->mimeType('video/mp4');
    }

    public function test_filament_upload_replaces_and_removes_home_hero_video(): void
    {
        $home = PageContent::create([
            'key' => 'home',
            'title' => ['pt' => 'Início'],
        ]);

        Livewire::test(EditPageContent::class, ['record' => $home->getKey()])
            ->assertSee('Vídeo do hero')
            ->fillForm([
                'hero_video' => UploadedFile::fake()->create('hero-um.mp4', 1024, 'video/mp4'),
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertCount(1, $home->refresh()->getMedia('hero_video'));
        $firstMediaId = $home->getFirstMedia('hero_video')->getKey();

        Livewire::test(EditPageContent::class, ['record' => $home->getKey()])
            ->fillForm([
                'hero_video' => UploadedFile::fake()->create('hero-dois.mp4', 1024, 'video/mp4'),
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertCount(1, $home->refresh()->getMedia('hero_video'));
        $this->assertNotSame($firstMediaId, $home->getFirstMedia('hero_video')->getKey());

        Livewire::test(EditPageContent::class, ['record' => $home->getKey()])
            ->fillForm(['hero_video' => null])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertNull($home->refresh()->heroVideoUrl());
        $this->assertCount(0, $home->getMedia('hero_video'));
    }

    public function test_pages_resource_only_allows_editing_the_home_video(): void
    {
        $home = PageContent::create([
            'key' => 'home',
            'title' => ['pt' => 'Início'],
        ]);
        $otherPage = PageContent::create([
            'key' => 'fazenda',
            'title' => ['pt' => 'Fazendas'],
        ]);

        $this->assertFalse(PageContentResource::canCreate());
        $this->assertTrue(PageContentResource::canEdit($home));
        $this->assertFalse(PageContentResource::canEdit($otherPage));
        $this->assertFalse(PageContentResource::canDelete($home));
        $this->assertArrayNotHasKey('create', PageContentResource::getPages());

        Livewire::test(ListPageContents::class)
            ->assertCanSeeTableRecords([$home])
            ->assertCanNotSeeTableRecords([$otherPage]);

        Livewire::test(EditPageContent::class, ['record' => $home->getKey()])
            ->assertSee('Vídeo do hero')
            ->assertDontSee('Conteúdo por idioma')
            ->assertDontSee('Título');

        $this->get(PageContentResource::getUrl('edit', ['record' => $otherPage]))
            ->assertNotFound();
    }

    public function test_video_over_100_mb_shows_inline_validation_feedback(): void
    {
        $home = PageContent::create([
            'key' => 'home',
            'title' => ['pt' => 'Início'],
        ]);

        Livewire::test(EditPageContent::class, ['record' => $home->getKey()])
            ->fillForm([
                'hero_video' => $this->videoWithRealSize('hero-grande.mp4', 102401),
            ])
            ->call('save')
            ->assertHasFormErrors(['hero_video'])
            ->assertSee('O vídeo excede o limite de 100 MB');

        $this->assertCount(0, $home->refresh()->getMedia('hero_video'));
    }

    public function test_media_library_size_exception_is_converted_to_admin_feedback(): void
    {
        $home = PageContent::create([
            'key' => 'home',
            'title' => ['pt' => 'Início'],
        ]);

        // Simula uma configuração externa mais restritiva como a que causou o 500.
        config()->set('media-library.max_file_size', 10 * 1024 * 1024);

        Livewire::test(EditPageContent::class, ['record' => $home->getKey()])
            ->fillForm([
                'hero_video' => $this->videoWithRealSize('hero-11mb.mp4', 11 * 1024),
            ])
            ->call('save')
            ->assertHasErrors(['data.hero_video'])
            ->assertNotified('Vídeo muito grande');

        $this->assertCount(0, $home->refresh()->getMedia('hero_video'));
    }

    public function test_home_uses_poster_without_video_and_renders_uploaded_mp4(): void
    {
        $home = PageContent::create([
            'key' => 'home',
            'title' => ['pt' => 'Início'],
        ]);

        $this->get('/pt')
            ->assertOk()
            ->assertSee('hero-rural-bahia', false)
            ->assertDontSee('data-hero-video', false);

        $home->addMedia(UploadedFile::fake()->create('hero.mp4', 1024, 'video/mp4'))
            ->toMediaCollection('hero_video', 'public');

        $this->get('/pt')
            ->assertOk()
            ->assertSee('data-hero-video', false)
            ->assertSee('hero.mp4', false)
            ->assertSee('type="video/mp4"', false);

        $home->clearMediaCollection('hero_video');

        $this->get('/pt')
            ->assertOk()
            ->assertDontSee('data-hero-video', false);
    }
}
