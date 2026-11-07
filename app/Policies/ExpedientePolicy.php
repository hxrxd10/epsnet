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
     * Cambiar lo registrado: solo su estudiante y mientras el EPS no esté aprobado. Si hay que corregir
     * un EPS aprobado, la supervisión retira la aprobación.
     */
    public function editar(User $usuario, Expediente $expediente): Response
    {
        $vista = $this->view($usuario, $expediente);

        if ($vista->denied()) {
            return $vista;
        }

        return $expediente->estado_expediente === EstadoExpediente::Verificado
            ? Response::deny('Tu EPS ya está aprobado: solo puedes ver lo que registraste.')
            : Response::allow();
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
     * La foto del EPS la ven su estudiante (aunque no haya verificado su correo), quien supervisa el
     * expediente y, una vez aprobado (aparece en el repositorio), quien puede consultarlo en el
     * repositorio. Fuera del estudiante, se exige el correo verificado.
     */
    public function verFoto(User $usuario, Expediente $expediente): Response
    {
        $esSuEstudiante = $usuario->estudiante !== null && $usuario->estudiante->id === $expediente->estudiante_id;

        $permitido = $esSuEstudiante || ($usuario->hasVerifiedEmail() && (
            ($expediente->estado_expediente === EstadoExpediente::Verificado && $usuario->consultaExpediente($expediente))
            || $usuario->alcanzaExpediente($expediente)
        ));

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
