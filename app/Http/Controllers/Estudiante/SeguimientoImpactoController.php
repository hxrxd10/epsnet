<?php

namespace App\Http\Controllers\Estudiante;

use App\Concerns\ArmaPasoDelExpediente;
use App\Enums\Eje;
use App\Enums\NivelCumplimiento;
use App\Enums\TipoRegistroSeguimiento;
use App\Http\Controllers\Controller;
use App\Http\Requests\Estudiante\SeguimientoImpactoRequest;
use App\Models\Expediente;
use App\Models\SeguimientoImpacto;
use Illuminate\Http\RedirectResponse;
use Inertia\Response;

class SeguimientoImpactoController extends Controller
{
    use ArmaPasoDelExpediente;

    public function index(Expediente $expediente): Response
    {
        return $this->paso($expediente, Eje::Seguimiento, [
            'registros' => $expediente->seguimientos()
                ->orderByDesc('fecha')->orderByDesc('id')
                ->get()
                ->map(fn (SeguimientoImpacto $registro): array => [
                    'id' => $registro->id,
                    'tipo_registro' => $registro->tipo_registro->value,
                    'tipo_registro_etiqueta' => $registro->tipo_registro->etiqueta(),
                    'indicador' => $registro->indicador,
                    'avance' => $registro->avance,
                    'porcentaje_avance' => $registro->porcentaje_avance,
                    'cumplimiento' => $registro->cumplimiento?->value,
                    'cumplimiento_etiqueta' => $registro->cumplimiento?->etiqueta(),
                    'observaciones' => $registro->observaciones,
                    'evaluacion_impacto' => $registro->evaluacion_impacto,
                    'fecha' => $registro->fecha->toDateString(),
                ])
                ->values(),
            'tiposRegistro' => TipoRegistroSeguimiento::opciones(),
            'nivelesCumplimiento' => NivelCumplimiento::opciones(),
        ]);
    }

    public function store(SeguimientoImpactoRequest $request, Expediente $expediente): RedirectResponse
    {
        $expediente->seguimientos()->create($request->validated());

        return $this->guardado($expediente, Eje::Seguimiento, 'Seguimiento registrado.');
    }

    public function update(SeguimientoImpactoRequest $request, Expediente $expediente, int $registro): RedirectResponse
    {
        $expediente->seguimientos()->findOrFail($registro)->update($request->validated());

        return $this->guardado($expediente, Eje::Seguimiento, 'Seguimiento actualizado.');
    }

    public function destroy(Expediente $expediente, int $registro): RedirectResponse
    {
        $expediente->seguimientos()->findOrFail($registro)->delete();

        return $this->guardado($expediente, Eje::Seguimiento, 'Seguimiento eliminado.');
    }
}
