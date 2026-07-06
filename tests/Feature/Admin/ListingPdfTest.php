<?php

namespace Tests\Feature\Admin;

use App\Models\Listing;
use App\Models\User;
use App\Support\ListingPdf;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ListingPdfTest extends TestCase
{
    use RefreshDatabase;

    public function test_generates_a_valid_pdf_for_a_listing(): void
    {
        Role::firstOrCreate(['name' => 'broker']);
        $broker = User::factory()->create(['whatsapp' => '5573999998888']);
        $broker->assignRole('broker');

        $listing = Listing::create([
            'user_id' => $broker->id,
            'category' => 'fazenda',
            'status' => 'published',
            'region' => 'Ilhéus',
            'price' => 2500000,
            'title' => ['pt' => 'Fazenda de Cacau', 'en' => 'Cocoa Farm', 'es' => 'Hacienda', 'it' => 'Tenuta'],
            'description' => ['pt' => 'Descrição completa.', 'en' => 'Full desc.', 'es' => 'Desc.', 'it' => 'Desc.'],
        ]);

        $output = ListingPdf::for($listing)->output();

        $this->assertStringStartsWith('%PDF-', $output);
        $this->assertGreaterThan(1000, strlen($output));
        $this->assertSame('anuncio-'.$listing->slug.'.pdf', ListingPdf::filename($listing));
    }
}
