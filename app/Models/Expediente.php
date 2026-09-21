<?php

namespace App\Models;

use App\Concerns\Auditable;
use App\Enums\EstadoExpediente;
use App\Enums\ProgramaEps;
use App\Enums\TipoCambioBitacora;
use Database\Factories\ExpedienteFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Carbon;

/**
 * EPS de un estudiante en una carrera concreta; agrupa los registros de los seis ejes.
 *
 * @property int $id
 * @property int $estudiante_id
 * @property int $unidad_academica_id
 * @property ProgramaEps|null $programa
 * @property string $codigo_unidad
 * @property string $codigo_extension
 * @property string $codigo_carrera
 * @property string $nombre_unidad
 * @property string|null $nombre_extension
 * @property string $nombre_carrera
 * @property string|null $nivel_academico
 * @property int $eje_actual
 * @property EstadoExpediente $estado_expediente
 * @property Carbon|null $completado_at
 * @property Carbon|null $verificado_at
 * @property int|null $verificado_por
 * @property Carbon|null $fecha_inicio_eps
 * @property Carbon|null $fecha_fin_eps
 * @property array<string, mixed>|null $datos_epsum
 */
#[Fillable([
    'estudiante_id',
    'unidad_academica_id',
    'programa',
    'codigo_unidad',
    'codigo_extension',
    'codigo_carrera',
    'nombre_unidad',
    'nombre_extension',
    'nombre_carrera',
    'nivel_academico',
    'eje_actual',
    'estado_expediente',
    'completado_at',
    'verificado_at',
    'verificado_por',
    'fecha_inicio_eps',
    'fecha_fin_eps',
    'datos_epsum',
])]
class Expediente extends Model
{
    /** Lo que confirma quien aprueba el EPS; queda anotado en la bitácora. */
    public const string CONSTANCIA_APROBACION = 'Confirmo que los bienes y servicios y todo lo descrito en este EPS está comprobado y que se ejecutó.';

    /** @use HasFactory<ExpedienteFactory> */
    use Auditable, HasFactory;

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'eje_actual' => 1,
        'estado_expediente' => EstadoExpediente::Activo,
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'programa' => ProgramaEps::class,
            'eje_actual' => 'integer',
            'estado_expediente' => EstadoExpediente::class,
            'completado_at' => 'datetime',
            'verificado_at' => 'datetime',
            'fecha_inicio_eps' => 'date',
            'fecha_fin_eps' => 'date',
            'datos_epsum' => 'array',
        ];
    }

    /**
     * @return BelongsTo<Estudiante, $this>
     */
    public function estudiante(): BelongsTo
    {
        return $this->belongsTo(Estudiante::class);
    }

    /**
     * @return BelongsTo<UnidadAcademica, $this>
     */
    public function unidadAcademica(): BelongsTo
    {
        return $this->belongsTo(UnidadAcademica::class);
    }

    /**
     * @return HasMany<BienServicio, $this>
     */
    public function bienesServicios(): HasMany
    {
        return $this->hasMany(BienServicio::class);
    }

    /**
     * @return HasMany<PublicacionInvestigacion, $this>
     */
    public function publicaciones(): HasMany
    {
        return $this->hasMany(PublicacionInvestigacion::class);
    }

    /**
     * @return HasMany<TransferenciaConocimiento, $this>
     */
    public function transferencias(): HasMany
    {
        return $this->hasMany(TransferenciaConocimiento::class);
    }

    /**
     * @return HasMany<UbicacionTerritorial, $this>
     */
    public function ubicaciones(): HasMany
    {
        return $this->hasMany(UbicacionTerritorial::class);
    }

    /**
     * @return HasMany<ActorParticipante, $this>
     */
    public function actores(): HasMany
    {
        return $this->hasMany(ActorParticipante::class);
    }

    /**
     * Instituciones aliadas (ministerios, ONG, socios) que cooperaron con el proyecto.
     *
     * @return HasMany<Alianza, $this>
     */
    public function alianzas(): HasMany
    {
        return $this->hasMany(Alianza::class);
    }

    /**
     * @return HasMany<SeguimientoImpacto, $this>
     */
    public function seguimientos(): HasMany
    {
        return $this->hasMany(SeguimientoImpacto::class);
    }

    /**
     * Documento con el que el estudiante valida su EPS.
     *
     * @return MorphOne<Adjunto, $this>
     */
    public function ordenImpresion(): MorphOne
    {
        return $this->morphOne(Adjunto::class, 'entidad', 'entidad_tipo', 'entidad_id')
            ->where('categoria', Adjunto::ORDEN_IMPRESION);
    }

    /**
     * @return MorphMany<Adjunto, $this>
     */
    public function adjuntos(): MorphMany
    {
        return $this->morphMany(Adjunto::class, 'entidad', 'entidad_tipo', 'entidad_id');
    }

    /**
     * Expedientes cuya información es válida para las estadísticas (completos o verificados).
     *
     * @param  Builder<Expediente>  $query
     */
    #[Scope]
    protected function validos(Builder $query): void
    {
        $query->whereIn('estado_expediente', array_map(
            fn (EstadoExpediente $estado): string => $estado->value,
            EstadoExpediente::validos(),
        ));
    }

    /**
     * Expedientes que el usuario puede consultar: todos para DIGEU y los de su unidad para una
     * unidad académica; ninguno para el resto.
     *
     * @param  Builder<Expediente>  $query
     */
    #[Scope]
    protected function visiblesPara(Builder $query, User $usuario): void
    {
        if ($usuario->esAdministrador()) {
            return;
        }

        if ($usuario->esUnidadAcademica() && $usuario->unidadAcademica !== null) {
            $query->where('unidad_academica_id', $usuario->unidadAcademica->id);

            return;
        }

        $query->whereKey([]);
    }

    /**
     * Aprobación de la unidad académica (o DIGEU): el expediente queda verificado.
     */
    public function verificarPor(User $usuario): void
    {
        $this->update([
            'estado_expediente' => EstadoExpediente::Verificado,
            'verificado_at' => now(),
            'verificado_por' => $usuario->id,
        ]);
    }

    /**
     * Retira la aprobación: el expediente vuelve a estar completo.
     */
    public function quitarVerificacion(): void
    {
        $this->update([
            'estado_expediente' => EstadoExpediente::Completo,
            'verificado_at' => null,
            'verificado_por' => null,
        ]);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function verificadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verificado_por');
    }

    /**
     * «EPS de Juan Pérez (Licenciatura en Pedagogía)»: cómo se nombra este EPS en la bitácora.
     */
    public function resumenBitacora(): string
    {
        return "EPS de {$this->estudiante?->nombre_completo} ({$this->nombre_carrera})";
    }

    public function moduloBitacora(): string
    {
        return 'Expedientes (EPS)';
    }

    protected function descripcionBitacora(): string
    {
        return 'el '.$this->resumenBitacora();
    }

    /**
     * @return list<string>
     */
    protected function atributosNoAuditablesPropios(): array
    {
        return ['eje_actual', 'datos_epsum'];
    }

    /**
     * Las aprobaciones y sus retiros los anota quien las hace, junto con la constancia del acepto.
     *
     * @param  array<string, mixed>  $cambios
     */
    protected function tipoBitacora(TipoCambioBitacora $tipo, array $cambios): ?TipoCambioBitacora
    {
        if (isset($cambios['estado_expediente'])
            && ($cambios['estado_expediente'] === EstadoExpediente::Verificado->value
                || $this->getRawOriginal('estado_expediente') === EstadoExpediente::Verificado->value)) {
            return null;
        }

        return $tipo;
    }
}
