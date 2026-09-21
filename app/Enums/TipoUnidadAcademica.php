<?php

namespace App\Enums;

use App\Concerns\ConOpciones;
use Illuminate\Support\Str;

enum TipoUnidadAcademica: string
{
    use ConOpciones;

    case Facultad = 'facultad';
    case Escuela = 'escuela';
    case CentroUniversitario = 'centro_universitario';
    case Otro = 'otro';

    public function etiqueta(): string
    {
        return match ($this) {
            self::Facultad => 'Facultad',
            self::Escuela => 'Escuela',
            self::CentroUniversitario => 'Centro universitario',
            self::Otro => 'Otro',
        };
    }

    /**
     * Deduce el tipo de unidad a partir de su nombre oficial (p. ej. "Facultad de Humanidades").
     */
    public static function desdeNombre(string $nombre): self
    {
        $nombre = Str::of($nombre)->ascii()->lower();

        return match (true) {
            $nombre->startsWith('facultad') => self::Facultad,
            $nombre->startsWith('escuela') => self::Escuela,
            $nombre->startsWith('centro') => self::CentroUniversitario,
            default => self::Otro,
        };
    }
}
