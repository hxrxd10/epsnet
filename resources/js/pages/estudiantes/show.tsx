import { Head, router } from '@inertiajs/react';
import { BadgeCheck, Download, FileText, Undo2 } from 'lucide-react';
import { useState } from 'react';
import DetalleEjes from '@/components/expediente/detalle-ejes';
import type { EjeDetalle } from '@/components/expediente/detalle-ejes';
import EstadoBadge from '@/components/expediente/estado-expediente';
import { Button } from '@/components/ui/button';
import { Spinner } from '@/components/ui/spinner';
import { dashboard } from '@/routes';
import { index } from '@/routes/panel/estudiantes';
import { destroy, store } from '@/routes/panel/estudiantes/verificacion';
import type { EstadoExpediente } from '@/types/estudiante';

type Props = {
    expediente: {
        id: number;
        estudiante: string;
        carnet: string;
        nacionalidad: string | null;
        carrera: string;
        extension: string | null;
        nivel: string | null;
        unidad: string;
        estado: EstadoExpediente;
        estado_etiqueta: string;
        completado_at: string | null;
        verificado_at: string | null;
        verificado_por: string | null;
    };
    orden_impresion: {
        nombre: string;
        tamano_bytes: number | null;
        descarga: string;
    } | null;
    ejes: EjeDetalle[];
    puedeVerificar: boolean;
};

const fecha = (iso: string) =>
    new Date(iso).toLocaleDateString('es-GT', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    });

export default function Estudiante({
    expediente,
    orden_impresion,
    ejes,
    puedeVerificar,
}: Props) {
    const [procesando, setProcesando] = useState(false);
    const verificado = expediente.estado === 'verificado';

    function cambiarAprobacion() {
        const accion = verificado
            ? destroy(expediente.id)
            : store(expediente.id);

        router.visit(accion.url, {
            method: accion.method,
            preserveScroll: true,
            onStart: () => setProcesando(true),
            onFinish: () => setProcesando(false),
        });
    }

    return (
        <>
            <Head title={`EPS de ${expediente.estudiante}`} />
            <div className="flex flex-1 flex-col gap-8 p-4">
                <div className="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <div className="flex flex-wrap items-center gap-3">
                            <h1 className="text-2xl font-semibold tracking-tight">
                                {expediente.estudiante}
                            </h1>
                            <EstadoBadge
                                estado={expediente.estado}
                                etiqueta={expediente.estado_etiqueta}
                            />
                        </div>
                        <p className="text-muted-foreground mt-1 text-sm">
                            <span className="font-mono">
                                {expediente.carnet}
                            </span>{' '}
                            · {expediente.carrera}
                            {expediente.extension &&
                                ` (${expediente.extension})`}
                        </p>
                        <p className="text-muted-foreground text-sm">
                            {expediente.unidad}
                            {expediente.nivel && ` · ${expediente.nivel}`}
                        </p>
                    </div>

                    {puedeVerificar && (
                        <Button
                            onClick={cambiarAprobacion}
                            disabled={procesando}
                            variant={verificado ? 'outline' : 'default'}
                        >
                            {procesando ? (
                                <Spinner />
                            ) : verificado ? (
                                <Undo2 />
                            ) : (
                                <BadgeCheck />
                            )}
                            {verificado ? 'Retirar aprobación' : 'Aprobar EPS'}
                        </Button>
                    )}
                </div>

                <section className="border-sidebar-border/70 grid gap-4 rounded-xl border p-5 text-sm sm:grid-cols-3">
                    <div>
                        <p className="text-muted-foreground font-mono text-[11px] tracking-wider uppercase">
                            Completado
                        </p>
                        <p className="mt-1">
                            {expediente.completado_at
                                ? fecha(expediente.completado_at)
                                : 'Aún no'}
                        </p>
                    </div>
                    <div>
                        <p className="text-muted-foreground font-mono text-[11px] tracking-wider uppercase">
                            Aprobado
                        </p>
                        <p className="mt-1">
                            {expediente.verificado_at
                                ? `${fecha(expediente.verificado_at)}${expediente.verificado_por ? ` por ${expediente.verificado_por}` : ''}`
                                : 'Pendiente'}
                        </p>
                    </div>
                    <div>
                        <p className="text-muted-foreground font-mono text-[11px] tracking-wider uppercase">
                            Orden de impresión
                        </p>
                        {orden_impresion ? (
                            <a
                                href={orden_impresion.descarga}
                                className="mt-1 inline-flex items-center gap-2 underline underline-offset-4"
                            >
                                <FileText className="size-4" />
                                {orden_impresion.nombre}
                                <Download className="size-3.5" />
                            </a>
                        ) : (
                            <p className="mt-1">No ha subido el archivo</p>
                        )}
                    </div>
                </section>

                <DetalleEjes ejes={ejes} />
            </div>
        </>
    );
}

Estudiante.layout = {
    breadcrumbs: [
        { title: 'Panel', href: dashboard() },
        { title: 'Estudiantes y EPS', href: index() },
    ],
};
