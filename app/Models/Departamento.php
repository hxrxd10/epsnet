<?php

namespace App\Models;

use Database\Factories\DepartamentoFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $codigo
 * @property string $nombre
 * @property string $cabecera
 * @property string|null $latitud
 * @property string|null $longitud
 */
#[Fillable(['codigo', 'nombre', 'cabecera', 'latitud', 'longitud'])]
class Departamento extends Model
{
    /** @use HasFactory<DepartamentoFactory> */
    use HasFactory;

    /**
     * @return HasMany<Municipio, $this>
     */
    public function municipios(): HasMany
    {
        return $this->hasMany(Municipio::class);
    }

    /**
     * Un departamento con municipios o ubicaciones no se elimina.
     */
    public function estaEnUso(): bool
    {
        return $this->municipios()->exists()
            || UbicacionTerritorial::withTrashed()->where('departamento_id', $this->id)->exists();
    }
}
