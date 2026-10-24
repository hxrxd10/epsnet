<?php

namespace App\Models;

use App\Concerns\Auditable;
use App\Enums\TipoUnidadAcademica;
use Database\Factories\UnidadAcademicaFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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
     * Usuarios de la unidad académica (coordinación y supervisores de EPS).
     *
     * @return HasMany<User, $this>
     */
    public function usuarios(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * @return HasMany<Carrera, $this>
     */
    public function carreras(): HasMany
    {
        return $this->hasMany(Carrera::class);
    }

    /**
     * La unidad académica con este nombre del registro académico; se crea si aún no existe.
     */
    public static function paraNombre(string $nombre): self
    {
        return self::withTrashed()->firstOrCreate(
            ['nombre' => $nombre],
            ['tipo' => TipoUnidadAcademica::desdeNombre($nombre), 'activa' => true],
        );
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
