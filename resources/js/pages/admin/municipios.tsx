import { Head, router, useForm } from '@inertiajs/react';
import { Pencil, Plus, Search, Trash2 } from 'lucide-react';
import { useState } from 'react';
import type { FormEvent } from 'react';
import {
    destroy,
    index,
    store,
    update,
} from '@/actions/App/Http/Controllers/Admin/MunicipioController';
import DialogoEliminar from '@/components/admin/dialogo-eliminar';
import SelectorCoordenadas from '@/components/admin/selector-coordenadas';
import type { ConfiguracionMapa } from '@/components/admin/selector-coordenadas';
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
import type { Opcion } from '@/types/estudiante';

type Municipio = {
    id: number;
    codigo: string;
    nombre: string;
    departamento_id: number;
    departamento: string;
    latitud: string | null;
    longitud: string | null;
    en_uso: boolean;
};

type Props = {
    municipios: Municipio[];
    pagina: Pagina;
    filtros: { q: string; departamento: string };
    departamentos: (Opcion & { codigo: string })[];
    googleMaps: ConfiguracionMapa;
};

type Formulario = {
    departamento_id: string;
    codigo: string;
    nombre: string;
    latitud: string;
    longitud: string;
};

const VACIO: Formulario = {
    departamento_id: '',
    codigo: '',
    nombre: '',
    latitud: '',
    longitud: '',
};
const TODOS = '__todos__';

