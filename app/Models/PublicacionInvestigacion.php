<?php

namespace App\Models;

use App\Enums\TipoPublicacion;
use Database\Factories\PublicacionInvestigacionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $expediente_id
 * @property string $titulo
 * @property TipoPublicacion $tipo
 * @property string $autores
 * @property string|null $medio_publicacion
 * @property string|null $resumen
 * @property string|null $enlace
 * @property Carbon|null $fecha_publicacion
 */
#[Table('publicaciones_investigacion')]
#[Fillable([
    'expediente_id',
    'titulo',
    'tipo',
    'autores',
    'medio_publicacion',
    'resumen',
    'enlace',
    'fecha_publicacion',
])]
class PublicacionInvestigacion extends Model
{
    /** @use HasFactory<PublicacionInvestigacionFactory> */
    use HasFactory, SoftDeletes;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tipo' => TipoPublicacion::class,
            'fecha_publicacion' => 'date',
        ];
    }

    /**
     * @return BelongsTo<Expediente, $this>
     */
    public function expediente(): BelongsTo
    {
        return $this->belongsTo(Expediente::class);
    }
}
