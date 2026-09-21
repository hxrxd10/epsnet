<?php

namespace App\Models;

use App\Concerns\Auditable;
use App\Enums\TipoActividadTransferencia;
use Database\Factories\TransferenciaConocimientoFactory;
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
 * @property TipoActividadTransferencia $tipo_actividad
 * @property string $actividad
 * @property string $comunidad
 * @property int|null $numero_participantes
 * @property Carbon $fecha
 */
#[Table('transferencias_conocimiento')]
#[Fillable([
    'expediente_id',
    'catalogo_id',
    'tipo_actividad',
    'actividad',
    'comunidad',
    'numero_participantes',
    'fecha',
])]
class TransferenciaConocimiento extends Model
{
    /** @use HasFactory<TransferenciaConocimientoFactory> */
    use Auditable, HasFactory, SoftDeletes;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tipo_actividad' => TipoActividadTransferencia::class,
            'numero_participantes' => 'integer',
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
        return 'Transferencia de conocimiento';
    }

    protected function descripcionBitacora(): string
    {
        return 'la actividad «'.Str::limit($this->actividad, 70).'» del '.$this->expediente?->resumenBitacora();
    }
}
