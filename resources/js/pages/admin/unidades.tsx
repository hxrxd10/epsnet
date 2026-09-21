import { Head, router, useForm } from '@inertiajs/react';
import { MapPin, Pencil, Plus, Trash2 } from 'lucide-react';
import { useState } from 'react';
import type { FormEvent } from 'react';
import {
    destroy,
    store,
    update,
} from '@/actions/App/Http/Controllers/Admin/UnidadAcademicaController';
import DialogoEliminar from '@/components/admin/dialogo-eliminar';
import SelectorCoordenadas from '@/components/admin/selector-coordenadas';
import type { ConfiguracionMapa } from '@/components/admin/selector-coordenadas';
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
import { dashboard } from '@/routes';
import { datos } from '@/routes/admin';
import type { Opcion } from '@/types/estudiante';

type Unidad = {
    id: number;
    nombre: string;
    siglas: string | null;
    tipo: string;
    tipo_etiqueta: string;
    nombre_contacto: string | null;
    correo_contacto: string | null;
    telefono_contacto: string | null;
    direccion: string | null;
    latitud: string | null;
    longitud: string | null;
    activa: boolean;
    administrador: string | null;
    expedientes: number;
};

type Formulario = {
    nombre: string;
    siglas: string;
    tipo: string;
    nombre_contacto: string;
    correo_contacto: string;
    telefono_contacto: string;
    direccion: string;
    latitud: string;
    longitud: string;
    activa: boolean;
};

const VACIO: Formulario = {
    nombre: '',
    siglas: '',
    tipo: 'facultad',
    nombre_contacto: '',
    correo_contacto: '',
    telefono_contacto: '',
    direccion: '',
    latitud: '',
    longitud: '',
    activa: true,
};

