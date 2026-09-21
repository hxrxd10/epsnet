<?php

namespace App\Actions\Expedientes;

use App\Enums\Eje;
use App\Models\Expediente;
use Illuminate\Support\Collection;

/**
 * Arma, para consulta de solo lectura, todo lo que el estudiante registró en cada eje.
 * Cada registro se normaliza a: etiqueta, título, datos breves y textos largos.
 */
class ArmarDetalleExpediente
{
    /**
     * @return list<array{eje: int, titulo: string, registros: list<array{etiqueta: string|null, titulo: string, meta: list<string>, textos: list<array{etiqueta: string, valor: string}>}>}>
     */
    public function handle(Expediente $expediente): array
    {
        return array_map(fn (Eje $eje): array => [
            'eje' => $eje->value,
            'titulo' => $eje->titulo(),
            'registros' => $this->registros($expediente, $eje)->values()->all(),
        ], Eje::cases());
    }

    /**
     * @return Collection<int, array{etiqueta: string|null, titulo: string, meta: list<string>, textos: list<array{etiqueta: string, valor: string}>}>
     */
    private function registros(Expediente $expediente, Eje $eje): Collection
    {
        return match ($eje) {
            Eje::BienesServicios => $expediente->bienesServicios()->with('catalogo')->orderBy('fecha')->get()
                ->map(fn ($r): array => $this->registro(
                    $r->tipo->etiqueta(),
                    $r->catalogo?->nombre ?? str($r->descripcion)->squish()->limit(90)->toString(),
                    [$r->fecha->format('d-m-Y'), $r->cantidad_beneficiarios !== null ? "{$r->cantidad_beneficiarios} beneficiarios" : null],
                    ['Descripción' => $r->descripcion, 'Beneficiarios' => $r->beneficiarios],
                )),
            Eje::Publicaciones => $expediente->publicaciones()->orderBy('id')->get()
                ->map(fn ($r): array => $this->registro(
                    $r->tipo->etiqueta(),
                    $r->titulo,
                    [$r->autores, $r->medio_publicacion, $r->fecha_publicacion?->format('d-m-Y')],
                    ['Resumen' => $r->resumen, 'Enlace' => $r->enlace],
                )),
            Eje::Transferencia => $expediente->transferencias()->with('catalogo')->orderBy('fecha')->get()
                ->map(fn ($r): array => $this->registro(
                    $r->tipo_actividad->etiqueta(),
                    $r->catalogo?->nombre ?? str($r->actividad)->squish()->limit(90)->toString(),
                    [$r->comunidad, $r->fecha->format('d-m-Y'), $r->numero_participantes !== null ? "{$r->numero_participantes} participantes" : null],
                    ['Actividad' => $r->actividad],
                )),
            Eje::Territorio => $expediente->ubicaciones()->with(['departamento', 'municipio'])->orderBy('id')->get()
                ->map(fn ($r): array => $this->registro(
                    $r->departamento->nombre,
                    collect([$r->municipio?->nombre, $r->comunidad])->filter()->implode(' · '),
                    [$r->latitud !== null && $r->longitud !== null ? "{$r->latitud}, {$r->longitud}" : null],
                    ['Referencia territorial' => $r->referencia],
                )),
            Eje::Actores => $expediente->actores()->orderBy('id')->get()
                ->map(fn ($r): array => $this->registro(
                    'Institución receptora',
                    $r->institucion_receptora,
                    ["Contraparte: {$r->contraparte}", "Comunidad: {$r->comunidad_beneficiada}"],
                    [],
                ))
                ->concat($expediente->alianzas()->with('institucionAliada')->orderBy('id')->get()
                    ->map(fn ($r): array => $this->registro(
                        'Institución aliada',
                        $r->institucionAliada->nombre,
                        [$r->institucionAliada->tipo],
                        ['Aporte' => $r->aporte],
                    ))),
            Eje::Seguimiento => $expediente->seguimientos()->orderBy('fecha')->get()
                ->map(fn ($r): array => $this->registro(
                    $r->tipo_registro->etiqueta(),
                    $r->indicador,
                    [$r->fecha->format('d-m-Y'), $r->porcentaje_avance !== null ? "{$r->porcentaje_avance}% de avance" : null, $r->cumplimiento?->etiqueta()],
                    ['Avance' => $r->avance, 'Observaciones' => $r->observaciones, 'Evaluación de impacto' => $r->evaluacion_impacto],
                )),
        };
    }

    /**
     * @param  list<string|null>  $meta
     * @param  array<string, string|null>  $textos
     * @return array{etiqueta: string|null, titulo: string, meta: list<string>, textos: list<array{etiqueta: string, valor: string}>}
     */
    private function registro(?string $etiqueta, string $titulo, array $meta, array $textos): array
    {
        return [
            'etiqueta' => $etiqueta,
            'titulo' => $titulo,
            'meta' => array_values(array_filter($meta, fn (?string $dato): bool => filled($dato))),
            'textos' => collect($textos)
                ->filter(fn (?string $valor): bool => filled($valor))
                ->map(fn (string $valor, string $etiqueta): array => ['etiqueta' => $etiqueta, 'valor' => $valor])
                ->values()
                ->all(),
        ];
    }
}
