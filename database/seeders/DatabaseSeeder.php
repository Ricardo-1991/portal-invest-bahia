<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Papéis do sistema.
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $brokerRole = Role::firstOrCreate(['name' => 'broker']);

        // Administrador geral.
        $admin = User::firstOrCreate(
            ['email' => 'admin@pib.com.br'],
            ['name' => 'Administrador PIB', 'password' => Hash::make('password')],
        );
        $admin->syncRoles([$adminRole]);

        // Corretor de exemplo (dev).
        $broker = User::firstOrCreate(
            ['email' => 'corretor@pib.com.br'],
            ['name' => 'Corretor Exemplo', 'password' => Hash::make('password')],
        );
        $broker->syncRoles([$brokerRole]);
    }
}