export default function Unidades({
    unidades,
    tipos,
    googleMaps,
}: {
    unidades: Unidad[];
    tipos: Opcion[];
    googleMaps: ConfiguracionMapa;
}) {
    const formulario = useForm<Formulario>(VACIO);
    const [abierto, setAbierto] = useState(false);
    const [editando, setEditando] = useState<Unidad | null>(null);
    const [eliminando, setEliminando] = useState<Unidad | null>(null);
    const [borrando, setBorrando] = useState(false);

    function campoTexto(
        nombre: keyof Omit<Formulario, 'activa'>,
        etiqueta: string,
        opciones: { max?: number; tipo?: string } = {},
    ) {
        return (
            <div className="grid gap-2">
                <Label htmlFor={nombre}>{etiqueta}</Label>
                <Input
                    id={nombre}
                    type={opciones.tipo ?? 'text'}
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

    function nueva() {
        formulario.reset();
        formulario.clearErrors();
        setEditando(null);
        setAbierto(true);
    }

    function editar(unidad: Unidad) {
        formulario.clearErrors();
        formulario.setData({
            nombre: unidad.nombre,
            siglas: unidad.siglas ?? '',
            tipo: unidad.tipo,
            nombre_contacto: unidad.nombre_contacto ?? '',
            correo_contacto: unidad.correo_contacto ?? '',
            telefono_contacto: unidad.telefono_contacto ?? '',
            direccion: unidad.direccion ?? '',
            latitud: unidad.latitud ?? '',
            longitud: unidad.longitud ?? '',
            activa: unidad.activa,
        });
        setEditando(unidad);
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
            <Head title="Unidades académicas" />
            <div className="flex flex-1 flex-col gap-6 p-4">
                <div className="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <h1 className="text-2xl font-semibold tracking-tight">
                            Unidades académicas
                        </h1>
                        <p className="text-muted-foreground mt-1 max-w-2xl text-sm">
                            Facultades, escuelas y centros universitarios, con
                            la ubicación de su edificio. Las unidades también se
                            crean solas cuando un estudiante registra su
                            carrera; aquí completas sus datos.
                        </p>
                    </div>
                    <Button onClick={nueva}>
                        <Plus />
                        Agregar unidad
                    </Button>
                </div>

                {unidades.length === 0 ? (
                    <p className="border-sidebar-border/70 text-muted-foreground rounded-xl border border-dashed p-10 text-center text-sm">
                        Aún no hay unidades académicas.
                    </p>
                ) : (
                    <ul className="border-sidebar-border/70 divide-sidebar-border/70 divide-y rounded-xl border">
                        {unidades.map((unidad) => (
                            <li
                                key={unidad.id}
                                className="flex flex-col gap-3 p-4 sm:flex-row sm:items-center sm:justify-between"
                            >
                                <div className="min-w-0">
                                    <div className="flex flex-wrap items-center gap-2">
                                        <p className="font-medium">
                                            {unidad.nombre}
                                        </p>
                                        {unidad.siglas && (
                                            <span className="text-muted-foreground font-mono text-xs">
                                                {unidad.siglas}
                                            </span>
                                        )}
                                        <Badge variant="secondary">
                                            {unidad.tipo_etiqueta}
                                        </Badge>
                                        {!unidad.activa && (
                                            <Badge variant="outline">
                                                Inactiva
                                            </Badge>
                                        )}
                                    </div>
                                    <p className="text-muted-foreground mt-1 flex flex-wrap items-center gap-x-4 gap-y-1 text-sm">
                                        <span className="inline-flex items-center gap-1.5">
                                            <MapPin className="size-3.5" />
                                            {unidad.latitud !== null &&
                                            unidad.longitud !== null
                                                ? `${Number(unidad.latitud).toFixed(4)}, ${Number(unidad.longitud).toFixed(4)}`
                                                : 'Sin ubicación'}
                                        </span>
                                        <span>{unidad.expedientes} EPS</span>
                                        {unidad.administrador && (
                                            <span>
                                                Administra:{' '}
                                                {unidad.administrador}
                                            </span>
                                        )}
                                    </p>
                                </div>
                                <div className="flex shrink-0 gap-2">
                                    <Button
                                        variant="outline"
                                        size="sm"
                                        onClick={() => editar(unidad)}
                                    >
                                        <Pencil />
                                        Editar
                                    </Button>
                                    <Button
                                        variant="outline"
                                        size="sm"
                                        onClick={() => setEliminando(unidad)}
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
                <DialogContent className="max-h-[90vh] overflow-y-auto sm:max-w-2xl">
                    <DialogHeader>
                        <DialogTitle>
                            {editando === null
                                ? 'Agregar unidad académica'
                                : 'Editar unidad académica'}
                        </DialogTitle>
                        <DialogDescription>
                            El nombre debe coincidir con el que devuelve el
                            Registro y Estadística para vincular las carreras.
                        </DialogDescription>
                    </DialogHeader>

                    <form onSubmit={enviar} className="grid gap-5">
                        {campoTexto('nombre', 'Nombre')}

                        <div className="grid gap-5 sm:grid-cols-2">
                            {campoTexto('siglas', 'Siglas', { max: 30 })}
                            <div className="grid gap-2">
                                <Label htmlFor="tipo">Tipo</Label>
                                <Select
                                    value={formulario.data.tipo}
                                    onValueChange={(valor) =>
                                        formulario.setData('tipo', valor)
                                    }
                                >
                                    <SelectTrigger id="tipo" className="w-full">
                                        <SelectValue />
                                    </SelectTrigger>
                                    <SelectContent>
                                        {tipos.map((tipo) => (
                                            <SelectItem
                                                key={tipo.value}
                                                value={tipo.value}
                                            >
                                                {tipo.label}
                                            </SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>
                                <InputError message={formulario.errors.tipo} />
                            </div>
                        </div>

                        <div className="grid gap-5 sm:grid-cols-2">
                            {campoTexto(
                                'nombre_contacto',
                                'Nombre de contacto',
                            )}
                            {campoTexto('telefono_contacto', 'Teléfono', {
                                max: 30,
                            })}
                        </div>
                        {campoTexto('correo_contacto', 'Correo de contacto', {
                            tipo: 'email',
                        })}
                        {campoTexto('direccion', 'Dirección del edificio', {
                            max: 500,
                        })}

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

                        <div className="flex items-center gap-3">
                            <Checkbox
                                id="activa"
                                checked={formulario.data.activa}
                                onCheckedChange={(marcado) =>
                                    formulario.setData(
                                        'activa',
                                        marcado === true,
                                    )
                                }
                            />
                            <Label htmlFor="activa">Activa</Label>
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

            <DialogoEliminar
                titulo="Eliminar unidad académica"
                descripcion={`${eliminando?.nombre ?? ''}. Si tiene EPS o un administrador asignado, no se eliminará: desactívala.`}
                abierto={eliminando !== null}
                procesando={borrando}
                onCancelar={() => setEliminando(null)}
                onConfirmar={eliminar}
            />
        </>
    );
}

Unidades.layout = {
    breadcrumbs: [
        { title: 'Panel', href: dashboard() },
        { title: 'Manejo de datos', href: datos() },
    ],
};
