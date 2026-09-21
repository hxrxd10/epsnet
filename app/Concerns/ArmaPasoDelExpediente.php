<?php

namespace App\Concerns;

use App\Enums\Eje;
use App\Models\Expediente;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Comportamiento común de los controladores del asistente del estudiante (ejes y cierre).
 */
trait ArmaPasoDelExpediente
{
    /**
     * Número del último paso del asistente: el resumen y la orden de impresión.
     */
    protected const int PASO_CIERRE = 7;

    /**
     * Renderiza la página del eje con el contexto del asistente (expediente y pasos) y recuerda
     * el eje para que el estudiante retome el llenado donde lo dejó.
     *
     * @param  array<string, mixed>  $props
     */
    protected function paso(Expediente $expediente, Eje $eje, array $props): Response
    {
        if ($expediente->eje_actual !== $eje->value) {
            $expediente->update(['eje_actual' => $eje->value]);
        }

        return Inertia::render("estudiante/pasos/{$eje->segmento()}", [
            ...$this->contextoDelAsistente($expediente, $eje->value),
            ...$props,
        ]);
    }

    /**
     * Datos que necesita el asistente en cada página: expediente, eje actual y pasos con su avance.
     *
     * @return array<string, mixed>
     */
    protected function contextoDelAsistente(Expediente $expediente, int $eje): array
    {
        $expediente->loadCount(array_map(fn (Eje $caso): string => $caso->relacion(), Eje::cases()));
        $expediente->loadExists('ordenImpresion');

        $pasos = array_map(fn (Eje $caso): array => [
            'eje' => $caso->value,
            'titulo' => $caso->titulo(),
            'href' => route($caso->ruta(), $expediente, absolute: false),
            'registros' => (int) $expediente->getAttribute(Str::snake($caso->relacion()).'_count'),
        ], Eje::cases());

        $pasos[] = [
            'eje' => self::PASO_CIERRE,
            'titulo' => 'Resumen y orden de impresión',
            'href' => route('estudiante.expedientes.cierre.index', $expediente, absolute: false),
            'registros' => $expediente->ordenImpresion()->exists() ? 1 : 0,
        ];

        return [
            'expediente' => [
                'id' => $expediente->id,
                'nombre_carrera' => $expediente->nombre_carrera,
                'nombre_unidad' => $expediente->nombre_unidad,
                'nivel_academico' => $expediente->nivel_academico,
                'estado' => $expediente->estado_expediente->value,
                'estado_etiqueta' => $expediente->estado_expediente->etiqueta(),
            ],
            'eje' => $eje,
            'pasos' => $pasos,
        ];
    }

    protected function guardado(Expediente $expediente, Eje $eje, string $mensaje): RedirectResponse
    {
        Inertia::flash('toast', ['type' => 'success', 'message' => $mensaje]);

        return to_route($eje->ruta(), $expediente);
    }
}
