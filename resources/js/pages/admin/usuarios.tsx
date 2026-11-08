import { Head, router, setLayoutProps, useForm } from '@inertiajs/react';
import { Mail, Search, ShieldCheck, UserPlus } from 'lucide-react';
import { useState } from 'react';
import type { FormEvent } from 'react';
import {
    correo,
    store,
    update,
} from '@/actions/App/Http/Controllers/Admin/UsuarioController';
import InputError from '@/components/input-error';
import Paginacion from '@/components/paginacion';
import type { Pagina } from '@/components/paginacion';
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
    carreras: { id: number; nombre: string }[];
    correo_verificado: boolean;
    editable: boolean;
    correo_editable: boolean;
    propio: boolean;
};

type UnidadOpcion = Opcion & {
    carreras: Opcion[];
};

type Acceso = {
    rol: string;
    unidad_academica_id: string;
    carreras: string[];
};

type CuentaNueva = Acceso & { name: string; email: string };

type Props = {
    usuarios: Usuario[];
    pagina: Pagina;
    filtros: { q: string; rol: string };
    roles: Opcion[];
    rolesAsignables: Opcion[];
    /** Para la cuenta principal de una unidad académica: solo gestiona a los supervisores de esa unidad. */
    unidadPropia: { id: number; nombre: string } | null;
    unidades: UnidadOpcion[];
};

const TODOS = '__todos__';

