<?php

namespace App\Actions\Estudiante;

use App\Models\Estudiante;
use App\Services\RegistroAcademico\DatosAcademicos;
use App\Services\RegistroAcademico\DetalleAcademico;

/**
 * Crea o actualiza al estudiante con los datos personales y las carreras que devuelve
 * el registro académico. El CUI no se almacena.
 */
class SincronizarEstudiante
{
    public function handle(DatosAcademicos $datos): Estudiante
    {
        $estudiante = Estudiante::withTrashed()->updateOrCreate(
            ['carnet' => $datos->carnet],
            [
                'nombre1' => $datos->nombre1,
                'nombre2' => $datos->nombre2,
                'nombre3' => $datos->nombre3,
                'apellido1' => $datos->apellido1,
                'apellido2' => $datos->apellido2,
                'direccion' => $datos->direccion,
                'codigo_nacionalidad' => $datos->codigoNacionalidad,
                'nacionalidad' => $datos->nacionalidad,
                'carreras' => array_map(fn (DetalleAcademico $carrera): array => $carrera->toArray(), $datos->carreras),
                'ultima_consulta_at' => now(),
            ],
        );

        if ($estudiante->trashed()) {
            $estudiante->restore();
        }

        return $estudiante;
    }
}
