<?php

namespace App\Http\Controllers\Estudiante;

use App\Concerns\ArmaPasoDelExpediente;
use App\Enums\Eje;
use App\Enums\TipoActividadTransferencia;
use App\Enums\TipoCatalogo;
use App\Http\Controllers\Controller;
use App\Http\Requests\Estudiante\TransferenciaConocimientoRequest;
use App\Models\Catalogo;
use App\Models\Expediente;
use App\Models\TransferenciaConocimiento;
use Illuminate\Http\RedirectResponse;
use Inertia\Response;

class TransferenciaConocimientoController extends Controller
{
    use ArmaPasoDelExpediente;

    public function index(Expediente $expediente): Response
    {
        return $this->paso($expediente, Eje::Transferencia, [
            'registros' => $expediente->transferencias()
                ->orderByDesc('fecha')->orderByDesc('id')
                ->get()
                ->map(fn (TransferenciaConocimiento $registro): array => [
                    'id' => $registro->id,
                    'catalogo_id' => $registro->catalogo_id,
                    'tipo_actividad' => $registro->tipo_actividad->value,
                    'tipo_actividad_etiqueta' => $registro->tipo_actividad->etiqueta(),
                    'actividad' => $registro->actividad,
                    'comunidad' => $registro->comunidad,
                    'numero_participantes' => $registro->numero_participantes,
                    'fecha' => $registro->fecha->toDateString(),
                ])
                ->values(),
            'tipos' => TipoActividadTransferencia::opciones(),
            'catalogo' => $this->opcionesDelCatalogo($expediente),
        ]);
    }

    public function store(TransferenciaConocimientoRequest $request, Expediente $expediente): RedirectResponse
    {
        $expediente->transferencias()->create($request->validated());

        return $this->guardado($expediente, Eje::Transferencia, 'Actividad registrada.');
    }

    public function update(TransferenciaConocimientoRequest $request, Expediente $expediente, int $registro): RedirectResponse
    {
        $expediente->transferencias()->findOrFail($registro)->update($request->validated());

        return $this->guardado($expediente, Eje::Transferencia, 'Actividad actualizada.');
    }

    public function destroy(Expediente $expediente, int $registro): RedirectResponse
    {
        $expediente->transferencias()->findOrFail($registro)->delete();

        return $this->guardado($expediente, Eje::Transferencia, 'Actividad eliminada.');
    }

    /**
     * Acciones activas del catálogo, más las que ya usa este expediente.
     *
     * @return list<array{value: string, label: string}>
     */
    private function opcionesDelCatalogo(Expediente $expediente): array
    {
        $usados = $expediente->transferencias()->whereNotNull('catalogo_id')->pluck('catalogo_id');

        return Catalogo::query()
            ->delCatalogo(TipoCatalogo::Acciones)
            ->where(fn ($consulta) => $consulta->where('activo', true)->orWhereIn('id', $usados))
            ->orderBy('nombre')
            ->get()
            ->map(fn (Catalogo $elemento): array => ['value' => (string) $elemento->id, 'label' => $elemento->nombre])
            ->all();
    }
}
