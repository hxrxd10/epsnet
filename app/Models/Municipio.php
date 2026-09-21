<?php

namespace App\Models;

use App\Concerns\Auditable;
use Database\Factories\MunicipioFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $departamento_id
 * @property string $codigo
 * @property string $nombre
 * @property string|null $latitud
 * @property string|null $longitud
 */
#[Fillable(['departamento_id', 'codigo', 'nombre', 'latitud', 'longitud'])]
class Municipio extends Model
{
    /** @use HasFactory<MunicipioFactory> */
    use Auditable, HasFactory;

    protected $table = 'municipios';

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'latitud' => 'decimal:7',
            'longitud' => 'decimal:7',
        ];
    }

    /**
     * @return BelongsTo<Departamento, $this>
     */
    public function departamento(): BelongsTo
    {
        return $this->belongsTo(Departamento::class);
    }

    /**
     * @return HasMany<UbicacionTerritorial, $this>
     */
    public function ubicaciones(): HasMany
    {
        return $this->hasMany(UbicacionTerritorial::class);
    }

    /**
     * Un municipio en uso no se elimina, para no dejar EPS sin ubicación.
     */
    public function estaEnUso(): bool
    {
        return $this->ubicaciones()->withTrashed()->exists();
    }

    public function moduloBitacora(): string
    {
        return 'Municipios';
    }

    protected function descripcionBitacora(): string
    {
        return 'el municipio «'.$this->nombre.'» ('.$this->codigo.')';
    }
}
