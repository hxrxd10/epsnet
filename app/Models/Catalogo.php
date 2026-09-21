<?php

namespace App\Models;

use App\Enums\TipoCatalogo;
use Database\Factories\CatalogoFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Elemento de un catálogo administrado (bienes y servicios, acciones, etc.).
 *
 * @property int $id
 * @property TipoCatalogo $catalogo
 * @property string $nombre
 * @property string|null $descripcion
 * @property string|null $categoria
 * @property bool $activo
 */
#[Fillable(['catalogo', 'nombre', 'descripcion', 'categoria', 'activo'])]
class Catalogo extends Model
{
    /** @use HasFactory<CatalogoFactory> */
    use HasFactory;

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'activo' => true,
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'catalogo' => TipoCatalogo::class,
            'activo' => 'boolean',
        ];
    }

    /**
     * @param  Builder<Catalogo>  $query
     */
    #[Scope]
    protected function delCatalogo(Builder $query, TipoCatalogo $catalogo): void
    {
        $query->where('catalogo', $catalogo->value);
    }

    /**
     * @param  Builder<Catalogo>  $query
     */
    #[Scope]
    protected function activos(Builder $query): void
    {
        $query->where('activo', true);
    }

    /**
     * @return HasMany<BienServicio, $this>
     */
    public function bienesServicios(): HasMany
    {
        return $this->hasMany(BienServicio::class);
    }

    /**
     * @return HasMany<TransferenciaConocimiento, $this>
     */
    public function transferencias(): HasMany
    {
        return $this->hasMany(TransferenciaConocimiento::class);
    }

    /**
     * Si algún registro de los estudiantes ya usa este elemento (entonces no puede eliminarse).
     */
    public function estaEnUso(): bool
    {
        return $this->bienesServicios()->withTrashed()->exists()
            || $this->transferencias()->withTrashed()->exists();
    }
}
