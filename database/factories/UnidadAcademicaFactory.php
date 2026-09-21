<?php

namespace Database\Factories;

use App\Enums\TipoUnidadAcademica;
use App\Models\UnidadAcademica;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UnidadAcademica>
 */
class UnidadAcademicaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nombre' => 'Facultad de '.fake()->unique()->words(2, true),
            'siglas' => null,
            'tipo' => TipoUnidadAcademica::Facultad,
            'activa' => true,
        ];
    }
}
