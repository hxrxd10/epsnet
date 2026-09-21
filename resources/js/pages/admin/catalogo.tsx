import { Head, router, useForm } from '@inertiajs/react';
import { Pencil, Plus, Trash2 } from 'lucide-react';
import { useState } from 'react';
import type { FormEvent } from 'react';
import {
    destroy,
    store,
    update,
} from '@/actions/App/Http/Controllers/Admin/CatalogoController';
import InputError from '@/components/input-error';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
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
import { Textarea } from '@/components/ui/textarea';
import { dashboard } from '@/routes';
import { datos } from '@/routes/admin';
import type { Opcion } from '@/types/estudiante';

type Elemento = {
    id: number;
    nombre: string;
    descripcion: string | null;
    categoria: string | null;
    categoria_etiqueta: string | null;
    activo: boolean;
    en_uso: boolean;
};

type Props = {
    catalogo: {
        clave: string;
        etiqueta: string;
        descripcion: string;
        usa_categoria: boolean;
        categorias: Opcion[];
    };
    elementos: Elemento[];
};

type Formulario = {
    nombre: string;
    descripcion: string;
    categoria: string;
    activo: boolean;
};

const VACIO: Formulario = {
    nombre: '',
    descripcion: '',
    categoria: '',
    activo: true,
};

