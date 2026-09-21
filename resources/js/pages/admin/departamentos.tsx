import { Head, router, useForm } from '@inertiajs/react';
import { Pencil, Plus, Trash2 } from 'lucide-react';
import { useState } from 'react';
import type { FormEvent } from 'react';
import {
    destroy,
    store,
    update,
} from '@/actions/App/Http/Controllers/Admin/DepartamentoController';
import DialogoEliminar from '@/components/admin/dialogo-eliminar';
import SelectorCoordenadas from '@/components/admin/selector-coordenadas';
import type { ConfiguracionMapa } from '@/components/admin/selector-coordenadas';
import InputError from '@/components/input-error';
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
import { Spinner } from '@/components/ui/spinner';
import { dashboard } from '@/routes';
import { datos } from '@/routes/admin';

type Departamento = {
    id: number;
    codigo: string;
    nombre: string;
    cabecera: string;
    latitud: string | null;
    longitud: string | null;
    municipios: number;
};

type Formulario = {
    codigo: string;
    nombre: string;
    cabecera: string;
    latitud: string;
    longitud: string;
};

const VACIO: Formulario = {
    codigo: '',
    nombre: '',
    cabecera: '',
    latitud: '',
    longitud: '',
};

export default function Departamentos({
    departamentos,
    googleMaps,
}: {
    departamentos: Departamento[];
    googleMaps: ConfiguracionMapa;
}) {
    const formulario = useForm<Formulario>(VACIO);
    const [abierto, setAbierto] = useState(false);
    const [editando, setEditando] = useState<Departamento | null>(null);
    const [eliminando, setEliminando] = useState<Departamento | null>(null);
    const [borrando, setBorrando] = useState(false);

    function nuevo() {
        formulario.reset();
        formulario.clearErrors();
        setEditando(null);
        setAbierto(true);
    }

    function editar(departamento: Departamento) {
        formulario.clearErrors();
        formulario.setData({
            codigo: departamento.codigo,
            nombre: departamento.nombre,
            cabecera: departamento.cabecera,
            latitud: departamento.latitud ?? '',
            longitud: departamento.longitud ?? '',
        });
        setEditando(departamento);
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
            <Head title="Departamentos" />
            <div className="flex flex-1 flex-col gap-6 p-4">
                <div className="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <h1 className="text-2xl font-semibold tracking-tight">
                            Departamentos
                        </h1>
                        <p className="text-muted-foreground mt-1 max-w-2xl text-sm">
                            Los departamentos del país, con su cabecera y una
                            coordenada de referencia. Un departamento con
                            municipios o EPS no se puede eliminar.
                        </p>
                    </div>
                    <Button onClick={nuevo}>
                        <Plus />
                        Agregar departamento
                    </Button>
                </div>

                <div className="border-sidebar-border/70 overflow-x-auto rounded-xl border">
                    <table className="w-full text-sm">
                        <thead>
                            <tr className="text-muted-foreground border-b text-left text-xs">
                                <th className="px-4 py-3 font-medium">
                                    Código
                                </th>
                                <th className="px-4 py-3 font-medium">
                                    Departamento
                                </th>
                                <th className="px-4 py-3 font-medium">
                                    Cabecera
                                </th>
                                <th className="px-4 py-3 text-right font-medium">
                                    Municipios
                                </th>
                                <th className="px-4 py-3 font-medium">
                                    Coordenadas
                                </th>
                                <th className="px-4 py-3" />
                            </tr>
                        </thead>
                        <tbody>
                            {departamentos.map((departamento) => (
                                <tr
                                    key={departamento.id}
                                    className="border-b last:border-0"
                                >
                                    <td className="px-4 py-3 font-mono">
                                        {departamento.codigo}
                                    </td>
                                    <td className="px-4 py-3 font-medium">
                                        {departamento.nombre}
                                    </td>
                                    <td className="px-4 py-3">
                                        {departamento.cabecera}
                                    </td>
                                    <td className="px-4 py-3 text-right font-mono tabular-nums">
                                        {departamento.municipios}
                                    </td>
                                    <td className="text-muted-foreground px-4 py-3 font-mono text-xs">
                                        {departamento.latitud !== null &&
                                        departamento.longitud !== null
                                            ? `${Number(departamento.latitud).toFixed(4)}, ${Number(departamento.longitud).toFixed(4)}`
                                            : 'Sin coordenadas'}
                                    </td>
                                    <td className="px-4 py-3">
                                        <div className="flex justify-end gap-2">
                                            <Button
                                                variant="outline"
                                                size="sm"
                                                onClick={() =>
                                                    editar(departamento)
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
                                                    setEliminando(departamento)
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
            </div>

            <Dialog open={abierto} onOpenChange={setAbierto}>
                <DialogContent className="max-h-[90vh] overflow-y-auto">
                    <DialogHeader>
                        <DialogTitle>
                            {editando === null
                                ? 'Agregar departamento'
                                : 'Editar departamento'}
                        </DialogTitle>
                        <DialogDescription>Departamentos</DialogDescription>
                    </DialogHeader>

                    <form onSubmit={enviar} className="grid gap-5">
                        <div className="grid grid-cols-[6rem_1fr] gap-3">
                            <div className="grid gap-2">
                                <Label htmlFor="codigo">Código</Label>
                                <Input
                                    id="codigo"
                                    value={formulario.data.codigo}
                                    maxLength={2}
                                    inputMode="numeric"
                                    placeholder="01"
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
                                    maxLength={100}
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

                        <div className="grid gap-2">
                            <Label htmlFor="cabecera">
                                Cabecera departamental
                            </Label>
                            <Input
                                id="cabecera"
                                value={formulario.data.cabecera}
                                maxLength={100}
                                onChange={(evento) =>
                                    formulario.setData(
                                        'cabecera',
                                        evento.target.value,
                                    )
                                }
                                aria-invalid={!!formulario.errors.cabecera}
                            />
                            <InputError message={formulario.errors.cabecera} />
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
                titulo="Eliminar departamento"
                descripcion={`${eliminando?.nombre ?? ''}. Si tiene municipios o EPS registrados, no se eliminará.`}
                abierto={eliminando !== null}
                procesando={borrando}
                onCancelar={() => setEliminando(null)}
                onConfirmar={eliminar}
            />
        </>
    );
}

Departamentos.layout = {
    breadcrumbs: [
        { title: 'Panel', href: dashboard() },
        { title: 'Manejo de datos', href: datos() },
    ],
};
