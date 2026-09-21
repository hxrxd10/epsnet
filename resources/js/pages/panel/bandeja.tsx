import { Head, Link, router } from '@inertiajs/react';
import { Clock, Inbox, Search } from 'lucide-react';
import { useState } from 'react';
import Paginacion from '@/components/paginacion';
import type { Pagina } from '@/components/paginacion';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { formatearFechaHora } from '@/lib/fechas';
import { dashboard } from '@/routes';
import { index } from '@/routes/panel/bandeja';
import { show } from '@/routes/panel/estudiantes';
import type { Opcion } from '@/types/estudiante';

type Solicitud = {
    id: number;
    posicion: number;
    estudiante: string;
    carnet: string;
    carrera: string;
    unidad: string;
    es_epsum: boolean;
    registros: number;
    enviado_at: string;
    dias_espera: number;
};

type Props = {
    solicitudes: Solicitud[];
    pagina: Pagina;
    filtros: { q: string; unidad: string };
    unidades: Opcion[];
    sinUnidad: boolean;
    ambito: string;
};

const TODAS = '__todas__';

const espera = (dias: number): string =>
    dias === 0
        ? 'Llegó hoy'
        : `Espera desde hace ${dias} ${dias === 1 ? 'día' : 'días'}`;

export default function Bandeja({
    solicitudes,
    pagina,
    filtros,
    unidades,
    sinUnidad,
    ambito,
}: Props) {
    const [texto, setTexto] = useState(filtros.q);

    function filtrar(nuevos: Partial<{ q: string; unidad: string }>) {
        const combinados = { ...filtros, q: texto, ...nuevos };

        router.get(
            index.url(),
            Object.fromEntries(
                Object.entries(combinados).filter(([, valor]) => valor !== ''),
            ),
            { preserveState: true, replace: true },
        );
    }

    return (
        <>
            <Head title="Bandeja de solicitudes" />
            <div className="flex flex-1 flex-col gap-6 p-4">
                <div>
                    <h1 className="text-2xl font-semibold tracking-tight">
                        Bandeja de solicitudes
                    </h1>
                    <p className="text-muted-foreground mt-1 max-w-2xl text-sm">
                        EPS que los estudiantes enviaron a aprobación sin orden
                        de impresión. El que llegó primero está arriba: revísalo
                        y apruébalo en ese orden. {ambito}.
                    </p>
                </div>

                {sinUnidad && (
                    <p className="border-sidebar-border/70 text-muted-foreground rounded-xl border border-dashed p-6 text-center text-sm">
                        Aún no tienes una unidad académica asignada. DIGEU te la
                        asignará y aquí verás las solicitudes de sus
                        estudiantes.
                    </p>
                )}

                <form
                    onSubmit={(evento) => {
                        evento.preventDefault();
                        filtrar({ q: texto });
                    }}
                    className="flex flex-wrap items-center gap-3"
                >
                    <div className="relative min-w-64 flex-1 sm:max-w-sm">
                        <Search className="text-muted-foreground absolute top-2.5 left-3 size-4" />
                        <Input
                            value={texto}
                            onChange={(evento) => setTexto(evento.target.value)}
                            placeholder="Nombre, carné o carrera"
                            className="pl-9"
                        />
                    </div>
                    {unidades.length > 0 && (
                        <Select
                            value={filtros.unidad || TODAS}
                            onValueChange={(valor) =>
                                filtrar({
                                    unidad: valor === TODAS ? '' : valor,
                                })
                            }
                        >
                            <SelectTrigger
                                className="w-64"
                                aria-label="Unidad académica"
                            >
                                <SelectValue />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value={TODAS}>
                                    Todas las unidades
                                </SelectItem>
                                {unidades.map((unidad) => (
                                    <SelectItem
                                        key={unidad.value}
                                        value={unidad.value}
                                    >
                                        {unidad.label}
                                    </SelectItem>
                                ))}
                            </SelectContent>
                        </Select>
                    )}
                    <Button type="submit" variant="outline">
                        Buscar
                    </Button>
                </form>

                {solicitudes.length === 0 ? (
                    <div className="border-sidebar-border/70 text-muted-foreground flex flex-col items-center gap-2 rounded-xl border border-dashed p-12 text-center text-sm">
                        <Inbox className="size-8" />
                        {pagina.total === 0 && !filtros.q && !filtros.unidad
                            ? 'No hay solicitudes pendientes. Cuando un estudiante envíe su EPS a aprobación, aparecerá aquí.'
                            : 'No hay solicitudes que coincidan.'}
                    </div>
                ) : (
                    <ol className="border-sidebar-border/70 divide-sidebar-border/70 divide-y rounded-xl border">
                        {solicitudes.map((solicitud) => (
                            <li key={solicitud.id}>
                                <Link
                                    href={show(solicitud.id, {
                                        query: { desde: 'bandeja' },
                                    })}
                                    className="hover:bg-accent flex flex-col gap-3 p-4 transition-colors sm:flex-row sm:items-center"
                                >
                                    <span className="bg-primary text-primary-foreground flex size-9 shrink-0 items-center justify-center rounded-full font-mono text-sm tabular-nums">
                                        {solicitud.posicion}
                                    </span>
                                    <div className="min-w-0 flex-1">
                                        <p className="font-medium">
                                            {solicitud.estudiante}
                                            {solicitud.es_epsum && (
                                                <Badge
                                                    variant="outline"
                                                    className="ml-2 align-middle"
                                                >
                                                    EPSUM
                                                </Badge>
                                            )}
                                        </p>
                                        <p className="text-muted-foreground truncate text-sm">
                                            <span className="font-mono">
                                                {solicitud.carnet}
                                            </span>{' '}
                                            · {solicitud.carrera}
                                            {unidades.length > 0 &&
                                                ` · ${solicitud.unidad}`}
                                        </p>
                                    </div>
                                    <div className="text-muted-foreground flex shrink-0 flex-col gap-1 text-sm sm:items-end">
                                        <span className="font-mono text-xs">
                                            Enviado el{' '}
                                            {formatearFechaHora(
                                                solicitud.enviado_at,
                                            )}
                                        </span>
                                        <span className="inline-flex items-center gap-1.5">
                                            <Clock className="size-3.5" />
                                            {espera(
                                                solicitud.dias_espera,
                                            )} ·{' '}
                                            {solicitud.registros}{' '}
                                            {solicitud.registros === 1
                                                ? 'registro'
                                                : 'registros'}
                                        </span>
                                    </div>
                                    <span className="bg-primary text-primary-foreground inline-flex shrink-0 items-center rounded-md px-3 py-1.5 text-sm font-medium">
                                        Revisar y aprobar
                                    </span>
                                </Link>
                            </li>
                        ))}
                    </ol>
                )}

                <Paginacion pagina={pagina} />
            </div>
        </>
    );
}

Bandeja.layout = {
    breadcrumbs: [
        { title: 'Panel', href: dashboard() },
        { title: 'Bandeja de solicitudes', href: index() },
    ],
};