export default function Catalogo({ catalogo, elementos }: Props) {
    const formulario = useForm<Formulario>(VACIO);
    const [abierto, setAbierto] = useState(false);
    const [editando, setEditando] = useState<Elemento | null>(null);
    const [eliminando, setEliminando] = useState<Elemento | null>(null);
    const [borrando, setBorrando] = useState(false);

    function nuevo() {
        formulario.reset();
        formulario.clearErrors();
        setEditando(null);
        setAbierto(true);
    }

    function editar(elemento: Elemento) {
        formulario.clearErrors();
        formulario.setData({
            nombre: elemento.nombre,
            descripcion: elemento.descripcion ?? '',
            categoria: elemento.categoria ?? '',
            activo: elemento.activo,
        });
        setEditando(elemento);
        setAbierto(true);
    }

    function enviar(evento: FormEvent) {
        evento.preventDefault();

        const opciones = {
            preserveScroll: true,
            onSuccess: () => setAbierto(false),
        };

        if (editando === null) {
            formulario.post(store.url(catalogo.clave), opciones);
        } else {
            formulario.put(
                update.url({ catalogo: catalogo.clave, item: editando.id }),
                opciones,
            );
        }
    }

    function eliminar() {
        if (eliminando === null) {
            return;
        }

        router.delete(
            destroy.url({ catalogo: catalogo.clave, item: eliminando.id }),
            {
                preserveScroll: true,
                onStart: () => setBorrando(true),
                onFinish: () => {
                    setBorrando(false);
                    setEliminando(null);
                },
            },
        );
    }

    return (
        <>
            <Head title={catalogo.etiqueta} />
            <div className="flex flex-1 flex-col gap-6 p-4">
                <div className="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <h1 className="text-2xl font-semibold tracking-tight">
                            {catalogo.etiqueta}
                        </h1>
                        <p className="text-muted-foreground mt-1 max-w-2xl text-sm">
                            {catalogo.descripcion}
                        </p>
                    </div>
                    <Button onClick={nuevo}>
                        <Plus />
                        Agregar elemento
                    </Button>
                </div>

                {elementos.length === 0 ? (
                    <p className="border-sidebar-border/70 text-muted-foreground rounded-xl border border-dashed p-10 text-center text-sm">
                        Este catálogo aún no tiene elementos. Agrega el primero
                        para que los estudiantes puedan elegirlo.
                    </p>
                ) : (
                    <ul className="border-sidebar-border/70 divide-sidebar-border/70 divide-y rounded-xl border">
                        {elementos.map((elemento) => (
                            <li
                                key={elemento.id}
                                className="flex flex-col gap-3 p-4 sm:flex-row sm:items-center sm:justify-between"
                            >
                                <div className="min-w-0">
                                    <div className="flex flex-wrap items-center gap-2">
                                        <p className="font-medium">
                                            {elemento.nombre}
                                        </p>
                                        {elemento.categoria_etiqueta && (
                                            <Badge variant="secondary">
                                                {elemento.categoria_etiqueta}
                                            </Badge>
                                        )}
                                        {!elemento.activo && (
                                            <Badge variant="outline">
                                                Inactivo
                                            </Badge>
                                        )}
                                        {elemento.en_uso && (
                                            <Badge variant="outline">
                                                En uso
                                            </Badge>
                                        )}
                                    </div>
                                    {elemento.descripcion && (
                                        <p className="text-muted-foreground mt-1 line-clamp-2 text-sm">
                                            {elemento.descripcion}
                                        </p>
                                    )}
                                </div>
                                <div className="flex shrink-0 gap-2">
                                    <Button
                                        variant="outline"
                                        size="sm"
                                        onClick={() => editar(elemento)}
                                    >
                                        <Pencil />
                                        Editar
                                    </Button>
                                    <Button
                                        variant="outline"
                                        size="sm"
                                        onClick={() => setEliminando(elemento)}
                                    >
                                        <Trash2 />
                                        Eliminar
                                    </Button>
                                </div>
                            </li>
                        ))}
                    </ul>
                )}
            </div>

            <Dialog open={abierto} onOpenChange={setAbierto}>
                <DialogContent>
                    <DialogHeader>
                        <DialogTitle>
                            {editando === null
                                ? 'Agregar elemento'
                                : 'Editar elemento'}
                        </DialogTitle>
                        <DialogDescription>
                            {catalogo.etiqueta}
                        </DialogDescription>
                    </DialogHeader>

                    <form onSubmit={enviar} className="grid gap-5">
                        <div className="grid gap-2">
                            <Label htmlFor="nombre">Nombre</Label>
                            <Input
                                id="nombre"
                                value={formulario.data.nombre}
                                maxLength={255}
                                onChange={(evento) =>
                                    formulario.setData(
                                        'nombre',
                                        evento.target.value,
                                    )
                                }
                                aria-invalid={!!formulario.errors.nombre}
                            />
                            <InputError message={formulario.errors.nombre} />
                        </div>

                        {catalogo.usa_categoria && (
                            <div className="grid gap-2">
                                <Label htmlFor="categoria">Categoría</Label>
                                <Select
                                    value={
                                        formulario.data.categoria || undefined
                                    }
                                    onValueChange={(valor) =>
                                        formulario.setData('categoria', valor)
                                    }
                                >
                                    <SelectTrigger
                                        id="categoria"
                                        className="w-full"
                                    >
                                        <SelectValue placeholder="Selecciona una categoría" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        {catalogo.categorias.map((opcion) => (
                                            <SelectItem
                                                key={opcion.value}
                                                value={opcion.value}
                                            >
                                                {opcion.label}
                                            </SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>
                                <InputError
                                    message={formulario.errors.categoria}
                                />
                            </div>
                        )}

                        <div className="grid gap-2">
                            <Label htmlFor="descripcion">Descripción</Label>
                            <Textarea
                                id="descripcion"
                                rows={4}
                                maxLength={5000}
                                value={formulario.data.descripcion}
                                onChange={(evento) =>
                                    formulario.setData(
                                        'descripcion',
                                        evento.target.value,
                                    )
                                }
                            />
                            <InputError
                                message={formulario.errors.descripcion}
                            />
                        </div>

                        <div className="flex items-center gap-3">
                            <Checkbox
                                id="activo"
                                checked={formulario.data.activo}
                                onCheckedChange={(marcado) =>
                                    formulario.setData(
                                        'activo',
                                        marcado === true,
                                    )
                                }
                            />
                            <Label htmlFor="activo">
                                Activo (los estudiantes pueden elegirlo)
                            </Label>
                        </div>

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

            <Dialog
                open={eliminando !== null}
                onOpenChange={(valor) => !valor && setEliminando(null)}
            >
                <DialogContent>
                    <DialogHeader>
                        <DialogTitle>Eliminar elemento</DialogTitle>
                        <DialogDescription>
                            {eliminando?.nombre}. Si los estudiantes ya lo usan,
                            no se eliminará: desactívalo para conservar sus
                            registros.
                        </DialogDescription>
                    </DialogHeader>
                    <DialogFooter>
                        <Button
                            variant="outline"
                            onClick={() => setEliminando(null)}
                        >
                            Cancelar
                        </Button>
                        <Button
                            variant="destructive"
                            disabled={borrando}
                            onClick={eliminar}
                        >
                            {borrando && <Spinner />}
                            Eliminar
                        </Button>
                    </DialogFooter>
                </DialogContent>
            </Dialog>
        </>
    );
}

Catalogo.layout = {
    breadcrumbs: [
        { title: 'Panel', href: dashboard() },
        { title: 'Manejo de datos', href: datos() },
    ],
};
