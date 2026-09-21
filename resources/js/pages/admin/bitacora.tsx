import { Head, router } from '@inertiajs/react';
import { Search } from 'lucide-react';
import { useState } from 'react';
import { index } from '@/actions/App/Http/Controllers/Admin/BitacoraController';
import Paginacion from '@/components/paginacion';
import type { Pagina } from '@/components/paginacion';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { InputFecha } from '@/components/ui/input-fecha';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { formatearFechaHora, formatearFechasEnTexto } from '@/lib/fechas';
import { cn } from '@/lib/utils';
import { dashboard } from '@/routes';
import type { Opcion } from '@/types/estudiante';

type Registro = {
    id: number;
    fecha: string;
    usuario: string | null;
    correo: string | null;
    rol: string | null;
    tipo: string;
    tipo_etiqueta: string;
    modulo: string;
    detalle: string | null;
    anteriores: Record<string, unknown> | null;
    nuevos: Record<string, unknown> | null;
    ip: string | null;
};

type Filtros = {
    q: string;
    modulo: string;
    tipo: string;
    desde: string;
    hasta: string;
};

type Props = {
    registros: Registro[];
    pagina: Pagina;
    filtros: Filtros;
    modulos: string[];
    tipos: Opcion[];
};

const TODOS = '__todos__';

const COLOR_TIPO: Record<string, string> = {
    creacion: 'bg-emerald-500/15 text-emerald-700 dark:text-emerald-300',
    edicion: 'bg-amber-500/15 text-amber-700 dark:text-amber-300',
    eliminacion: 'bg-red-500/15 text-red-700 dark:text-red-300',
    aprobacion: 'bg-sky-500/15 text-sky-700 dark:text-sky-300',
    retiro_aprobacion: 'bg-orange-500/15 text-orange-700 dark:text-orange-300',
};

const mostrar = (valor: unknown): string =>
    valor === null || valor === undefined || valor === ''
        ? '—'
        : typeof valor === 'object'
          ? JSON.stringify(valor)
          : formatearFechasEnTexto(String(valor));

function Cambios({ registro }: { registro: Registro }) {
    const anteriores = registro.anteriores ?? {};
    const nuevos = registro.nuevos ?? {};
    const campos = [
        ...new Set([...Object.keys(anteriores), ...Object.keys(nuevos)]),
    ];

    if (campos.length === 0) {
        return null;
    }

    return (
        <details className="mt-2 text-xs">
            <summary className="text-muted-foreground hover:text-foreground cursor-pointer">
                Ver los datos ({campos.length})
            </summary>
            <table className="mt-2 w-full">
                <thead>
                    <tr className="text-muted-foreground text-left">
                        <th className="py-1 pr-3 font-medium">Campo</th>
                        {registro.anteriores && (
                            <th className="py-1 pr-3 font-medium">Antes</th>
                        )}
                        {registro.nuevos && (
                            <th className="py-1 font-medium">Después</th>
                        )}
                    </tr>
                </thead>
                <tbody>
                    {campos.map((campo) => (
                        <tr key={campo} className="border-t align-top">
                            <td className="py-1 pr-3 font-mono">{campo}</td>
                            {registro.anteriores && (
                                <td className="py-1 pr-3 break-words">
                                    {mostrar(anteriores[campo])}
                                </td>
                            )}
                            {registro.nuevos && (
                                <td className="py-1 break-words">
                                    {mostrar(nuevos[campo])}
                                </td>
                            )}
                        </tr>
                    ))}
                </tbody>
            </table>
        </details>
    );
}

