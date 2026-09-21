<?php

namespace Database\Seeders;

use App\Models\Departamento;
use Illuminate\Database\Seeder;

class DepartamentoSeeder extends Seeder
{
    /**
     * Catálogo de los 22 departamentos de Guatemala con su cabecera departamental.
     */
    public function run(): void
    {
        $departamentos = [
            ['01', 'Guatemala', 'Ciudad de Guatemala', 14.63, -90.51],
            ['02', 'El Progreso', 'Guastatoya', 14.85, -90.07],
            ['03', 'Sacatepéquez', 'Antigua Guatemala', 14.56, -90.73],
            ['04', 'Chimaltenango', 'Chimaltenango', 14.66, -90.82],
            ['05', 'Escuintla', 'Escuintla', 14.30, -90.79],
            ['06', 'Santa Rosa', 'Cuilapa', 14.28, -90.30],
            ['07', 'Sololá', 'Sololá', 14.77, -91.18],
            ['08', 'Totonicapán', 'Totonicapán', 14.91, -91.36],
            ['09', 'Quetzaltenango', 'Quetzaltenango', 14.83, -91.52],
            ['10', 'Suchitepéquez', 'Mazatenango', 14.53, -91.50],
            ['11', 'Retalhuleu', 'Retalhuleu', 14.54, -91.68],
            ['12', 'San Marcos', 'San Marcos', 14.97, -91.79],
            ['13', 'Huehuetenango', 'Huehuetenango', 15.32, -91.47],
            ['14', 'Quiché', 'Santa Cruz del Quiché', 15.03, -91.15],
            ['15', 'Baja Verapaz', 'Salamá', 15.10, -90.32],
            ['16', 'Alta Verapaz', 'Cobán', 15.47, -90.37],
            ['17', 'Petén', 'Flores', 16.93, -89.89],
            ['18', 'Izabal', 'Puerto Barrios', 15.73, -88.59],
            ['19', 'Zacapa', 'Zacapa', 14.97, -89.53],
            ['20', 'Chiquimula', 'Chiquimula', 14.80, -89.54],
            ['21', 'Jalapa', 'Jalapa', 14.63, -89.99],
            ['22', 'Jutiapa', 'Jutiapa', 14.29, -89.90],
        ];

        foreach ($departamentos as [$codigo, $nombre, $cabecera, $latitud, $longitud]) {
            Departamento::updateOrCreate(
                ['codigo' => $codigo],
                ['nombre' => $nombre, 'cabecera' => $cabecera, 'latitud' => $latitud, 'longitud' => $longitud],
            );
        }
    }
}
