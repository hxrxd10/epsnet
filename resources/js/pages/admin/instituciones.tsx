import { Head, router, useForm } from '@inertiajs/react';
import { Pencil, Plus, Search, Trash2 } from 'lucide-react';
import { useState } from 'react';
import type { FormEvent } from 'react';
import {
    destroy,
    index,
    store,
    update,
} from '@/actions/App/Http/Controllers/Admin/InstitucionAliadaController';
import DialogoEliminar from '@/components/admin/dialogo-eliminar';
import InputError from '@/components/input-error';
import Paginacion from '@/components/paginacion';
import type { Pagina } from '@/components/paginacion';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Spinner } from '@/components/ui/spinner';
import { dashboard } from '@/routes';
import { datos } from '@/routes/admin';

type Institucion = {
    id: number;
    nombre: string;
    tipo: string | null;
    nombre_contacto: string | null;
    correo_contacto: string | null;
    telefono_contacto: string | null;
    direccion: string | null;
    eps: number;
};

type Props = {
    instituciones: Institucion[];
    pagina: Pagina;
    filtros: { q: string; tipo: string };
    tipos: string[];
};

type Formulario = {
    nombre: string;
    tipo: string;
    nombre_contacto: string;
    correo_contacto: string;
    telefono_contacto: string;
    direccion: string;
};

const VACIO: Formulario = {
    nombre: '',
    tipo: '',
    nombre_contacto: '',
    correo_contacto: '',
    telefono_contacto: '',
    direccion: '',
};
const TODOS = '__todos__';
const TIPOS_SUGERIDOS = [
    'Gobierno central',
    'Gobierno local',
    'Organización no gubernamental',
    'Cooperación internacional',
    'Organización comunitaria',
    'Universidad o centro educativo',
    'Empresa privada',
];

