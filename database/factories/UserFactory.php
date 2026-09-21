<?php

namespace Database\Factories;

use App\Enums\ClaveRol;
use App\Models\Rol;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null,
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Indicate that the model has two-factor authentication configured.
     */
    public function withTwoFactor(): static
    {
        return $this->state(fn (array $attributes) => [
            'two_factor_secret' => encrypt('secret'),
            'two_factor_recovery_codes' => encrypt(json_encode(['recovery-code-1'])),
            'two_factor_confirmed_at' => now(),
        ]);
    }

    /**
     * Usuario con el rol de estudiante.
     */
    public function estudiante(): static
    {
        return $this->state(fn (array $attributes) => [
            'rol_id' => Rol::delSistema(ClaveRol::Estudiante)->id,
        ]);
    }

    /**
     * Usuario con el rol de invitado (solo consulta estadísticas).
     */
    public function invitado(): static
    {
        return $this->state(fn (array $attributes) => [
            'rol_id' => Rol::delSistema(ClaveRol::Invitado)->id,
        ]);
    }

    /**
     * Usuario administrador (DIGEU).
     */
    public function administrador(): static
    {
        return $this->state(fn (array $attributes) => [
            'rol_id' => Rol::delSistema(ClaveRol::Digeu)->id,
        ]);
    }
}
