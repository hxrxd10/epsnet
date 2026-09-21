<?php

namespace Database\Factories;

use App\Models\Adjunto;
use App\Models\AdjuntoContenido;
use App\Models\Expediente;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Adjunto>
 */
class AdjuntoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'categoria' => Adjunto::ORDEN_IMPRESION,
            'entidad_tipo' => 'expediente',
            'entidad_id' => Expediente::factory(),
            'nombre_original' => 'orden-de-impresion.pdf',
            'mime_type' => 'application/pdf',
            'tamano_bytes' => 14,
            'sha256' => hash('sha256', '%PDF-1.4 falso'),
            'fecha_subida' => now(),
        ];
    }

    /**
     * Guarda también el contenido (base64) del archivo.
     */
    public function conContenido(string $bytes = '%PDF-1.4 falso'): static
    {
        return $this->afterCreating(function (Adjunto $adjunto) use ($bytes): void {
            AdjuntoContenido::create([
                'adjunto_id' => $adjunto->id,
                'contenido' => base64_encode($bytes),
            ]);
        });
    }
}
