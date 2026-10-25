import { Head, Link, router } from '@inertiajs/react';
import { MapPin, Search } from 'lucide-react';
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
import { dashboard } from '@/routes';
import { index, show } from '@/routes/repositorio';
import type { Opcion } from '@/types/estudiante';
import { ArticulacionInsignia } from '@/components/expediente/articulacion';
import type { ArticulacionEps } from '@/components/expediente/articulacion';
import AvisoAlcance from '@/components/estadisticas/aviso-alcance';

type Tarjeta = {
    id: number;
    estudiante: string;
    carrera: string;
    unidad: string;
    es_epsum: boolean;
    ubicacion: string;
    descripcion: string | null;
    bienes_servicios: number;
    acciones: number;
    publicaciones: number;
    aprobado: string | null;
    foto: string | null;
    articulacion: ArticulacionEps;
};

type Props = {
    expedientes: Tarjeta[];
    pagina: Pagina;
    filtros: Filtros;
    unidades: Opcion[];
    poblaciones: Opcion[];
};

type Filtros = { q: string; unidad: string; poblacion: string };

const TODOS = '__todos__';

export default function Repositorio({
    expedientes,
    pagina,
    filtros,
    unidades,
    poblaciones,
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
            <Head title="Repositorio" />
            <div className="flex flex-1 flex-col gap-6 p-4">
                <AvisoAlcance />
                <div>
                    <h1 className="text-2xl font-semibold tracking-tight">
                        Repositorio
                    </h1>
                    <p className="text-muted-foreground mt-1 max-w-2xl text-sm">
                        Los EPS aprobados por su unidad académica, con lo que se
                        hizo en cada uno.
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
                            placeholder="Estudiante o carrera"
                            className="pl-9"
                        />
                    </div>
                    <Select
                        value={filtros.unidad || TODOS}
                        onValueChange={(valor) =>
                            filtrar({ unidad: valor === TODOS ? '' : valor })
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
                    {poblaciones.length > 0 && (
                        <Select
                            value={filtros.poblacion || TODOS}
                            onValueChange={(valor) =>
                                filtrar({
                                    poblacion: valor === TODOS ? '' : valor,
                                })
                            }
                        >
                            <SelectTrigger
                                className="w-64"
                                aria-label="Población vulnerable"
                            >
                                <SelectValue />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value={TODOS}>
                                    Todas las poblaciones
                                </SelectItem>
                                {poblaciones.map((poblacion) => (
                                    <SelectItem
                                        key={poblacion.value}
                                        value={poblacion.value}
                                    >
                                        {poblacion.label}
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
                        Aún no hay EPS aprobados que coincidan.
                    </p>
                ) : (
                    <ul className="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                        {expedientes.map((tarjeta) => (
                            <li key={tarjeta.id}>
                                <Link
                                    href={show(tarjeta.id)}
                                    className="border-sidebar-border/70 hover:bg-accent flex h-full flex-col rounded-xl border p-5 transition-colors"
                                >
                                    {tarjeta.foto && (
                                        <img
                                            src={tarjeta.foto}
                                            alt={`Foto del EPS de ${tarjeta.estudiante}`}
                                            loading="lazy"
                                            className="-mx-5 -mt-5 mb-4 aspect-[16/9] w-[calc(100%+2.5rem)] max-w-none rounded-t-xl object-cover"
                                        />
                                    )}
                                    <p className="text-muted-foreground font-mono text-[11px] tracking-wider uppercase">
                                        {tarjeta.unidad}
                                    </p>
                                    <h2 className="mt-2 leading-snug font-semibold">
                                        {tarjeta.carrera}
                                        {tarjeta.es_epsum && (
                                            <Badge
                                                variant="outline"
                                                className="ml-2 align-middle"
                                            >
                                                EPSUM
                                            </Badge>
                                        )}
                                    </h2>
                                    <p className="text-muted-foreground mt-1 text-sm">
                                        {tarjeta.estudiante}
                                    </p>
                                    {tarjeta.ubicacion && (
                                        <p className="text-muted-foreground mt-2 flex items-center gap-1.5 text-sm">
                                            <MapPin className="size-3.5" />
                                            {tarjeta.ubicacion}
                                        </p>
                                    )}
                                    {tarjeta.descripcion && (
                                        <p className="mt-3 line-clamp-4 text-sm leading-relaxed">
                                            {tarjeta.descripcion}
                                        </p>
                                    )}
                                    <div className="mt-auto flex flex-wrap gap-2 pt-4">
                                        <Badge variant="secondary">
                                            {tarjeta.bienes_servicios} bienes y
                                            servicios
                                        </Badge>
                                        <Badge variant="secondary">
                                            {tarjeta.acciones} acciones
                                        </Badge>
                                        {tarjeta.publicaciones > 0 && (
                                            <Badge variant="secondary">
                                                {tarjeta.publicaciones}{' '}
                                                publicaciones
                                            </Badge>
                                        )}
                                        <ArticulacionInsignia
                                            articulacion={tarjeta.articulacion}
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

Repositorio.layout = {
    breadcrumbs: [
        { title: 'Panel', href: dashboard() },
        { title: 'Repositorio', href: index() },
    ],
};
