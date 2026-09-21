<?php

namespace App\Models;

use App\Concerns\Auditable;
use Database\Factories\ActorParticipanteFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property int $expediente_id
 * @property string $institucion_receptora
 * @property string $contraparte
 * @property string $comunidad_beneficiada
 */
#[Table('actores_participantes')]
#[Fillable([
    'expediente_id',
    'institucion_receptora',
    'contraparte',
    'comunidad_beneficiada',
])]
class ActorParticipante extends Model
{
    /** @use HasFactory<ActorParticipanteFactory> */
    use Auditable, HasFactory, SoftDeletes;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [

        ];
    }

    /**
     * @return BelongsTo<Expediente, $this>
     */
    public function expediente(): BelongsTo
    {
        return $this->belongsTo(Expediente::class);
    }

    public function moduloBitacora(): string
    {
        return 'Actores y participantes';
    }

    protected function descripcionBitacora(): string
    {
        return 'la institución receptora «'.$this->institucion_receptora.'» (contraparte '.$this->contraparte.') del '.$this->expediente?->resumenBitacora();
    }
}
