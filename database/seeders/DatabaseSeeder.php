<?php

namespace Database\Seeders;

use App\Enums\ClaveRol;
use App\Models\Rol;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([RolSeeder::class, DepartamentoSeeder::class, MunicipioSeeder::class, CatalogoBienesServiciosSeeder::class, PoblacionVulnerableSeeder::class]);

        // Solo en desarrollo: administrador de prueba (contraseña "password").
        if (app()->isLocal() && User::where('email', 'admin@epsnet.test')->doesntExist()) {
            User::forceCreate([
                'name' => 'Administrador DIGEU',
                'email' => 'admin@epsnet.test',
                'email_verified_at' => now(),
                'password' => 'password',
                'rol_id' => Rol::delSistema(ClaveRol::Digeu)->id,
            ]);
        }
    }
}
