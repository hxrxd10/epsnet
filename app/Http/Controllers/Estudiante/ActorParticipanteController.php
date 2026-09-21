<?php

namespace App\Http\Controllers\Estudiante;

use App\Concerns\ArmaPasoDelExpediente;
use App\Enums\Eje;
use App\Http\Controllers\Controller;
use App\Http\Requests\Estudiante\ActorParticipanteRequest;
use App\Models\ActorParticipante;
use App\Models\Expediente;
use App\Models\InstitucionReceptora;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Inertia\Response;

class ActorParticipanteController extends Controller
{
    use ArmaPasoDelExpediente;

    public function index(Expediente $expediente): Response
    {
        return $this->paso($expediente, Eje::Actores, [
            'registros' => $expediente->actores()
                ->with('institucionReceptora')
                ->orderByDesc('id')
                ->get()
                ->map(fn (ActorParticipante $registro): array => [
                    'id' => $registro->id,
                    'institucion_receptora_id' => $registro->institucion_receptora_id,
                    'institucion' => $registro->institucionReceptora->nombre,
                    'contraparte' => $registro->contraparte,
                    'comunidad_beneficiada' => $registro->comunidad_beneficiada,
                ])
                ->values(),
            'instituciones' => InstitucionReceptora::orderBy('nombre')
                ->get(['id', 'nombre', 'tipo'])
                ->map(fn (InstitucionReceptora $institucion): array => [
                    'value' => (string) $institucion->id,
                    'label' => $institucion->tipo ? "{$institucion->nombre} ({$institucion->tipo})" : $institucion->nombre,
                ])
                ->values(),
        ]);
    }

    public function store(ActorParticipanteRequest $request, Expediente $expediente): RedirectResponse
    {
        DB::transaction(function () use ($request, $expediente): void {
            $expediente->actores()->create([
                'institucion_receptora_id' => $this->institucion($request)->id,
                'contraparte' => $request->validated('contraparte'),
                'comunidad_beneficiada' => $request->validated('comunidad_beneficiada'),
            ]);
        });

        return $this->guardado($expediente, Eje::Actores, 'Actores registrados.');
    }

    public function update(ActorParticipanteRequest $request, Expediente $expediente, int $registro): RedirectResponse
    {
        $actor = $expediente->actores()->findOrFail($registro);

        DB::transaction(function () use ($request, $actor): void {
            $actor->update([
                'institucion_receptora_id' => $this->institucion($request)->id,
                'contraparte' => $request->validated('contraparte'),
                'comunidad_beneficiada' => $request->validated('comunidad_beneficiada'),
            ]);
        });

        return $this->guardado($expediente, Eje::Actores, 'Actores actualizados.');
    }

    public function destroy(Expediente $expediente, int $registro): RedirectResponse
    {
        $expediente->actores()->findOrFail($registro)->delete();

        return $this->guardado($expediente, Eje::Actores, 'Actores eliminados.');
    }

    /**
     * Reutiliza la institución elegida o, si se registra una nueva, la que ya exista con el mismo nombre.
     */
    private function institucion(ActorParticipanteRequest $request): InstitucionReceptora
    {
        if ($request->filled('institucion_receptora_id')) {
            return InstitucionReceptora::findOrFail($request->integer('institucion_receptora_id'));
        }

        $nombre = trim((string) $request->validated('institucion_nombre'));

        $existente = InstitucionReceptora::withTrashed()
            ->whereRaw('lower(nombre) = ?', [mb_strtolower($nombre)])
            ->first();

        if ($existente !== null) {
            $existente->trashed() && $existente->restore();

            return $existente;
        }

        return InstitucionReceptora::create([
            'nombre' => $nombre,
            'tipo' => $request->validated('institucion_tipo'),
            'nombre_contacto' => $request->validated('institucion_nombre_contacto'),
            'correo_contacto' => $request->validated('institucion_correo_contacto'),
            'telefono_contacto' => $request->validated('institucion_telefono_contacto'),
        ]);
    }
}
