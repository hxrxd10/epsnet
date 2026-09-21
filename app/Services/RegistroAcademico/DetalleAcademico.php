<?php

namespace App\Services\RegistroAcademico;

/**
 * Una carrera del estudiante según el Registro y Estadística (bloque DETALLE_ACADEMICO).
 */
final readonly class DetalleAcademico
{
    public function __construct(
        public string $unidad,
        public string $extension,
        public string $carrera,
        public string $nombreUnidad,
        public ?string $nombreExtension,
        public string $nombreCarrera,
        public ?string $nivel,
        public ?string $grado,
        public ?string $estado,
        public ?string $cicloActivo,
        public ?string $fechaInscrito,
        public ?string $fechaCierre,
        public ?string $fechaGraduado,
    ) {}

    /**
     * Identificador estable de la carrera dentro de la respuesta: unidad-extensión-carrera.
     */
    public function clave(): string
    {
        return "{$this->unidad}-{$this->extension}-{$this->carrera}";
    }

    public function estaGraduado(): bool
    {
        return $this->fechaGraduado !== null;
    }

    /**
     * @return array<string, string|null>
     */
    public function toArray(): array
    {
        return get_object_vars($this);
    }

    /**
     * @param  array<string, string|null>  $datos
     */
    public static function fromArray(array $datos): self
    {
        return new self(
            unidad: (string) $datos['unidad'],
            extension: (string) $datos['extension'],
            carrera: (string) $datos['carrera'],
            nombreUnidad: (string) $datos['nombreUnidad'],
            nombreExtension: $datos['nombreExtension'] ?? null,
            nombreCarrera: (string) $datos['nombreCarrera'],
            nivel: $datos['nivel'] ?? null,
            grado: $datos['grado'] ?? null,
            estado: $datos['estado'] ?? null,
            cicloActivo: $datos['cicloActivo'] ?? null,
            fechaInscrito: $datos['fechaInscrito'] ?? null,
            fechaCierre: $datos['fechaCierre'] ?? null,
            fechaGraduado: $datos['fechaGraduado'] ?? null,
        );
    }
}