export default function Usuarios({
    usuarios,
    pagina,
    filtros,
    roles,
    rolesAsignables,
    unidadPropia,
    unidades,
}: Props) {
    const titulo = unidadPropia ? 'Supervisores' : 'Usuarios';
    setLayoutProps({
        breadcrumbs: [
            { title: 'Panel', href: dashboard() },
            { title: titulo, href: index() },
        ],
    });
    const [texto, setTexto] = useState(filtros.q);
    const [editando, setEditando] = useState<Usuario | null>(null);
    const [creando, setCreando] = useState(false);
    const [corrigiendo, setCorrigiendo] = useState<Usuario | null>(null);
    const cambioDeCorreo = useForm({ email: '' });
    const formulario = useForm<Acceso>({
        rol: '',
        unidad_academica_id: '',
        carreras: [],
    });
    const nueva = useForm<CuentaNueva>({
        name: '',
        email: '',
        rol: 'unidad_academica',
        unidad_academica_id: unidadPropia?.id.toString() ?? '',
        carreras: [],
    });

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
            carreras: usuario.carreras.map((carrera) => carrera.id.toString()),
        });
        setEditando(usuario);
    }

    function abrirCorreo(usuario: Usuario) {
        cambioDeCorreo.clearErrors();
        cambioDeCorreo.setData('email', usuario.email);
        setCorrigiendo(usuario);
    }

    function guardarCorreo(evento: FormEvent) {
        evento.preventDefault();

        if (corrigiendo === null) {
            return;
        }

        cambioDeCorreo.put(correo.url(corrigiendo.id), {
            preserveScroll: true,
            onSuccess: () => setCorrigiendo(null),
        });
    }

    function crear(evento: FormEvent) {
        evento.preventDefault();
        nueva.transform(normalizar);
        nueva.post(store.url(), {
            preserveScroll: true,
            onSuccess: () => {
                nueva.reset();
                setCreando(false);
            },
        });
    }

    function guardar(evento: FormEvent) {
        evento.preventDefault();

        if (editando === null) {
            return;
        }

        formulario.transform(normalizar);
        formulario.put(update.url(editando.id), {
            preserveScroll: true,
            onSuccess: () => setEditando(null),
        });
    }

    return (
        <>
            <Head title={titulo} />
            <div className="flex flex-1 flex-col gap-6 p-4">
                <div className="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <h1 className="text-2xl font-semibold tracking-tight">
                            {titulo}
                        </h1>
                        <p className="text-muted-foreground mt-1 max-w-2xl text-sm">
                            {unidadPropia
                                ? `Crea las cuentas de los supervisores de ${unidadPropia.nombre}: cada uno solo ve y aprueba los EPS de las carreras que le asignes. Si falta una carrera, agrégala en "Carreras".`
                                : 'Crea las cuentas de las unidades académicas: cada una puede tener varios usuarios y, si les asignas carreras, solo ven y aprueban los EPS de esas carreras (por ejemplo, un supervisor por carrera). El rol de estudiante se obtiene al verificar el registro académico.'}
                        </p>
                    </div>
                    <Button
                        onClick={() => {
                            nueva.reset();
                            nueva.clearErrors();
                            setCreando(true);
                        }}
                    >
                        <UserPlus />
                        {unidadPropia ? 'Crear supervisor' : 'Crear cuenta'}
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
                            placeholder="Buscar por nombre o correo"
                            className="pl-9"
                        />
                    </div>
                    {!unidadPropia && (
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
                                    <SelectItem
                                        key={rol.value}
                                        value={rol.value}
                                    >
                                        {rol.label}
                                    </SelectItem>
                                ))}
                            </SelectContent>
                        </Select>
                    )}
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
                                    {usuario.rol === 'unidad_academica' && (
                                        <p className="text-muted-foreground text-xs">
                                            {usuario.carreras.length === 0
                                                ? 'Ve todas las carreras de su unidad'
                                                : `Carreras: ${usuario.carreras.map((carrera) => carrera.nombre).join(', ')}`}
                                        </p>
                                    )}
                                </div>
                                <div className="flex shrink-0 flex-wrap gap-2">
                                    {usuario.correo_editable && (
                                        <Button
                                            variant="outline"
                                            size="sm"
                                            onClick={() => abrirCorreo(usuario)}
                                        >
                                            <Mail />
                                            Cambiar correo
                                        </Button>
                                    )}
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
                                </div>
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
                        <CamposAcceso
                            formulario={formulario}
                            rolesAsignables={rolesAsignables}
                            unidades={unidades}
                            unidadFija={unidadPropia !== null}
                        />

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

            <Dialog
                open={corrigiendo !== null}
                onOpenChange={(abierto) => !abierto && setCorrigiendo(null)}
            >
                <DialogContent>
                    <DialogHeader>
                        <DialogTitle>Cambiar correo</DialogTitle>
                        <DialogDescription>
                            {corrigiendo?.name}. Enviaremos el enlace de
                            verificación al correo que escribas. Si la persona
                            olvidó su contraseña, puede restablecerla con ese
                            correo en "¿Olvidaste tu contraseña?".
                        </DialogDescription>
                    </DialogHeader>

                    <form onSubmit={guardarCorreo} className="grid gap-5">
                        <div className="grid gap-2">
                            <Label htmlFor="correo-corregido">Correo</Label>
                            <Input
                                id="correo-corregido"
                                type="email"
                                value={cambioDeCorreo.data.email}
                                maxLength={255}
                                onChange={(evento) =>
                                    cambioDeCorreo.setData(
                                        'email',
                                        evento.target.value.toLowerCase(),
                                    )
                                }
                            />
                            <InputError message={cambioDeCorreo.errors.email} />
                        </div>

                        <DialogFooter>
                            <Button
                                type="button"
                                variant="outline"
                                onClick={() => setCorrigiendo(null)}
                            >
                                Cancelar
                            </Button>
                            <Button
                                type="submit"
                                disabled={cambioDeCorreo.processing}
                            >
                                {cambioDeCorreo.processing && <Spinner />}
                                Guardar
                            </Button>
                        </DialogFooter>
                    </form>
                </DialogContent>
            </Dialog>

            <Dialog open={creando} onOpenChange={setCreando}>
                <DialogContent>
                    <DialogHeader>
                        <DialogTitle>
                            {unidadPropia ? 'Crear supervisor' : 'Crear cuenta'}
                        </DialogTitle>
                        <DialogDescription>
                            Le enviaremos un correo con el enlace para definir
                            su contraseña.
                        </DialogDescription>
                    </DialogHeader>

                    <form onSubmit={crear} className="grid gap-5">
                        <div className="grid gap-2">
                            <Label htmlFor="nueva-nombre">Nombre</Label>
                            <Input
                                id="nueva-nombre"
                                value={nueva.data.name}
                                maxLength={255}
                                onChange={(evento) =>
                                    nueva.setData('name', evento.target.value)
                                }
                            />
                            <InputError message={nueva.errors.name} />
                        </div>
                        <div className="grid gap-2">
                            <Label htmlFor="nueva-correo">Correo</Label>
                            <Input
                                id="nueva-correo"
                                type="email"
                                value={nueva.data.email}
                                maxLength={255}
                                onChange={(evento) =>
                                    nueva.setData(
                                        'email',
                                        evento.target.value.toLowerCase(),
                                    )
                                }
                            />
                            <InputError message={nueva.errors.email} />
                        </div>

                        <CamposAcceso
                            formulario={nueva}
                            rolesAsignables={rolesAsignables}
                            unidades={unidades}
                            unidadFija={unidadPropia !== null}
                        />

                        <DialogFooter>
                            <Button
                                type="button"
                                variant="outline"
                                onClick={() => setCreando(false)}
                            >
                                Cancelar
                            </Button>
                            <Button type="submit" disabled={nueva.processing}>
                                {nueva.processing && <Spinner />}
                                {unidadPropia
                                    ? 'Crear supervisor'
                                    : 'Crear cuenta'}
                            </Button>
                        </DialogFooter>
                    </form>
                </DialogContent>
            </Dialog>
        </>
    );
}

/**
 * Solo una unidad académica lleva unidad y carreras.
 */
function normalizar<T extends Acceso>(datos: T): T {
    const esUnidad = datos.rol === 'unidad_academica';

    return {
        ...datos,
        unidad_academica_id: esUnidad ? datos.unidad_academica_id : '',
        carreras: esUnidad ? datos.carreras : [],
    };
}