export default function Instituciones({
    instituciones,
    pagina,
    filtros,
    tipos,
}: Props) {
    const formulario = useForm<Formulario>(VACIO);
    const [texto, setTexto] = useState(filtros.q);
    const [abierto, setAbierto] = useState(false);
    const [editando, setEditando] = useState<Institucion | null>(null);
    const [eliminando, setEliminando] = useState<Institucion | null>(null);
    const [borrando, setBorrando] = useState(false);

    const sugerencias = [...new Set([...TIPOS_SUGERIDOS, ...tipos])];

    function filtrar(nuevos: Partial<{ q: string; tipo: string }>) {
        const combinados = { ...filtros, q: texto, ...nuevos };

        router.get(
            index.url(),
            Object.fromEntries(
                Object.entries(combinados).filter(([, valor]) => valor !== ''),
            ),
            { preserveState: true, replace: true },
        );
    }

    function nueva() {
        formulario.reset();
        formulario.clearErrors();
        setEditando(null);
        setAbierto(true);
    }

    function editar(institucion: Institucion) {
        formulario.clearErrors();
        formulario.setData({
            nombre: institucion.nombre,
            tipo: institucion.tipo ?? '',
            nombre_contacto: institucion.nombre_contacto ?? '',
            correo_contacto: institucion.correo_contacto ?? '',
            telefono_contacto: institucion.telefono_contacto ?? '',
            direccion: institucion.direccion ?? '',
        });
        setEditando(institucion);
        setAbierto(true);
    }

    function enviar(evento: FormEvent) {
        evento.preventDefault();

        const opciones = {
            preserveScroll: true,
            onSuccess: () => setAbierto(false),
        };

        if (editando === null) {
            formulario.post(store.url(), opciones);
        } else {
            formulario.put(update.url(editando.id), opciones);
        }
    }

    function eliminar() {
        if (eliminando === null) {
            return;
        }

        router.delete(destroy.url(eliminando.id), {
            preserveScroll: true,
            onStart: () => setBorrando(true),
            onFinish: () => {
                setBorrando(false);
                setEliminando(null);
            },
        });
    }

    function campo(
        nombre: keyof Formulario,
        etiqueta: string,
        opciones: { max?: number; tipo?: string; lista?: string } = {},
    ) {
        return (
            <div className="grid gap-2">
                <Label htmlFor={nombre}>{etiqueta}</Label>
                <Input
                    id={nombre}
                    type={opciones.tipo ?? 'text'}
                    list={opciones.lista}
                    value={formulario.data[nombre]}
                    maxLength={opciones.max ?? 255}
                    onChange={(evento) =>
                        formulario.setData(nombre, evento.target.value)
                    }
                    aria-invalid={!!formulario.errors[nombre]}
                />
                <InputError message={formulario.errors[nombre]} />
            </div>
        );
    }

    return (
        <>
            <Head title="Instituciones aliadas" />
            <div className="flex flex-1 flex-col gap-6 p-4">
                <div className="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <h1 className="text-2xl font-semibold tracking-tight">
                            Instituciones aliadas
                        </h1>
                        <p className="text-muted-foreground mt-1 max-w-2xl text-sm">
                            Instituciones que participaron o cooperaron con los
                            proyectos de EPS: ministerios, ONG, socios. Los
                            estudiantes las eligen de este catálogo (no pueden
                            crearlas) y son las que se cuantifican en las
                            estadísticas. Una que ya figura en algún EPS no se
                            puede eliminar.
                        </p>
                    </div>
                    <Button onClick={nueva}>
                        <Plus />
                        Agregar institución aliada
                    </Button>
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
                            placeholder="Nombre, contacto o correo"
                            className="pl-9"
                        />
                    </div>
                    <Select
                        value={filtros.tipo || TODOS}
                        onValueChange={(valor) =>
                            filtrar({ tipo: valor === TODOS ? '' : valor })
                        }
                    >
                        <SelectTrigger className="w-64" aria-label="Tipo">
                            <SelectValue />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value={TODOS}>
                                Todos los tipos
                            </SelectItem>
                            {tipos.map((tipo) => (
                                <SelectItem key={tipo} value={tipo}>
                                    {tipo}
                                </SelectItem>
                            ))}
                        </SelectContent>
                    </Select>
                    <Button type="submit" variant="outline">
                        Buscar
                    </Button>
                </form>

                {instituciones.length === 0 ? (
                    <p className="border-sidebar-border/70 text-muted-foreground rounded-xl border border-dashed p-10 text-center text-sm">
                        No hay instituciones que coincidan.
                    </p>
                ) : (
                    <div className="border-sidebar-border/70 overflow-x-auto rounded-xl border">
                        <table className="w-full text-sm">
                            <thead>
                                <tr className="text-muted-foreground border-b text-left text-xs">
                                    <th className="px-4 py-3 font-medium">
                                        Institución
                                    </th>
                                    <th className="px-4 py-3 font-medium">
                                        Tipo
                                    </th>
                                    <th className="px-4 py-3 font-medium">
                                        Contacto
                                    </th>
                                    <th className="px-4 py-3 text-right font-medium">
                                        En EPS
                                    </th>
                                    <th className="px-4 py-3" />
                                </tr>
                            </thead>
                            <tbody>
                                {instituciones.map((institucion) => (
                                    <tr
                                        key={institucion.id}
                                        className="border-b align-top last:border-0"
                                    >
                                        <td className="px-4 py-3 font-medium">
                                            {institucion.nombre}
                                        </td>
                                        <td className="px-4 py-3">
                                            {institucion.tipo ? (
                                                <Badge variant="secondary">
                                                    {institucion.tipo}
                                                </Badge>
                                            ) : (
                                                <span className="text-muted-foreground">
                                                    —
                                                </span>
                                            )}
                                        </td>
                                        <td className="text-muted-foreground px-4 py-3 text-xs">
                                            {[
                                                institucion.nombre_contacto,
                                                institucion.correo_contacto,
                                                institucion.telefono_contacto,
                                            ]
                                                .filter(Boolean)
                                                .join(' · ') || '—'}
                                        </td>
                                        <td className="px-4 py-3 text-right font-mono tabular-nums">
                                            {institucion.eps}
                                        </td>
                                        <td className="px-4 py-3">
                                            <div className="flex justify-end gap-2">
                                                <Button
                                                    variant="outline"
                                                    size="sm"
                                                    onClick={() =>
                                                        editar(institucion)
                                                    }
                                                >
                                                    <Pencil />
                                                    <span className="sr-only xl:not-sr-only">
                                                        Editar
                                                    </span>
                                                </Button>
                                                <Button
                                                    variant="outline"
                                                    size="sm"
                                                    onClick={() =>
                                                        setEliminando(
                                                            institucion,
                                                        )
                                                    }
                                                >
                                                    <Trash2 />
                                                    <span className="sr-only xl:not-sr-only">
                                                        Eliminar
                                                    </span>
                                                </Button>
                                            </div>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                )}

                <Paginacion pagina={pagina} />
            </div>

            <Dialog open={abierto} onOpenChange={setAbierto}>
                <DialogContent className="max-h-[90vh] overflow-y-auto">
                    <DialogHeader>
                        <DialogTitle>
                            {editando === null
                                ? 'Agregar institución aliada'
                                : 'Editar institución aliada'}
                        </DialogTitle>
                        <DialogDescription>
                            Ministerio, ONG o socio que participa o coopera con
                            los proyectos de EPS.
                        </DialogDescription>
                    </DialogHeader>

                    <form onSubmit={enviar} className="grid gap-5">
                        {campo('nombre', 'Nombre')}
                        {campo('tipo', 'Tipo', { max: 100, lista: 'tipos' })}
                        <datalist id="tipos">
                            {sugerencias.map((tipo) => (
                                <option key={tipo} value={tipo} />
                            ))}
                        </datalist>
                        <div className="grid gap-5 sm:grid-cols-2">
                            {campo('nombre_contacto', 'Persona de contacto')}
                            {campo('telefono_contacto', 'Teléfono', {
                                max: 30,
                            })}
                        </div>
                        {campo('correo_contacto', 'Correo de contacto', {
                            tipo: 'email',
                        })}
                        {campo('direccion', 'Dirección', { max: 500 })}

                        <DialogFooter>
                            <Button
                                type="button"
                                variant="outline"
                                onClick={() => setAbierto(false)}
                            >
                                Cancelar
                            </Button>
                            <Button
                                type="submit"
                                disabled={formulario.processing}
                            >
                                {formulario.processing && <Spinner />}
                                Guardar
                            </Button>
                        </DialogFooter>
                    </form>
                </DialogContent>
            </Dialog>

            <DialogoEliminar
                titulo="Eliminar institución aliada"
                descripcion={`${eliminando?.nombre ?? ''}. Si figura en el EPS de algún estudiante, no se eliminará.`}
                abierto={eliminando !== null}
                procesando={borrando}
                onCancelar={() => setEliminando(null)}
                onConfirmar={eliminar}
            />
        </>
    );
}

Instituciones.layout = {
    breadcrumbs: [
        { title: 'Panel', href: dashboard() },
        { title: 'Manejo de datos', href: datos() },
    ],
};
