<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CarreraRequest;
use App\Models\Carrera;
use App\Models\UnidadAcademica;
use App\Services\RegistroAcademico\DatosAcademicos;
use App\Services\RegistroAcademico\DetalleAcademico;
use App\Services\RegistroAcademico\RegistroAcademicoClient;
use App\Services\RegistroAcademico\RegistroAcademicoNoDisponible;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class CarreraController extends Controller
{
    /**
     * Portal de carreras: las ya registradas y, si se consultó un carné, sus carreras en el registro
     * académico indicando cuáles son nuevas y cuáles ya están agregadas.
     */
    public function index(Request $request, RegistroAcademicoClient $registro): Response
    {
        $filtros = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'unidad' => ['nullable', 'integer'],
            'carnet' => ['nullable', 'string', 'regex:/^\d{4,15}$/'],
        ], ['carnet.regex' => 'El carné solo lleva números.']);

        $carreras = Carrera::query()
            ->with('unidadAcademica:id,nombre')
            ->withCount('supervisores')
            ->when($filtros['q'] ?? null, fn (Builder $consulta, string $texto) => $consulta->where(
                fn (Builder $consulta) => $consulta
                    ->where('nombre', 'like', "%{$texto}%")
                    ->orWhere('nombre_extension', 'like', "%{$texto}%")
                    ->orWhere('codigo_carrera', $texto),
            ))
            ->when($filtros['unidad'] ?? null, fn (Builder $consulta, int|string $unidad) => $consulta->where('unidad_academica_id', $unidad))
            ->orderBy('nombre')
            ->orderBy('nombre_extension')
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('admin/carreras', [
            'carreras' => $carreras->getCollection()->map(fn (Carrera $carrera): array => [
                'id' => $carrera->id,
                'clave' => $carrera->clave(),
                'nombre' => $carrera->nombre,
                'extension' => $carrera->nombre_extension,
                'nivel' => $carrera->nivel_academico,
                'unidad' => $carrera->unidadAcademica->nombre,
                'supervisores' => $carrera->supervisores_count,
            ])->values(),
            'pagina' => [
                'actual' => $carreras->currentPage(),
                'ultima' => $carreras->lastPage(),
                'total' => $carreras->total(),
                'anterior' => $carreras->previousPageUrl(),
                'siguiente' => $carreras->nextPageUrl(),
            ],
            'filtros' => ['q' => $filtros['q'] ?? '', 'unidad' => (string) ($filtros['unidad'] ?? ''), 'carnet' => $filtros['carnet'] ?? ''],
            'unidades' => UnidadAcademica::query()
                ->whereHas('carreras')
                ->orderBy('nombre')
                ->get(['id', 'nombre'])
                ->map(fn (UnidadAcademica $unidad): array => ['value' => (string) $unidad->id, 'label' => $unidad->nombre])
                ->all(),
            'consulta' => isset($filtros['carnet']) ? $this->consultar($registro, $filtros['carnet']) : null,
        ]);
    }

    /**
     * Agrega las carreras elegidas de un carné. Se vuelven a pedir al registro académico para no
     * confiar en los datos que manda el navegador; las que ya existían se ignoran.
     */
    public function store(CarreraRequest $request, RegistroAcademicoClient $registro): RedirectResponse
    {
        $carnet = $request->validated('carnet');
        $datos = $this->datos($registro, $carnet);

        if ($datos === null) {
            throw ValidationException::withMessages(['carnet' => 'El registro académico no tiene información de ese carné.']);
        }

        $elegidas = collect($datos->carreras)
            ->filter(fn (DetalleAcademico $detalle): bool => in_array($detalle->clave(), $request->validated('claves'), true));

        if ($elegidas->isEmpty()) {
            throw ValidationException::withMessages(['claves' => 'Las carreras elegidas no son de ese carné.']);
        }

        $nuevas = DB::transaction(fn (): int => $elegidas
            ->map(fn (DetalleAcademico $detalle): Carrera => Carrera::registrar($detalle))
            ->filter(fn (Carrera $carrera): bool => $carrera->wasRecentlyCreated)
            ->count());

        Inertia::flash('toast', ['type' => 'success', 'message' => match ($nuevas) {
            0 => 'Esas carreras ya estaban agregadas.',
            1 => 'Carrera agregada.',
            default => "{$nuevas} carreras agregadas.",
        }]);

        return to_route('admin.carreras.index', ['carnet' => $carnet]);
    }

    /**
     * Quita una carrera del catálogo; sus EPS no se tocan. Si un supervisor la tiene asignada no se
     * elimina: sin ella podría quedarse sin carreras y pasaría a ver toda su unidad.
     */
    public function destroy(Carrera $carrera): RedirectResponse
    {
        if ($carrera->supervisores()->exists()) {
            Inertia::flash('toast', ['type' => 'error', 'message' => 'Esta carrera está asignada a supervisores. Quítasela en Usuarios antes de eliminarla.']);

            return to_route('admin.carreras.index');
        }

        $carrera->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Carrera eliminada.']);

        return to_route('admin.carreras.index');
    }

    /**
     * Resultado de la consulta de un carné para la página.
     *
     * @return array{carnet: string, estudiante: string|null, error: string|null, carreras: list<array<string, mixed>>, nuevas: int}
     */
    private function consultar(RegistroAcademicoClient $registro, string $carnet): array
    {
        try {
            $datos = $this->datos($registro, $carnet);
        } catch (ValidationException $error) {
            return ['carnet' => $carnet, 'estudiante' => null, 'error' => $error->getMessage(), 'carreras' => [], 'nuevas' => 0];
        }

        if ($datos === null) {
            return ['carnet' => $carnet, 'estudiante' => null, 'error' => 'El registro académico no tiene información de ese carné.', 'carreras' => [], 'nuevas' => 0];
        }

        $registradas = Carrera::query()
            ->where(function (Builder $consulta) use ($datos): void {
                foreach ($datos->carreras as $detalle) {
                    $consulta->orWhere(fn (Builder $deLaCarrera) => $deLaCarrera
                        ->where('codigo_unidad', $detalle->unidad)
                        ->where('codigo_extension', $detalle->extension)
                        ->where('codigo_carrera', $detalle->carrera));
                }
            })
            ->get()
            ->keyBy(fn (Carrera $carrera): string => $carrera->clave());

        $carreras = array_map(fn (DetalleAcademico $detalle): array => [
            'clave' => $detalle->clave(),
            'nombre' => $detalle->nombreCarrera,
            'extension' => $detalle->nombreExtension,
            'unidad' => $detalle->nombreUnidad,
            'nivel' => $detalle->grado,
            'agregada' => $registradas->has($detalle->clave()),
        ], $datos->carreras);

        return [
            'carnet' => $carnet,
            'estudiante' => trim(implode(' ', array_filter([$datos->nombre1, $datos->nombre2, $datos->nombre3, $datos->apellido1, $datos->apellido2]))),
            'error' => $carreras === [] ? 'El carné no tiene carreras en el registro académico.' : null,
            'carreras' => $carreras,
            'nuevas' => count(array_filter($carreras, fn (array $carrera): bool => ! $carrera['agregada'])),
        ];
    }

    /**
     * @throws ValidationException cuando el servicio no responde.
     */
    private function datos(RegistroAcademicoClient $registro, string $carnet): ?DatosAcademicos
    {
        try {
            return $registro->consultar($carnet);
        } catch (RegistroAcademicoNoDisponible) {
            throw ValidationException::withMessages(['carnet' => 'El registro académico no responde en este momento. Intenta de nuevo en unos minutos.']);
        }
    }
}
