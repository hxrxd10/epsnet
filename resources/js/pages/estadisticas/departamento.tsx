import { Head, Link } from '@inertiajs/react';
import { ArrowLeft, ExternalLink, FileDown } from 'lucide-react';
import FiltrosEstadisticosBarra from '@/components/estadisticas/filtros-estadisticos';
import { METRICAS, formatearNumero } from '@/components/estadisticas/metricas';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { dashboard } from '@/routes';
import { departamento as rutaDepartamento, index } from '@/routes/estadisticas';
import { pdf } from '@/routes/estadisticas/departamento';
import type {
    FiltrosEstadisticos,
    ItemBienServicio,
    Metricas,
    MunicipioEstadistico,
    OpcionesFiltros,
} from '@/types/estadisticas';

type Props = OpcionesFiltros & {
    departamento: { codigo: string; nombre: string; cabecera: string };
    metricas: Metricas;
    municipios: MunicipioEstadistico[];
    filtros: FiltrosEstadisticos;
};

function TablaBienesServicios({ items }: { items: ItemBienServicio[] }) {
    if (items.length === 0) {
        return (
            <p className="text-muted-foreground text-sm">
                No hay bienes ni servicios registrados.
            </p>
        );
    }

    return (
        <div className="overflow-x-auto">
            <table className="w-full text-sm">
                <thead>
                    <tr className="text-muted-foreground border-b text-left text-xs">
                        <th className="py-2 pr-3 font-medium">
                            Bien o servicio
                        </th>
                        <th className="px-3 py-2 font-medium">Tipo</th>
                        <th className="px-3 py-2 text-right font-medium">
                            Registros
                        </th>
                        <th className="py-2 pl-3 text-right font-medium">
                            Beneficiarios
                        </th>
                    </tr>
                </thead>
                <tbody>
                    {items.map((item) => (
                        <tr
                            key={`${item.tipo}-${item.nombre}`}
                            className="border-b last:border-0"
                        >
                            <td className="py-2 pr-3">{item.nombre}</td>
                            <td className="px-3 py-2">
                                <Badge variant="secondary">
                                    {item.tipo === 'bien' ? 'Bien' : 'Servicio'}
                                </Badge>
                            </td>
                            <td className="px-3 py-2 text-right font-mono tabular-nums">
                                {formatearNumero(item.cantidad)}
                            </td>
                            <td className="py-2 pl-3 text-right font-mono tabular-nums">
                                {formatearNumero(item.beneficiarios)}
                            </td>
                        </tr>
                    ))}
                </tbody>
            </table>
        </div>
    );
}

