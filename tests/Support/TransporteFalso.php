<?php

namespace Tests\Support;

use App\Services\RegistroAcademico\RegistroAcademicoNoDisponible;
use App\Services\RegistroAcademico\TransporteRegistroAcademico;

/**
 * Transporte de prueba: registra las solicitudes y responde con un XML fijo o con una falla.
 */
class TransporteFalso implements TransporteRegistroAcademico
{
    /** @var list<string> */
    public array $solicitudes = [];

    public function __construct(private ?string $respuesta = null, private bool $falla = false) {}

    public function datosGenerales(string $xml): string
    {
        $this->solicitudes[] = $xml;

        if ($this->falla) {
            throw new RegistroAcademicoNoDisponible('Sin conexión con el registro académico.');
        }

        return $this->respuesta ?? '';
    }
}
