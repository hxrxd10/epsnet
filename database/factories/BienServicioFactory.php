<?php

namespace Database\Factories;

use App\Enums\TipoBienServicio;
use App\Models\BienServicio;
use App\Models\Expediente;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BienServicio>
 */
class BienServicioFactory extends Factory
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
            'tipo' => fake()->randomElement(TipoBienServicio::cases()),
            'descripcion' => fake()->paragraph(),
            'beneficiarios' => fake()->sentence(),
            'cantidad_beneficiarios' => fake()->numberBetween(1, 300),
            'fecha' => fake()->date(),
        ];
    }
}
