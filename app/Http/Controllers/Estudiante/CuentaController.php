<?php

namespace App\Http\Controllers\Estudiante;

use App\Enums\ClaveRol;
use App\Http\Controllers\Controller;
use App\Http\Requests\Estudiante\CrearCuentaRequest;
use App\Models\Estudiante;
use App\Models\Rol;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class CuentaController extends Controller
{
    /**
     * Paso 1 (continuación): el estudiante verificado crea su correo y contraseña de acceso.
     */
    public function create(Request $request): Response|RedirectResponse
    {
        $estudiante = $this->estudianteVerificado($request);

        if ($estudiante === null) {
            return to_route('estudiante.acceso');
        }

        return Inertia::render('estudiante/cuenta', [
            'perfil' => [
                'carnet' => $estudiante->carnet,
                'nombre_completo' => $estudiante->nombre_completo,
            ],
        ]);
    }

    /**
     * Crea el usuario con rol de estudiante, lo vincula a su perfil e inicia su sesión.
     */
    public function store(CrearCuentaRequest $request): RedirectResponse
    {
        $estudiante = $this->estudianteVerificado($request);

        if ($estudiante === null) {
            return to_route('estudiante.acceso');
        }

        $usuario = DB::transaction(function () use ($request, $estudiante): User {
            $perfil = Estudiante::lockForUpdate()->findOrFail($estudiante->id);

            if ($perfil->usuario_id !== null) {
                throw ValidationException::withMessages([
                    'email' => 'Este registro académico ya tiene una cuenta. Inicia sesión.',
                ]);
            }

            $usuario = new User([
                'name' => $perfil->nombre_completo,
                'email' => $request->validated('email'),
                'password' => $request->validated('password'),
            ]);
            $usuario->forceFill([
                'rol_id' => Rol::delSistema(ClaveRol::Estudiante)->id,
                'activo' => true,
            ])->save();

            $perfil->forceFill(['usuario_id' => $usuario->id])->save();

            return $usuario;
        });

        $request->session()->forget('estudiante.verificado');

        Auth::login($usuario);
        $request->session()->regenerate();

        return to_route('estudiante.carreras');
    }

    /**
     * Estudiante cuya identidad se verificó recientemente en esta sesión y aún no tiene cuenta.
     */
    private function estudianteVerificado(Request $request): ?Estudiante
    {
        $verificacion = $request->session()->get('estudiante.verificado');

        if (! is_array($verificacion) || ($verificacion['hasta'] ?? 0) < now()->timestamp) {
            $request->session()->forget('estudiante.verificado');

            return null;
        }

        return Estudiante::whereNull('usuario_id')->find($verificacion['estudiante_id']);
    }
}
