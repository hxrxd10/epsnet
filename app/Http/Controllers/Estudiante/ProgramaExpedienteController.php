<?php

namespace App\Http\Controllers\Estudiante;

use App\Enums\ProgramaEps;
use App\Http\Controllers\Controller;
use App\Models\Expediente;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProgramaExpedienteController extends Controller
{
    /**
     * Indica si el EPS se realiza dentro del EPSUM (Programa de EPS Multiprofesional) o es un EPS normal.
     */
    public function update(Request $request, Expediente $expediente): RedirectResponse
    {
        $datos = $request->validate(['epsum' => ['required', 'boolean']]);

        $expediente->update(['programa' => $datos['epsum'] ? ProgramaEps::Epsum : ProgramaEps::EpsFacultativo]);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => $datos['epsum'] ? 'Tu EPS quedó registrado como parte del EPSUM.' : 'Tu EPS quedó registrado como EPS normal.',
        ]);

        return back();
    }
}
