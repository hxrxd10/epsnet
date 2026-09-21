<?php

namespace App\Models;

use App\Concerns\Auditable;
use Database\Factories\AlianzaFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Una institución aliada (ministerio, ONG, socio) que participó o cooperó con el proyecto de un EPS.
 *
 * @property int $id
 * @property int $expediente_id
 * @property int $institucion_aliada_id
 * @property string|null $aporte
 */
#[Table('alianzas')]
#[Fillable(['expediente_id', 'institucion_aliada_id', 'aporte'])]
class Alianza extends Model
{
    /** @use HasFactory<AlianzaFactory> */
    use Auditable, HasFactory, SoftDeletes;

    /**
     * @return BelongsTo<Expediente, $this>
     */
    public function expediente(): BelongsTo
    {
        return $this->belongsTo(Expediente::class);
    }

    /**
     * @return BelongsTo<InstitucionAliada, $this>
     */
    public function institucionAliada(): BelongsTo
    {
        return $this->belongsTo(InstitucionAliada::class);
    }

    public function moduloBitacora(): string
    {
        return 'Instituciones aliadas del EPS';
    }

    protected function descripcionBitacora(): string
    {
        return 'la alianza con «'.$this->institucionAliada?->nombre.'» del '.$this->expediente?->resumenBitacora();
    }
}
