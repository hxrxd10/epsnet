<?php

namespace App\Concerns;

use App\Enums\TipoCambioBitacora;
use App\Models\Bitacora;
use App\Models\User;
use BackedEnum;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

/**
 * Deja constancia en la bitácora de quién creó, editó o eliminó un registro, y cuándo.
 *
 * El modelo indica en qué módulo se ve el cambio y cómo se describe; lo demás es automático.
 *
 * @mixin Model
 */
trait Auditable
{
    private const int LIMITE_VALOR = 300;

    public static function bootAuditable(): void
    {
        static::created(fn (Model $modelo) => $modelo->anotarEnBitacora(TipoCambioBitacora::Creacion));
        static::updated(fn (Model $modelo) => $modelo->anotarEnBitacora(TipoCambioBitacora::Edicion));
        static::deleted(fn (Model $modelo) => $modelo->anotarEnBitacora(TipoCambioBitacora::Eliminacion));
    }

    /** Módulo del sistema donde se hizo el cambio, tal como se muestra en la bitácora. */
    abstract public function moduloBitacora(): string;

    /** Qué registro es, en lenguaje natural: «el bien "Pupitres" del EPS de …». */
    abstract protected function descripcionBitacora(): string;

    /**
     * Atributos que no interesan a la auditoría (o que no deben guardarse, como las contraseñas).
     *
     * @return list<string>
     */
    protected function atributosNoAuditables(): array
    {
        return [
            'created_at', 'updated_at', 'deleted_at',
            'password', 'remember_token', 'two_factor_secret', 'two_factor_recovery_codes', 'two_factor_confirmed_at',
            ...$this->atributosNoAuditablesPropios(),
        ];
    }

    /**
     * Atributos adicionales del modelo que no se auditan.
     *
     * @return list<string>
     */
    protected function atributosNoAuditablesPropios(): array
    {
        return [];
    }

    /**
     * Permite reclasificar el cambio o, devolviendo null, omitirlo (cuando otro código ya lo anota).
     *
     * @param  array<string, mixed>  $cambios
     */
    protected function tipoBitacora(TipoCambioBitacora $tipo, array $cambios): ?TipoCambioBitacora
    {
        return $tipo;
    }

    /**
     * Traduce valores técnicos (p. ej. ids) a algo legible antes de guardarlos.
     *
     * @param  array<string, mixed>  $valores
     * @return array<string, mixed>
     */
    protected function valoresBitacora(array $valores): array
    {
        return $valores;
    }

    /** Quién realiza el cambio cuando no hay sesión (p. ej. alguien que se registra). */
    protected function actorBitacora(): ?User
    {
        return null;
    }

    private function anotarEnBitacora(TipoCambioBitacora $tipo): void
    {
        $ignorados = $this->atributosNoAuditables();
        $anteriores = null;
        $nuevos = null;
        $cambios = [];

        if ($tipo === TipoCambioBitacora::Edicion) {
            $cambios = Arr::except($this->getChanges(), $ignorados);

            if ($cambios === []) {
                return;
            }

            $anteriores = Arr::only($this->getOriginal(), array_keys($cambios));
            $nuevos = $cambios;
        } elseif ($tipo === TipoCambioBitacora::Creacion) {
            $nuevos = Arr::except($this->getAttributes(), $ignorados);
        } else {
            $anteriores = Arr::except($this->getAttributes(), $ignorados);
        }

        $tipo = $this->tipoBitacora($tipo, $cambios);

        if ($tipo === null) {
            return;
        }

        $detalle = "{$tipo->etiqueta()} {$this->descripcionBitacora()}";

        if ($cambios !== []) {
            $detalle .= ' · Campos: '.implode(', ', array_keys($this->valoresBitacora($cambios)));
        }

        Bitacora::registrar(
            $this->moduloBitacora(),
            $tipo,
            $detalle,
            $this,
            $anteriores === null ? null : $this->legible($this->valoresBitacora($anteriores)),
            $nuevos === null ? null : $this->legible($this->valoresBitacora($nuevos)),
            $this->actorBitacora(),
        );
    }

    /**
     * @param  array<string, mixed>  $valores
     * @return array<string, mixed>
     */
    private function legible(array $valores): array
    {
        return array_map(function (mixed $valor): mixed {
            $valor = match (true) {
                $valor instanceof BackedEnum => $valor->value,
                $valor instanceof DateTimeInterface => $valor->format('Y-m-d H:i:s'),
                is_array($valor) => json_encode($valor, JSON_UNESCAPED_UNICODE),
                default => $valor,
            };

            return is_string($valor) ? mb_strimwidth($valor, 0, self::LIMITE_VALOR, '…') : $valor;
        }, $valores);
    }
}
