<?php

namespace App\Http\Controllers\Estudiante;

use App\Concerns\ArmaPasoDelExpediente;
use App\Enums\Eje;
use App\Enums\TipoBienServicio;
use App\Enums\TipoCatalogo;
use App\Http\Controllers\Controller;
use App\Http\Requests\Estudiante\BienServicioRequest;
use App\Models\BienServicio;
use App\Models\Catalogo;
use App\Models\Expediente;
use Illuminate\Http\RedirectResponse;
use Inertia\Response;

class BienServicioController extends Controller
{
    use ArmaPasoDelExpediente;

    public function index(Expediente $expediente): Response
    {
        return $this->paso($expediente, Eje::BienesServicios, [
            'registros' => $expediente->bienesServicios()
                ->orderByDesc('fecha')->orderByDesc('id')
                ->get()
                ->map(fn (BienServicio $registro): array => [
                    'id' => $registro->id,
                    'catalogo_id' => $registro->catalogo_id,
                    'tipo' => $registro->tipo->value,
                    'tipo_etiqueta' => $registro->tipo->etiqueta(),
                    'descripcion' => $registro->descripcion,
                    'cantidad' => $registro->cantidad,
                    'beneficiarios' => $registro->beneficiarios,
                    'cantidad_beneficiarios' => $registro->cantidad_beneficiarios,
                    'fecha' => $registro->fecha->toDateString(),
                ])
                ->values(),
            'tipos' => TipoBienServicio::opciones(),
            'catalogo' => $this->opcionesDelCatalogo($expediente),
            'lineas' => Catalogo::query()
                ->delCatalogo(TipoCatalogo::LineasIntervencion)
                ->whereIn('id', Catalogo::query()->delCatalogo(TipoCatalogo::BienesServicios)->whereNotNull('linea_intervencion_id')->select('linea_intervencion_id'))
                ->orderBy('nombre')
                ->get(['id', 'nombre'])
                ->map(fn (Catalogo $linea): array => ['value' => (string) $linea->id, 'label' => $linea->nombre])
                ->all(),
        ]);
    }

    public function store(BienServicioRequest $request, Expediente $expediente): RedirectResponse
    {
        $expediente->bienesServicios()->create($request->datos());

        return $this->guardado($expediente, Eje::BienesServicios, 'Bien o servicio registrado.');
    }

    public function update(BienServicioRequest $request, Expediente $expediente, int $registro): RedirectResponse
    {
        $expediente->bienesServicios()->findOrFail($registro)->update($request->datos());

        return $this->guardado($expediente, Eje::BienesServicios, 'Bien o servicio actualizado.');
    }

    public function destroy(Expediente $expediente, int $registro): RedirectResponse
    {
        $expediente->bienesServicios()->findOrFail($registro)->delete();

        return $this->guardado($expediente, Eje::BienesServicios, 'Bien o servicio eliminado.');
    }

    /**
     * Elementos activos del catálogo de bienes y servicios, más los que ya usa este expediente.
     *
     * @return list<array{value: string, label: string, linea: string|null}>
     */
    private function opcionesDelCatalogo(Expediente $expediente): array
    {
        $usados = $expediente->bienesServicios()->whereNotNull('catalogo_id')->pluck('catalogo_id');

        return Catalogo::query()
            ->delCatalogo(TipoCatalogo::BienesServicios)
            ->where(fn ($consulta) => $consulta->where('activo', true)->orWhereIn('id', $usados))
            ->orderBy('nombre')
            ->get()
            ->map(fn (Catalogo $elemento): array => [
                'value' => (string) $elemento->id,
                'label' => $elemento->nombre,
                'linea' => $elemento->linea_intervencion_id === null ? null : (string) $elemento->linea_intervencion_id,
            ])
            ->values()
            ->all();
    }
}
