<?php

namespace App\Http\Middleware;

use App\Enums\ClaveRol;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Restringe una ruta a usuarios con alguno de los roles indicados: ->middleware('rol:estudiante').
 * Un usuario sin rol asignado no puede acceder a ningún módulo.
 */
class EnsureUserHasRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $usuario = $request->user();

        $permitido = $usuario !== null && collect($roles)->contains(
            fn (string $rol): bool => ($clave = ClaveRol::tryFrom($rol)) !== null && $usuario->tieneRol($clave),
        );

        abort_unless($permitido, 403, 'No tienes permiso para acceder a esta sección.');

        return $next($request);
    }
}
