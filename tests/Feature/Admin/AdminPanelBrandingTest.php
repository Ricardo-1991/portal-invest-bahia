<?php

namespace Tests\Feature\Admin;

use App\Filament\Widgets\PortalBrandWidget;
use App\Models\User;
use Filament\Facades\Filament;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminPanelBrandingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'admin']);
        Filament::setCurrentPanel('admin');
    }

    public function test_dashboard_uses_brazilian_portuguese_and_project_branding(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->actingAs($admin)
            ->get('/admin')
            ->assertOk()
            ->assertSee('Painel de Controle')
            ->assertSee('Portal Invest Bahia')
            ->assertSee('images/logo.jpeg', escape: false)
            ->assertSee('<link rel="icon" href="'.asset('images/logo.jpeg').'"', escape: false)
            ->assertDontSee('Painel de Controlo')
            ->assertDontSee('filamentphp.com')
            ->assertDontSee('github.com/filamentphp');

        $this->assertSame('pt_BR', config('app.locale'));
        $this->assertNull(Filament::getGlobalSearchProvider());
        $this->assertContains(PortalBrandWidget::class, Filament::getWidgets());
        $this->assertNotContains(FilamentInfoWidget::class, Filament::getWidgets());
    }

    public function test_login_page_uses_project_logo(): void
    {
        $response = $this->get('/admin/login');

        $response
            ->assertOk()
            ->assertSee('src="'.asset('images/logo.jpeg').'"', escape: false)
            ->assertSee('style="height: 6rem;"', escape: false);
    }
}
