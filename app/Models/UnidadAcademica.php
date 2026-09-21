<?php

namespace App\Models;

use App\Concerns\Auditable;
use App\Enums\TipoUnidadAcademica;
use Database\Factories\UnidadAcademicaFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property string $nombre
 * @property string|null $siglas
 * @property TipoUnidadAcademica $tipo
 * @property string|null $nombre_contacto
 * @property string|null $correo_contacto
 * @property string|null $telefono_contacto
 * @property string|null $direccion
 * @property string|null $latitud
 * @property string|null $longitud
 * @property int|null $administrador_id
 * @property bool $activa
 */
#[Table('unidades_academicas')]
#[Fillable([
    'nombre',
    'siglas',
    'tipo',
    'nombre_contacto',
    'correo_contacto',
    'telefono_contacto',
    'direccion',
    'latitud',
    'longitud',
    'administrador_id',
    'activa',
])]
class UnidadAcademica extends Model
{
    /** @use HasFactory<UnidadAcademicaFactory> */
    use Auditable, HasFactory, SoftDeletes;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tipo' => TipoUnidadAcademica::class,
            'latitud' => 'decimal:7',
            'longitud' => 'decimal:7',
            'activa' => 'boolean',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function administrador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'administrador_id');
    }

    /**
     * @return HasMany<Expediente, $this>
     */
    public function expedientes(): HasMany
    {
        return $this->hasMany(Expediente::class);
    }

    public function moduloBitacora(): string
    {
        return 'Unidades académicas';
    }

    protected function descripcionBitacora(): string
    {
        return 'la unidad académica «'.$this->nombre.'»';
    }
}
