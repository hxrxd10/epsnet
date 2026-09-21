<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ClaveRol;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UsuarioRequest;
use App\Models\Rol;
use App\Models\UnidadAcademica;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class UsuarioController extends Controller
{
    /**
     * Gestión de usuarios: DIGEU asigna el rol (y la unidad académica) a quienes se registraron.
     */
    public function index(Request $request): Response
    {
        $filtros = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'rol' => ['nullable', Rule::enum(ClaveRol::class)],
        ]);

        $usuarios = User::query()
            ->with(['rol', 'unidadAcademica'])
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
                'unidad_id' => $usuario->unidadAcademica?->id,
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
            'unidades' => UnidadAcademica::with('administrador')->orderBy('nombre')->get()->map(fn (UnidadAcademica $unidad): array => [
                'value' => (string) $unidad->id,
                'label' => $unidad->nombre,
                'administrador_id' => $unidad->administrador_id,
                'administrador' => $unidad->administrador?->name,
            ])->all(),
        ]);
    }

    /**
     * Cambia el rol de un usuario y, para una unidad académica, la unidad que administra.
     */
    public function update(UsuarioRequest $request, User $usuario): RedirectResponse
    {
        abort_if($usuario->id === $request->user()->id, 403, 'No puedes cambiar tu propio acceso.');
        abort_if($usuario->esEstudiante(), 403, 'El rol de un estudiante depende de su registro académico.');

        $rol = ClaveRol::from($request->validated('rol'));

        DB::transaction(function () use ($request, $usuario, $rol): void {
            UnidadAcademica::where('administrador_id', $usuario->id)->update(['administrador_id' => null]);

            $usuario->forceFill(['rol_id' => Rol::delSistema($rol)->id])->save();

            if ($rol === ClaveRol::UnidadAcademica) {
                UnidadAcademica::whereKey($request->validated('unidad_academica_id'))->update(['administrador_id' => $usuario->id]);
            }
        });

        Inertia::flash('toast', ['type' => 'success', 'message' => "Acceso de {$usuario->name} actualizado."]);

        return back();
    }
}
