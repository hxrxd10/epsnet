<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Symfony\Component\HttpFoundation\Response;

/**
 * Tras iniciar sesión, los estudiantes van directo a su EPS y el resto al panel.
 */
class LoginResponse implements LoginResponseContract
{
    public function toResponse($request): Response
    {
        if ($request->wantsJson()) {
            return new JsonResponse(['two_factor' => false]);
        }

        $inicio = $request->user()->esEstudiante()
            ? route('estudiante.carreras')
            : config('fortify.home');

        return redirect()->intended($inicio);
    }
}
