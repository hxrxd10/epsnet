<?php

namespace App\Services\RegistroAcademico;

/**
 * Respuesta de la consulta de datos de un estudiante (RESP_CONSULTA_DATOS).
 */
final readonly class DatosAcademicos
{
    /**
     * @param  list<DetalleAcademico>  $carreras
     */
    public function __construct(
        public string $carnet,
        public string $nombre1,
        public ?string $nombre2,
        public ?string $nombre3,
        public string $apellido1,
        public ?string $apellido2,
        public ?string $direccion,
        public string $cui,
        public ?string $codigoNacionalidad,
        public ?string $nacionalidad,
        public array $carreras,
    ) {}

    /**
     * Compara el CUI/DPI del servicio con el ingresado, ignorando espacios y guiones.
     */
    public function coincideCui(string $cui): bool
    {
        $esperado = self::soloDigitos($this->cui);
        $recibido = self::soloDigitos($cui);

        return $esperado !== '' && hash_equals($esperado, $recibido);
    }

    public static function soloDigitos(string $valor): string
    {
        return preg_replace('/\D+/', '', $valor) ?? '';
    }
}
