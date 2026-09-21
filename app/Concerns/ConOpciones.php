<?php

namespace App\Concerns;

/**
 * Para enums con método etiqueta(): expone sus casos como opciones de lista desplegable.
 */
trait ConOpciones
{
    /**
     * @return list<array{value: string, label: string}>
     */
    public static function opciones(): array
    {
        return array_map(
            fn (self $caso): array => ['value' => $caso->value, 'label' => $caso->etiqueta()],
            self::cases(),
        );
    }
}