export default function Municipios({
    municipios,
    pagina,
    filtros,
    departamentos,
    googleMaps,
}: Props) {
    const formulario = useForm<Formulario>(VACIO);
    const [texto, setTexto] = useState(filtros.q);
    const [abierto, setAbierto] = useState(false);
    const [editando, setEditando] = useState<Municipio | null>(null);
    const [eliminando, setEliminando] = useState<Municipio | null>(null);
    const [borrando, setBorrando] = useState(false);

    function filtrar(nuevos: Partial<{ q: string; departamento: string }>) {
        const combinados = { ...filtros, q: texto, ...nuevos };

        router.get(
            index.url(),
            Object.fromEntries(
                Object.entries(combinados).filter(([, valor]) => valor !== ''),
            ),
            { preserveState: true, replace: true },
        );
    }

    function nuevo() {
        formulario.reset();
        formulario.clearErrors();
        formulario.setData({
            ...VACIO,
            departamento_id: filtros.departamento,
            codigo:
                departamentos.find((d) => d.value === filtros.departamento)
                    ?.codigo ?? '',
        });
        setEditando(null);
        setAbierto(true);
    }

    function editar(municipio: Municipio) {
        formulario.clearErrors();
        formulario.setData({
            departamento_id: String(municipio.departamento_id),
            codigo: municipio.codigo,
            nombre: municipio.nombre,
            latitud: municipio.latitud ?? '',
            longitud: municipio.longitud ?? '',
        });
        setEditando(municipio);
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

    return (
        <>
            <Head title="Municipios" />
            <div className="flex flex-1 flex-col gap-6 p-4">
                <div className="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <h1 className="text-2xl font-semibold tracking-tight">
                            Municipios
                        </h1>
                        <p className="text-muted-foreground mt-1 max-w-2xl text-sm">
                            Los estudiantes eligen su municipio de este
                            catálogo. Un municipio donde ya hay EPS no se puede
                            eliminar.
                        </p>
                    </div>
                    <Button onClick={nuevo}>
                        <Plus />
                        Agregar municipio
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
                            placeholder="Nombre o código"
                            className="pl-9"
                        />
                    </div>
                    <Select
                        value={filtros.departamento || TODOS}
                        onValueChange={(valor) =>
                            filtrar({
                                departamento: valor === TODOS ? '' : valor,
                            })
                        }
                    >
                        <SelectTrigger
                            className="w-64"
                            aria-label="Departamento"
                        >
                            <SelectValue />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value={TODOS}>
                                Todos los departamentos
                            </SelectItem>
                            {departamentos.map((departamento) => (
                                <SelectItem
                                    key={departamento.value}
                                    value={departamento.value}
                                >
                                    {departamento.label}
                                </SelectItem>
                            ))}
                        </SelectContent>
                    </Select>
                    <Button type="submit" variant="outline">
                        Buscar
                    </Button>
                </form>

                {municipios.length === 0 ? (
                    <p className="border-sidebar-border/70 text-muted-foreground rounded-xl border border-dashed p-10 text-center text-sm">
                        No hay municipios que coincidan.
                    </p>
                ) : (
                    <div className="border-sidebar-border/70 overflow-x-auto rounded-xl border">
                        <table className="w-full text-sm">
                            <thead>
                                <tr className="text-muted-foreground border-b text-left text-xs">
                                    <th className="px-4 py-3 font-medium">
                                        Código
                                    </th>
                                    <th className="px-4 py-3 font-medium">
                                        Municipio
                                    </th>
                                    <th className="px-4 py-3 font-medium">
                                        Departamento
                                    </th>
                                    <th className="px-4 py-3 font-medium">
                                        Coordenadas
                                    </th>
                                    <th className="px-4 py-3" />
                                </tr>
                            </thead>
                            <tbody>
                                {municipios.map((municipio) => (
                                    <tr
                                        key={municipio.id}
                                        className="border-b last:border-0"
                                    >
                                        <td className="px-4 py-3 font-mono">
                                            {municipio.codigo}
                                        </td>
                                        <td className="px-4 py-3 font-medium">
                                            <span className="mr-2">
                                                {municipio.nombre}
                                            </span>
                                            {municipio.en_uso && (
                                                <Badge variant="outline">
                                                    En uso
                                                </Badge>
                                            )}
                                        </td>
                                        <td className="px-4 py-3">
                                            {municipio.departamento}
                                        </td>
                                        <td className="text-muted-foreground px-4 py-3 font-mono text-xs">
                                            {municipio.latitud !== null &&
                                            municipio.longitud !== null
                                                ? `${Number(municipio.latitud).toFixed(4)}, ${Number(municipio.longitud).toFixed(4)}`
                                                : 'Sin coordenadas'}
                                        </td>
                                        <td className="px-4 py-3">
                                            <div className="flex justify-end gap-2">
                                                <Button
                                                    variant="outline"
                                                    size="sm"
                                                    onClick={() =>
                                                        editar(municipio)
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
                                                        setEliminando(municipio)
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
                                ? 'Agregar municipio'
                                : 'Editar municipio'}
                        </DialogTitle>
                        <DialogDescription>
                            El código empieza con el del departamento (por
                            ejemplo 0301 para el primer municipio de
                            Sacatepéquez).
                        </DialogDescription>
                    </DialogHeader>

                    <form onSubmit={enviar} className="grid gap-5">
                        <div className="grid gap-2">
                            <Label htmlFor="departamento_id">
                                Departamento
                            </Label>
                            <Select
                                value={
                                    formulario.data.departamento_id || undefined
                                }
                                onValueChange={(valor) =>
                                    formulario.setData('departamento_id', valor)
                                }
                            >
                                <SelectTrigger
                                    id="departamento_id"
                                    className="w-full"
                                    aria-invalid={
                                        !!formulario.errors.departamento_id
                                    }
                                >
                                    <SelectValue placeholder="Selecciona un departamento" />
                                </SelectTrigger>
                                <SelectContent>
                                    {departamentos.map((departamento) => (
                                        <SelectItem
                                            key={departamento.value}
                                            value={departamento.value}
                                        >
                                            {departamento.label}
                                        </SelectItem>
                                    ))}
                                </SelectContent>
                            </Select>
                            <InputError
                                message={formulario.errors.departamento_id}
                            />
                        </div>

                        <div className="grid grid-cols-[7rem_1fr] gap-3">
                            <div className="grid gap-2">
                                <Label htmlFor="codigo">Código</Label>
                                <Input
                                    id="codigo"
                                    value={formulario.data.codigo}
                                    maxLength={4}
                                    inputMode="numeric"
                                    placeholder="0301"
                                    onChange={(evento) =>
                                        formulario.setData(
                                            'codigo',
                                            evento.target.value,
                                        )
                                    }
                                    aria-invalid={!!formulario.errors.codigo}
                                />
                                <InputError
                                    message={formulario.errors.codigo}
                                />
                            </div>
                            <div className="grid gap-2">
                                <Label htmlFor="nombre">Nombre</Label>
                                <Input
                                    id="nombre"
                                    value={formulario.data.nombre}
                                    maxLength={150}
                                    onChange={(evento) =>
                                        formulario.setData(
                                            'nombre',
                                            evento.target.value,
                                        )
                                    }
                                    aria-invalid={!!formulario.errors.nombre}
                                />
                                <InputError
                                    message={formulario.errors.nombre}
                                />
                            </div>
                        </div>

                        <SelectorCoordenadas
                            googleMaps={googleMaps}
                            latitud={formulario.data.latitud}
                            longitud={formulario.data.longitud}
                            errorLatitud={formulario.errors.latitud}
                            errorLongitud={formulario.errors.longitud}
                            onCambiar={(latitud, longitud) =>
                                formulario.setData((previos) => ({
                                    ...previos,
                                    latitud,
                                    longitud,
                                }))
                            }
                        />

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
                titulo="Eliminar municipio"
                descripcion={`${eliminando?.nombre ?? ''}. Si ya hay EPS registrados en él, no se eliminará.`}
                abierto={eliminando !== null}
                procesando={borrando}
                onCancelar={() => setEliminando(null)}
                onConfirmar={eliminar}
            />
        </>
    );
}

Municipios.layout = {
    breadcrumbs: [
        { title: 'Panel', href: dashboard() },
        { title: 'Manejo de datos', href: datos() },
    ],
};
