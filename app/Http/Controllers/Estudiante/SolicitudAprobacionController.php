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
     * Todo EPS necesita la aprobación de supervisión: el estudiante lo envía a la bandeja de su unidad
     * académica, que lo revisa en orden de llegada. La orden de impresión es opcional.
     */
    public function store(Expediente $expediente): RedirectResponse
    {
        if ($expediente->estado_expediente === EstadoExpediente::Verificado) {
            throw ValidationException::withMessages(['solicitud' => 'Tu EPS ya está aprobado: no necesita otra solicitud.']);
        }

        if ($expediente->estado_expediente === EstadoExpediente::Completo) {
            throw ValidationException::withMessages(['solicitud' => 'Ya enviaste tu EPS a aprobación: tu unidad académica lo revisará en orden de llegada.']);
        }

        if (collect(Eje::cases())->sum(fn (Eje $eje): int => $expediente->{$eje->relacion()}()->count()) === 0) {
            throw ValidationException::withMessages(['registros' => 'Registra al menos un elemento en alguno de los ejes antes de enviar tu EPS a aprobación.']);
        }

        $expediente->enviarAAprobacion();

        Bitacora::registrar(
            $expediente->moduloBitacora(),
            TipoCambioBitacora::SolicitudAprobacion,
            "Envió a aprobación de su unidad académica el {$expediente->resumenBitacora()}".($expediente->ordenImpresion()->exists() ? '' : ' (sin orden de impresión)'),
            $expediente,
        );

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Tu EPS quedó en la bandeja de tu unidad académica. Lo revisarán en orden de llegada.']);

        return to_route('estudiante.expedientes.cierre.index', $expediente);
    }
}
