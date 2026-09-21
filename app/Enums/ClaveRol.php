<?php

namespace App\Enums;

use App\Concerns\ConOpciones;

enum ClaveRol: string
{
    use ConOpciones;

    case Digeu = 'digeu';
    case UnidadAcademica = 'unidad_academica';
    case Estudiante = 'estudiante';
    case Invitado = 'invitado';

    public function etiqueta(): string
    {
        return match ($this) {
            self::Digeu => 'DIGEU',
            self::UnidadAcademica => 'Unidad académica',
            self::Estudiante => 'Estudiante',
            self::Invitado => 'Invitado',
        };
    }

    public function descripcion(): string
    {
        return match ($this) {
            self::Digeu => 'Administración central: consolida la información de todas las unidades académicas.',
            self::UnidadAcademica => 'Coordinación de una facultad, escuela o centro universitario.',
            self::Estudiante => 'Estudiante en Ejercicio Profesional Supervisado.',
            self::Invitado => 'Persona registrada sin vínculo con una unidad académica ni un EPS: solo consulta las estadísticas.',
        };
    }
}
