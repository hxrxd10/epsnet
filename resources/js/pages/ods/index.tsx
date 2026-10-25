import { Head } from '@inertiajs/react';
import { FileSpreadsheet } from 'lucide-react';
import { useState } from 'react';
import FiltrosEstadisticosBarra from '@/components/estadisticas/filtros-estadisticos';
import { formatearNumero } from '@/components/estadisticas/metricas';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { formatearQuetzales } from '@/lib/moneda';
import { cn } from '@/lib/utils';
import { dashboard } from '@/routes';
import { excel, index } from '@/routes/ods';
import type {
    FiltrosEstadisticos,
    Ods,
    OpcionesFiltros,
    ResumenOds,
    TotalesOds,
} from '@/types/estadisticas';
import AvisoAlcance from '@/components/estadisticas/aviso-alcance';

type Props = OpcionesFiltros & {
    totales: TotalesOds;
    sin_ods: { bienes_servicios: number; aporte: number };
    ods: Ods[];
    filtros: FiltrosEstadisticos;
};

/** Colores oficiales de los 17 ODS (Naciones Unidas): identifican a cada objetivo. */
const COLORES_ODS: Record<number, string> = {
    1: '#E5243B',
    2: '#DDA63A',
    3: '#4C9F38',
    4: '#C5192D',
    5: '#FF3A21',
    6: '#26BDE2',
    7: '#FCC30B',
    8: '#A21942',
    9: '#FD6925',
    10: '#DD1367',
    11: '#FD9D24',
    12: '#BF8B2E',
    13: '#3F7E44',
    14: '#0A97D9',
    15: '#56C02B',
    16: '#00689D',
    17: '#19486A',
};

function NumeroOds({
    numero,
    grande = false,
}: {
    numero: number | null;
    grande?: boolean;
}) {
    return (
        <span
            className={cn(
                'flex shrink-0 items-center justify-center rounded-lg font-mono font-bold text-white',
                grande ? 'size-14 text-2xl' : 'size-10 text-base',
            )}
            style={{
                backgroundColor:
                    numero !== null
                        ? (COLORES_ODS[numero] ?? '#0f1031')
                        : '#0f1031',
            }}
            aria-hidden
        >
            {numero ?? '·'}
        </span>
    );
}

function Cifra({ valor, etiqueta }: { valor: string; etiqueta: string }) {
    return (
        <div className="border-sidebar-border/70 min-w-0 rounded-xl border p-4">
            <p className="text-xl font-semibold tracking-tight break-words tabular-nums sm:text-2xl">
                {valor}
            </p>
            <p className="text-muted-foreground mt-0.5 text-sm">{etiqueta}</p>
        </div>
    );
}

function Tabla({
    titulo,
    filas,
    conBeneficiarios = false,
}: {
    titulo: string;
    filas: ResumenOds[];
    conBeneficiarios?: boolean;
}) {
    if (filas.length === 0) {
        return null;
    }

    return (
        <section>
            <h3 className="text-muted-foreground font-mono text-[11px] tracking-[0.18em] uppercase">
                {titulo}
            </h3>
            <table className="mt-2 w-full text-sm">
                <thead className="sr-only">
                    <tr>
                        <th>Nombre</th>
                        <th>Cantidad</th>
                        {conBeneficiarios && <th>Beneficiarios</th>}
                        <th>Aporte</th>
                    </tr>
                </thead>
                <tbody>
                    {filas.map((fila) => (
                        <tr
                            key={fila.nombre}
                            className="border-b align-top last:border-0"
                        >
                            <td className="py-2 pr-3">{fila.nombre}</td>
                            <td className="text-muted-foreground px-2 py-2 text-right font-mono text-xs whitespace-nowrap tabular-nums">
                                {formatearNumero(fila.cantidad)}
                            </td>
                            {conBeneficiarios && (
                                <td className="text-muted-foreground px-2 py-2 text-right font-mono text-xs whitespace-nowrap tabular-nums">
                                    {formatearNumero(fila.beneficiarios)} benef.
                                </td>
                            )}
                            <td className="py-2 pl-2 text-right font-mono text-xs whitespace-nowrap tabular-nums">
                                {formatearQuetzales(fila.aporte)}
                            </td>
                        </tr>
                    ))}
                </tbody>
            </table>
        </section>
    );
}

