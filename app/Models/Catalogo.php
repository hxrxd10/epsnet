<?php

namespace App\Models;

use App\Concerns\Auditable;
use App\Enums\TipoCatalogo;
use Database\Factories\CatalogoFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Elemento de un catálogo administrado (bienes y servicios, acciones, etc.).
 *
 * @property int $id
 * @property TipoCatalogo $catalogo
 * @property string $nombre
 * @property string|null $descripcion
 * @property string|null $categoria
 * @property string|null $valor_base Quetzales por unidad (solo bienes y servicios).
 * @property int|null $linea_intervencion_id
 * @property int|null $politica_publica_id
 * @property int|null $area_intervencion_id
 * @property int|null $ods_id
 * @property bool $activo
 */
#[Fillable([
    'catalogo',
    'nombre',
    'descripcion',
    'categoria',
    'valor_base',
    'linea_intervencion_id',
    'politica_publica_id',
    'area_intervencion_id',
    'ods_id',
    'activo',
])]
class Catalogo extends Model
{
    /** @use HasFactory<CatalogoFactory> */
    use Auditable, HasFactory;

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
            'valor_base' => 'decimal:2',
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
     * Bienes y servicios que incluyeron a esta población vulnerable entre sus beneficiarios.
     *
     * @return BelongsToMany<BienServicio, $this>
     */
    public function bienesConPoblacion(): BelongsToMany
    {
        return $this->belongsToMany(BienServicio::class, 'bien_servicio_poblacion', 'catalogo_id', 'bien_servicio_id');
    }

    /**
     * @return HasMany<TransferenciaConocimiento, $this>
     */
    public function transferencias(): HasMany
    {
        return $this->hasMany(TransferenciaConocimiento::class);
    }

    /**
     * @return BelongsTo<Catalogo, $this>
     */
    public function lineaIntervencion(): BelongsTo
    {
        return $this->belongsTo(self::class, 'linea_intervencion_id');
    }

    /**
     * @return BelongsTo<Catalogo, $this>
     */
    public function politicaPublica(): BelongsTo
    {
        return $this->belongsTo(self::class, 'politica_publica_id');
    }

    /**
     * @return BelongsTo<Catalogo, $this>
     */
    public function areaIntervencion(): BelongsTo
    {
        return $this->belongsTo(self::class, 'area_intervencion_id');
    }

    /**
     * @return BelongsTo<Catalogo, $this>
     */
    public function ods(): BelongsTo
    {
        return $this->belongsTo(self::class, 'ods_id');
    }

    /**
     * Si algún registro de los estudiantes ya usa este elemento, o algún bien o servicio está anclado
     * a él (una línea, política, área u ODS): entonces no puede eliminarse.
     */
    public function estaEnUso(): bool
    {
        return $this->bienesServicios()->withTrashed()->exists()
            || $this->transferencias()->withTrashed()->exists()
            || $this->bienesConPoblacion()->exists()
            || self::query()->where(fn (Builder $consulta) => $consulta
                ->where('linea_intervencion_id', $this->id)
                ->orWhere('politica_publica_id', $this->id)
                ->orWhere('area_intervencion_id', $this->id)
                ->orWhere('ods_id', $this->id))->exists();
    }

    public function moduloBitacora(): string
    {
        return 'Catálogo: '.$this->catalogo->etiqueta();
    }

    protected function descripcionBitacora(): string
    {
        return 'el elemento «'.$this->nombre.'»';
    }
}
