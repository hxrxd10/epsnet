<?php

namespace App\Http\Controllers\Estudiante;

use App\Actions\Estudiante\SincronizarEstudiante;
use App\Enums\Eje;
use App\Enums\EstadoExpediente;
use App\Enums\TipoUnidadAcademica;
use App\Http\Controllers\Controller;
use App\Http\Requests\Estudiante\StoreExpedienteRequest;
use App\Models\Estudiante;
use App\Models\Expediente;
use App\Models\UnidadAcademica;
use App\Services\RegistroAcademico\DetalleAcademico;
use App\Services\RegistroAcademico\RegistroAcademicoClient;
use App\Services\RegistroAcademico\RegistroAcademicoNoDisponible;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class ExpedienteController extends Controller
{
    /**
     * Horas tras las cuales se vuelven a consultar las carreras al registro académico.
     */
    private const int HORAS_VIGENCIA_CARRERAS = 24;

    /**
     * Paso 2: muestra las carreras del estudiante para elegir en cuál realiza su EPS.
     */
    public function index(Request $request, RegistroAcademicoClient $registro, SincronizarEstudiante $sincronizar): Response
    {
        $estudiante = $this->estudiante($request);

        $this->actualizarCarreras($estudiante, $registro, $sincronizar);

        $expedientes = $estudiante->expedientes()
            ->withCount(array_map(fn (Eje $eje): string => $eje->relacion(), Eje::cases()))
            ->get()
            ->keyBy(fn (Expediente $expediente): string => "{$expediente->codigo_unidad}-{$expediente->codigo_extension}-{$expediente->codigo_carrera}");

        return Inertia::render('estudiante/carreras', [
            'carreras' => $estudiante->carrerasAcademicas()->map(function (DetalleAcademico $carrera) use ($expedientes): array {
                $expediente = $expedientes->get($carrera->clave());

                return [
                    'clave' => $carrera->clave(),
                    'nombre_carrera' => $carrera->nombreCarrera,
                    'nombre_unidad' => $carrera->nombreUnidad,
                    'nombre_extension' => $carrera->nombreExtension,
                    'grado' => $carrera->grado,
                    'ciclo_activo' => $carrera->cicloActivo,
                    'graduado' => $carrera->estaGraduado(),
                    'fecha_graduado' => $carrera->fechaGraduado,
                    'expediente' => $expediente === null ? null : [
                        'id' => $expediente->id,
                        'eje_actual' => $expediente->eje_actual,
                        'estado' => $expediente->estado_expediente->value,
                        'estado_etiqueta' => $expediente->estado_expediente->etiqueta(),
                        'registros' => collect(Eje::cases())->sum(
                            fn (Eje $eje): int => (int) $expediente->getAttribute(str($eje->relacion())->snake().'_count'),
                        ),
                    ],
                ];
            })->values(),
        ]);
    }

    /**
     * Crea (o retoma) el expediente de la carrera elegida; la unidad académica se obtiene de la carrera.
     */
    public function store(StoreExpedienteRequest $request): RedirectResponse
    {
        $estudiante = $this->estudiante($request);

        $carrera = $estudiante->carrerasAcademicas()
            ->first(fn (DetalleAcademico $detalle): bool => $detalle->clave() === $request->validated('carrera'));

        if ($carrera === null) {
            throw ValidationException::withMessages(['carrera' => 'La carrera seleccionada no es válida.']);
        }

        $unidad = UnidadAcademica::withTrashed()->firstOrCreate(
            ['nombre' => $carrera->nombreUnidad],
            ['tipo' => TipoUnidadAcademica::desdeNombre($carrera->nombreUnidad), 'activa' => true],
        );

        $expediente = Expediente::firstOrCreate(
            [
                'estudiante_id' => $estudiante->id,
                'codigo_unidad' => $carrera->unidad,
                'codigo_extension' => $carrera->extension,
                'codigo_carrera' => $carrera->carrera,
            ],
            [
                'unidad_academica_id' => $unidad->id,
                'nombre_unidad' => $carrera->nombreUnidad,
                'nombre_extension' => $carrera->nombreExtension,
                'nombre_carrera' => $carrera->nombreCarrera,
                'nivel_academico' => $carrera->grado,
            ],
        );

        return $this->retomar($expediente);
    }

    /**
     * Retoma el expediente donde el estudiante lo dejó.
     */
    public function show(Expediente $expediente): RedirectResponse
    {
        return $this->retomar($expediente);
    }

    private function retomar(Expediente $expediente): RedirectResponse
    {
        if ($expediente->estado_expediente !== EstadoExpediente::Activo) {
            return to_route('estudiante.expedientes.cierre.index', $expediente);
        }

        return to_route(Eje::from($expediente->eje_actual)->ruta(), $expediente);
    }

    private function estudiante(Request $request): Estudiante
    {
        $estudiante = $request->user()->estudiante;

        abort_if($estudiante === null, 403, 'Tu usuario no tiene un perfil de estudiante.');

        return $estudiante;
    }

    /**
     * Refresca las carreras desde el registro académico cuando la última consulta ya es antigua.
     * Si el servicio no responde se conserva la información guardada.
     */
    private function actualizarCarreras(Estudiante $estudiante, RegistroAcademicoClient $registro, SincronizarEstudiante $sincronizar): void
    {
        $vigente = $estudiante->ultima_consulta_at?->gt(now()->subHours(self::HORAS_VIGENCIA_CARRERAS)) ?? false;

        if ($vigente) {
            return;
        }

        try {
            $consulta = $registro->consultar($estudiante->carnet);
        } catch (RegistroAcademicoNoDisponible) {
            return;
        }

        if ($consulta !== null) {
            $sincronizar->handle($consulta);
            $estudiante->refresh();
        }
    }
}
