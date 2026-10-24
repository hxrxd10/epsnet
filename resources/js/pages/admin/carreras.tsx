import { Head, router, useForm } from '@inertiajs/react';
import {
    CheckCircle2,
    GraduationCap,
    Plus,
    Search,
    Trash2,
} from 'lucide-react';
import { useState } from 'react';
import type { FormEvent } from 'react';
import {
    destroy,
    index,
    store,
} from '@/actions/App/Http/Controllers/Admin/CarreraController';
import DialogoEliminar from '@/components/admin/dialogo-eliminar';
import InputError from '@/components/input-error';
import Paginacion from '@/components/paginacion';
import type { Pagina } from '@/components/paginacion';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
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

type Carrera = {
    id: number;
    clave: string;
    nombre: string;
    extension: string | null;
    nivel: string | null;
    unidad: string;
    supervisores: number;
};

type CarreraConsultada = {
    clave: string;
    nombre: string;
    extension: string | null;
    unidad: string;
    nivel: string | null;
    agregada: boolean;
};

type Consulta = {
    carnet: string;
    estudiante: string | null;
    error: string | null;
    carreras: CarreraConsultada[];
    nuevas: number;
};

type Filtros = { q: string; unidad: string; carnet: string };

type Props = {
    carreras: Carrera[];
    pagina: Pagina;
    filtros: Filtros;
    unidades: Opcion[];
    consulta: Consulta | null;
    errors: { carnet?: string };
};

const TODAS = '__todas__';

