<?php

namespace Database\Seeders;

use App\Enums\TipoCatalogo;
use App\Models\Catalogo;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CatalogoBienesServiciosSeeder extends Seeder
{
    /**
     * Los 17 Objetivos de Desarrollo Sostenible, por su número.
     */
    public const array ODS = [
        1 => 'Fin de la pobreza',
        2 => 'Hambre cero',
        3 => 'Salud y bienestar',
        4 => 'Educación de calidad',
        5 => 'Igualdad de género',
        6 => 'Agua limpia y saneamiento',
        7 => 'Energía asequible y no contaminante',
        8 => 'Trabajo decente y crecimiento económico',
        9 => 'Industria, innovación e infraestructura',
        10 => 'Reducción de las desigualdades',
        11 => 'Ciudades y comunidades sostenibles',
        12 => 'Producción y consumo responsables',
        13 => 'Acción por el clima',
        14 => 'Vida submarina',
        15 => 'Vida de ecosistemas terrestres',
        16 => 'Paz, justicia e instituciones sólidas',
        17 => 'Alianzas para lograr los objetivos',
    ];

    /**
     * Catálogo de los bienes y servicios más usados, con su valor promedio y anclados a su línea de
     * intervención, política pública, área de intervención y ODS. Es idempotente: no pisa lo que DIGEU
     * haya cambiado; a un elemento que ya existía solo le completa lo que le falte.
     */
    public function run(): void
    {
        /** @var list<array{0: string, 1: string, 2: float, 3: string, 4: string, 5: string, 6: int}> $bienes */
        $bienes = require __DIR__.'/data/bienes_servicios.php';

        DB::transaction(function () use ($bienes): void {
            $ods = collect(self::ODS)->mapWithKeys(fn (string $nombre, int $numero): array => [
                $numero => $this->elemento(TipoCatalogo::Ods, self::nombreOds($numero)),
            ]);

            foreach ($bienes as [$nombre, $categoria, $valor, $linea, $politica, $area, $numeroOds]) {
                $vinculos = [
                    'linea_intervencion_id' => $this->elemento(TipoCatalogo::LineasIntervencion, $linea),
                    'politica_publica_id' => $this->elemento(TipoCatalogo::PoliticasPublicas, $politica),
                    'area_intervencion_id' => $this->elemento(TipoCatalogo::AreasIntervencion, $area),
                    'ods_id' => $ods[$numeroOds],
                ];

                $bien = Catalogo::firstOrCreate(
                    ['catalogo' => TipoCatalogo::BienesServicios->value, 'nombre' => $nombre],
                    ['categoria' => $categoria, 'valor_base' => $valor, ...$vinculos, 'activo' => true],
                );

                if (! $bien->wasRecentlyCreated) {
                    $faltantes = array_filter(
                        ['valor_base' => $valor, 'categoria' => $categoria, ...$vinculos],
                        fn (string $campo): bool => $bien->{$campo} === null,
                        ARRAY_FILTER_USE_KEY,
                    );

                    $faltantes === [] || $bien->update($faltantes);
                }
            }
        });
    }

    /**
     * «ODS 6: Agua limpia y saneamiento».
     */
    public static function nombreOds(int $numero): string
    {
        return "ODS {$numero}: ".self::ODS[$numero];
    }

    /**
     * Id del elemento con ese nombre en el catálogo; se crea si aún no existe.
     */
    private function elemento(TipoCatalogo $catalogo, string $nombre): int
    {
        return Catalogo::firstOrCreate(['catalogo' => $catalogo->value, 'nombre' => $nombre], ['activo' => true])->id;
    }
}
