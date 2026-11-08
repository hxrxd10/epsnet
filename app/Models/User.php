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
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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
 * @property int|null $unidad_academica_id
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
     * Unidad académica a la que pertenece este usuario (rol de unidad académica).
     *
     * @return BelongsTo<UnidadAcademica, $this>
     */
    public function unidadAcademica(): BelongsTo
    {
        return $this->belongsTo(UnidadAcademica::class);
    }

    /**
     * Carreras que este supervisor puede ver y aprobar. Sin carreras asignadas ve toda su unidad.
     *
     * @return BelongsToMany<Carrera, $this>
     */
    public function carreras(): BelongsToMany
    {
        return $this->belongsToMany(Carrera::class)->withTimestamps();
    }

    /**
     * Si el usuario puede consultar (y aprobar) el expediente: DIGEU todos; una unidad académica los
     * de su unidad, limitados a sus carreras cuando tiene carreras asignadas.
     */
    public function alcanzaExpediente(Expediente $expediente): bool
    {
        if ($this->esAdministrador()) {
            return true;
        }

        if (! $this->esUnidadAcademica() || $this->unidad_academica_id === null || $this->unidad_academica_id !== $expediente->unidad_academica_id) {
            return false;
        }

        return $this->carreras->isEmpty() || $this->carreras->contains(fn (Carrera $carrera): bool => $carrera->incluye($expediente));
    }

    /**
     * Cuenta principal de una unidad académica: tiene su unidad y ninguna carrera asignada, así que ve
     * toda la unidad. Crea las cuentas de los supervisores de su unidad y agrega sus carreras.
     */
    public function esPrincipalDeUnidad(): bool
    {
        return $this->esUnidadAcademica() && $this->unidad_academica_id !== null && $this->carreras->isEmpty();
    }

    /**
     * Si puede crear supervisores y agregar carreras: DIGEU en todas las unidades y la cuenta principal
     * de una unidad académica, solo en la suya.
     */
    public function gestionaSupervisores(): bool
    {
        return $this->esAdministrador() || $this->esPrincipalDeUnidad();
    }

    /**
     * Si puede cambiar el acceso o el correo de la cuenta: DIGEU, el de cualquiera; la cuenta principal,
     * solo el de los supervisores de su unidad (los que tienen carreras asignadas). Nadie el suyo.
     */
    public function gestionaCuentaDe(User $cuenta): bool
    {
        if ($cuenta->is($this)) {
            return false;
        }

        if ($this->esAdministrador()) {
            return true;
        }

        return $this->esPrincipalDeUnidad()
            && $cuenta->esUnidadAcademica()
            && $cuenta->unidad_academica_id === $this->unidad_academica_id
            && $cuenta->carreras->isNotEmpty();
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

    /**
     * Si el usuario puede entrar a las consultas (estadísticas, ODS, repositorio e investigaciones):
     * DIGEU y las unidades académicas. Los estudiantes solo ven su propio EPS y los invitados nada.
     */
    public function puedeConsultar(): bool
    {
        return $this->esAdministrador() || $this->esUnidadAcademica();
    }

    /**
     * Si el expediente entra en lo que el usuario ve en las consultas.
     */
    public function consultaExpediente(Expediente $expediente): bool
    {
        if ($this->esUnidadAcademica()) {
            return $this->alcanzaExpediente($expediente);
        }

        return $this->puedeConsultar();
    }

    /**
     * Qué EPS consulta el usuario, para mostrarlo en el panel: «Todas las unidades académicas»,
     * el nombre de su unidad o su unidad con las carreras que supervisa.
     */
    public function ambitoDeConsulta(): string
    {
        if ($this->esAdministrador()) {
            return 'Todas las unidades académicas';
        }

        if ($this->unidadAcademica === null) {
            return 'Sin unidad asignada';
        }

        if ($this->carreras->isEmpty()) {
            return $this->unidadAcademica->nombre;
        }

        return $this->unidadAcademica->nombre.' · '.$this->carreras->pluck('nombre')->implode(', ');
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
        if (array_key_exists('unidad_academica_id', $valores)) {
            $valores['unidad_academica'] = UnidadAcademica::find($valores['unidad_academica_id'])?->nombre;
            unset($valores['unidad_academica_id']);
        }

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
