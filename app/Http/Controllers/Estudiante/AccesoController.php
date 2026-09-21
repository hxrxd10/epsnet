<?php

namespace App\Http\Controllers\Estudiante;

use App\Actions\Estudiante\SincronizarEstudiante;
use App\Enums\ClaveRol;
use App\Http\Controllers\Controller;
use App\Http\Requests\Estudiante\AccesoRequest;
use App\Models\Estudiante;
use App\Models\Rol;
use App\Models\User;
use App\Services\RegistroAcademico\RegistroAcademicoClient;
use App\Services\RegistroAcademico\RegistroAcademicoNoDisponible;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class AccesoController extends Controller
{
    /**
     * Minutos que dura la verificación de identidad para crear la cuenta.
     */
    public const int VIGENCIA_VERIFICACION = 30;

    /**
     * Paso 1: verificación de identidad con registro académico y DPI (solo la primera vez).
     * La pueden hacer quienes aún no tienen cuenta y los invitados que quieren pasar a estudiante.
     */
    public function create(Request $request): Response|RedirectResponse
    {
        $usuario = $request->user();

        if ($usuario?->esEstudiante()) {
            return to_route('estudiante.carreras');
        }

        $this->exigirInvitadoOAnonimo($usuario);

        return Inertia::render('estudiante/acceso', ['esInvitado' => $usuario !== null]);
    }

    /**
     * Verifica el registro académico y el DPI contra el servicio de Registro y Estadística.
     * Si el CUI coincide con el DPI, la persona queda verificada como estudiante: un invitado con
     * sesión pasa a estudiante de inmediato y quien no tiene cuenta continúa a crearla.
     */
    public function store(AccesoRequest $request, RegistroAcademicoClient $registro, SincronizarEstudiante $sincronizar): RedirectResponse
    {
        $usuario = $request->user();

        $this->exigirInvitadoOAnonimo($usuario);

        $datos = $request->validated();

        try {
            $consulta = $registro->consultar($datos['registro_academico']);
        } catch (RegistroAcademicoNoDisponible) {
            throw ValidationException::withMessages([
                'acceso' => 'No pudimos consultar el registro académico en este momento. Intenta de nuevo en unos minutos.',
            ]);
        }

        if ($consulta === null || ! $consulta->coincideCui($datos['dpi'])) {
            throw ValidationException::withMessages([
                'acceso' => 'El registro académico y el DPI no coinciden con los datos de la USAC.',
            ]);
        }

        $estudiante = $sincronizar->handle($consulta);

        if ($usuario !== null) {
            return $this->convertirEnEstudiante($usuario, $estudiante);
        }

        if ($estudiante->usuario_id !== null) {
            return to_route('login')->with('status', 'Tu cuenta ya está activada. Inicia sesión con tu correo y tu contraseña.');
        }

        $request->session()->put('estudiante.verificado', [
            'estudiante_id' => $estudiante->id,
            'hasta' => now()->addMinutes(self::VIGENCIA_VERIFICACION)->timestamp,
        ]);

        return to_route('estudiante.cuenta.create');
    }

    /**
     * Vincula la cuenta del invitado con su perfil de estudiante y le asigna el rol correspondiente.
     */
    private function convertirEnEstudiante(User $usuario, Estudiante $estudiante): RedirectResponse
    {
        DB::transaction(function () use ($usuario, $estudiante): void {
            $perfil = Estudiante::lockForUpdate()->findOrFail($estudiante->id);

            if ($perfil->usuario_id !== null && $perfil->usuario_id !== $usuario->id) {
                throw ValidationException::withMessages([
                    'acceso' => 'Este registro académico ya está vinculado a otra cuenta.',
                ]);
            }

            $perfil->forceFill(['usuario_id' => $usuario->id])->save();
            $usuario->forceFill(['rol_id' => Rol::delSistema(ClaveRol::Estudiante)->id])->save();
        });

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Tu cuenta ahora es de estudiante.']);

        return to_route('estudiante.carreras');
    }

    /**
     * Solo quienes no han iniciado sesión o son invitados pueden verificar su identidad.
     */
    private function exigirInvitadoOAnonimo(?User $usuario): void
    {
        abort_if(
            $usuario !== null && ! $usuario->tieneRol(ClaveRol::Invitado) && ! $usuario->esEstudiante(),
            403,
            'Tu cuenta no puede activarse como estudiante.',
        );
    }
}
