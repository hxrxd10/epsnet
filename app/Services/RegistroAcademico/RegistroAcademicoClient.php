<?php

namespace App\Services\RegistroAcademico;

use Illuminate\Support\Facades\Log;
use SimpleXMLElement;

/**
 * Cliente del servicio web de Registro y Estadística (RYE) de la USAC.
 *
 * Arma la SOLICITUD_DATOS_RYE con las credenciales de la dependencia, la envía por el transporte
 * (operación `datosGenerales`) y traduce la RESP_CONSULTA_DATOS a objetos de dominio.
 */
class RegistroAcademicoClient
{
    public function __construct(private TransporteRegistroAcademico $transporte) {}

    /**
     * @return DatosAcademicos|null null cuando el servicio no conoce el carné.
     *
     * @throws RegistroAcademicoNoDisponible
     */
    public function consultar(string $carnet): ?DatosAcademicos
    {
        return $this->interpretar($this->transporte->datosGenerales($this->solicitud($carnet)), $carnet);
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

        if ($documento->getName() !== 'RESP_CONSULTA_DATOS') {
            return null;
        }

        $estado = $this->texto($documento->STATUS);

        if ($estado !== null && $estado !== '0' && $this->texto($documento->CARNET) === null) {
            // El servicio responde "usuario no autorizado" (estado 3) también cuando no reconoce el
            // carné, así que no se distingue de un carné inexistente. Se registra por si fallan las credenciales.
            Log::warning('El registro académico no devolvió datos del estudiante.', ['estado' => $estado, 'mensaje' => $this->texto($documento->MSG)]);

            return null;
        }

        if ($this->texto($documento->CARNET) === null) {
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

        [$nombre1, $nombre2, $nombre3, $apellido1, $apellido2] = $this->nombres($documento);

        return new DatosAcademicos(
            carnet: $carnet,
            nombre1: $nombre1,
            nombre2: $nombre2,
            nombre3: $nombre3,
            apellido1: $apellido1,
            apellido2: $apellido2,
            direccion: $this->texto($documento->DIRECCION),
            cui: (string) $this->texto($documento->CUI),
            codigoNacionalidad: $this->texto($documento->COD_NAC),
            nacionalidad: $this->texto($documento->NOM_NAC),
            carreras: $carreras,
        );
    }

    /**
     * Nombres y apellidos del estudiante. El servicio puede traerlos separados (NOMBRE1…APELLIDO2) o
     * en un solo campo NOMBRE; en ese caso se toman las dos últimas palabras como apellidos. La
     * separación es aproximada, pero el nombre completo conserva el orden original del servicio.
     *
     * @return array{0: string, 1: string|null, 2: string|null, 3: string, 4: string|null}
     */
    private function nombres(SimpleXMLElement $documento): array
    {
        if ($this->texto($documento->NOMBRE1) !== null) {
            return [
                (string) $this->texto($documento->NOMBRE1),
                $this->texto($documento->NOMBRE2),
                $this->texto($documento->NOMBRE3),
                (string) $this->texto($documento->APELLIDO1),
                $this->texto($documento->APELLIDO2),
            ];
        }

        $palabras = preg_split('/\s+/', (string) $this->texto($documento->NOMBRE), -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $cantidad = count($palabras);

        return match (true) {
            $cantidad >= 3 => [
                $palabras[0],
                $cantidad >= 4 ? $palabras[1] : null,
                $cantidad >= 5 ? implode(' ', array_slice($palabras, 2, $cantidad - 4)) : null,
                $palabras[$cantidad - 2],
                $palabras[$cantidad - 1],
            ],
            $cantidad === 2 => [$palabras[0], null, null, $palabras[1], null],
            default => [$palabras[0] ?? '', null, null, '', null],
        };
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
