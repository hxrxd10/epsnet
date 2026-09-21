import { Head, Link, router } from '@inertiajs/react';
import { Search } from 'lucide-react';
import { useState } from 'react';
import EstadoBadge from '@/components/expediente/estado-expediente';
import Paginacion from '@/components/paginacion';
import type { Pagina } from '@/components/paginacion';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { dashboard } from '@/routes';
import { index, show } from '@/routes/panel/estudiantes';
import type { EstadoExpediente, Opcion } from '@/types/estudiante';

type Fila = {
    id: number;
    estudiante: string;
    carnet: string;
    carrera: string;
    unidad: string;
    estado: EstadoExpediente;
    estado_etiqueta: string;
    registros: number;
    actualizado: string | null;
};

type Props = {
    expedientes: Fila[];
    pagina: Pagina;
    filtros: { q: string; estado: string; unidad: string };
    estados: Opcion[];
    unidades: Opcion[];
    sinUnidad: boolean;
    ambito: string;
};

const TODOS = '__todos__';

export default function Estudiantes({
    expedientes,
    pagina,
    filtros,
    estados,
    unidades,
    sinUnidad,
    ambito,
}: Props) {
    const [texto, setTexto] = useState(filtros.q);

    function filtrar(
        nuevos: Partial<{ q: string; estado: string; unidad: string }>,
    ) {
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
            <Head title="Estudiantes y EPS" />
            <div className="flex flex-1 flex-col gap-6 p-4">
                <div>
                    <h1 className="text-2xl font-semibold tracking-tight">
                        Estudiantes y EPS
                    </h1>
                    <p className="text-muted-foreground mt-1 text-sm">
                        {ambito}
                    </p>
                </div>

                {sinUnidad && (
                    <p className="border-sidebar-border/70 text-muted-foreground rounded-xl border border-dashed p-6 text-sm">
                        Aún no tienes una unidad académica asignada. Pídele a
                        DIGEU que te la asigne para ver a sus estudiantes.
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
                    <Select
                        value={filtros.estado || TODOS}
                        onValueChange={(valor) =>
                            filtrar({ estado: valor === TODOS ? '' : valor })
                        }
                    >
                        <SelectTrigger className="w-52">
                            <SelectValue />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value={TODOS}>
                                Todos los estados
                            </SelectItem>
                            {estados.map((estado) => (
                                <SelectItem
                                    key={estado.value}
                                    value={estado.value}
                                >
                                    {estado.label}
                                </SelectItem>
                            ))}
                        </SelectContent>
                    </Select>
                    {unidades.length > 0 && (
                        <Select
                            value={filtros.unidad || TODOS}
                            onValueChange={(valor) =>
                                filtrar({
                                    unidad: valor === TODOS ? '' : valor,
                                })
                            }
                        >
                            <SelectTrigger className="w-64">
                                <SelectValue />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value={TODOS}>
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

                {expedientes.length === 0 ? (
                    <p className="border-sidebar-border/70 text-muted-foreground rounded-xl border border-dashed p-10 text-center text-sm">
                        No hay EPS que coincidan.
                    </p>
                ) : (
                    <ul className="border-sidebar-border/70 divide-sidebar-border/70 divide-y rounded-xl border">
                        {expedientes.map((fila) => (
                            <li key={fila.id}>
                                <Link
                                    href={show(fila.id)}
                                    className="hover:bg-accent flex flex-col gap-2 p-4 transition-colors sm:flex-row sm:items-center sm:justify-between"
                                >
                                    <div className="min-w-0">
                                        <p className="font-medium">
                                            {fila.estudiante}{' '}
                                            <span className="text-muted-foreground font-mono text-xs">
                                                {fila.carnet}
                                            </span>
                                        </p>
                                        <p className="text-muted-foreground mt-1 text-sm">
                                            {fila.carrera}
                                            {unidades.length > 0 &&
                                                ` · ${fila.unidad}`}
                                        </p>
                                    </div>
                                    <div className="flex shrink-0 items-center gap-3 text-sm">
                                        <span className="text-muted-foreground">
                                            {fila.registros}{' '}
                                            {fila.registros === 1
                                                ? 'registro'
                                                : 'registros'}
                                        </span>
                                        <EstadoBadge
                                            estado={fila.estado}
                                            etiqueta={fila.estado_etiqueta}
                                        />
                                    </div>
                                </Link>
                            </li>
                        ))}
                    </ul>
                )}

                <Paginacion pagina={pagina} />
            </div>
        </>
    );
}

Estudiantes.layout = {
    breadcrumbs: [
        { title: 'Panel', href: dashboard() },
        { title: 'Estudiantes y EPS', href: index() },
    ],
};
