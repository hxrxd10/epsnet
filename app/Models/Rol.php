<?php

namespace App\Models;

use App\Enums\ClaveRol;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $clave
 * @property string $nombre
 * @property string|null $descripcion
 */
#[Table('roles')]
#[Fillable(['clave', 'nombre', 'descripcion'])]
class Rol extends Model
{
    /**
     * Rol registrado para la clave indicada (lo crea si aún no existe).
     */
    public static function delSistema(ClaveRol $clave): self
    {
        return self::firstOrCreate(
            ['clave' => $clave->value],
            ['nombre' => $clave->etiqueta(), 'descripcion' => $clave->descripcion()],
        );
    }

    /**
     * @return HasMany<User, $this>
     */
    public function usuarios(): HasMany
    {
        return $this->hasMany(User::class, 'rol_id');
    }
}