export default function Bitacora({
    registros,
    pagina,
    filtros,
    modulos,
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

    const hayFiltros = Object.values(filtros).some((valor) => valor !== '');

    return (
        <>
            <Head title="Bitácora" />
            <div className="flex flex-1 flex-col gap-6 p-4">
                <div>
                    <h1 className="text-2xl font-semibold tracking-tight">
                        Bitácora
                    </h1>
                    <p className="text-muted-foreground mt-1 max-w-2xl text-sm">
                        Quién hizo cada cambio, cuándo y dónde. Se registra sola
                        y nadie puede editarla ni eliminarla.
                    </p>
                </div>

                <form
                    onSubmit={(evento) => {
                        evento.preventDefault();
                        filtrar({ q: texto });
                    }}
                    className="flex flex-wrap items-end gap-3"
                >
                    <div className="relative min-w-64 flex-1 sm:max-w-xs">
                        <Search className="text-muted-foreground absolute top-2.5 left-3 size-4" />
                        <Input
                            value={texto}
                            onChange={(evento) => setTexto(evento.target.value)}
                            placeholder="Usuario, correo o detalle"
                            className="pl-9"
                        />
                    </div>
                    <Select
                        value={filtros.modulo || TODOS}
                        onValueChange={(valor) =>
                            filtrar({ modulo: valor === TODOS ? '' : valor })
                        }
                    >
                        <SelectTrigger className="w-56" aria-label="Módulo">
                            <SelectValue />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value={TODOS}>
                                Todos los módulos
                            </SelectItem>
                            {modulos.map((modulo) => (
                                <SelectItem key={modulo} value={modulo}>
                                    {modulo}
                                </SelectItem>
                            ))}
                        </SelectContent>
                    </Select>
                    <Select
                        value={filtros.tipo || TODOS}
                        onValueChange={(valor) =>
                            filtrar({ tipo: valor === TODOS ? '' : valor })
                        }
                    >
                        <SelectTrigger className="w-44" aria-label="Acción">
                            <SelectValue />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value={TODOS}>
                                Todas las acciones
                            </SelectItem>
                            {tipos.map((tipo) => (
                                <SelectItem key={tipo.value} value={tipo.value}>
                                    {tipo.label}
                                </SelectItem>
                            ))}
                        </SelectContent>
                    </Select>
                    <label className="text-muted-foreground grid gap-1 text-xs">
                        Desde
                        <InputFecha
                            value={filtros.desde}
                            onChange={(iso) => filtrar({ desde: iso })}
                            className="w-40"
                        />
                    </label>
                    <label className="text-muted-foreground grid gap-1 text-xs">
                        Hasta
                        <InputFecha
                            value={filtros.hasta}
                            onChange={(iso) => filtrar({ hasta: iso })}
                            className="w-40"
                        />
                    </label>
                    <Button type="submit" variant="outline">
                        Buscar
                    </Button>
                    {hayFiltros && (
                        <Button
                            type="button"
                            variant="ghost"
                            onClick={() => {
                                setTexto('');
                                router.get(index.url());
                            }}
                        >
                            Limpiar
                        </Button>
                    )}
                </form>

                {registros.length === 0 ? (
                    <p className="border-sidebar-border/70 text-muted-foreground rounded-xl border border-dashed p-10 text-center text-sm">
                        No hay registros en la bitácora con esos filtros.
                    </p>
                ) : (
                    <div className="border-sidebar-border/70 overflow-x-auto rounded-xl border">
                        <table className="w-full text-sm">
                            <thead>
                                <tr className="text-muted-foreground border-b text-left text-xs">
                                    <th className="px-4 py-3 font-medium">
                                        Fecha
                                    </th>
                                    <th className="px-4 py-3 font-medium">
                                        Usuario
                                    </th>
                                    <th className="px-4 py-3 font-medium">
                                        Acción
                                    </th>
                                    <th className="px-4 py-3 font-medium">
                                        Dónde y qué
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                {registros.map((registro) => (
                                    <tr
                                        key={registro.id}
                                        className="border-b align-top last:border-0"
                                    >
                                        <td className="px-4 py-3 font-mono text-xs whitespace-nowrap">
                                            {formatearFechaHora(registro.fecha)}
                                        </td>
                                        <td className="px-4 py-3">
                                            <p className="font-medium">
                                                {registro.usuario ?? 'Sistema'}
                                            </p>
                                            {registro.correo && (
                                                <p className="text-muted-foreground text-xs">
                                                    {registro.correo}
                                                </p>
                                            )}
                                            {registro.rol && (
                                                <Badge
                                                    variant="outline"
                                                    className="mt-1"
                                                >
                                                    {registro.rol}
                                                </Badge>
                                            )}
                                        </td>
                                        <td className="px-4 py-3">
                                            <span
                                                className={cn(
                                                    'inline-block rounded-full px-2.5 py-0.5 text-xs font-medium whitespace-nowrap',
                                                    COLOR_TIPO[registro.tipo] ??
                                                        'bg-secondary text-secondary-foreground',
                                                )}
                                            >
                                                {registro.tipo_etiqueta}
                                            </span>
                                        </td>
                                        <td className="px-4 py-3">
                                            <p className="text-muted-foreground font-mono text-[11px] tracking-wider uppercase">
                                                {registro.modulo}
                                            </p>
                                            <p className="mt-0.5">
                                                {registro.detalle}
                                            </p>
                                            <Cambios registro={registro} />
                                            {registro.ip && (
                                                <p className="text-muted-foreground mt-1 font-mono text-[11px]">
                                                    IP {registro.ip}
                                                </p>
                                            )}
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                )}

                <Paginacion pagina={pagina} />
            </div>
        </>
    );
}

Bitacora.layout = {
    breadcrumbs: [
        { title: 'Panel', href: dashboard() },
        { title: 'Bitácora', href: index() },
    ],
};
