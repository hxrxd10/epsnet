<?php

namespace App\Models;

use App\Concerns\Auditable;
use App\Services\RegistroAcademico\DetalleAcademico;
use Database\Factories\EstudianteFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * Persona identificada por su registro académico (carné). Su identidad se verifica una sola vez
 * con carné y DPI contra el servicio web de Registro y Estadística; después ingresa con su usuario.
 *
 * @property int $id
 * @property int|null $usuario_id
 * @property string $carnet
 * @property string $nombre1
 * @property string|null $nombre2
 * @property string|null $nombre3
 * @property string $apellido1
 * @property string|null $apellido2
 * @property string|null $direccion
 * @property string|null $codigo_nacionalidad
 * @property string|null $nacionalidad
 * @property list<array<string, string|null>>|null $carreras
 * @property Carbon|null $ultima_consulta_at
 * @property-read string $nombre_completo
 */
#[Fillable([
    'carnet',
    'nombre1',
    'nombre2',
    'nombre3',
    'apellido1',
    'apellido2',
    'direccion',
    'codigo_nacionalidad',
    'nacionalidad',
    'carreras',
    'ultima_consulta_at',
])]
class Estudiante extends Model
{
    /** @use HasFactory<EstudianteFactory> */
    use Auditable, HasFactory, SoftDeletes;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'carreras' => 'array',
            'ultima_consulta_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    /**
     * Carreras del estudiante según la última consulta al registro académico.
     *
     * @return Collection<int, DetalleAcademico>
     */
    public function carrerasAcademicas(): Collection
    {
        return collect($this->carreras ?? [])
            ->map(fn (array $carrera): DetalleAcademico => DetalleAcademico::fromArray($carrera))
            ->values();
    }

    /**
     * @return HasMany<Expediente, $this>
     */
    public function expedientes(): HasMany
    {
        return $this->hasMany(Expediente::class);
    }

    /**
     * @return Attribute<string, never>
     */
    protected function nombreCompleto(): Attribute
    {
        return Attribute::get(fn (): string => collect([
            $this->nombre1,
            $this->nombre2,
            $this->nombre3,
            $this->apellido1,
            $this->apellido2,
        ])->filter()->implode(' '));
    }

    public function moduloBitacora(): string
    {
        return 'Estudiantes';
    }

    protected function descripcionBitacora(): string
    {
        return 'al estudiante '.$this->nombre_completo.' (carné '.$this->carnet.')';
    }

    /**
     * @return list<string>
     */
    protected function atributosNoAuditablesPropios(): array
    {
        return ['ultima_consulta_at'];
    }
}