type FormularioAcceso = {
    data: Acceso;
    errors: Partial<Record<string, string>>;
    setData: (campo: keyof Acceso, valor: string | string[]) => void;
};

/**
 * Rol, unidad y carreras de la cuenta. Con `unidadFija` (cuenta principal de una unidad) el rol y la
 * unidad ya están dados: solo se eligen las carreras, al menos una.
 */
function CamposAcceso({
    formulario,
    rolesAsignables,
    unidades,
    unidadFija,
}: {
    formulario: FormularioAcceso;
    rolesAsignables: Opcion[];
    unidades: UnidadOpcion[];
    unidadFija: boolean;
}) {
    const unidad = unidades.find(
        (opcion) => opcion.value === formulario.data.unidad_academica_id,
    );
    const errorCarreras = Object.entries(formulario.errors).find(([campo]) =>
        campo.startsWith('carreras'),
    )?.[1];

    function alternar(carrera: string, marcada: boolean) {
        formulario.setData(
            'carreras',
            marcada
                ? [...formulario.data.carreras, carrera]
                : formulario.data.carreras.filter((otra) => otra !== carrera),
        );
    }

    return (
        <>
            <div className={unidadFija ? 'hidden' : 'grid gap-2'}>
                <Label htmlFor="rol">Rol</Label>
                <Select
                    value={formulario.data.rol || undefined}
                    onValueChange={(valor) => formulario.setData('rol', valor)}
                >
                    <SelectTrigger id="rol" className="w-full">
                        <SelectValue placeholder="Selecciona un rol" />
                    </SelectTrigger>
                    <SelectContent>
                        {rolesAsignables.map((rol) => (
                            <SelectItem key={rol.value} value={rol.value}>
                                {rol.label}
                            </SelectItem>
                        ))}
                    </SelectContent>
                </Select>
                <InputError message={formulario.errors.rol} />
            </div>

            {formulario.data.rol === 'unidad_academica' && (
                <>
                    <div className={unidadFija ? 'hidden' : 'grid gap-2'}>
                        <Label htmlFor="unidad">Unidad académica</Label>
                        <Select
                            value={
                                formulario.data.unidad_academica_id || undefined
                            }
                            onValueChange={(valor) => {
                                formulario.setData(
                                    'unidad_academica_id',
                                    valor,
                                );
                                formulario.setData('carreras', []);
                            }}
                        >
                            <SelectTrigger id="unidad" className="w-full">
                                <SelectValue placeholder="Selecciona una unidad" />
                            </SelectTrigger>
                            <SelectContent>
                                {unidades.map((opcion) => (
                                    <SelectItem
                                        key={opcion.value}
                                        value={opcion.value}
                                    >
                                        {opcion.label}
                                    </SelectItem>
                                ))}
                            </SelectContent>
                        </Select>
                        {unidades.length === 0 && (
                            <p className="text-muted-foreground text-xs">
                                Aún no hay unidades académicas. Se crean al
                                agregar carreras o cuando un estudiante elige su
                                carrera.
                            </p>
                        )}
                        <InputError
                            message={formulario.errors.unidad_academica_id}
                        />
                    </div>

                    {unidad && (
                        <fieldset className="grid gap-2">
                            <legend className="text-sm font-medium">
                                Carreras que puede ver y aprobar
                            </legend>
                            <p className="text-muted-foreground text-xs">
                                {unidadFija
                                    ? 'Marca al menos una: solo verá y aprobará los EPS de esas carreras.'
                                    : 'Si no marcas ninguna, ve todas las carreras de la unidad.'}
                            </p>
                            {unidad.carreras.length === 0 ? (
                                <p className="text-muted-foreground text-xs">
                                    {unidadFija
                                        ? 'Tu unidad aún no tiene carreras. Agrégalas en "Carreras".'
                                        : 'Esta unidad aún no tiene carreras. Agrégalas en Manejo de datos → Carreras.'}
                                </p>
                            ) : (
                                <ul className="max-h-56 space-y-2 overflow-y-auto rounded-md border p-3">
                                    {unidad.carreras.map((carrera) => (
                                        <li
                                            key={carrera.value}
                                            className="flex items-start gap-2"
                                        >
                                            <Checkbox
                                                id={`carrera-${carrera.value}`}
                                                checked={formulario.data.carreras.includes(
                                                    carrera.value,
                                                )}
                                                onCheckedChange={(valor) =>
                                                    alternar(
                                                        carrera.value,
                                                        valor === true,
                                                    )
                                                }
                                            />
                                            <Label
                                                htmlFor={`carrera-${carrera.value}`}
                                                className="text-sm leading-snug font-normal"
                                            >
                                                {carrera.label}
                                            </Label>
                                        </li>
                                    ))}
                                </ul>
                            )}
                            <InputError message={errorCarreras} />
                        </fieldset>
                    )}
                </>
            )}
        </>
    );
}

Usuarios.layout = {
    breadcrumbs: [
        { title: 'Panel', href: dashboard() },
        { title: 'Usuarios', href: index() },
    ],
};
