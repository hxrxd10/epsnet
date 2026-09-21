<?php

namespace App\Models;

use Database\Factories\InstitucionReceptoraFactory;
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
#[Table('instituciones_receptoras')]
#[Fillable([
    'nombre',
    'tipo',
    'nombre_contacto',
    'correo_contacto',
    'telefono_contacto',
    'direccion',
])]
class InstitucionReceptora extends Model
{
    /** @use HasFactory<InstitucionReceptoraFactory> */
    use HasFactory, SoftDeletes;

    /**
     * @return HasMany<ActorParticipante, $this>
     */
    public function actores(): HasMany
    {
        return $this->hasMany(ActorParticipante::class);
    }
}
