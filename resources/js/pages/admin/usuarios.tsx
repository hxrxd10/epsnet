import { Head, router, useForm } from '@inertiajs/react';
import { Search, ShieldCheck } from 'lucide-react';
import { useState } from 'react';
import type { FormEvent } from 'react';
import { update } from '@/actions/App/Http/Controllers/Admin/UsuarioController';
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
import { index } from '@/routes/admin/usuarios';
import type { Opcion } from '@/types/estudiante';

type Usuario = {
    id: number;
    name: string;
    email: string;
    rol: string | null;
    rol_etiqueta: string;
    unidad: string | null;
    unidad_id: number | null;
    correo_verificado: boolean;
    editable: boolean;
    propio: boolean;
};

type UnidadOpcion = Opcion & {
    administrador_id: number | null;
    administrador: string | null;
};

type Props = {
    usuarios: Usuario[];
    pagina: Pagina;
    filtros: { q: string; rol: string };
    roles: Opcion[];
    rolesAsignables: Opcion[];
    unidades: UnidadOpcion[];
};

const TODOS = '__todos__';

export default function Usuarios({
    usuarios,
    pagina,
    filtros,
    roles,
    rolesAsignables,
    unidades,
}: Props) {
    const [texto, setTexto] = useState(filtros.q);
    const [editando, setEditando] = useState<Usuario | null>(null);
    const formulario = useForm({ rol: '', unidad_academica_id: '' });

    function filtrar(nuevos: Partial<{ q: string; rol: string }>) {
        const combinados = { q: texto, rol: filtros.rol, ...nuevos };

        router.get(
            index.url(),
            Object.fromEntries(
                Object.entries(combinados).filter(([, valor]) => valor !== ''),
            ),
            { preserveState: true, replace: true },
        );
    }

    function abrir(usuario: Usuario) {
        formulario.clearErrors();
        formulario.setData({
            rol: usuario.rol ?? '',
            unidad_academica_id: usuario.unidad_id?.toString() ?? '',
        });
        setEditando(usuario);
    }

    function guardar(evento: FormEvent) {
        evento.preventDefault();

        if (editando === null) {
            return;
        }

        formulario.transform((datos) => ({
            ...datos,
            unidad_academica_id:
                datos.rol === 'unidad_academica'
                    ? datos.unidad_academica_id
                    : '',
        }));
        formulario.put(update.url(editando.id), {
            preserveScroll: true,
            onSuccess: () => setEditando(null),
        });
    }

    return (
        <>
            <Head title="Usuarios" />
            <div className="flex flex-1 flex-col gap-6 p-4">
                <div>
                    <h1 className="text-2xl font-semibold tracking-tight">
                        Usuarios
                    </h1>
                    <p className="text-muted-foreground mt-1 max-w-2xl text-sm">
                        Quien se registra entra como invitado. Desde aquí le das
                        el rol de DIGEU o de unidad académica (con la unidad que
                        administrará). El rol de estudiante se obtiene al
                        verificar el registro académico.
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
                            placeholder="Buscar por nombre o correo"
                            className="pl-9"
                        />
                    </div>
                    <Select
                        value={filtros.rol || TODOS}
                        onValueChange={(valor) =>
                            filtrar({ rol: valor === TODOS ? '' : valor })
                        }
                    >
                        <SelectTrigger className="w-52">
                            <SelectValue />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value={TODOS}>
                                Todos los roles
                            </SelectItem>
                            {roles.map((rol) => (
                                <SelectItem key={rol.value} value={rol.value}>
                                    {rol.label}
                                </SelectItem>
                            ))}
                        </SelectContent>
                    </Select>
                    <Button type="submit" variant="outline">
                        Buscar
                    </Button>
                </form>

                {usuarios.length === 0 ? (
                    <p className="border-sidebar-border/70 text-muted-foreground rounded-xl border border-dashed p-10 text-center text-sm">
                        No hay usuarios que coincidan con la búsqueda.
                    </p>
                ) : (
                    <ul className="border-sidebar-border/70 divide-sidebar-border/70 divide-y rounded-xl border">
                        {usuarios.map((usuario) => (
                            <li
                                key={usuario.id}
                                className="flex flex-col gap-3 p-4 sm:flex-row sm:items-center sm:justify-between"
                            >
                                <div className="min-w-0">
                                    <div className="flex flex-wrap items-center gap-2">
                                        <p className="font-medium">
                                            {usuario.name}
                                        </p>
                                        <Badge variant="secondary">
                                            {usuario.rol_etiqueta}
                                        </Badge>
                                        {usuario.propio && (
                                            <Badge variant="outline">Tú</Badge>
                                        )}
                                        {!usuario.correo_verificado && (
                                            <Badge variant="outline">
                                                Correo sin verificar
                                            </Badge>
                                        )}
                                    </div>
                                    <p className="text-muted-foreground mt-1 text-sm">
                                        {usuario.email}
                                        {usuario.unidad &&
                                            ` · ${usuario.unidad}`}
                                    </p>
                                </div>
                                {usuario.editable && (
                                    <Button
                                        variant="outline"
                                        size="sm"
                                        onClick={() => abrir(usuario)}
                                    >
                                        <ShieldCheck />
                                        Editar acceso
                                    </Button>
                                )}
                            </li>
                        ))}
                    </ul>
                )}

                <Paginacion pagina={pagina} />
            </div>

            <Dialog
                open={editando !== null}
                onOpenChange={(abierto) => !abierto && setEditando(null)}
            >
                <DialogContent>
                    <DialogHeader>
                        <DialogTitle>Editar acceso</DialogTitle>
                        <DialogDescription>
                            {editando?.name} · {editando?.email}
                        </DialogDescription>
                    </DialogHeader>

                    <form onSubmit={guardar} className="grid gap-5">
                        <div className="grid gap-2">
                            <Label htmlFor="rol">Rol</Label>
                            <Select
                                value={formulario.data.rol || undefined}
                                onValueChange={(valor) =>
                                    formulario.setData('rol', valor)
                                }
                            >
                                <SelectTrigger id="rol" className="w-full">
                                    <SelectValue placeholder="Selecciona un rol" />
                                </SelectTrigger>
                                <SelectContent>
                                    {rolesAsignables.map((rol) => (
                                        <SelectItem
                                            key={rol.value}
                                            value={rol.value}
                                        >
                                            {rol.label}
                                        </SelectItem>
                                    ))}
                                </SelectContent>
                            </Select>
                            <InputError message={formulario.errors.rol} />
                        </div>

                        {formulario.data.rol === 'unidad_academica' && (
                            <div className="grid gap-2">
                                <Label htmlFor="unidad">
                                    Unidad académica que administrará
                                </Label>
                                <Select
                                    value={
                                        formulario.data.unidad_academica_id ||
                                        undefined
                                    }
                                    onValueChange={(valor) =>
                                        formulario.setData(
                                            'unidad_academica_id',
                                            valor,
                                        )
                                    }
                                >
                                    <SelectTrigger
                                        id="unidad"
                                        className="w-full"
                                    >
                                        <SelectValue placeholder="Selecciona una unidad" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        {unidades.map((unidad) => (
                                            <SelectItem
                                                key={unidad.value}
                                                value={unidad.value}
                                                disabled={
                                                    unidad.administrador_id !==
                                                        null &&
                                                    unidad.administrador_id !==
                                                        editando?.id
                                                }
                                            >
                                                {unidad.label}
                                                {unidad.administrador &&
                                                    unidad.administrador_id !==
                                                        editando?.id &&
                                                    ` (administra: ${unidad.administrador})`}
                                            </SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>
                                {unidades.length === 0 && (
                                    <p className="text-muted-foreground text-xs">
                                        Aún no hay unidades académicas. Se crean
                                        cuando un estudiante elige su carrera.
                                    </p>
                                )}
                                <InputError
                                    message={
                                        formulario.errors.unidad_academica_id
                                    }
                                />
                            </div>
                        )}

                        <DialogFooter>
                            <Button
                                type="button"
                                variant="outline"
                                onClick={() => setEditando(null)}
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
        </>
    );
}

Usuarios.layout = {
    breadcrumbs: [
        { title: 'Panel', href: dashboard() },
        { title: 'Usuarios', href: index() },
    ],
};
