import mapa from './guatemala-departamentos.json';

/**
 * Celdas de la rejilla de puntos del mapa (paso de 26 unidades sobre public/images/mapa.svg) asignadas a su
 * departamento con los límites oficiales (COD-AB de Guatemala, 2019). El JSON es la única fuente: también lo
 * lee el PDF de estadísticas (App\Support\MapaEstadisticoSvg).
 */

const PASO = 26;

export type DepartamentoMapa = {
    codigo: string;
    /** Trazo de un punto por celda. */
    puntos: string;
    /** Área que responde al cursor. */
    celdas: string;
    centro: [number, number];
};

export const DEPARTAMENTOS_MAPA: DepartamentoMapa[] = mapa.map(
    ({ codigo, puntos, centro }) => ({
        codigo,
        puntos: puntos.map(([x, y]) => `M${x} ${y}h0`).join(''),
        celdas: puntos
            .map(
                ([x, y]) =>
                    `M${x - PASO / 2} ${y - PASO / 2}h${PASO}v${PASO}h-${PASO}z`,
            )
            .join(''),
        centro: centro as [number, number],
    }),
);
