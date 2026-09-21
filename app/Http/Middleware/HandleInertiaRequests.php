<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'auth' => [
                'user' => $request->user(),
            ],
            'rol' => fn () => $request->user()?->rol?->clave,
            'estudiante' => fn () => $this->estudiante($request),
            'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
        ];
    }

    /**
     * Datos mínimos del estudiante con sesión activa, para el asistente de llenado.
     *
     * @return array{carnet: string, nombre_completo: string}|null
     */
    protected function estudiante(Request $request): ?array
    {
        $usuario = $request->user();

        if ($usuario === null || ! $usuario->esEstudiante()) {
            return null;
        }

        $estudiante = $usuario->estudiante;

        return $estudiante === null ? null : [
            'carnet' => $estudiante->carnet,
            'nombre_completo' => $estudiante->nombre_completo,
        ];
    }
}
