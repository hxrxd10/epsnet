<?php

namespace App\Http\Controllers\Estudiante;

use App\Concerns\ArmaPasoDelExpediente;
use App\Enums\Eje;
use App\Http\Controllers\Controller;
use App\Http\Requests\Estudiante\UbicacionTerritorialRequest;
use App\Models\Departamento;
use App\Models\Expediente;
use App\Models\Municipio;
use App\Models\UbicacionTerritorial;
use Illuminate\Http\RedirectResponse;
use Inertia\Response;

class UbicacionTerritorialController extends Controller
{
    use ArmaPasoDelExpediente;

    public function index(Expediente $expediente): Response
    {
        return $this->paso($expediente, Eje::Territorio, [
            'registros' => $expediente->ubicaciones()
                ->with(['departamento', 'municipio'])->orderByDesc('id')
                ->get()
                ->map(fn (UbicacionTerritorial $registro): array => [
                    'id' => $registro->id,
                    'departamento_id' => $registro->departamento_id,
                    'departamento' => $registro->departamento->nombre,
                    'municipio_id' => $registro->municipio_id,
                    'municipio' => $registro->municipio?->nombre,
                    'comunidad' => $registro->comunidad,
                    'latitud' => $registro->latitud,
                    'longitud' => $registro->longitud,
                    'referencia' => $registro->referencia,
                ])
                ->values(),
            'googleMaps' => $this->googleMaps(),
            'departamentos' => Departamento::orderBy('nombre')->get(['id', 'nombre', 'latitud', 'longitud'])->map(fn (Departamento $departamento): array => [
                'value' => (string) $departamento->id,
                'label' => $departamento->nombre,
            ])->values(),
            'municipios' => Municipio::orderBy('nombre')->get(['id', 'departamento_id', 'nombre', 'latitud', 'longitud'])->map(fn (Municipio $municipio): array => [
                'value' => (string) $municipio->id,
                'label' => $municipio->nombre,
                'departamento_id' => (string) $municipio->departamento_id,
                'latitud' => $municipio->latitud,
                'longitud' => $municipio->longitud,
            ])->values(),
        ]);
    }

    public function store(UbicacionTerritorialRequest $request, Expediente $expediente): RedirectResponse
    {
        $expediente->ubicaciones()->create($request->validated());

        return $this->guardado($expediente, Eje::Territorio, 'Ubicación registrada.');
    }

    public function update(UbicacionTerritorialRequest $request, Expediente $expediente, int $registro): RedirectResponse
    {
        $expediente->ubicaciones()->findOrFail($registro)->update($request->validated());

        return $this->guardado($expediente, Eje::Territorio, 'Ubicación actualizada.');
    }

    public function destroy(Expediente $expediente, int $registro): RedirectResponse
    {
        $expediente->ubicaciones()->findOrFail($registro)->delete();

        return $this->guardado($expediente, Eje::Territorio, 'Ubicación eliminada.');
    }
}
