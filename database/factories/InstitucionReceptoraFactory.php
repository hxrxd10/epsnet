<?php

namespace Database\Factories;

use App\Models\InstitucionReceptora;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<InstitucionReceptora>
 */
class InstitucionReceptoraFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nombre' => 'Escuela '.fake()->unique()->words(3, true),
            'tipo' => 'Educación',
            'nombre_contacto' => fake()->name(),
            'correo_contacto' => fake()->safeEmail(),
            'telefono_contacto' => fake()->numerify('2#######'),
        ];
    }
}
