<?php

namespace App\Models;

use App\Concerns\Auditable;
use App\Services\RegistroAcademico\DetalleAcademico;
use Database\Factories\CarreraFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Carrera registrada por DIGEU desde el servicio web de Registro y Estadística. Los expedientes se
 * relacionan con ella por sus códigos (unidad, extensión y carrera).
 *
 * @property int $id
 * @property int $unidad_academica_id
 * @property string $codigo_unidad
 * @property string $codigo_extension
 * @property string $codigo_carrera
 * @property string $nombre
 * @property string|null $nombre_extension
 * @property string|null $nivel_academico
 */
#[Fillable([
    'unidad_academica_id',
    'codigo_unidad',
    'codigo_extension',
    'codigo_carrera',
    'nombre',
    'nombre_extension',
    'nivel_academico',
])]
class Carrera extends Model
{
    /** @use HasFactory<CarreraFactory> */
    use Auditable, HasFactory;

    /**
     * @return BelongsTo<UnidadAcademica, $this>
     */
    public function unidadAcademica(): BelongsTo
    {
        return $this->belongsTo(UnidadAcademica::class);
    }

    /**
     * Supervisores de unidad académica que pueden ver y aprobar los EPS de esta carrera.
     *
     * @return BelongsToMany<User, $this>
     */
    public function supervisores(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }

    /**
     * «unidad-extensión-carrera», la misma clave que usa el registro académico.
     */
    public function clave(): string
    {
        return "{$this->codigo_unidad}-{$this->codigo_extension}-{$this->codigo_carrera}";
    }

    /**
     * Si el expediente es de esta carrera.
     */
    public function incluye(Expediente $expediente): bool
    {
        return $expediente->codigo_unidad === $this->codigo_unidad
            && $expediente->codigo_extension === $this->codigo_extension
            && $expediente->codigo_carrera === $this->codigo_carrera;
    }

    /**
     * Registra la carrera del servicio web (con su unidad académica) si aún no existe.
     */
    public static function registrar(DetalleAcademico $detalle): self
    {
        return self::firstOrCreate(
            [
                'codigo_unidad' => $detalle->unidad,
                'codigo_extension' => $detalle->extension,
                'codigo_carrera' => $detalle->carrera,
            ],
            [
                'unidad_academica_id' => UnidadAcademica::paraNombre($detalle->nombreUnidad)->id,
                'nombre' => $detalle->nombreCarrera,
                'nombre_extension' => $detalle->nombreExtension,
                'nivel_academico' => $detalle->grado,
            ],
        );
    }

    public function moduloBitacora(): string
    {
        return 'Carreras';
    }

    protected function descripcionBitacora(): string
    {
        return 'la carrera «'.$this->nombre.'»'.($this->nombre_extension ? " ({$this->nombre_extension})" : '');
    }
}
