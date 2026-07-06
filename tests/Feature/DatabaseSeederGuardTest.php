<?php

namespace Tests\Feature;

use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Tests\TestCase;

class DatabaseSeederGuardTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeder_refuses_to_run_in_production_with_default_password(): void
    {
        app()['env'] = 'production';

        $this->expectException(RuntimeException::class);

        (new DatabaseSeeder())->run();
    }

    public function test_seeder_runs_in_production_when_passwords_are_customized(): void
    {
        app()['env'] = 'production';
        config([
            'pib.seed.admin_password' => 'uma-senha-forte-de-verdade',
            'pib.seed.broker_password' => 'outra-senha-forte-de-verdade',
        ]);

        (new DatabaseSeeder())->run();

        $this->assertDatabaseHas('users', ['email' => config('pib.seed.admin_email')]);
    }
}
