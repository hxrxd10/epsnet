<?php

namespace App\Models;

use App\Concerns\Auditable;
use App\Enums\ClaveRol;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\TwoFactorAuthenticatable;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $two_factor_secret
 * @property string|null $two_factor_recovery_codes
 * @property Carbon|null $two_factor_confirmed_at
 * @property string|null $remember_token
 * @property int|null $rol_id
 * @property bool $activo
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'two_factor_secret', 'two_factor_recovery_codes', 'remember_token'])]
class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use Auditable, HasFactory, Notifiable, TwoFactorAuthenticatable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_confirmed_at' => 'datetime',
            'activo' => 'boolean',
        ];
    }

    /**
     * @return BelongsTo<Rol, $this>
     */
    public function rol(): BelongsTo
    {
        return $this->belongsTo(Rol::class, 'rol_id');
    }

    /**
     * Perfil de estudiante en EPS de este usuario (solo existe si su rol es estudiante).
     *
     * @return HasOne<Estudiante, $this>
     */
    public function estudiante(): HasOne
    {
        return $this->hasOne(Estudiante::class, 'usuario_id');
    }

    /**
     * Unidad académica que administra este usuario (rol de unidad académica).
     *
     * @return HasOne<UnidadAcademica, $this>
     */
    public function unidadAcademica(): HasOne
    {
        return $this->hasOne(UnidadAcademica::class, 'administrador_id');
    }

    public function tieneRol(ClaveRol $clave): bool
    {
        return $this->rol?->clave === $clave->value;
    }

    public function esEstudiante(): bool
    {
        return $this->tieneRol(ClaveRol::Estudiante);
    }

    public function esAdministrador(): bool
    {
        return $this->tieneRol(ClaveRol::Digeu);
    }

    public function esUnidadAcademica(): bool
    {
        return $this->tieneRol(ClaveRol::UnidadAcademica);
    }

    public function moduloBitacora(): string
    {
        return 'Usuarios';
    }

    protected function descripcionBitacora(): string
    {
        return "al usuario {$this->name} ({$this->email})";
    }

    /**
     * @param  array<string, mixed>  $valores
     * @return array<string, mixed>
     */
    protected function valoresBitacora(array $valores): array
    {
        if (array_key_exists('rol_id', $valores)) {
            $valores['rol'] = Rol::find($valores['rol_id'])?->clave;
            unset($valores['rol_id']);
        }

        return $valores;
    }

    /**
     * Quien se registra por su cuenta aún no tiene sesión: el cambio es suyo.
     */
    protected function actorBitacora(): ?self
    {
        return Auth::check() ? null : $this;
    }
}
