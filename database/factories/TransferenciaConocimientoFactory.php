<?php

namespace Database\Factories;

use App\Enums\TipoActividadTransferencia;
use App\Models\Expediente;
use App\Models\TransferenciaConocimiento;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TransferenciaConocimiento>
 */
class TransferenciaConocimientoFactory extends Factory
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
            'tipo_actividad' => fake()->randomElement(TipoActividadTransferencia::cases()),
            'actividad' => fake()->paragraph(),
            'comunidad' => fake()->city(),
            'numero_participantes' => fake()->numberBetween(5, 80),
            'fecha' => fake()->date(),
        ];
    }
}
