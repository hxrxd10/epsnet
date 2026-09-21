<?php

namespace App\Http\Controllers\Estudiante;

use App\Enums\EstadoExpediente;
use App\Http\Controllers\Controller;
use App\Http\Requests\Estudiante\OrdenImpresionRequest;
use App\Models\Adjunto;
use App\Models\Expediente;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\StreamedResponse;

class OrdenImpresionController extends Controller
{
    /**
     * Descarga la orden de impresión guardada en la base de datos (solo para su dueño).
     */
    public function show(Expediente $expediente): StreamedResponse
    {
        return $expediente->ordenImpresion()->with('contenido')->firstOrFail()->descarga();
    }

    /**
     * Guarda o reemplaza la orden de impresión (contenido en base64 en la base de datos). Si el
     * expediente ya estaba verificado por la unidad, queda completo a la espera de una nueva verificación.
     */
    public function store(OrdenImpresionRequest $request, Expediente $expediente): RedirectResponse
    {
        $archivo = $request->file('orden_impresion');
        $bytes = $archivo->get();

        DB::transaction(function () use ($request, $expediente, $archivo, $bytes): void {
            $expediente->ordenImpresion()->get()->each->delete();

            $adjunto = $expediente->adjuntos()->create([
                'categoria' => Adjunto::ORDEN_IMPRESION,
                'subido_por' => $request->user()->id,
                'nombre_original' => $archivo->getClientOriginalName(),
                'mime_type' => $archivo->getMimeType(),
                'tamano_bytes' => strlen($bytes),
                'sha256' => hash('sha256', $bytes),
                'fecha_subida' => now(),
            ]);

            $adjunto->contenido()->create(['contenido' => base64_encode($bytes)]);

            if ($expediente->estado_expediente === EstadoExpediente::Verificado) {
                $expediente->update([
                    'estado_expediente' => EstadoExpediente::Completo,
                    'verificado_at' => null,
                    'verificado_por' => null,
                ]);
            }
        });

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Orden de impresión guardada.']);

        return to_route('estudiante.expedientes.cierre.index', $expediente);
    }

    /**
     * Elimina la orden de impresión: sin ella el expediente vuelve a estar en progreso.
     */
    public function destroy(Expediente $expediente): RedirectResponse
    {
        DB::transaction(function () use ($expediente): void {
            $expediente->ordenImpresion()->get()->each->delete();

            $expediente->update([
                'estado_expediente' => EstadoExpediente::Activo,
                'completado_at' => null,
                'verificado_at' => null,
                'verificado_por' => null,
            ]);
        });

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Orden de impresión eliminada. Tu EPS vuelve a estar en progreso.']);

        return to_route('estudiante.expedientes.cierre.index', $expediente);
    }
}
