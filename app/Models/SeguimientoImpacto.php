<?php

namespace App\Models;

use App\Concerns\Auditable;
use App\Enums\NivelCumplimiento;
use App\Enums\TipoRegistroSeguimiento;
use Database\Factories\SeguimientoImpactoFactory;
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
 * @property int|null $registrado_por
 * @property TipoRegistroSeguimiento $tipo_registro
 * @property string $indicador
 * @property string|null $avance
 * @property int|null $porcentaje_avance
 * @property NivelCumplimiento|null $cumplimiento
 * @property string|null $observaciones
 * @property string|null $evaluacion_impacto
 * @property Carbon $fecha
 */
#[Table('seguimientos_impacto')]
#[Fillable([
    'expediente_id',
    'registrado_por',
    'tipo_registro',
    'indicador',
    'avance',
    'porcentaje_avance',
    'cumplimiento',
    'observaciones',
    'evaluacion_impacto',
    'fecha',
])]
class SeguimientoImpacto extends Model
{
    /** @use HasFactory<SeguimientoImpactoFactory> */
    use Auditable, HasFactory, SoftDeletes;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tipo_registro' => TipoRegistroSeguimiento::class,
            'cumplimiento' => NivelCumplimiento::class,
            'porcentaje_avance' => 'integer',
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

    public function moduloBitacora(): string
    {
        return 'Seguimiento e impacto';
    }

    protected function descripcionBitacora(): string
    {
        return 'el registro de seguimiento «'.Str::limit($this->indicador, 70).'» del '.$this->expediente?->resumenBitacora();
    }
}
