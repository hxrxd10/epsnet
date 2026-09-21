<?php

namespace App\Enums;

use App\Concerns\ConOpciones;

enum TipoPublicacion: string
{
    use ConOpciones;

    case Articulo = 'articulo';
    case Libro = 'libro';
    case CapituloLibro = 'capitulo_libro';
    case Tesis = 'tesis';
    case Informe = 'informe';
    case Ponencia = 'ponencia';
    case Otro = 'otro';

    public function etiqueta(): string
    {
        return match ($this) {
            self::Articulo => 'Artículo',
            self::Libro => 'Libro',
            self::CapituloLibro => 'Capítulo de libro',
            self::Tesis => 'Tesis',
            self::Informe => 'Informe de investigación',
            self::Ponencia => 'Ponencia',
            self::Otro => 'Otro',
        };
    }
}
