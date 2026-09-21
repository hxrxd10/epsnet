<?php

namespace App\Support;

/**
 * Dibuja el mapa de puntos de Guatemala como imagen SVG (para el PDF): cada punto se pinta con más
 * intensidad cuanto mayor es el valor de su departamento. La geometría es la misma que usa el mapa web.
 */
class MapaEstadisticoSvg
{
    /** Caja del mapa: la misma de resources/js/components/landing/guatemala-data.ts. */
    private const array CAJA = [95.25, 27.6, 1989.76, 2123.58];

    private const int RADIO = 8;

    private const string COLOR_MARCA = '#0F1031';

    private const string COLOR_VACIO = '#DDDFE8';

    /**
     * @param  array<string, int>  $valores  Valor de cada departamento, por código ("01"…"22").
     * @param  string|null  $resaltado  Código de un departamento que debe destacarse sobre los demás.
     */
    public static function dataUri(array $valores, ?string $resaltado = null, int $ancho = 320): string
    {
        return 'data:image/svg+xml;base64,'.base64_encode(self::generar($valores, $resaltado, $ancho));
    }

    /**
     * @param  array<string, int>  $valores
     */
    public static function generar(array $valores, ?string $resaltado = null, int $ancho = 320): string
    {
        [$x0, $y0, $w, $h] = self::CAJA;
        $escala = $ancho / $w;
        $alto = (int) round($h * $escala);
        $radio = round(self::RADIO * $escala, 2);
        $maximo = max(1, ...array_values($valores) ?: [1]);
        $puntos = '';

        foreach (self::geometria() as $departamento) {
            $codigo = (string) $departamento['codigo'];
            $color = $resaltado !== null
                ? ($codigo === $resaltado ? self::COLOR_MARCA : self::COLOR_VACIO)
                : self::color((int) ($valores[$codigo] ?? 0), $maximo);

            foreach ($departamento['puntos'] as [$x, $y]) {
                // Las coordenadas ya van a escala: el generador de PDF no aplica el viewBox.
                $puntos .= '<circle cx="'.round(($x - $x0) * $escala, 1).'" cy="'.round(($y - $y0) * $escala, 1).'" r="'.$radio.'" fill="'.$color.'"/>';
            }
        }

        return '<svg xmlns="http://www.w3.org/2000/svg" width="'.$ancho.'" height="'.$alto.'">'.$puntos.'</svg>';
    }

    /**
     * Mezcla del gris claro con el color de marca según la raíz de la proporción (igual que en pantalla).
     */
    private static function color(int $valor, int $maximo): string
    {
        if ($valor <= 0) {
            return self::COLOR_VACIO;
        }

        $t = sqrt($valor / $maximo);
        $desde = sscanf(self::COLOR_VACIO, '#%02x%02x%02x');
        $hasta = sscanf(self::COLOR_MARCA, '#%02x%02x%02x');

        return sprintf('#%02X%02X%02X', ...array_map(
            fn (int $a, int $b): int => (int) round($a + ($b - $a) * $t),
            $desde,
            $hasta,
        ));
    }

    /**
     * @return list<array{codigo: string, puntos: list<array{0: int, 1: int}>, centro: array{0: int, 1: int}}>
     */
    private static function geometria(): array
    {
        static $geometria = null;

        return $geometria ??= json_decode(
            (string) file_get_contents(resource_path('js/components/estadisticas/guatemala-departamentos.json')),
            true,
            flags: JSON_THROW_ON_ERROR,
        );
    }
}
