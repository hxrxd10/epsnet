<?php

namespace Database\Factories;

use App\Models\Departamento;
use App\Models\Expediente;
use App\Models\UbicacionTerritorial;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UbicacionTerritorial>
 */
class UbicacionTerritorialFactory extends Factory
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
            'departamento_id' => Departamento::factory(),
            'municipio' => fake()->city(),
            'comunidad' => fake()->streetName(),
            'latitud' => fake()->latitude(13.7, 17.8),
            'longitud' => fake()->longitude(-92.2, -88.2),
            'referencia' => null,
        ];
    }
}