export default function Carreras({
    carreras,
    pagina,
    filtros,
    unidades,
    consulta,
    errors,
}: Props) {
    const [texto, setTexto] = useState(filtros.q);
    const [carnet, setCarnet] = useState(filtros.carnet);
    const [consultando, setConsultando] = useState(false);
    const [eliminando, setEliminando] = useState<Carrera | null>(null);
    const [borrando, setBorrando] = useState(false);
    const formulario = useForm<{ carnet: string; claves: string[] }>({
        carnet: consulta?.carnet ?? '',
        claves: [],
    });

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

    function consultar(evento: FormEvent) {
        evento.preventDefault();
        formulario.setData({ carnet: carnet.trim(), claves: [] });
        router.get(
            index.url(),
            Object.fromEntries(
                Object.entries({
                    ...filtros,
                    carnet: carnet.trim(),
                }).filter(([, valor]) => valor !== ''),
            ),
            {
                preserveState: true,
                onStart: () => setConsultando(true),
                onFinish: () => setConsultando(false),
            },
        );
    }

    function alternar(clave: string, marcada: boolean) {
        formulario.setData(
            'claves',
            marcada
                ? [...formulario.data.claves, clave]
                : formulario.data.claves.filter((otra) => otra !== clave),
        );
    }

    function agregar(claves: string[]) {
        if (consulta === null) {
            return;
        }

        formulario.transform(() => ({ carnet: consulta.carnet, claves }));
        formulario.post(store.url(), {
            preserveScroll: true,
            onSuccess: () => formulario.setData('claves', []),
        });
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

    const nuevas =
        consulta?.carreras.filter((carrera) => !carrera.agregada) ?? [];

    return (
        <>
            <Head title="Carreras" />
            <div className="flex flex-1 flex-col gap-6 p-4">
                <div>
                    <h1 className="text-2xl font-semibold tracking-tight">
                        Carreras
                    </h1>
                    <p className="text-muted-foreground mt-1 max-w-2xl text-sm">
                        Consulta el carné de un estudiante en el registro
                        académico para agregar sus carreras. Las carreras se
                        asignan a los supervisores de EPS de cada unidad
                        académica para que solo vean y aprueben las suyas.
                    </p>
                </div>

                <section className="border-sidebar-border/70 rounded-xl border p-5">
                    <h2 className="font-semibold">
                        Agregar carreras por carné
                    </h2>
                    <form
                        onSubmit={consultar}
                        className="mt-3 flex flex-wrap items-end gap-3"
                    >
                        <div className="grid gap-2">
                            <Label htmlFor="carnet">Carné</Label>
                            <Input
                                id="carnet"
                                inputMode="numeric"
                                value={carnet}
                                onChange={(evento) =>
                                    setCarnet(evento.target.value)
                                }
                                placeholder="Por ejemplo, 201912345"
                                className="w-64"
                                maxLength={15}
                            />
                        </div>
                        <Button
                            type="submit"
                            disabled={consultando || carnet.trim() === ''}
                        >
                            {consultando ? <Spinner /> : <Search />}
                            Consultar
                        </Button>
                    </form>
                    <InputError
                        message={errors.carnet ?? formulario.errors.carnet}
                        className="mt-2"
                    />

                    {consulta && (
                        <div className="mt-5">
                            {consulta.estudiante && (
                                <p className="text-sm">
                                    <span className="font-mono">
                                        {consulta.carnet}
                                    </span>{' '}
                                    · {consulta.estudiante}
                                </p>
                            )}

                            {consulta.error ? (
                                <p className="text-destructive mt-2 text-sm">
                                    {consulta.error}
                                </p>
                            ) : (
                                <>
                                    <p className="text-muted-foreground mt-1 text-sm">
                                        {consulta.nuevas === 0
                                            ? consulta.carreras.length === 1
                                                ? `La carrera ${consulta.carreras[0].nombre} ya está agregada.`
                                                : 'Todas sus carreras ya están agregadas.'
                                            : `${consulta.nuevas} de ${consulta.carreras.length} ${consulta.carreras.length === 1 ? 'carrera es nueva' : 'carreras son nuevas'}.`}
                                    </p>
                                    <ul className="border-sidebar-border/70 divide-sidebar-border/70 mt-3 divide-y rounded-lg border">
                                        {consulta.carreras.map((carrera) => (
                                            <li
                                                key={carrera.clave}
                                                className="flex items-start gap-3 p-3"
                                            >
                                                {carrera.agregada ? (
                                                    <CheckCircle2 className="text-muted-foreground mt-0.5 size-4 shrink-0" />
                                                ) : (
                                                    <Checkbox
                                                        id={`carrera-${carrera.clave}`}
                                                        className="mt-0.5"
                                                        checked={formulario.data.claves.includes(
                                                            carrera.clave,
                                                        )}
                                                        onCheckedChange={(
                                                            valor,
                                                        ) =>
                                                            alternar(
                                                                carrera.clave,
                                                                valor === true,
                                                            )
                                                        }
                                                    />
                                                )}
                                                <label
                                                    htmlFor={`carrera-${carrera.clave}`}
                                                    className="min-w-0 flex-1 text-sm"
                                                >
                                                    <span className="font-medium">
                                                        {carrera.nombre}
                                                    </span>
                                                    {carrera.extension &&
                                                        ` · ${carrera.extension}`}
                                                    <span className="text-muted-foreground block">
                                                        {carrera.unidad}
                                                        {carrera.nivel &&
                                                            ` · ${carrera.nivel}`}{' '}
                                                        ·{' '}
                                                        <span className="font-mono">
                                                            {carrera.clave}
                                                        </span>
                                                    </span>
                                                </label>
                                                {carrera.agregada && (
                                                    <Badge variant="secondary">
                                                        Ya agregada
                                                    </Badge>
                                                )}
                                            </li>
                                        ))}
                                    </ul>
                                    <InputError
                                        message={formulario.errors.claves}
                                        className="mt-2"
                                    />
                                    {nuevas.length > 0 && (
                                        <div className="mt-3 flex flex-wrap gap-2">
                                            <Button
                                                onClick={() =>
                                                    agregar(
                                                        formulario.data.claves,
                                                    )
                                                }
                                                disabled={
                                                    formulario.processing ||
                                                    formulario.data.claves
                                                        .length === 0
                                                }
                                            >
                                                {formulario.processing ? (
                                                    <Spinner />
                                                ) : (
                                                    <Plus />
                                                )}
                                                Agregar las seleccionadas
                                            </Button>
                                            <Button
                                                variant="outline"
                                                onClick={() =>
                                                    agregar(
                                                        nuevas.map(
                                                            (carrera) =>
                                                                carrera.clave,
                                                        ),
                                                    )
                                                }
                                                disabled={formulario.processing}
                                            >
                                                Agregar todas las nuevas
                                            </Button>
                                        </div>
                                    )}
                                </>
                            )}
                        </div>
                    )}
                </section>

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
                            placeholder="Nombre, extensión o código"
                            className="pl-9"
                        />
                    </div>
                    <Select
                        value={filtros.unidad || TODAS}
                        onValueChange={(valor) =>
                            filtrar({ unidad: valor === TODAS ? '' : valor })
                        }
                    >
                        <SelectTrigger
                            className="w-72"
                            aria-label="Unidad académica"
                        >
                            <SelectValue />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value={TODAS}>
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

                {carreras.length === 0 ? (
                    <p className="border-sidebar-border/70 text-muted-foreground rounded-xl border border-dashed p-10 text-center text-sm">
                        {filtros.q || filtros.unidad
                            ? 'No hay carreras que coincidan.'
                            : 'Aún no hay carreras. Consulta un carné para agregar las primeras.'}
                    </p>
                ) : (
                    <ul className="border-sidebar-border/70 divide-sidebar-border/70 divide-y rounded-xl border">
                        {carreras.map((carrera) => (
                            <li
                                key={carrera.id}
                                className="flex flex-col gap-3 p-4 sm:flex-row sm:items-center sm:justify-between"
                            >
                                <div className="flex min-w-0 items-start gap-3">
                                    <GraduationCap className="text-muted-foreground mt-0.5 size-5 shrink-0" />
                                    <div className="min-w-0">
                                        <p className="font-medium">
                                            {carrera.nombre}
                                            {carrera.extension && (
                                                <span className="text-muted-foreground font-normal">
                                                    {' '}
                                                    · {carrera.extension}
                                                </span>
                                            )}
                                        </p>
                                        <p className="text-muted-foreground text-sm">
                                            {carrera.unidad}
                                            {carrera.nivel &&
                                                ` · ${carrera.nivel}`}{' '}
                                            ·{' '}
                                            <span className="font-mono">
                                                {carrera.clave}
                                            </span>{' '}
                                            · {carrera.supervisores}{' '}
                                            {carrera.supervisores === 1
                                                ? 'supervisor'
                                                : 'supervisores'}
                                        </p>
                                    </div>
                                </div>
                                <Button
                                    variant="outline"
                                    size="sm"
                                    onClick={() => setEliminando(carrera)}
                                >
                                    <Trash2 />
                                    Eliminar
                                </Button>
                            </li>
                        ))}
                    </ul>
                )}

                <Paginacion pagina={pagina} />
            </div>

            <DialogoEliminar
                titulo="Eliminar carrera"
                descripcion={`${eliminando?.nombre ?? ''}. Sus EPS no se modifican. Si está asignada a supervisores, no se eliminará: primero quítasela en Usuarios.`}
                abierto={eliminando !== null}
                procesando={borrando}
                onCancelar={() => setEliminando(null)}
                onConfirmar={eliminar}
            />
        </>
    );
}

Carreras.layout = {
    breadcrumbs: [
        { title: 'Panel', href: dashboard() },
        { title: 'Manejo de datos', href: datos() },
        { title: 'Carreras', href: index() },
    ],
};
