<?php

namespace App\Policies;

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
     * Consulta de solo lectura: DIGEU ve todos los expedientes y cada unidad académica los suyos (o
     * solo los de sus carreras, si tiene carreras asignadas).
     */
    public function consultar(User $usuario, Expediente $expediente): Response
    {
        return $usuario->alcanzaExpediente($expediente) ? Response::allow() : Response::denyAsNotFound();
    }

    /**
     * La foto del EPS la ven su estudiante, quien puede consultar el expediente y, una vez aprobado
     * (aparece en el repositorio), cualquier usuario con sesión.
     */
    public function verFoto(User $usuario, Expediente $expediente): Response
    {
        $permitido = $expediente->estado_expediente === EstadoExpediente::Verificado
            || $usuario->alcanzaExpediente($expediente)
            || ($usuario->estudiante !== null && $usuario->estudiante->id === $expediente->estudiante_id);

        return $permitido ? Response::allow() : Response::denyAsNotFound();
    }

    /**
     * Aprobar o retirar la aprobación: quien puede consultar el expediente, siempre que el
     * estudiante ya lo haya enviado a aprobación.
     */
    public function verificar(User $usuario, Expediente $expediente): Response
    {
        $consulta = $this->consultar($usuario, $expediente);

        if ($consulta->denied()) {
            return $consulta;
        }

        return $expediente->estado_expediente === EstadoExpediente::Activo
            ? Response::deny('El estudiante aún no envía su EPS a aprobación.')
            : Response::allow();
    }
}
