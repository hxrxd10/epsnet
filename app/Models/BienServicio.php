<?php

namespace App\Models;

use App\Concerns\Auditable;
use App\Enums\TipoBienServicio;
use Database\Factories\BienServicioFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property int $expediente_id
 * @property int|null $catalogo_id
 * @property TipoBienServicio $tipo
 * @property string $descripcion
 * @property string|null $beneficiarios
 * @property int|null $cantidad_beneficiarios
 * @property Carbon $fecha
 */
#[Table('bienes_servicios')]
#[Fillable([
    'expediente_id',
    'catalogo_id',
    'tipo',
    'descripcion',
    'beneficiarios',
    'cantidad_beneficiarios',
    'fecha',
])]
class BienServicio extends Model
{
    /** @use HasFactory<BienServicioFactory> */
    use Auditable, HasFactory, SoftDeletes;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tipo' => TipoBienServicio::class,
            'cantidad_beneficiarios' => 'integer',
            'fecha' => 'date',
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
     * @return BelongsTo<Catalogo, $this>
     */
    public function catalogo(): BelongsTo
    {
        return $this->belongsTo(Catalogo::class);
    }

    public function moduloBitacora(): string
    {
        return 'Bienes y servicios';
    }

    protected function descripcionBitacora(): string
    {
        return 'el '.mb_strtolower($this->tipo->etiqueta()).' «'.Str::limit($this->descripcion, 70).'» del '.$this->expediente?->resumenBitacora();
    }
}
