<?php

namespace Database\Factories;

use App\Models\ActorParticipante;
use App\Models\Expediente;
use App\Models\InstitucionReceptora;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ActorParticipante>
 */
class ActorParticipanteFactory extends Factory
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
            'institucion_receptora_id' => InstitucionReceptora::factory(),
            'contraparte' => fake()->name(),
            'comunidad_beneficiada' => fake()->city(),
        ];
    }
}
