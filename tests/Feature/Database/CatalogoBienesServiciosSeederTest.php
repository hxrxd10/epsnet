<?php

use App\Enums\TipoCatalogo;
use App\Models\Catalogo;
use Database\Seeders\CatalogoBienesServiciosSeeder;
use Database\Seeders\PoblacionVulnerableSeeder;

function bienDelCatalogo(string $nombre): Catalogo
{
    return Catalogo::delCatalogo(TipoCatalogo::BienesServicios)->where('nombre', $nombre)->sole();
}

it('siembra los bienes y servicios más usados con su valor promedio y sus vínculos', function () {
    $this->seed(CatalogoBienesServiciosSeeder::class);

    $investigacion = bienDelCatalogo('Investigación en agua y saneamiento');

    expect(Catalogo::delCatalogo(TipoCatalogo::BienesServicios)->count())->toBe(239)
        ->and(Catalogo::delCatalogo(TipoCatalogo::LineasIntervencion)->count())->toBe(8)
        ->and(Catalogo::delCatalogo(TipoCatalogo::PoliticasPublicas)->count())->toBe(8)
        ->and(Catalogo::delCatalogo(TipoCatalogo::AreasIntervencion)->count())->toBe(5)
        ->and(Catalogo::delCatalogo(TipoCatalogo::Ods)->count())->toBe(17)
        ->and(Catalogo::delCatalogo(TipoCatalogo::BienesServicios)->whereNull('valor_base')->count())->toBe(0)
        ->and(Catalogo::delCatalogo(TipoCatalogo::BienesServicios)->where(fn ($consulta) => $consulta
            ->whereNull('linea_intervencion_id')->orWhereNull('politica_publica_id')->orWhereNull('area_intervencion_id')->orWhereNull('ods_id'))->count())->toBe(0)
        ->and($investigacion)
        ->valor_base->toBe('12500.00')
        ->categoria->toBe('bien')
        ->and($investigacion->lineaIntervencion->nombre)->toBe('Agua y saneamiento')
        ->and($investigacion->politicaPublica->nombre)->toBe('Política Nacional del Sector del agua potable y saneamiento')
        ->and($investigacion->areaIntervencion->nombre)->toBe('Salud')
        ->and($investigacion->ods->nombre)->toBe('ODS 6: Agua limpia y saneamiento')
        ->and(bienDelCatalogo('Talleres de capacitación en agua y saneamiento')->categoria)->toBe('servicio');
});

it('junta los bienes repetidos promediando sus valores y limpia los espacios de los nombres', function () {
    $this->seed(CatalogoBienesServiciosSeeder::class);

    // En el CSV aparece dos veces, con promedios de Q1,333.33 y Q3,092.02.
    expect(bienDelCatalogo('Huertos para el desarrollo económico')->valor_base)->toBe('2212.68')
        ->and(Catalogo::where('nombre', 'like', "%\n%")->orWhere('nombre', 'like', '%  %')->count())->toBe(0);
});

it('es idempotente y no pisa lo que DIGEU ya cambió', function () {
    $this->seed(CatalogoBienesServiciosSeeder::class);
    bienDelCatalogo('Investigación en agua y saneamiento')->update(['valor_base' => 999, 'categoria' => 'servicio']);

    $this->seed(CatalogoBienesServiciosSeeder::class);

    expect(Catalogo::delCatalogo(TipoCatalogo::BienesServicios)->count())->toBe(239)
        ->and(Catalogo::delCatalogo(TipoCatalogo::Ods)->count())->toBe(17)
        ->and(bienDelCatalogo('Investigación en agua y saneamiento'))->valor_base->toBe('999.00')->categoria->toBe('servicio');
});

it('completa los vínculos y el valor de un bien que ya existía sin ellos', function () {
    $existente = Catalogo::factory()->create(['nombre' => 'Jornada de salud', 'categoria' => 'servicio', 'valor_base' => null]);

    $this->seed(CatalogoBienesServiciosSeeder::class);

    expect($existente->fresh())
        ->valor_base->not->toBeNull()
        ->linea_intervencion_id->not->toBeNull()
        ->ods_id->not->toBeNull();
});

it('siembra las poblaciones vulnerables sin duplicarlas', function () {
    $this->seed(PoblacionVulnerableSeeder::class);
    $this->seed(PoblacionVulnerableSeeder::class);

    expect(Catalogo::delCatalogo(TipoCatalogo::PoblacionesVulnerables)->count())->toBe(count(PoblacionVulnerableSeeder::POBLACIONES))
        ->and(Catalogo::delCatalogo(TipoCatalogo::PoblacionesVulnerables)->where('nombre', 'Pueblos indígenas')->exists())->toBeTrue();
});
