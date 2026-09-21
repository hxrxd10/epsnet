<?php

namespace App\Http\Controllers\Estudiante;

use App\Enums\Eje;
use App\Enums\EstadoExpediente;
use App\Enums\TipoCambioBitacora;
use App\Http\Controllers\Controller;
use App\Models\Bitacora;
use App\Models\Expediente;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class SolicitudAprobacionController extends Controller
{
    /**
     * Un estudiante sin orden de impresión (p. ej. porque no tiene un informe escrito) envía su EPS a la bandeja
     * de la unidad académica, que lo revisa y aprueba en orden de llegada.
     */
    public function store(Expediente $expediente): RedirectResponse
    {
        if ($expediente->estado_expediente !== EstadoExpediente::Activo) {
            throw ValidationException::withMessages(['solicitud' => 'Tu EPS ya está completo o aprobado: no necesita una solicitud.']);
        }

        if ($expediente->ordenImpresion()->exists()) {
            throw ValidationException::withMessages(['solicitud' => 'Ya tienes tu orden de impresión: completa tu EPS con ella.']);
        }

        if ($expediente->aprobacion_solicitada_at !== null) {
            throw ValidationException::withMessages(['solicitud' => 'Ya enviaste tu EPS a aprobación: tu unidad académica lo revisará en orden de llegada.']);
        }

        if (collect(Eje::cases())->sum(fn (Eje $eje): int => $expediente->{$eje->relacion()}()->count()) === 0) {
            throw ValidationException::withMessages(['registros' => 'Registra al menos un elemento en alguno de los ejes antes de enviar tu EPS a aprobación.']);
        }

        $expediente->update(['aprobacion_solicitada_at' => now()]);

        Bitacora::registrar(
            $expediente->moduloBitacora(),
            TipoCambioBitacora::SolicitudAprobacion,
            "Envió a aprobación de su unidad académica el {$expediente->resumenBitacora()} (sin orden de impresión)",
            $expediente,
        );

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Tu EPS quedó en la bandeja de tu unidad académica. Lo revisarán en orden de llegada.']);

        return to_route('estudiante.expedientes.cierre.index', $expediente);
    }
}
