<?php

namespace App\Services\RegistroAcademico;

/**
 * Canal de comunicación con el servicio web de Registro y Estadística.
 */
interface TransporteRegistroAcademico
{
    /**
     * Invoca la operación `datosGenerales` con la SOLICITUD_DATOS_RYE y devuelve el XML de respuesta.
     *
     * @throws RegistroAcademicoNoDisponible
     */
    public function datosGenerales(string $xml): string;
}
