<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\Listing;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PublicWatermarkTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
    }

    public function test_listing_uses_watermarked_main_gallery_and_thumbnail_while_preserving_originals(): void
    {
        $listing = Listing::create([
            'user_id' => User::factory()->create()->id,
            'category' => 'casa',
            'status' => 'published',
            'title' => ['pt' => 'Casa com marca'],
            'description' => ['pt' => 'Descrição da casa.'],
        ]);

        $file = UploadedFile::fake()->image('casa.jpg', 1200, 800);
        $originalHash = hash_file('sha256', $file->getRealPath());
        $main = $listing->addMedia($file)->toMediaCollection('main', 'public');
        $gallery = $listing->addMedia(UploadedFile::fake()->image('galeria.jpg', 1200, 800))
            ->toMediaCollection('gallery', 'public');

        $this->assertSame($originalHash, hash_file('sha256', $main->getPath()));
        $this->assertFileExists($main->getPath('watermarked'));
        $this->assertFileExists($main->getPath('public_thumb'));
        $this->assertFileExists($gallery->getPath('watermarked'));
        $this->assertNotSame($originalHash, hash_file('sha256', $main->getPath('watermarked')));
        $this->assertSame($main->getUrl('watermarked'), $listing->mainImageUrl());

        $this->get('/pt/casas')->assertOk()->assertSee($main->getUrl('public_thumb'), false);
        $this->get('/pt/anuncio/'.$listing->slug)
            ->assertOk()
            ->assertSee($main->getUrl('watermarked'), false)
            ->assertSee($gallery->getUrl('watermarked'), false)
            ->assertDontSee($main->getUrl(), false);
    }

    public function test_event_uses_watermarked_image_and_thumbnail(): void
    {
        $event = Event::create([
            'user_id' => User::factory()->create()->id,
            'title' => ['pt' => 'Evento com marca'],
            'is_published' => true,
        ]);
        $media = $event->addMedia(UploadedFile::fake()->image('evento.jpg', 1200, 800))
            ->toMediaCollection('image', 'public');

        $this->assertFileExists($media->getPath('watermarked'));
        $this->assertFileExists($media->getPath('public_thumb'));
        $this->assertSame($media->getUrl('watermarked'), $event->imageUrl());
        $this->get('/pt')->assertOk()->assertSee($media->getUrl('public_thumb'), false);
    }
}
