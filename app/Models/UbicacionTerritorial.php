<?php

namespace App\Models;

use App\Concerns\Auditable;
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
 * @property int|null $municipio_id
 * @property string|null $comunidad
 * @property string|null $latitud
 * @property string|null $longitud
 * @property string|null $referencia
 */
#[Table('ubicaciones_territoriales')]
#[Fillable([
    'expediente_id',
    'departamento_id',
    'municipio_id',
    'comunidad',
    'latitud',
    'longitud',
    'referencia',
])]
class UbicacionTerritorial extends Model
{
    /** @use HasFactory<UbicacionTerritorialFactory> */
    use Auditable, HasFactory, SoftDeletes;

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

    /**
     * @return BelongsTo<Municipio, $this>
     */
    public function municipio(): BelongsTo
    {
        return $this->belongsTo(Municipio::class);
    }

    public function moduloBitacora(): string
    {
        return 'Territorio y geolocalización';
    }

    protected function descripcionBitacora(): string
    {
        return 'la ubicación '.collect([$this->municipio?->nombre, $this->departamento?->nombre])->filter()->implode(', ').' del '.$this->expediente?->resumenBitacora();
    }
}