export default function EstadisticasDepartamento({
    departamento,
    metricas,
    municipios,
    filtros,
    anios,
    unidades,
    carreras,
}: Props) {
    return (
        <>
            <Head title={`Estadísticas de ${departamento.nombre}`} />
            <div className="flex flex-1 flex-col gap-6 p-4">
                <div>
                    <Link
                        href={index({
                            query: Object.fromEntries(
                                Object.entries(filtros).filter(
                                    ([, valor]) => valor !== '',
                                ),
                            ),
                        })}
                        className="text-muted-foreground hover:text-foreground inline-flex items-center gap-1.5 text-sm"
                    >
                        <ArrowLeft className="size-4" />
                        Volver al mapa
                    </Link>
                    <h1 className="mt-3 text-2xl font-semibold tracking-tight">
                        {departamento.nombre}
                    </h1>
                    <p className="text-muted-foreground mt-1 text-sm">
                        Cabecera departamental: {departamento.cabecera}. Todo lo
                        consolidado por municipio.
                    </p>
                </div>

                <div className="flex flex-wrap items-start justify-between gap-3">
                    <FiltrosEstadisticosBarra
                        url={rutaDepartamento.url(departamento.codigo)}
                        filtros={filtros}
                        anios={anios}
                        unidades={unidades}
                        carreras={carreras}
                    />
                    <Button asChild variant="outline">
                        <a
                            href={pdf.url(departamento.codigo, {
                                query: Object.fromEntries(
                                    Object.entries(filtros).filter(
                                        ([, valor]) => valor !== '',
                                    ),
                                ),
                            })}
                        >
                            <FileDown />
                            Generar PDF
                        </a>
                    </Button>
                </div>

                <dl className="grid grid-cols-2 gap-3 md:grid-cols-4">
                    {METRICAS.map((item) => (
                        <div
                            key={item.clave}
                            className="border-sidebar-border/70 rounded-xl border p-4"
                        >
                            <dt className="text-muted-foreground text-xs">
                                {item.etiqueta}
                            </dt>
                            <dd className="mt-1 text-2xl font-semibold tracking-tight tabular-nums">
                                {formatearNumero(metricas[item.clave])}
                            </dd>
                        </div>
                    ))}
                </dl>

                {municipios.length === 0 ? (
                    <p className="border-sidebar-border/70 text-muted-foreground rounded-xl border border-dashed p-10 text-center text-sm">
                        No hay EPS con ubicación en este departamento para los
                        filtros elegidos.
                    </p>
                ) : (
                    <>
                        <nav
                            aria-label="Municipios"
                            className="flex flex-wrap gap-2"
                        >
                            {municipios.map((municipio, posicion) => (
                                <a
                                    key={municipio.nombre}
                                    href={`#municipio-${posicion}`}
                                    className="border-sidebar-border/70 hover:bg-accent rounded-full border px-3 py-1 text-sm"
                                >
                                    {municipio.nombre}
                                </a>
                            ))}
                        </nav>

                        {municipios.map((municipio, posicion) => (
                            <section
                                key={municipio.nombre}
                                id={`municipio-${posicion}`}
                                className="border-sidebar-border/70 scroll-mt-20 rounded-xl border p-5"
                            >
                                <div className="flex flex-wrap items-baseline justify-between gap-2">
                                    <h2 className="text-lg font-semibold tracking-tight">
                                        {municipio.nombre}
                                    </h2>
                                    <div className="flex flex-wrap gap-1.5">
                                        <Badge variant="secondary">
                                            {formatearNumero(
                                                municipio.metricas.eps,
                                            )}{' '}
                                            EPS
                                        </Badge>
                                        <Badge variant="secondary">
                                            {formatearNumero(
                                                municipio.metricas
                                                    .beneficiarios,
                                            )}{' '}
                                            beneficiarios
                                        </Badge>
                                        <Badge variant="secondary">
                                            {formatearNumero(
                                                municipio.metricas.acciones,
                                            )}{' '}
                                            acciones
                                        </Badge>
                                    </div>
                                </div>

                                <div className="mt-5 grid gap-6 xl:grid-cols-2">
                                    <div>
                                        <h3 className="text-muted-foreground mb-2 font-mono text-[11px] tracking-[0.18em] uppercase">
                                            Bienes y servicios
                                        </h3>
                                        <TablaBienesServicios
                                            items={municipio.bienes_servicios}
                                        />
                                    </div>

                                    <div>
                                        <h3 className="text-muted-foreground mb-2 font-mono text-[11px] tracking-[0.18em] uppercase">
                                            Investigaciones (
                                            {municipio.investigaciones.length})
                                        </h3>
                                        {municipio.investigaciones.length ===
                                        0 ? (
                                            <p className="text-muted-foreground text-sm">
                                                No hay investigaciones
                                                registradas.
                                            </p>
                                        ) : (
                                            <ul className="flex flex-col gap-3">
                                                {municipio.investigaciones.map(
                                                    (investigacion) => (
                                                        <li
                                                            key={
                                                                investigacion.id
                                                            }
                                                            className="rounded-lg border p-3 text-sm"
                                                        >
                                                            <div className="flex items-start justify-between gap-3">
                                                                <p className="leading-snug font-medium">
                                                                    {
                                                                        investigacion.titulo
                                                                    }
                                                                </p>
                                                                <Badge variant="secondary">
                                                                    {
                                                                        investigacion.tipo
                                                                    }
                                                                </Badge>
                                                            </div>
                                                            <p className="text-muted-foreground mt-1">
                                                                {
                                                                    investigacion.autores
                                                                }
                                                                {investigacion.medio &&
                                                                    ` · ${investigacion.medio}`}
                                                                {investigacion.fecha &&
                                                                    ` · ${investigacion.fecha}`}
                                                            </p>
                                                            <p className="text-muted-foreground mt-1 text-xs">
                                                                {
                                                                    investigacion.carrera
                                                                }{' '}
                                                                ·{' '}
                                                                {
                                                                    investigacion.unidad
                                                                }
                                                            </p>
                                                            {investigacion.enlace && (
                                                                <a
                                                                    href={
                                                                        investigacion.enlace
                                                                    }
                                                                    target="_blank"
                                                                    rel="noopener noreferrer"
                                                                    className="mt-2 inline-flex items-center gap-1 text-xs underline underline-offset-4"
                                                                >
                                                                    Ver
                                                                    documento
                                                                    <ExternalLink className="size-3" />
                                                                </a>
                                                            )}
                                                        </li>
                                                    ),
                                                )}
                                            </ul>
                                        )}
                                    </div>
                                </div>

                                <div className="mt-6">
                                    <h3 className="text-muted-foreground mb-2 font-mono text-[11px] tracking-[0.18em] uppercase">
                                        Instituciones aliadas (
                                        {municipio.instituciones.length})
                                    </h3>
                                    {municipio.instituciones.length === 0 ? (
                                        <p className="text-muted-foreground text-sm">
                                            No hay instituciones aliadas
                                            registradas.
                                        </p>
                                    ) : (
                                        <ul className="flex flex-wrap gap-2">
                                            {municipio.instituciones.map(
                                                (institucion) => (
                                                    <li
                                                        key={institucion.nombre}
                                                        className="rounded-lg border px-3 py-1.5 text-sm"
                                                    >
                                                        {institucion.nombre}
                                                        {institucion.tipo && (
                                                            <span className="text-muted-foreground">
                                                                {' '}
                                                                ·{' '}
                                                                {
                                                                    institucion.tipo
                                                                }
                                                            </span>
                                                        )}
                                                        <span className="text-muted-foreground font-mono text-xs">
                                                            {' '}
                                                            · {
                                                                institucion.eps
                                                            }{' '}
                                                            EPS
                                                        </span>
                                                    </li>
                                                ),
                                            )}
                                        </ul>
                                    )}
                                </div>
                            </section>
                        ))}
                    </>
                )}
            </div>
        </>
    );
}

EstadisticasDepartamento.layout = {
    breadcrumbs: [
        { title: 'Panel', href: dashboard() },
        { title: 'Estadísticas', href: index() },
    ],
};
