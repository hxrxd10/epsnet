<?php

namespace App\Http\Controllers\Estudiante;

use App\Concerns\ArmaPasoDelExpediente;
use App\Enums\Eje;
use App\Http\Controllers\Controller;
use App\Http\Requests\Estudiante\ActorParticipanteRequest;
use App\Models\ActorParticipante;
use App\Models\Alianza;
use App\Models\Expediente;
use App\Models\InstitucionAliada;
use Illuminate\Http\RedirectResponse;
use Inertia\Response;

class ActorParticipanteController extends Controller
{
    use ArmaPasoDelExpediente;

    public function index(Expediente $expediente): Response
    {
        return $this->paso($expediente, Eje::Actores, [
            'registros' => $expediente->actores()
                ->orderByDesc('id')
                ->get()
                ->map(fn (ActorParticipante $registro): array => [
                    'id' => $registro->id,
                    'institucion_receptora' => $registro->institucion_receptora,
                    'contraparte' => $registro->contraparte,
                    'comunidad_beneficiada' => $registro->comunidad_beneficiada,
                ])
                ->values(),
            'alianzas' => $expediente->alianzas()
                ->with('institucionAliada')
                ->orderByDesc('id')
                ->get()
                ->map(fn (Alianza $alianza): array => [
                    'id' => $alianza->id,
                    'institucion_aliada_id' => $alianza->institucion_aliada_id,
                    'institucion' => $alianza->institucionAliada->nombre,
                    'tipo' => $alianza->institucionAliada->tipo,
                    'aporte' => $alianza->aporte,
                ])
                ->values(),
            'instituciones' => InstitucionAliada::orderBy('nombre')
                ->get(['id', 'nombre', 'tipo'])
                ->map(fn (InstitucionAliada $institucion): array => [
                    'value' => (string) $institucion->id,
                    'label' => $institucion->tipo ? "{$institucion->nombre} ({$institucion->tipo})" : $institucion->nombre,
                ])
                ->values(),
        ]);
    }

    public function store(ActorParticipanteRequest $request, Expediente $expediente): RedirectResponse
    {
        $expediente->actores()->create($request->validated());

        return $this->guardado($expediente, Eje::Actores, 'Actores registrados.');
    }

    public function update(ActorParticipanteRequest $request, Expediente $expediente, int $registro): RedirectResponse
    {
        $expediente->actores()->findOrFail($registro)->update($request->validated());

        return $this->guardado($expediente, Eje::Actores, 'Actores actualizados.');
    }

    public function destroy(Expediente $expediente, int $registro): RedirectResponse
    {
        $expediente->actores()->findOrFail($registro)->delete();

        return $this->guardado($expediente, Eje::Actores, 'Actores eliminados.');
    }
}
