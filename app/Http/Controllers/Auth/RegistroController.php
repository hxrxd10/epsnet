<?php

namespace App\Http\Controllers\Auth;

use App\Enums\ClaveRol;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegistroRequest;
use App\Models\Rol;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class RegistroController extends Controller
{
    /**
     * Registro abierto: quien crea una cuenta queda como invitado y solo consulta estadísticas.
     */
    public function create(): Response
    {
        return Inertia::render('auth/register');
    }

    public function store(RegistroRequest $request): RedirectResponse
    {
        $usuario = new User($request->safe()->only(['name', 'email', 'password']));
        $usuario->forceFill([
            'rol_id' => Rol::delSistema(ClaveRol::Invitado)->id,
            'activo' => true,
        ])->save();

        event(new Registered($usuario));

        Auth::login($usuario);
        $request->session()->regenerate();

        return to_route('dashboard');
    }
}
