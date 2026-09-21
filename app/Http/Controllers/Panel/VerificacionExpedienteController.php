<?php

namespace App\Http\Controllers\Panel;

use App\Enums\EstadoExpediente;
use App\Http\Controllers\Controller;
use App\Models\Expediente;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class VerificacionExpedienteController extends Controller
{
    /**
     * Aprueba el EPS (doble verificación): queda visible en el repositorio.
     */
    public function store(Request $request, Expediente $expediente): RedirectResponse
    {
        Gate::authorize('verificar', $expediente);

        if ($expediente->estado_expediente !== EstadoExpediente::Verificado) {
            $expediente->verificarPor($request->user());
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => 'EPS aprobado: ya aparece en el repositorio.']);

        return to_route('panel.estudiantes.show', $expediente);
    }

    /**
     * Retira la aprobación: el EPS sigue completo pero deja de mostrarse en el repositorio.
     */
    public function destroy(Expediente $expediente): RedirectResponse
    {
        Gate::authorize('verificar', $expediente);

        if ($expediente->estado_expediente === EstadoExpediente::Verificado) {
            $expediente->quitarVerificacion();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Se retiró la aprobación del EPS.']);

        return to_route('panel.estudiantes.show', $expediente);
    }
}
