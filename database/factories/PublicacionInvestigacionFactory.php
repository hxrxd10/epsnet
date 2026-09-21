<?php

namespace Database\Factories;

use App\Enums\TipoPublicacion;
use App\Models\Expediente;
use App\Models\PublicacionInvestigacion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PublicacionInvestigacion>
 */
class PublicacionInvestigacionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'expediente_id' => Expediente::factory(),
            'titulo' => fake()->sentence(8),
            'tipo' => fake()->randomElement(TipoPublicacion::cases()),
            'autores' => fake()->name().', '.fake()->name(),
            'medio_publicacion' => fake()->company(),
            'resumen' => fake()->paragraph(),
            'enlace' => null,
            'fecha_publicacion' => fake()->date(),
        ];
    }
}
