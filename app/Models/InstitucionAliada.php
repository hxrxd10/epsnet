<?php

namespace App\Models;

use App\Concerns\Auditable;
use Database\Factories\InstitucionAliadaFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property string $nombre
 * @property string|null $tipo
 * @property string|null $nombre_contacto
 * @property string|null $correo_contacto
 * @property string|null $telefono_contacto
 * @property string|null $direccion
 */
#[Table('instituciones_aliadas')]
#[Fillable([
    'nombre',
    'tipo',
    'nombre_contacto',
    'correo_contacto',
    'telefono_contacto',
    'direccion',
])]
class InstitucionAliada extends Model
{
    /** @use HasFactory<InstitucionAliadaFactory> */
    use Auditable, HasFactory, SoftDeletes;

    /**
     * @return HasMany<Alianza, $this>
     */
    public function alianzas(): HasMany
    {
        return $this->hasMany(Alianza::class);
    }

    public function moduloBitacora(): string
    {
        return 'Instituciones aliadas';
    }

    protected function descripcionBitacora(): string
    {
        return 'la institución «'.$this->nombre.'»';
    }

    /**
     * Una institución aliada que ya figura en el EPS de algún estudiante no se elimina.
     */
    public function estaEnUso(): bool
    {
        return $this->alianzas()->withTrashed()->exists();
    }
}