export default function OdsIndex({
    totales,
    sin_ods,
    ods,
    filtros,
    anios,
    unidades,
    carreras,
}: Props) {
    const conActividad = ods.filter((objetivo) => objetivo.eps > 0);
    const masAporte = [...conActividad].sort((a, b) => b.aporte - a.aporte)[0];
    const [elegido, setElegido] = useState<number | null>(
        masAporte?.id ?? null,
    );
    const detalle =
        ods.find((objetivo) => objetivo.id === elegido && objetivo.eps > 0) ??
        masAporte;
    const maximo = Math.max(1, ...ods.map((objetivo) => objetivo.aporte));

    return (
        <>
            <Head title="ODS" />
            <div className="flex flex-1 flex-col gap-6 p-4">
                <AvisoAlcance />
                <div>
                    <h1 className="text-2xl font-semibold tracking-tight">
                        Objetivos de Desarrollo Sostenible
                    </h1>
                    <p className="text-muted-foreground mt-1 max-w-2xl text-sm">
                        Lo que aportan los EPS aprobados a cada ODS, según los
                        bienes y servicios que entregaron: cada bien o servicio
                        del catálogo está anclado a un ODS.
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
                            href={excel.url({
                                query: Object.fromEntries(
                                    Object.entries(filtros).filter(
                                        ([, valor]) => valor !== '',
                                    ),
                                ),
                            })}
                        >
                            <FileSpreadsheet />
                            Exportar Excel
                        </a>
                    </Button>
                </div>

                <div className="grid grid-cols-2 gap-3 md:grid-cols-3 2xl:grid-cols-5">
                    <Cifra
                        valor={formatearQuetzales(totales.aporte)}
                        etiqueta="de aporte en bienes y servicios"
                    />
                    <Cifra
                        valor={formatearNumero(totales.bienes_servicios)}
                        etiqueta="bienes y servicios entregados"
                    />
                    <Cifra
                        valor={formatearNumero(totales.beneficiarios)}
                        etiqueta="beneficiarios"
                    />
                    <Cifra
                        valor={formatearNumero(totales.eps)}
                        etiqueta="EPS con bienes y servicios"
                    />
                    <Cifra
                        valor={`${totales.ods_con_actividad} de ${ods.length}`}
                        etiqueta="ODS con aporte"
                    />
                </div>

                {sin_ods.bienes_servicios > 0 && (
                    <p className="text-muted-foreground text-sm">
                        {formatearNumero(sin_ods.bienes_servicios)} bienes y
                        servicios ({formatearQuetzales(sin_ods.aporte)}) no
                        están anclados a ningún ODS y no se muestran por
                        objetivo.
                    </p>
                )}

                <div className="grid items-start gap-6 xl:grid-cols-[minmax(0,1fr)_26rem]">
                    <ul className="grid gap-3 sm:grid-cols-2">
                        {ods.map((objetivo) => {
                            const activo = objetivo.eps > 0;
                            const seleccionado =
                                activo && detalle?.id === objetivo.id;

                            return (
                                <li key={objetivo.id}>
                                    <button
                                        type="button"
                                        disabled={!activo}
                                        aria-pressed={seleccionado}
                                        onClick={() => setElegido(objetivo.id)}
                                        className={cn(
                                            'flex h-full w-full flex-col gap-3 rounded-xl border p-4 text-left transition-colors',
                                            seleccionado
                                                ? 'border-brand ring-brand ring-1'
                                                : 'border-sidebar-border/70 hover:bg-accent',
                                            !activo &&
                                                'cursor-default opacity-50 hover:bg-transparent',
                                        )}
                                    >
                                        <div className="flex items-start gap-3">
                                            <NumeroOds
                                                numero={objetivo.numero}
                                            />
                                            <div className="min-w-0">
                                                <p className="leading-snug font-medium">
                                                    {objetivo.nombre}
                                                </p>
                                                <p className="mt-0.5 font-mono text-sm tabular-nums">
                                                    {activo
                                                        ? formatearQuetzales(
                                                              objetivo.aporte,
                                                          )
                                                        : 'Sin aporte registrado'}
                                                </p>
                                            </div>
                                        </div>
                                        {activo && (
                                            <>
                                                <div
                                                    className="bg-muted h-1.5 overflow-hidden rounded-full"
                                                    role="img"
                                                    aria-label={`${Math.round((objetivo.aporte / totales.aporte) * 100)} % del aporte total`}
                                                >
                                                    <div
                                                        className="bg-brand h-full rounded-full"
                                                        style={{
                                                            width: `${Math.max(2, (objetivo.aporte / maximo) * 100)}%`,
                                                        }}
                                                    />
                                                </div>
                                                <p className="text-muted-foreground text-xs">
                                                    {formatearNumero(
                                                        objetivo.bienes_servicios,
                                                    )}{' '}
                                                    bienes y servicios ·{' '}
                                                    {formatearNumero(
                                                        objetivo.beneficiarios,
                                                    )}{' '}
                                                    beneficiarios ·{' '}
                                                    {formatearNumero(
                                                        objetivo.eps,
                                                    )}{' '}
                                                    EPS
                                                </p>
                                            </>
                                        )}
                                    </button>
                                </li>
                            );
                        })}
                    </ul>

                    <aside className="border-sidebar-border/70 flex flex-col gap-5 rounded-xl border p-5 xl:sticky xl:top-4">
                        {detalle === undefined ? (
                            <p className="text-muted-foreground text-sm">
                                Aún no hay bienes y servicios de EPS aprobados
                                con estos filtros.
                            </p>
                        ) : (
                            <>
                                <div className="flex items-start gap-3">
                                    <NumeroOds numero={detalle.numero} grande />
                                    <div>
                                        <p className="text-muted-foreground font-mono text-[11px] tracking-wider uppercase">
                                            ODS {detalle.numero}
                                        </p>
                                        <h2 className="text-lg leading-snug font-semibold">
                                            {detalle.nombre}
                                        </h2>
                                    </div>
                                </div>

                                <div>
                                    <p className="text-3xl font-semibold tracking-tight tabular-nums">
                                        {formatearQuetzales(detalle.aporte)}
                                    </p>
                                    <p className="text-muted-foreground text-sm">
                                        {totales.aporte > 0 &&
                                            `${Math.round((detalle.aporte / totales.aporte) * 100)} % del aporte total`}
                                    </p>
                                    <div className="mt-3 flex flex-wrap gap-1.5">
                                        <Badge variant="secondary">
                                            {formatearNumero(
                                                detalle.bienes_servicios,
                                            )}{' '}
                                            bienes y servicios
                                        </Badge>
                                        <Badge variant="secondary">
                                            {formatearNumero(
                                                detalle.beneficiarios,
                                            )}{' '}
                                            beneficiarios
                                        </Badge>
                                        <Badge variant="secondary">
                                            {formatearNumero(detalle.eps)} EPS
                                        </Badge>
                                        <Badge variant="secondary">
                                            {formatearNumero(
                                                detalle.estudiantes,
                                            )}{' '}
                                            estudiantes
                                        </Badge>
                                        <Badge variant="secondary">
                                            {formatearNumero(
                                                detalle.departamentos,
                                            )}{' '}
                                            {detalle.departamentos === 1
                                                ? 'departamento'
                                                : 'departamentos'}
                                        </Badge>
                                    </div>
                                </div>

                                <Tabla
                                    titulo="Bienes y servicios"
                                    filas={detalle.items}
                                    conBeneficiarios
                                />
                                <Tabla
                                    titulo="Líneas de intervención"
                                    filas={detalle.lineas}
                                />
                                <Tabla
                                    titulo="Áreas de intervención"
                                    filas={detalle.areas}
                                />
                                <Tabla
                                    titulo="Unidades académicas"
                                    filas={detalle.unidades}
                                />
                            </>
                        )}
                    </aside>
                </div>
            </div>
        </>
    );
}

OdsIndex.layout = {
    breadcrumbs: [
        { title: 'Panel', href: dashboard() },
        { title: 'ODS', href: index() },
    ],
};
