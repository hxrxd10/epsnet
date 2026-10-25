import { Head, Link, router } from '@inertiajs/react';
import { ExternalLink, Search } from 'lucide-react';
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
import { formatearFecha } from '@/lib/fechas';
import { dashboard } from '@/routes';
import { index } from '@/routes/investigaciones';
import { show as verEps } from '@/routes/repositorio';
import type { Opcion } from '@/types/estudiante';
import AvisoAlcance from '@/components/estadisticas/aviso-alcance';

type Investigacion = {
    id: number;
    titulo: string;
    tipo: string;
    autores: string;
    medio: string | null;
    resumen: string | null;
    enlace: string | null;
    fecha: string | null;
    carrera: string;
    unidad: string;
    estudiante: string;
    expediente_id: number;
};

type Filtros = { q: string; unidad: string; tipo: string };

type Props = {
    investigaciones: Investigacion[];
    pagina: Pagina;
    filtros: Filtros;
    unidades: Opcion[];
    tipos: Opcion[];
};

const TODOS = '__todos__';

/** Solo se enlazan direcciones web (http o https). */
const esEnlaceWeb = (enlace: string | null): enlace is string =>
    enlace !== null && /^https?:\/\//i.test(enlace);

export default function Investigaciones({
    investigaciones,
    pagina,
    filtros,
    unidades,
    tipos,
}: Props) {
    const [texto, setTexto] = useState(filtros.q);

    function filtrar(nuevos: Partial<Filtros>) {
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
            <Head title="Investigaciones" />
            <div className="flex flex-1 flex-col gap-6 p-4">
                <AvisoAlcance />
                <div>
                    <h1 className="text-2xl font-semibold tracking-tight">
                        Investigaciones
                    </h1>
                    <p className="text-muted-foreground mt-1 max-w-2xl text-sm">
                        Las publicaciones de investigación de los EPS aprobados
                        por supervisión: artículos, tesis, informes y más.
                    </p>
                </div>

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
                            placeholder="Título, autores, resumen o carrera"
                            className="pl-9"
                        />
                    </div>
                    <Select
                        value={filtros.tipo || TODOS}
                        onValueChange={(valor) =>
                            filtrar({ tipo: valor === TODOS ? '' : valor })
                        }
                    >
                        <SelectTrigger className="w-56" aria-label="Tipo">
                            <SelectValue />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value={TODOS}>
                                Todos los tipos
                            </SelectItem>
                            {tipos.map((tipo) => (
                                <SelectItem key={tipo.value} value={tipo.value}>
                                    {tipo.label}
                                </SelectItem>
                            ))}
                        </SelectContent>
                    </Select>
                    <Select
                        value={filtros.unidad || TODOS}
                        onValueChange={(valor) =>
                            filtrar({ unidad: valor === TODOS ? '' : valor })
                        }
                    >
                        <SelectTrigger
                            className="w-64"
                            aria-label="Unidad académica"
                        >
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
                    <Button type="submit" variant="outline">
                        Buscar
                    </Button>
                </form>

                {investigaciones.length === 0 ? (
                    <p className="border-sidebar-border/70 text-muted-foreground rounded-xl border border-dashed p-10 text-center text-sm">
                        Aún no hay investigaciones de EPS aprobados que
                        coincidan.
                    </p>
                ) : (
                    <ul className="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                        {investigaciones.map((investigacion) => (
                            <li
                                key={investigacion.id}
                                className="border-sidebar-border/70 flex flex-col rounded-xl border p-5"
                            >
                                <div className="flex flex-wrap items-center gap-2">
                                    <Badge variant="secondary">
                                        {investigacion.tipo}
                                    </Badge>
                                    {investigacion.fecha && (
                                        <span className="text-muted-foreground text-xs">
                                            {formatearFecha(
                                                investigacion.fecha,
                                            )}
                                        </span>
                                    )}
                                </div>
                                <h2 className="mt-3 leading-snug font-semibold">
                                    {investigacion.titulo}
                                </h2>
                                <p className="text-muted-foreground mt-1 text-sm">
                                    {investigacion.autores}
                                    {investigacion.medio &&
                                        ` · ${investigacion.medio}`}
                                </p>
                                {investigacion.resumen && (
                                    <p className="mt-3 line-clamp-5 text-sm leading-relaxed">
                                        {investigacion.resumen}
                                    </p>
                                )}
                                <div className="mt-auto pt-4">
                                    <p className="text-muted-foreground font-mono text-[11px] tracking-wider uppercase">
                                        {investigacion.unidad}
                                    </p>
                                    <p className="text-sm">
                                        {investigacion.carrera} ·{' '}
                                        {investigacion.estudiante}
                                    </p>
                                    <div className="mt-3 flex flex-wrap gap-3 text-sm">
                                        <Link
                                            href={verEps(
                                                investigacion.expediente_id,
                                            )}
                                            className="font-medium underline-offset-4 hover:underline"
                                        >
                                            Ver el EPS
                                        </Link>
                                        {esEnlaceWeb(investigacion.enlace) && (
                                            <a
                                                href={investigacion.enlace}
                                                target="_blank"
                                                rel="noopener noreferrer"
                                                className="inline-flex items-center gap-1 font-medium underline-offset-4 hover:underline"
                                            >
                                                Abrir publicación
                                                <ExternalLink className="size-3.5" />
                                            </a>
                                        )}
                                    </div>
                                </div>
                            </li>
                        ))}
                    </ul>
                )}

                <Paginacion pagina={pagina} />
            </div>
        </>
    );
}

Investigaciones.layout = {
    breadcrumbs: [
        { title: 'Panel', href: dashboard() },
        { title: 'Investigaciones', href: index() },
    ],
};
