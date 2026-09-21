<?php

namespace App\Services\RegistroAcademico;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use SimpleXMLElement;

/**
 * Cliente del servicio web de Registro y Estadística (RYE) de la USAC.
 *
 * Envía la SOLICITUD_DATOS_RYE con las credenciales de la dependencia y traduce la
 * RESP_CONSULTA_DATOS a objetos de dominio.
 */
class RegistroAcademicoClient
{
    /**
     * @return DatosAcademicos|null null cuando el servicio no conoce el carné.
     *
     * @throws RegistroAcademicoNoDisponible
     */
    public function consultar(string $carnet): ?DatosAcademicos
    {
        $url = config('services.registro_academico.url');

        if (blank($url)) {
            throw new RegistroAcademicoNoDisponible('El servicio de registro académico no está configurado.');
        }

        try {
            $respuesta = Http::connectTimeout(3)
                ->timeout((int) config('services.registro_academico.timeout'))
                ->withBody($this->solicitud($carnet), 'text/xml; charset=UTF-8')
                ->post($url)
                ->throw();
        } catch (ConnectionException|RequestException $excepcion) {
            Log::warning('Falló la consulta al registro académico.', ['motivo' => $excepcion->getMessage()]);

            throw new RegistroAcademicoNoDisponible('No se pudo consultar el registro académico.', previous: $excepcion);
        }

        return $this->interpretar($respuesta->body(), $carnet);
    }

    private function solicitud(string $carnet): string
    {
        $valores = [
            'DEPENDENCIA' => config('services.registro_academico.dependencia'),
            'LOGIN' => config('services.registro_academico.login'),
            'PWD' => config('services.registro_academico.password'),
            'CARNET' => $carnet,
        ];

        $contenido = collect($valores)
            ->map(fn (?string $valor, string $etiqueta): string => "<{$etiqueta}>".htmlspecialchars((string) $valor, ENT_XML1 | ENT_QUOTES, 'UTF-8')."</{$etiqueta}>")
            ->implode('');

        return "<SOLICITUD_DATOS_RYE>{$contenido}</SOLICITUD_DATOS_RYE>";
    }

    private function interpretar(string $cuerpo, string $carnetSolicitado): ?DatosAcademicos
    {
        $documento = $this->cargarXml($cuerpo);

        if ($documento->getName() !== 'RESP_CONSULTA_DATOS' || $this->texto($documento->CARNET) === null) {
            return null;
        }

        $carnet = (string) $this->texto($documento->CARNET);

        if ($carnet !== $carnetSolicitado) {
            return null;
        }

        $carreras = [];

        foreach ($documento->DETALLE_ACADEMICO as $detalle) {
            $carreras[] = new DetalleAcademico(
                unidad: (string) $this->texto($detalle->UNIDAD),
                extension: (string) $this->texto($detalle->EXTENSION),
                carrera: (string) $this->texto($detalle->CARRERA),
                nombreUnidad: (string) $this->texto($detalle->NOMBRE_UNIDAD),
                nombreExtension: $this->texto($detalle->NOMBRE_EXTENSION),
                nombreCarrera: (string) $this->texto($detalle->NOMBRE_CARRERA),
                nivel: $this->texto($detalle->NIVEL_ACADEMICO->NIVEL),
                grado: $this->texto($detalle->NIVEL_ACADEMICO->GRADO),
                estado: $this->texto($detalle->ESTADO),
                cicloActivo: $this->texto($detalle->CICLO_ACTIVO),
                fechaInscrito: $this->texto($detalle->FECHA_INSCRITO),
                fechaCierre: $this->texto($detalle->FECHA_CIERRE),
                fechaGraduado: $this->texto($detalle->FECHA_GRADUADO),
            );
        }

        return new DatosAcademicos(
            carnet: $carnet,
            nombre1: (string) $this->texto($documento->NOMBRE1),
            nombre2: $this->texto($documento->NOMBRE2),
            nombre3: $this->texto($documento->NOMBRE3),
            apellido1: (string) $this->texto($documento->APELLIDO1),
            apellido2: $this->texto($documento->APELLIDO2),
            direccion: $this->texto($documento->DIRECCION),
            cui: (string) $this->texto($documento->CUI),
            codigoNacionalidad: $this->texto($documento->COD_NAC),
            nacionalidad: $this->texto($documento->NOM_NAC),
            carreras: $carreras,
        );
    }

    /**
     * @throws RegistroAcademicoNoDisponible
     */
    private function cargarXml(string $cuerpo): SimpleXMLElement
    {
        $cuerpo = trim((string) preg_replace('/^\s*<\?xml[^>]*\?>/i', '', trim($cuerpo)));

        if (! mb_check_encoding($cuerpo, 'UTF-8')) {
            $cuerpo = mb_convert_encoding($cuerpo, 'UTF-8', 'Windows-1252');
        }

        $anterior = libxml_use_internal_errors(true);
        $documento = $cuerpo === '' ? false : simplexml_load_string($cuerpo, options: LIBXML_NONET);
        libxml_clear_errors();
        libxml_use_internal_errors($anterior);

        if ($documento === false) {
            throw new RegistroAcademicoNoDisponible('El registro académico respondió con un formato inesperado.');
        }

        return $documento;
    }

    /**
     * Normaliza un nodo a texto: vacío pasa a null y se corrige el texto UTF-8 leído dos veces
     * como Latin-1 (p. ej. "PedagogÃ­a" en lugar de "Pedagogía").
     */
    private function texto(?SimpleXMLElement $nodo): ?string
    {
        $texto = trim((string) $nodo);

        if ($texto === '') {
            return null;
        }

        if (preg_match('/[ÃÂ]/u', $texto) === 1) {
            $corregido = mb_convert_encoding($texto, 'ISO-8859-1', 'UTF-8');

            if (mb_check_encoding($corregido, 'UTF-8') && mb_convert_encoding($corregido, 'UTF-8', 'ISO-8859-1') === $texto) {
                return $corregido;
            }
        }

        return $texto;
    }
}
