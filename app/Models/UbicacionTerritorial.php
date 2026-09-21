<?php

namespace App\Models;

use Database\Factories\UbicacionTerritorialFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property int $expediente_id
 * @property int $departamento_id
 * @property string $municipio
 * @property string|null $comunidad
 * @property string|null $latitud
 * @property string|null $longitud
 * @property string|null $referencia
 */
#[Table('ubicaciones_territoriales')]
#[Fillable([
    'expediente_id',
    'departamento_id',
    'municipio',
    'comunidad',
    'latitud',
    'longitud',
    'referencia',
])]
class UbicacionTerritorial extends Model
{
    /** @use HasFactory<UbicacionTerritorialFactory> */
    use HasFactory, SoftDeletes;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'latitud' => 'decimal:7',
            'longitud' => 'decimal:7',
        ];
    }

    /**
     * @return BelongsTo<Expediente, $this>
     */
    public function expediente(): BelongsTo
    {
        return $this->belongsTo(Expediente::class);
    }

    /**
     * @return BelongsTo<Departamento, $this>
     */
    public function departamento(): BelongsTo
    {
        return $this->belongsTo(Departamento::class);
    }
}
