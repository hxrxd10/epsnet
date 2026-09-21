<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Contenido del archivo en base64, separado de los metadatos para no cargarlo al listar adjuntos.
 *
 * @property int $adjunto_id
 * @property string $contenido
 */
#[Table(key: 'adjunto_id', incrementing: false)]
#[Fillable(['adjunto_id', 'contenido'])]
class AdjuntoContenido extends Model
{
    /**
     * @return BelongsTo<Adjunto, $this>
     */
    public function adjunto(): BelongsTo
    {
        return $this->belongsTo(Adjunto::class);
    }

    /**
     * Bytes originales del archivo.
     */
    public function bytes(): string
    {
        return (string) base64_decode($this->contenido, true);
    }
}
