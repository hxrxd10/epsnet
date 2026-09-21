<?php

namespace App\Policies;

use App\Enums\Eje;
use App\Enums\EstadoExpediente;
use App\Models\Expediente;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ExpedientePolicy
{
    /**
     * Un estudiante solo puede ver y editar sus propios expedientes; los ajenos aparentan no existir.
     */
    public function view(User $usuario, Expediente $expediente): Response
    {
        $estudiante = $usuario->estudiante;

        return $estudiante !== null && $estudiante->id === $expediente->estudiante_id
            ? Response::allow()
            : Response::denyAsNotFound();
    }

    /**
     * Consulta de solo lectura: DIGEU ve todos los expedientes y cada unidad académica los suyos.
     */
    public function consultar(User $usuario, Expediente $expediente): Response
    {
        $permitido = $usuario->esAdministrador()
            || ($usuario->esUnidadAcademica() && $usuario->unidadAcademica?->id === $expediente->unidad_academica_id);

        return $permitido ? Response::allow() : Response::denyAsNotFound();
    }

    /**
     * Aprobar (doble verificación) o retirar la aprobación: quien puede consultar el expediente,
     * siempre que el estudiante lo haya completado o, aun sin completarlo (p. ej. sin orden de
     * impresión por no tener un informe escrito), tenga información registrada que aprobar.
     */
    public function verificar(User $usuario, Expediente $expediente): Response
    {
        $consulta = $this->consultar($usuario, $expediente);

        if ($consulta->denied()) {
            return $consulta;
        }

        return $expediente->estado_expediente === EstadoExpediente::Activo && ! $this->tieneInformacion($expediente)
            ? Response::deny('El estudiante aún no registra información en su EPS.')
            : Response::allow();
    }

    private function tieneInformacion(Expediente $expediente): bool
    {
        return collect(Eje::cases())->contains(fn (Eje $eje): bool => $expediente->{$eje->relacion()}()->exists());
    }
}
