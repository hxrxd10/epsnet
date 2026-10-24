<?php

namespace App\Http\Controllers\Estudiante;

use App\Http\Controllers\Controller;
use App\Http\Requests\Estudiante\FotoExpedienteRequest;
use App\Models\Adjunto;
use App\Models\Expediente;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class FotoExpedienteController extends Controller
{
    /**
     * Guarda o reemplaza la mejor foto del EPS: la imagen se guarda en base64 en la base de datos.
     */
    public function store(FotoExpedienteRequest $request, Expediente $expediente): RedirectResponse
    {
        $archivo = $request->file('foto');
        $bytes = $archivo->getContent();

        DB::transaction(function () use ($request, $expediente, $archivo, $bytes): void {
            $expediente->foto()->get()->each->delete();

            $adjunto = $expediente->adjuntos()->create([
                'categoria' => Adjunto::FOTO_EPS,
                'subido_por' => $request->user()->id,
                'nombre_original' => $archivo->getClientOriginalName(),
                'mime_type' => $archivo->getMimeType(),
                'tamano_bytes' => strlen($bytes),
                'sha256' => hash('sha256', $bytes),
                'fecha_subida' => now(),
            ]);

            $adjunto->contenido()->create(['contenido' => base64_encode($bytes)]);
        });

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Foto de tu EPS guardada.']);

        return to_route('estudiante.expedientes.cierre.index', $expediente);
    }

    public function destroy(Expediente $expediente): RedirectResponse
    {
        DB::transaction(fn () => $expediente->foto()->get()->each->delete());

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Foto de tu EPS eliminada.']);

        return to_route('estudiante.expedientes.cierre.index', $expediente);
    }
}
