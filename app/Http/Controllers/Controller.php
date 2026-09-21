<?php

namespace App\Http\Controllers;

abstract class Controller
{
    /**
     * Configuración del mapa de Google que usan los formularios con selector de ubicación.
     *
     * @return array{key: string|null, mapId: string}
     */
    protected function googleMaps(): array
    {
        return [
            'key' => config('services.google_maps.key'),
            'mapId' => config('services.google_maps.map_id'),
        ];
    }
}
