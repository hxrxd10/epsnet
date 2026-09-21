import { useState } from 'react';
import { cn } from '@/lib/utils';
import {
    MAP_HEIGHT,
    MAP_VIEWBOX,
    MAP_WIDTH,
} from '@/components/landing/guatemala-data';
import { DEPARTAMENTOS_MAPA } from './guatemala-departamentos';
import { formatearNumero } from './metricas';

export type ValorDepartamento = {
    codigo: string;
    nombre: string;
    valor: number;
};

type Props = {
    departamentos: ValorDepartamento[];
    seleccionado: string | null;
    onSeleccionar: (codigo: string | null) => void;
    /** Nombre de lo que se cuenta, para el texto accesible y el tooltip. */
    etiqueta: string;
};

const [VIEWBOX_X, VIEWBOX_Y] = MAP_VIEWBOX;

export default function MapaEstadistico({
    departamentos,
    seleccionado,
    onSeleccionar,
    etiqueta,
}: Props) {
    const [activo, setActivo] = useState<string | null>(null);

    const porCodigo = new Map(departamentos.map((d) => [d.codigo, d]));
    const maximo = Math.max(1, ...departamentos.map((d) => d.valor));
    const enfocado =
        activo !== null
            ? DEPARTAMENTOS_MAPA.find((d) => d.codigo === activo)
            : null;
    const datosEnfocado = activo !== null ? porCodigo.get(activo) : undefined;

    return (
        <div
            className="relative mx-auto w-full max-w-[540px]"
            style={{ aspectRatio: `${MAP_WIDTH} / ${MAP_HEIGHT}` }}
            onPointerLeave={() => setActivo(null)}
        >
            <svg
                viewBox={MAP_VIEWBOX.join(' ')}
                className="size-full overflow-visible"
                role="group"
                aria-label={`Mapa de Guatemala: ${etiqueta} por departamento`}
            >
                {DEPARTAMENTOS_MAPA.map((mapa) => {
                    const datos = porCodigo.get(mapa.codigo);
                    const valor = datos?.valor ?? 0;
                    const intensidad = valor / maximo;
                    const esSeleccionado = seleccionado === mapa.codigo;
                    const esActivo = activo === mapa.codigo;

                    return (
                        <g key={mapa.codigo}>
                            <path
                                d={mapa.puntos}
                                fill="none"
                                stroke="currentColor"
                                strokeLinecap="round"
                                strokeWidth={
                                    esSeleccionado || esActivo ? 17 : 12
                                }
                                strokeOpacity={
                                    valor === 0
                                        ? 0.14
                                        : 0.3 + 0.7 * Math.sqrt(intensidad)
                                }
                                className="text-white transition-[stroke-width,stroke-opacity] duration-200"
                            />
                            <path
                                d={mapa.celdas}
                                role="button"
                                tabIndex={0}
                                aria-pressed={esSeleccionado}
                                aria-label={`${datos?.nombre ?? mapa.codigo}: ${formatearNumero(valor)} ${etiqueta}`}
                                fill="white"
                                fillOpacity={
                                    esSeleccionado ? 0.16 : esActivo ? 0.08 : 0
                                }
                                stroke="white"
                                strokeOpacity={esSeleccionado ? 0.9 : 0}
                                strokeWidth={4}
                                strokeLinejoin="round"
                                className={cn(
                                    'cursor-pointer transition-[fill-opacity,stroke-opacity] duration-200 outline-none',
                                )}
                                onPointerEnter={() => setActivo(mapa.codigo)}
                                onFocus={() => setActivo(mapa.codigo)}
                                onBlur={() => setActivo(null)}
                                onClick={() =>
                                    onSeleccionar(
                                        esSeleccionado ? null : mapa.codigo,
                                    )
                                }
                                onKeyDown={(evento) => {
                                    if (
                                        evento.key === 'Enter' ||
                                        evento.key === ' '
                                    ) {
                                        evento.preventDefault();
                                        onSeleccionar(
                                            esSeleccionado ? null : mapa.codigo,
                                        );
                                    }
                                }}
                            />
                        </g>
                    );
                })}

                <g className="pointer-events-none" aria-hidden>
                    {DEPARTAMENTOS_MAPA.map((mapa) => {
                        const valor = porCodigo.get(mapa.codigo)?.valor ?? 0;

                        if (valor === 0) {
                            return null;
                        }

                        return (
                            <text
                                key={mapa.codigo}
                                x={mapa.centro[0]}
                                y={mapa.centro[1]}
                                textAnchor="middle"
                                dominantBaseline="central"
                                fontSize={48}
                                fontWeight={600}
                                fill="#0f1031"
                                stroke="white"
                                strokeWidth={9}
                                strokeLinejoin="round"
                                paintOrder="stroke"
                                className="font-mono"
                            >
                                {formatearNumero(valor)}
                            </text>
                        );
                    })}
                </g>
            </svg>

            {enfocado && datosEnfocado && (
                <div
                    className="bg-brand/90 pointer-events-none absolute z-10 -translate-x-1/2 -translate-y-[calc(100%+12px)] rounded-xl border border-white/15 px-3 py-2 text-left whitespace-nowrap text-white shadow-2xl backdrop-blur-md"
                    style={{
                        left: `${((enfocado.centro[0] - VIEWBOX_X) / MAP_WIDTH) * 100}%`,
                        top: `${((enfocado.centro[1] - VIEWBOX_Y) / MAP_HEIGHT) * 100}%`,
                    }}
                >
                    <p className="text-sm font-medium">
                        {datosEnfocado.nombre}
                    </p>
                    <p className="font-mono text-xs text-white/60">
                        {formatearNumero(datosEnfocado.valor)} {etiqueta}
                    </p>
                </div>
            )}
        </div>
    );
}
