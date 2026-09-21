import { Head, Link } from '@inertiajs/react';
import { ArrowRight, FileDown, MapPin } from 'lucide-react';
import { useState } from 'react';
import FiltrosEstadisticosBarra from '@/components/estadisticas/filtros-estadisticos';
import MapaEstadistico from '@/components/estadisticas/mapa-estadistico';
import { METRICAS, formatearNumero } from '@/components/estadisticas/metricas';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { cn } from '@/lib/utils';
import { dashboard } from '@/routes';
import {
    departamento as rutaDepartamento,
    index,
    pdf,
} from '@/routes/estadisticas';
import type {
    ClaveMetrica,
    DepartamentoEstadistico,
    FiltrosEstadisticos,
    Metricas,
    OpcionesFiltros,
} from '@/types/estadisticas';

type Props = OpcionesFiltros & {
    totales: Metricas;
    sin_ubicacion: Metricas;
    departamentos: DepartamentoEstadistico[];
    filtros: FiltrosEstadisticos;
};

export default function Estadisticas({
    totales,
    sin_ubicacion,
    departamentos,
    filtros,
    anios,
    unidades,
    carreras,
}: Props) {
    const [clave, setClave] = useState<ClaveMetrica>('investigaciones');
    const [codigo, setCodigo] = useState<string | null>(null);

    const metrica = METRICAS.find((item) => item.clave === clave)!;
    const seleccionado = departamentos.find((item) => item.codigo === codigo);
    const consulta = Object.fromEntries(
        Object.entries(filtros).filter(([, valor]) => valor !== ''),
    );

    return (
        <>
            <Head title="Estadísticas" />
            <div className="flex flex-1 flex-col gap-6 p-4">
                <div>
                    <h1 className="text-2xl font-semibold tracking-tight">
                        Estadísticas
                    </h1>
                    <p className="text-muted-foreground mt-1 max-w-2xl text-sm">
                        Lo que la red de EPS ha hecho en cada departamento. El
                        año es el de la orden de impresión de cada EPS o, si no
                        la tiene, el de su aprobación.
                    </p>
                </div>

                <div className="flex flex-wrap items-start justify-between gap-3">
                    <FiltrosEstadisticosBarra
                        url={index.url()}
                        filtros={filtros}
                        anios={anios}
                        unidades={unidades}
                        carreras={carreras}
                    />
                    <Button asChild variant="outline">
                        <a
                            href={pdf.url({
                                query: { ...consulta, metrica: clave },
                            })}
                        >
                            <FileDown />
                            Generar PDF
                        </a>
                    </Button>
                </div>

                <div className="grid gap-6 lg:grid-cols-[minmax(0,1fr)_23rem]">
                    <section
                        aria-label="Mapa de Guatemala"
                        className="bg-brand landing-grain relative overflow-hidden rounded-2xl p-6 text-white ring-1 ring-white/10"
                    >
                        <div className="flex items-end justify-between gap-4">
                            <div>
                                <p className="font-mono text-[11px] tracking-[0.2em] text-white/50 uppercase">
                                    {metrica.etiqueta}
                                </p>
                                <p className="mt-1 text-4xl font-semibold tracking-tight tabular-nums">
                                    {formatearNumero(totales[clave])}
                                </p>
                            </div>
                            <p className="max-w-[14rem] text-right text-xs text-white/50">
                                {metrica.descripcion}, en todo el país
                            </p>
                        </div>

                        <div className="mt-4">
                            <MapaEstadistico
                                etiqueta={metrica.etiqueta.toLowerCase()}
                                seleccionado={codigo}
                                onSeleccionar={setCodigo}
                                departamentos={departamentos.map((item) => ({
                                    codigo: item.codigo,
                                    nombre: item.nombre,
                                    valor: item.metricas[clave],
                                }))}
                            />
                        </div>

                        <p className="mt-4 text-center font-mono text-[11px] tracking-[0.18em] text-white/40 uppercase">
                            Selecciona un departamento
                            {sin_ubicacion.eps > 0 &&
                                ` · ${formatearNumero(sin_ubicacion.eps)} EPS sin ubicación no se dibujan`}
                        </p>
                    </section>

                    <aside className="flex flex-col gap-6">
                        <section aria-label="Qué mostrar">
                            <h2 className="text-muted-foreground mb-2 font-mono text-[11px] tracking-[0.18em] uppercase">
                                Qué mostrar
                            </h2>
                            <div
                                role="radiogroup"
                                aria-label="Dato que se muestra en el mapa"
                                className="flex flex-col gap-1.5"
                            >
                                {METRICAS.map((item) => (
                                    <button
                                        key={item.clave}
                                        type="button"
                                        role="radio"
                                        aria-checked={item.clave === clave}
                                        onClick={() => setClave(item.clave)}
                                        className={cn(
                                            'flex items-center justify-between gap-3 rounded-lg border px-3.5 py-2.5 text-left text-sm transition-colors focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:outline-none',
                                            item.clave === clave
                                                ? 'border-brand bg-brand text-white'
                                                : 'border-sidebar-border/70 hover:bg-accent',
                                        )}
                                    >
                                        <span className="font-medium">
                                            {item.etiqueta}
                                        </span>
                                        <span
                                            className={cn(
                                                'font-mono tabular-nums',
                                                item.clave === clave
                                                    ? 'text-white/70'
                                                    : 'text-muted-foreground',
                                            )}
                                        >
                                            {formatearNumero(
                                                totales[item.clave],
                                            )}
                                        </span>
                                    </button>
                                ))}
                            </div>
                        </section>

                        <section
                            aria-label="Departamento seleccionado"
                            aria-live="polite"
                            className="border-sidebar-border/70 rounded-xl border p-5"
                        >
                            {seleccionado === undefined ? (
                                <p className="text-muted-foreground flex items-center gap-2 text-sm">
                                    <MapPin className="size-4 shrink-0" />
                                    Elige un departamento en el mapa para ver
                                    sus investigaciones y sus principales bienes
                                    y servicios.
                                </p>
                            ) : (
                                <>
                                    <p className="text-muted-foreground font-mono text-[11px] tracking-wider uppercase">
                                        Cabecera: {seleccionado.cabecera}
                                    </p>
                                    <h2 className="mt-1 text-xl font-semibold tracking-tight">
                                        {seleccionado.nombre}
                                    </h2>

                                    {seleccionado.metricas.eps === 0 ? (
                                        <p className="text-muted-foreground mt-4 text-sm">
                                            Sin EPS registrados aquí con estos
                                            filtros.
                                        </p>
                                    ) : (
                                        <>
                                            <div className="mt-4 flex items-baseline gap-2">
                                                <span className="text-4xl font-semibold tracking-tight tabular-nums">
                                                    {formatearNumero(
                                                        seleccionado.metricas
                                                            .investigaciones,
                                                    )}
                                                </span>
                                                <span className="text-muted-foreground text-sm">
                                                    {seleccionado.metricas
                                                        .investigaciones === 1
                                                        ? 'investigación'
                                                        : 'investigaciones'}
                                                </span>
                                            </div>

                                            <div className="mt-2 flex flex-wrap gap-1.5">
                                                <Badge variant="secondary">
                                                    {formatearNumero(
                                                        seleccionado.metricas
                                                            .eps,
                                                    )}{' '}
                                                    EPS
                                                </Badge>
                                                <Badge variant="secondary">
                                                    {formatearNumero(
                                                        seleccionado.metricas
                                                            .beneficiarios,
                                                    )}{' '}
                                                    beneficiarios
                                                </Badge>
                                                <Badge variant="secondary">
                                                    {formatearNumero(
                                                        seleccionado.metricas
                                                            .acciones,
                                                    )}{' '}
                                                    acciones
                                                </Badge>
                                            </div>

                                            <h3 className="text-muted-foreground mt-5 font-mono text-[11px] tracking-[0.18em] uppercase">
                                                Top de bienes y servicios
                                            </h3>
                                            {seleccionado.top_bienes_servicios
                                                .length === 0 ? (
                                                <p className="text-muted-foreground mt-2 text-sm">
                                                    Aún no hay bienes ni
                                                    servicios registrados.
                                                </p>
                                            ) : (
                                                <ol className="mt-2 flex flex-col gap-2">
                                                    {seleccionado.top_bienes_servicios.map(
                                                        (item, posicion) => (
                                                            <li
                                                                key={`${item.tipo}-${item.nombre}`}
                                                                className="flex items-center gap-3 text-sm"
                                                            >
                                                                <span className="text-muted-foreground w-4 font-mono text-xs">
                                                                    {posicion +
                                                                        1}
                                                                </span>
                                                                <span className="min-w-0 flex-1 truncate">
                                                                    {
                                                                        item.nombre
                                                                    }
                                                                </span>
                                                                <span className="font-mono text-xs tabular-nums">
                                                                    {formatearNumero(
                                                                        item.cantidad,
                                                                    )}
                                                                </span>
                                                            </li>
                                                        ),
                                                    )}
                                                </ol>
                                            )}

                                            <Button
                                                asChild
                                                className="mt-5 w-full"
                                            >
                                                <Link
                                                    href={rutaDepartamento(
                                                        seleccionado.codigo,
                                                        { query: consulta },
                                                    )}
                                                >
                                                    Ver más
                                                    <ArrowRight />
                                                </Link>
                                            </Button>
                                        </>
                                    )}
                                </>
                            )}
                        </section>
                    </aside>
                </div>
            </div>
        </>
    );
}

Estadisticas.layout = {
    breadcrumbs: [
        { title: 'Panel', href: dashboard() },
        { title: 'Estadísticas', href: index() },
    ],
};
