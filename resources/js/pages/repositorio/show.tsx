import { Head } from '@inertiajs/react';
import DetalleEjes from '@/components/expediente/detalle-ejes';
import type { EjeDetalle } from '@/components/expediente/detalle-ejes';
import { formatearFecha } from '@/lib/fechas';
import { dashboard } from '@/routes';
import { index } from '@/routes/repositorio';
import Articulacion from '@/components/expediente/articulacion';
import type { ArticulacionEps } from '@/components/expediente/articulacion';

type Props = {
    expediente: {
        id: number;
        estudiante: string;
        carrera: string;
        unidad: string;
        aprobado: string | null;
        foto: string | null;
    };
    ejes: EjeDetalle[];
    articulacion: ArticulacionEps;
};

export default function RepositorioEps({
    expediente,
    ejes,
    articulacion,
}: Props) {
    return (
        <>
            <Head title={expediente.carrera} />
            <div className="flex flex-1 flex-col gap-8 p-4">
                <div>
                    <p className="text-muted-foreground font-mono text-[11px] tracking-wider uppercase">
                        {expediente.unidad}
                    </p>
                    <h1 className="mt-1 text-2xl font-semibold tracking-tight">
                        {expediente.carrera}
                    </h1>
                    <p className="text-muted-foreground mt-1 text-sm">
                        {expediente.estudiante}
                        {expediente.aprobado &&
                            ` · Aprobado el ${formatearFecha(expediente.aprobado)}`}
                    </p>
                </div>

                {expediente.foto && (
                    <img
                        src={expediente.foto}
                        alt={`Foto del EPS de ${expediente.estudiante}`}
                        className="border-sidebar-border/70 max-h-[28rem] w-full max-w-3xl rounded-xl border object-cover"
                    />
                )}

                <Articulacion
                    articulacion={articulacion}
                    className="max-w-3xl"
                />

                <DetalleEjes ejes={ejes} />
            </div>
        </>
    );
}

RepositorioEps.layout = {
    breadcrumbs: [
        { title: 'Panel', href: dashboard() },
        { title: 'Repositorio', href: index() },
    ],
};
