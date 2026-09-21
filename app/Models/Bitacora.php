<?php

namespace App\Models;

use App\Enums\TipoCambioBitacora;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use LogicException;

/**
 * Registro de auditoría: quién hizo qué, cuándo y dónde. Una vez escrito no se edita ni se elimina.
 *
 * @property int $id
 * @property int|null $usuario_id
 * @property int|null $estudiante_id
 * @property string|null $usuario_nombre
 * @property string|null $usuario_correo
 * @property string|null $usuario_rol
 * @property string $modulo
 * @property TipoCambioBitacora $tipo_cambio
 * @property string|null $entidad_tipo
 * @property int|null $entidad_id
 * @property string|null $detalle
 * @property array<string, mixed>|null $valores_anteriores
 * @property array<string, mixed>|null $valores_nuevos
 * @property string|null $ip
 * @property Carbon $fecha_hora
 */
#[Fillable([
    'usuario_id',
    'estudiante_id',
    'usuario_nombre',
    'usuario_correo',
    'usuario_rol',
    'modulo',
    'tipo_cambio',
    'entidad_tipo',
    'entidad_id',
    'detalle',
    'valores_anteriores',
    'valores_nuevos',
    'ip',
    'fecha_hora',
])]
class Bitacora extends Model
{
    public $timestamps = false;

    protected static function booted(): void
    {
        static::updating(fn () => throw new LogicException('Los registros de la bitácora no se pueden editar.'));
        static::deleting(fn () => throw new LogicException('Los registros de la bitácora no se pueden eliminar.'));
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tipo_cambio' => TipoCambioBitacora::class,
            'valores_anteriores' => 'array',
            'valores_nuevos' => 'array',
            'fecha_hora' => 'datetime',
        ];
    }

    /**
     * Anota una acción del usuario con sesión (o, si no hay sesión, del visitante).
     *
     * @param  array<string, mixed>|null  $anteriores
     * @param  array<string, mixed>|null  $nuevos
     */
    public static function registrar(
        string $modulo,
        TipoCambioBitacora $tipo,
        string $detalle,
        ?Model $entidad = null,
        ?array $anteriores = null,
        ?array $nuevos = null,
        ?User $actor = null,
    ): ?self {
        if (Auth::user() === null && request()->route() === null) {
            // Procesos de consola (siembras, tareas): no es una acción de nadie con sesión ni de un visitante.
            return null;
        }

        $actor ??= Auth::user();

        return self::create([
            // Un usuario que acaba de eliminar su propia cuenta ya no existe: queda el nombre y el correo.
            'usuario_id' => $actor?->exists ? $actor->id : null,
            'estudiante_id' => ! $actor?->exists ? null : Estudiante::where('usuario_id', $actor->id)->value('id'),
            'usuario_nombre' => $actor?->name ?? 'Visitante',
            'usuario_correo' => $actor?->email,
            'usuario_rol' => $actor?->rol_id === null ? null : Rol::whereKey($actor->rol_id)->value('clave'),
            'modulo' => $modulo,
            'tipo_cambio' => $tipo,
            'entidad_tipo' => $entidad === null ? null : class_basename($entidad),
            'entidad_id' => $entidad?->getKey(),
            'detalle' => $detalle,
            'valores_anteriores' => $anteriores === [] ? null : $anteriores,
            'valores_nuevos' => $nuevos === [] ? null : $nuevos,
            'ip' => request()->ip(),
            'fecha_hora' => now(),
        ]);
    }
}
