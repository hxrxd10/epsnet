<?php

namespace App\Services\RegistroAcademico;

use Illuminate\Support\Facades\Log;
use SoapClient;
use SoapFault;

/**
 * Transporte SOAP: el servicio publica un WSDL (nusoap, rpc/encoded) con la operación
 * `datosGenerales(xmlDatos: string)`; `services.registro_academico.url` es la dirección de ese WSDL.
 */
class TransporteSoap implements TransporteRegistroAcademico
{
    public function datosGenerales(string $xml): string
    {
        $wsdl = config('services.registro_academico.url');

        if (blank($wsdl)) {
            throw new RegistroAcademicoNoDisponible('El servicio de registro académico no está configurado.');
        }

        $espera = (string) config('services.registro_academico.timeout');
        $anterior = ini_set('default_socket_timeout', $espera);
        $erroresXml = libxml_use_internal_errors(true);

        // SoapClient avisa con warnings además de lanzar SoapFault; aquí solo interesa la excepción.
        set_error_handler(static fn (): bool => true, E_WARNING | E_NOTICE);

        try {
            $cliente = new SoapClient($wsdl, [
                'exceptions' => true,
                'connection_timeout' => 3,
            ]);

            $respuesta = $cliente->datosGenerales($xml);
        } catch (SoapFault $falla) {
            Log::warning('Falló la consulta al registro académico.', ['motivo' => $falla->getMessage()]);

            throw new RegistroAcademicoNoDisponible('No se pudo consultar el registro académico.', previous: $falla);
        } finally {
            restore_error_handler();
            libxml_clear_errors();
            libxml_use_internal_errors($erroresXml);

            if ($anterior !== false) {
                ini_set('default_socket_timeout', $anterior);
            }
        }

        if (! is_string($respuesta)) {
            throw new RegistroAcademicoNoDisponible('El registro académico respondió con un formato inesperado.');
        }

        return $respuesta;
    }
}
