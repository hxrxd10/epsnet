<?php

namespace App\Models;

use Database\Factories\AdjuntoFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Metadatos de un archivo adjunto a cualquier registro (por ahora, la orden de impresión de un
 * expediente). El contenido se guarda en base64 en `adjunto_contenidos`.
 *
 * @property int $id
 * @property string|null $categoria
 * @property string $entidad_tipo
 * @property int $entidad_id
 * @property int|null $subido_por
 * @property string $nombre_original
 * @property string|null $ruta_archivo
 * @property string|null $disco
 * @property string|null $mime_type
 * @property int|null $tamano_bytes
 * @property string|null $sha256
 * @property Carbon $fecha_subida
 */
#[Fillable([
    'categoria',
    'subido_por',
    'nombre_original',
    'ruta_archivo',
    'disco',
    'mime_type',
    'tamano_bytes',
    'sha256',
    'fecha_subida',
])]
class Adjunto extends Model
{
    /** @use HasFactory<AdjuntoFactory> */
    use HasFactory;

    public const string ORDEN_IMPRESION = 'orden_impresion';

    public $timestamps = false;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tamano_bytes' => 'integer',
            'fecha_subida' => 'datetime',
        ];
    }

    /**
     * @return MorphTo<Model, $this>
     */
    public function entidad(): MorphTo
    {
        return $this->morphTo('entidad', 'entidad_tipo', 'entidad_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function subidoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'subido_por');
    }

    /**
     * @return HasOne<AdjuntoContenido, $this>
     */
    public function contenido(): HasOne
    {
        return $this->hasOne(AdjuntoContenido::class);
    }

    /**
     * Respuesta de descarga con el contenido original del archivo.
     */
    public function descarga(): StreamedResponse
    {
        $contenido = $this->contenido;

        abort_if($contenido === null, 404);

        $bytes = $contenido->bytes();

        return response()->streamDownload(
            function () use ($bytes): void {
                echo $bytes;
            },
            $this->nombre_original,
            [
                'Content-Type' => $this->mime_type ?? 'application/octet-stream',
                'Content-Length' => (string) strlen($bytes),
            ],
        );
    }
}
