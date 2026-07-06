<?php

namespace Database\Seeders;

use App\Models\PageContent;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use RuntimeException;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $seed = config('pib.seed');

        // Nunca deixa a senha padrão de desenvolvimento ir para produção.
        if (app()->environment('production') && in_array('password', [$seed['admin_password'], $seed['broker_password']], true)) {
            throw new RuntimeException(
                'Defina PIB_ADMIN_PASSWORD e PIB_BROKER_PASSWORD no .env antes de rodar o seeder em produção.'
            );
        }

        // Papéis do sistema.
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $brokerRole = Role::firstOrCreate(['name' => 'broker']);

        // Administrador geral.
        $admin = User::firstOrCreate(
            ['email' => $seed['admin_email']],
            ['name' => 'Administrador PIB', 'password' => Hash::make($seed['admin_password'])],
        );
        $admin->syncRoles([$adminRole]);

        // Corretor de exemplo (dev).
        $broker = User::firstOrCreate(
            ['email' => $seed['broker_email']],
            [
                'name' => 'Corretor Exemplo',
                'password' => Hash::make($seed['broker_password']),
                'email_public' => $seed['broker_email'],
                'whatsapp' => '5573999998888',
            ],
        );
        $broker->syncRoles([$brokerRole]);

        // Páginas fixas editáveis (título em português como ponto de partida).
        $pages = [
            'home' => 'Portal Invest Bahia',
            'fazenda' => 'Fazendas',
            'ativo' => 'Ativos',
            'servico' => 'Serviços',
            'informacoes' => 'Informações',
            'contatos' => 'Contatos',
        ];
        foreach ($pages as $key => $titlePt) {
            $page = PageContent::firstOrNew(['key' => $key]);
            if (! $page->exists) {
                $page->setTranslation('title', 'pt', $titlePt);
                $page->save();
            }
        }
    }
}
