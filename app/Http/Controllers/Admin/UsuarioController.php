<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ClaveRol;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CrearUsuarioRequest;
use App\Http\Requests\Admin\UsuarioRequest;
use App\Models\Carrera;
use App\Models\Rol;
use App\Models\UnidadAcademica;
use App\Models\User;
use App\Notifications\CuentaCreada;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class UsuarioController extends Controller
{
    /**
     * Gestión de usuarios: DIGEU crea las cuentas de las unidades académicas y asigna el rol, la unidad
     * y las carreras que cada supervisor puede ver y aprobar.
     */
    public function index(Request $request): Response
    {
        $filtros = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'rol' => ['nullable', Rule::enum(ClaveRol::class)],
        ]);

        $usuarios = User::query()
            ->with(['rol', 'unidadAcademica', 'carreras:id,nombre,nombre_extension'])
            ->when($filtros['q'] ?? null, fn ($consulta, string $texto) => $consulta->where(
                fn ($consulta) => $consulta->where('name', 'like', "%{$texto}%")->orWhere('email', 'like', "%{$texto}%"),
            ))
            ->when($filtros['rol'] ?? null, fn ($consulta, string $rol) => $consulta->whereHas('rol', fn ($consulta) => $consulta->where('clave', $rol)))
            ->orderBy('name')
            ->orderBy('id')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('admin/usuarios', [
            'usuarios' => $usuarios->getCollection()->map(fn (User $usuario): array => [
                'id' => $usuario->id,
                'name' => $usuario->name,
                'email' => $usuario->email,
                'rol' => $usuario->rol?->clave,
                'rol_etiqueta' => $usuario->rol?->nombre ?? 'Sin rol',
                'unidad' => $usuario->unidadAcademica?->nombre,
                'unidad_id' => $usuario->unidad_academica_id,
                'carreras' => $usuario->carreras->map(fn (Carrera $carrera): array => ['id' => $carrera->id, 'nombre' => $carrera->nombre])->values()->all(),
                'correo_verificado' => $usuario->email_verified_at !== null,
                'editable' => ! $usuario->esEstudiante() && $usuario->id !== $request->user()->id,
                'propio' => $usuario->id === $request->user()->id,
            ])->values(),
            'pagina' => [
                'actual' => $usuarios->currentPage(),
                'ultima' => $usuarios->lastPage(),
                'total' => $usuarios->total(),
                'anterior' => $usuarios->previousPageUrl(),
                'siguiente' => $usuarios->nextPageUrl(),
            ],
            'filtros' => ['q' => $filtros['q'] ?? '', 'rol' => $filtros['rol'] ?? ''],
            'roles' => collect(ClaveRol::cases())->map(fn (ClaveRol $rol): array => ['value' => $rol->value, 'label' => $rol->etiqueta()])->all(),
            'rolesAsignables' => array_map(fn (ClaveRol $rol): array => ['value' => $rol->value, 'label' => $rol->etiqueta()], UsuarioRequest::rolesAsignables()),
            'unidades' => UnidadAcademica::query()
                ->with(['carreras' => fn ($consulta) => $consulta->orderBy('nombre')->orderBy('nombre_extension')])
                ->orderBy('nombre')
                ->get()
                ->map(fn (UnidadAcademica $unidad): array => [
                    'value' => (string) $unidad->id,
                    'label' => $unidad->nombre,
                    'carreras' => $unidad->carreras->map(fn (Carrera $carrera): array => [
                        'value' => (string) $carrera->id,
                        'label' => $carrera->nombre.($carrera->nombre_extension ? " ({$carrera->nombre_extension})" : ''),
                    ])->values()->all(),
                ])->all(),
        ]);
    }

    /**
     * Crea la cuenta (p. ej. de un supervisor de EPS) y le envía el enlace para definir su contraseña.
     * El correo queda verificado: solo quien lo recibe puede definir la contraseña y entrar.
     */
    public function store(CrearUsuarioRequest $request): RedirectResponse
    {
        $acceso = $request->acceso();

        $usuario = DB::transaction(function () use ($request, $acceso): User {
            $usuario = User::create([
                'name' => $request->validated('name'),
                'email' => $request->validated('email'),
                'password' => Str::password(32),
            ]);

            $this->asignarAcceso($usuario, $acceso);

            return $usuario;
        });

        $usuario->notify(new CuentaCreada(Password::broker()->createToken($usuario), $usuario->unidadAcademica?->nombre ?? 'Dirección General de Extensión Universitaria'));

        Inertia::flash('toast', ['type' => 'success', 'message' => "Cuenta de {$usuario->name} creada. Le enviamos un correo para definir su contraseña."]);

        return to_route('admin.usuarios.index');
    }

    /**
     * Cambia el rol de un usuario y, para una unidad académica, su unidad y las carreras que supervisa.
     */
    public function update(UsuarioRequest $request, User $usuario): RedirectResponse
    {
        abort_if($usuario->id === $request->user()->id, 403, 'No puedes cambiar tu propio acceso.');
        abort_if($usuario->esEstudiante(), 403, 'El rol de un estudiante depende de su registro académico.');

        DB::transaction(fn () => $this->asignarAcceso($usuario, $request->acceso()));

        Inertia::flash('toast', ['type' => 'success', 'message' => "Acceso de {$usuario->name} actualizado."]);

        return back();
    }

    /**
     * @param  array{rol: ClaveRol, unidad_academica_id: int|null, carreras: list<int>}  $acceso
     */
    private function asignarAcceso(User $usuario, array $acceso): void
    {
        $usuario->forceFill([
            'rol_id' => Rol::delSistema($acceso['rol'])->id,
            'unidad_academica_id' => $acceso['unidad_academica_id'],
            'email_verified_at' => $usuario->email_verified_at ?? ($usuario->wasRecentlyCreated ? now() : null),
        ])->save();

        $usuario->carreras()->sync($acceso['carreras']);
    }
}
