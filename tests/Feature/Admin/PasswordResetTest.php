<?php

namespace Tests\Feature\Admin;

use App\Filament\Pages\Auth\RequestPasswordReset;
use App\Filament\Pages\Auth\ResetPassword as ResetPasswordPage;
use App\Models\User;
use App\Notifications\ResetPassword as ResetPasswordNotification;
use Filament\Facades\Filament;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification as LaravelNotification;
use Illuminate\Support\Facades\Password;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'broker']);
        Filament::setCurrentPanel('admin');
    }

    private function panelUser(string $role = 'broker'): User
    {
        $user = User::factory()->create();
        $user->assignRole($role);

        return $user;
    }

    public function test_login_and_portuguese_password_reset_pages_use_project_branding(): void
    {
        $user = $this->panelUser();
        $resetUrl = Filament::getResetPasswordUrl('token-de-teste', $user);

        $this->get('/admin/login')
            ->assertOk()
            ->assertSee('Esqueceu sua senha?')
            ->assertSee('/admin/recuperar-senha/solicitar', escape: false);

        $this->get('/admin/recuperar-senha/solicitar')
            ->assertOk()
            ->assertSee('Esqueceu sua senha?')
            ->assertSee('src="'.asset('images/logo.jpeg').'"', escape: false)
            ->assertSee('style="height: 6rem;"', escape: false);

        $this->assertStringContainsString('/admin/recuperar-senha/redefinir', $resetUrl);

        $this->get($resetUrl)
            ->assertOk()
            ->assertSee('Redefina sua senha')
            ->assertSee('Confirmar senha')
            ->assertSee('A senha necessita de pelo menos 8 caracteres.')
            ->assertSee('src="'.asset('images/logo.jpeg').'"', escape: false);
    }

    public function test_admin_and_broker_can_request_a_queued_reset_link(): void
    {
        foreach (['admin', 'broker'] as $role) {
            LaravelNotification::fake();
            $user = $this->panelUser($role);

            Livewire::test(RequestPasswordReset::class)
                ->fillForm(['email' => $user->email])
                ->call('request')
                ->assertHasNoFormErrors()
                ->assertNotified('Solicitação recebida');

            LaravelNotification::assertSentTo(
                $user,
                ResetPasswordNotification::class,
                fn (ResetPasswordNotification $notification): bool => str_contains(
                    $notification->url,
                    '/admin/recuperar-senha/redefinir',
                ),
            );

            $this->assertDatabaseHas('password_reset_tokens', ['email' => $user->email]);
        }
    }

    public function test_unknown_and_unauthorized_accounts_receive_the_same_generic_feedback(): void
    {
        LaravelNotification::fake();
        $unauthorized = User::factory()->create();

        foreach ([$unauthorized->email, 'nao-existe@pib.test'] as $email) {
            Livewire::test(RequestPasswordReset::class)
                ->fillForm(['email' => $email])
                ->call('request')
                ->assertHasNoFormErrors()
                ->assertNotified('Solicitação recebida');
        }

        LaravelNotification::assertNothingSent();
        $this->assertDatabaseMissing('password_reset_tokens', ['email' => $unauthorized->email]);
        $this->assertDatabaseMissing('password_reset_tokens', ['email' => 'nao-existe@pib.test']);
    }

    public function test_reset_email_is_queued_and_uses_portal_branding(): void
    {
        $user = $this->panelUser();
        $notification = new ResetPasswordNotification('token-de-teste');
        $notification->url = 'https://portalinvestbahia.com.br/admin/recuperar-senha/redefinir?token=teste';

        $mail = $notification->toMail($user);
        $html = (string) $mail->render();

        $this->assertInstanceOf(ShouldQueue::class, $notification);
        $this->assertSame('Redefinição de senha | Portal Invest Bahia', $mail->subject);
        $this->assertStringContainsString('Redefinir minha senha', $html);
        $this->assertStringContainsString(asset('images/logo.jpeg'), $html);
        $this->assertStringContainsString('expira em 60 minutos', $html);
        $this->assertStringContainsString($notification->url, $html);
    }

    public function test_valid_token_changes_password_rotates_remember_token_and_returns_to_login(): void
    {
        $user = $this->panelUser();
        $user->forceFill([
            'password' => Hash::make('senha-antiga'),
            'remember_token' => 'token-antigo',
        ])->save();
        $token = Password::broker()->createToken($user);

        Livewire::test(ResetPasswordPage::class, [
            'email' => $user->email,
            'token' => $token,
        ])
            ->fillForm([
                'email' => $user->email,
                'password' => 'nova-senha-segura',
                'passwordConfirmation' => 'nova-senha-segura',
            ])
            ->call('resetPassword')
            ->assertHasNoFormErrors()
            ->assertRedirect('/admin/login');

        $user->refresh();

        $this->assertTrue(Hash::check('nova-senha-segura', $user->password));
        $this->assertFalse(Hash::check('senha-antiga', $user->password));
        $this->assertNotSame('token-antigo', $user->remember_token);
        $this->assertDatabaseMissing('password_reset_tokens', ['email' => $user->email]);
    }

    public function test_weak_or_mismatched_password_is_rejected(): void
    {
        $user = $this->panelUser();
        $token = Password::broker()->createToken($user);

        Livewire::test(ResetPasswordPage::class, [
            'email' => $user->email,
            'token' => $token,
        ])
            ->fillForm([
                'email' => $user->email,
                'password' => 'curta',
                'passwordConfirmation' => 'diferente',
            ])
            ->call('resetPassword')
            ->assertHasFormErrors(['password'])
            ->assertSee('A senha necessita de pelo menos 8 caracteres.');
    }

    public function test_invalid_token_cannot_change_the_password(): void
    {
        $user = $this->panelUser();
        $originalPassword = $user->password;

        Livewire::test(ResetPasswordPage::class, [
            'email' => $user->email,
            'token' => 'token-invalido',
        ])
            ->fillForm([
                'email' => $user->email,
                'password' => 'senha-segura-invalida',
                'passwordConfirmation' => 'senha-segura-invalida',
            ])
            ->call('resetPassword');

        $this->assertSame($originalPassword, $user->refresh()->password);
    }

    public function test_expired_token_cannot_change_the_password(): void
    {
        $user = $this->panelUser();
        $originalPassword = $user->password;

        $expiredToken = Password::broker()->createToken($user);
        DB::table('password_reset_tokens')
            ->where('email', $user->email)
            ->update(['created_at' => now()->subMinutes(61)]);

        Livewire::test(ResetPasswordPage::class, [
            'email' => $user->email,
            'token' => $expiredToken,
        ])
            ->fillForm([
                'email' => $user->email,
                'password' => 'senha-segura-expirada',
                'passwordConfirmation' => 'senha-segura-expirada',
            ])
            ->call('resetPassword');

        $this->assertSame($originalPassword, $user->refresh()->password);
    }

    public function test_used_token_cannot_change_the_password_again(): void
    {
        $user = $this->panelUser();

        $validToken = Password::broker()->createToken($user);
        $componentData = [
            'email' => $user->email,
            'password' => 'primeira-senha-valida',
            'passwordConfirmation' => 'primeira-senha-valida',
        ];

        Livewire::test(ResetPasswordPage::class, ['email' => $user->email, 'token' => $validToken])
            ->fillForm($componentData)
            ->call('resetPassword');

        Livewire::test(ResetPasswordPage::class, ['email' => $user->email, 'token' => $validToken])
            ->fillForm([
                'email' => $user->email,
                'password' => 'segunda-senha-invalida',
                'passwordConfirmation' => 'segunda-senha-invalida',
            ])
            ->call('resetPassword');

        $this->assertTrue(Hash::check('primeira-senha-valida', $user->refresh()->password));
        $this->assertFalse(Hash::check('segunda-senha-invalida', $user->password));
    }

    public function test_request_rate_limit_blocks_the_third_immediate_attempt(): void
    {
        LaravelNotification::fake();
        $user = $this->panelUser();

        for ($attempt = 1; $attempt <= 2; $attempt++) {
            Livewire::test(RequestPasswordReset::class)
                ->fillForm(['email' => $user->email])
                ->call('request');
        }

        Livewire::test(RequestPasswordReset::class)
            ->fillForm(['email' => $user->email])
            ->call('request')
            ->assertNotified('Muitas solicitações');
    }
}
