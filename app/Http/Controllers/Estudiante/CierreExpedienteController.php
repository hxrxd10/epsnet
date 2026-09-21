<?php

namespace App\Http\Controllers\Estudiante;

use App\Concerns\ArmaPasoDelExpediente;
use App\Enums\Eje;
use App\Enums\EstadoExpediente;
use App\Http\Controllers\Controller;
use App\Models\Expediente;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class CierreExpedienteController extends Controller
{
    use ArmaPasoDelExpediente;

    /**
     * Último paso: resumen de todo lo registrado para que el estudiante corrija lo que necesite
     * y orden de impresión que valida el EPS.
     */
    public function index(Expediente $expediente): Response
    {
        $orden = $expediente->ordenImpresion()->first();

        $resumen = array_map(fn (Eje $eje): array => [
            'eje' => $eje->value,
            'titulo' => $eje->titulo(),
            'href' => route($eje->ruta(), $expediente, absolute: false),
            'items' => $this->elementos($expediente, $eje),
        ], Eje::cases());

        return Inertia::render('estudiante/pasos/cierre', [
            ...$this->contextoDelAsistente($expediente, self::PASO_CIERRE),
            'resumen' => $resumen,
            'total_registros' => collect($resumen)->sum(fn (array $eje): int => count($eje['items'])),
            'completado_at' => $expediente->completado_at?->toIso8601String(),
            'orden_impresion' => $orden === null ? null : [
                'nombre' => $orden->nombre_original,
                'mime_type' => $orden->mime_type,
                'tamano_bytes' => $orden->tamano_bytes,
                'subido_at' => $orden->fecha_subida->toIso8601String(),
                'descarga' => route('estudiante.expedientes.orden-impresion.show', $expediente, absolute: false),
            ],
        ]);
    }

    /**
     * Guarda el expediente como completo. Requiere la orden de impresión y al menos un registro.
     */
    public function store(Expediente $expediente): RedirectResponse
    {
        if (! $expediente->ordenImpresion()->exists()) {
            throw ValidationException::withMessages([
                'orden_impresion' => 'Sube tu orden de impresión para completar tu EPS.',
            ]);
        }

        $total = collect(Eje::cases())->sum(fn (Eje $eje): int => $expediente->{$eje->relacion()}()->count());

        if ($total === 0) {
            throw ValidationException::withMessages([
                'registros' => 'Registra al menos un elemento en alguno de los ejes antes de completar tu EPS.',
            ]);
        }

        if ($expediente->estado_expediente === EstadoExpediente::Activo) {
            $expediente->update([
                'estado_expediente' => EstadoExpediente::Completo,
                'completado_at' => now(),
            ]);
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Tu EPS quedó completo y cuenta para las estadísticas.']);

        return to_route('estudiante.expedientes.cierre.index', $expediente);
    }

    /**
     * Textos breves de los registros de un eje, para el resumen.
     *
     * @return list<string>
     */
    private function elementos(Expediente $expediente, Eje $eje): array
    {
        $registros = match ($eje) {
            Eje::BienesServicios => $expediente->bienesServicios()->orderBy('fecha')->pluck('descripcion'),
            Eje::Publicaciones => $expediente->publicaciones()->orderBy('id')->pluck('titulo'),
            Eje::Transferencia => $expediente->transferencias()->orderBy('fecha')->pluck('actividad'),
            Eje::Territorio => $expediente->ubicaciones()->with(['departamento', 'municipio'])->orderBy('id')->get()
                ->map(fn ($ubicacion): string => collect([$ubicacion->departamento->nombre, $ubicacion->municipio?->nombre])->filter()->implode(' · ')),
            Eje::Actores => $expediente->actores()->with('institucionReceptora')->orderBy('id')->get()
                ->map(fn ($actor): string => "{$actor->institucionReceptora->nombre} · {$actor->contraparte}"),
            Eje::Seguimiento => $expediente->seguimientos()->orderBy('fecha')->pluck('indicador'),
        };

        return $registros->map(fn (string $texto): string => Str::limit(Str::squish($texto), 140))->values()->all();
    }
}
