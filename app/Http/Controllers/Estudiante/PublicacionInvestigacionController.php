<?php

namespace App\Http\Controllers\Estudiante;

use App\Concerns\ArmaPasoDelExpediente;
use App\Enums\Eje;
use App\Enums\TipoPublicacion;
use App\Http\Controllers\Controller;
use App\Http\Requests\Estudiante\PublicacionInvestigacionRequest;
use App\Models\Expediente;
use App\Models\PublicacionInvestigacion;
use Illuminate\Http\RedirectResponse;
use Inertia\Response;

class PublicacionInvestigacionController extends Controller
{
    use ArmaPasoDelExpediente;

    public function index(Expediente $expediente): Response
    {
        return $this->paso($expediente, Eje::Publicaciones, [
            'registros' => $expediente->publicaciones()
                ->orderByDesc('fecha_publicacion')->orderByDesc('id')
                ->get()
                ->map(fn (PublicacionInvestigacion $registro): array => [
                    'id' => $registro->id,
                    'titulo' => $registro->titulo,
                    'tipo' => $registro->tipo->value,
                    'tipo_etiqueta' => $registro->tipo->etiqueta(),
                    'autores' => $registro->autores,
                    'medio_publicacion' => $registro->medio_publicacion,
                    'resumen' => $registro->resumen,
                    'enlace' => $registro->enlace,
                    'fecha_publicacion' => $registro->fecha_publicacion?->toDateString(),
                ])
                ->values(),
            'tipos' => TipoPublicacion::opciones(),
        ]);
    }

    public function store(PublicacionInvestigacionRequest $request, Expediente $expediente): RedirectResponse
    {
        $expediente->publicaciones()->create($request->validated());

        return $this->guardado($expediente, Eje::Publicaciones, 'Publicación registrada.');
    }

    public function update(PublicacionInvestigacionRequest $request, Expediente $expediente, int $registro): RedirectResponse
    {
        $expediente->publicaciones()->findOrFail($registro)->update($request->validated());

        return $this->guardado($expediente, Eje::Publicaciones, 'Publicación actualizada.');
    }

    public function destroy(Expediente $expediente, int $registro): RedirectResponse
    {
        $expediente->publicaciones()->findOrFail($registro)->delete();

        return $this->guardado($expediente, Eje::Publicaciones, 'Publicación eliminada.');
    }
}
