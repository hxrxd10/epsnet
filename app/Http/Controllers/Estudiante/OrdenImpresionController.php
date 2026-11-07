<?php

namespace App\Http\Controllers\Estudiante;

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
     * Guarda o reemplaza la orden de impresión (contenido en base64 en la base de datos).
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
        });

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Orden de impresión guardada.']);

        return to_route('estudiante.expedientes.cierre.index', $expediente);
    }

    /**
     * Elimina la orden de impresión (es opcional).
     */
    public function destroy(Expediente $expediente): RedirectResponse
    {
        DB::transaction(fn () => $expediente->ordenImpresion()->get()->each->delete());

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Orden de impresión eliminada.']);

        return to_route('estudiante.expedientes.cierre.index', $expediente);
    }
}
