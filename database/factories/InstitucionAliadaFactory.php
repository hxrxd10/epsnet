<?php

namespace Database\Factories;

use App\Models\InstitucionAliada;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<InstitucionAliada>
 */
class InstitucionAliadaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nombre' => 'Fundación '.fake()->unique()->words(3, true),
            'tipo' => 'Organización no gubernamental',
            'nombre_contacto' => fake()->name(),
            'correo_contacto' => fake()->safeEmail(),
            'telefono_contacto' => fake()->numerify('2#######'),
        ];
    }
}
