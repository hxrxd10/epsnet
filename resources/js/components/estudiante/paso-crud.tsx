import { router, useForm } from '@inertiajs/react';
import { Pencil, Plus, Trash2 } from 'lucide-react';
import { useRef, useState } from 'react';
import type { FormEvent, ReactNode } from 'react';
import InputError from '@/components/input-error';
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
import { cn } from '@/lib/utils';
import type { Opcion } from '@/types/estudiante';

export type Valores = Record<string, string>;

export type ContextoCampo = {
    valores: Valores;
    /** Combina valores en el formulario (p. ej. las coordenadas elegidas en un mapa). */
    establecer: (parcial: Valores) => void;
};

export type Campo = {
    name: string;
    label: string;
    tipo:
        | 'text'
        | 'textarea'
        | 'select'
        | 'date'
        | 'number'
        | 'decimal'
        | 'url'
        | 'email';
    requerido?: boolean;
    opciones?: Opcion[];
    ayuda?: string;
    placeholder?: string;
    filas?: number;
    /** Máximo de caracteres (campos de texto). */
    max?: number;
    min?: number;
    maxNumero?: number;
    ancho?: 'completo' | 'mitad';
    visibleSi?: (valores: Valores) => boolean;
    /** Sustituye el control estándar por uno propio (p. ej. un mapa). */
    render?: (contexto: ContextoCampo) => ReactNode;
};

type PasoCrudProps<T extends { id: number }> = {
    /** Nombre del registro en minúsculas, p. ej. "bien o servicio". */
    etiquetaRegistro: string;
    registros: T[];
    campos: Campo[];
    valoresIniciales: Valores;
    aFormulario: (registro: T) => Valores;
    resumen: (registro: T) => ReactNode;
    urlGuardar: string;
    urlActualizar: (id: number) => string;
    urlEliminar: (id: number) => string;
    antesDeEnviar?: (valores: Valores) => Valores;
    vacio: string;
};

const SIN_SELECCION = '__ninguno__';
const LIMITE_TEXTO = 40000;

function CampoFormulario({
    campo,
    valor,
    error,
    onChange,
}: {
    campo: Campo;
    valor: string;
    error?: string;
    onChange: (valor: string) => void;
}) {
    const id = `campo-${campo.name}`;

    return (
        <div
            className={cn(
                'grid content-start gap-2',
                campo.ancho === 'mitad' ? 'sm:col-span-1' : 'sm:col-span-2',
            )}
        >
            <Label htmlFor={id}>
                {campo.label}
                {campo.requerido && (
                    <span aria-hidden className="text-brand/50">
                        {' '}
                        *
                    </span>
                )}
            </Label>

            {campo.tipo === 'textarea' && (
                <>
                    <Textarea
                        id={id}
                        value={valor}
                        rows={campo.filas ?? 5}
                        maxLength={campo.max ?? LIMITE_TEXTO}
                        placeholder={campo.placeholder}
                        aria-invalid={!!error}
                        onChange={(evento) => onChange(evento.target.value)}
                    />
                    <p className="text-brand/40 text-right font-mono text-[11px]">
                        {valor.length.toLocaleString('es-GT')} /{' '}
                        {(campo.max ?? LIMITE_TEXTO).toLocaleString('es-GT')}{' '}
                        caracteres
                    </p>
                </>
            )}

            {campo.tipo === 'select' && (
                <Select
                    value={valor === '' ? undefined : valor}
                    onValueChange={(nuevo) =>
                        onChange(nuevo === SIN_SELECCION ? '' : nuevo)
                    }
                >
                    <SelectTrigger
                        id={id}
                        className="w-full"
                        aria-invalid={!!error}
                    >
                        <SelectValue
                            placeholder={
                                campo.placeholder ?? 'Selecciona una opción'
                            }
                        />
                    </SelectTrigger>
                    <SelectContent>
                        {!campo.requerido && (
                            <SelectItem value={SIN_SELECCION}>
                                Sin especificar
                            </SelectItem>
                        )}
                        {campo.opciones?.map((opcion) => (
                            <SelectItem key={opcion.value} value={opcion.value}>
                                {opcion.label}
                            </SelectItem>
                        ))}
                    </SelectContent>
                </Select>
            )}

            {campo.tipo !== 'textarea' && campo.tipo !== 'select' && (
                <Input
                    id={id}
                    value={valor}
                    type={
                        campo.tipo === 'decimal'
                            ? 'number'
                            : campo.tipo === 'text'
                              ? 'text'
                              : campo.tipo
                    }
                    step={campo.tipo === 'decimal' ? 'any' : undefined}
                    min={campo.min}
                    max={campo.maxNumero}
                    maxLength={
                        campo.tipo === 'text' ? (campo.max ?? 255) : undefined
                    }
                    placeholder={campo.placeholder}
                    aria-invalid={!!error}
                    onChange={(evento) => onChange(evento.target.value)}
                />
            )}

            {campo.ayuda && !error && (
                <p className="text-brand/50 text-xs">{campo.ayuda}</p>
            )}
            <InputError message={error} />
        </div>
    );
}

export default function PasoCrud<T extends { id: number }>({
    etiquetaRegistro,
    registros,
    campos,
    valoresIniciales,
    aFormulario,
    resumen,
    urlGuardar,
    urlActualizar,
    urlEliminar,
    antesDeEnviar,
    vacio,
}: PasoCrudProps<T>) {
    const formulario = useForm<Valores>(valoresIniciales);
    const [editando, setEditando] = useState<number | null>(null);
    const [eliminando, setEliminando] = useState<T | null>(null);
    const [borrando, setBorrando] = useState(false);
    const seccionFormulario = useRef<HTMLElement>(null);

    function limpiar() {
        formulario.reset();
        formulario.clearErrors();
        setEditando(null);
    }

    function enviar(evento: FormEvent) {
        evento.preventDefault();

        formulario.transform((datos) =>
            antesDeEnviar ? antesDeEnviar(datos) : datos,
        );

        const opciones = { preserveScroll: true, onSuccess: limpiar };

        if (editando === null) {
            formulario.post(urlGuardar, opciones);
        } else {
            formulario.put(urlActualizar(editando), opciones);
        }
    }

    function editar(registro: T) {
        formulario.clearErrors();
        formulario.setData(aFormulario(registro));
        setEditando(registro.id);
        seccionFormulario.current?.scrollIntoView({
            behavior: 'smooth',
            block: 'start',
        });
    }

    function confirmarEliminacion() {
        if (eliminando === null) {
            return;
        }

        const id = eliminando.id;

        router.delete(urlEliminar(id), {
            preserveScroll: true,
            onStart: () => setBorrando(true),
            onSuccess: () => {
                if (editando === id) {
                    limpiar();
                }
            },
            onFinish: () => {
                setBorrando(false);
                setEliminando(null);
            },
        });
    }

    return (
        <div className="space-y-6">
            <section
                ref={seccionFormulario}
                className="border-brand/10 scroll-mt-6 rounded-3xl border bg-white p-6 shadow-[0_30px_80px_-40px_rgba(15,16,49,0.35)] sm:p-8"
            >
                <div className="flex items-center justify-between gap-4">
                    <h2 className="text-xl font-semibold tracking-tight">
                        {editando === null
                            ? `Agregar ${etiquetaRegistro}`
                            : `Editar ${etiquetaRegistro}`}
                    </h2>
                    {editando !== null && (
                        <button
                            type="button"
                            onClick={limpiar}
                            className="text-brand/60 text-sm underline-offset-4 hover:underline"
                        >
                            Cancelar edición
                        </button>
                    )}
                </div>

                <form
                    onSubmit={enviar}
                    className="mt-6 grid gap-5 sm:grid-cols-2"
                >
                    {campos
                        .filter(
                            (campo) =>
                                !campo.visibleSi ||
                                campo.visibleSi(formulario.data),
                        )
                        .map((campo) =>
                            campo.render ? (
                                <div key={campo.name} className="sm:col-span-2">
                                    {campo.render({
                                        valores: formulario.data,
                                        establecer: (parcial) =>
                                            formulario.setData((previos) => ({
                                                ...previos,
                                                ...parcial,
                                            })),
                                    })}
                                </div>
                            ) : (
                                <CampoFormulario
                                    key={campo.name}
                                    campo={campo}
                                    valor={formulario.data[campo.name] ?? ''}
                                    error={formulario.errors[campo.name]}
                                    onChange={(valor) =>
                                        formulario.setData(campo.name, valor)
                                    }
                                />
                            ),
                        )}

                    <div className="flex items-center gap-3 sm:col-span-2">
                        <button
                            type="submit"
                            disabled={formulario.processing}
                            className="bg-brand inline-flex items-center gap-2 rounded-full px-6 py-3 text-sm font-medium text-white transition-transform hover:scale-[1.03] active:scale-95 disabled:opacity-60"
                        >
                            {formulario.processing ? (
                                <Spinner />
                            ) : (
                                <Plus className="size-4" />
                            )}
                            {editando === null
                                ? 'Guardar registro'
                                : 'Guardar cambios'}
                        </button>
                        <p className="text-brand/50 text-xs">
                            Tu avance se guarda al instante; puedes volver y
                            modificarlo cuando quieras.
                        </p>
                    </div>
                </form>
            </section>

            <section>
                <h2 className="text-brand/50 mb-4 font-mono text-xs tracking-[0.2em] uppercase">
                    Registros guardados · {registros.length}
                </h2>

                {registros.length === 0 ? (
                    <p className="border-brand/20 text-brand/55 rounded-3xl border border-dashed p-8 text-center text-sm">
                        {vacio}
                    </p>
                ) : (
                    <ul className="space-y-3">
                        {registros.map((registro) => (
                            <li
                                key={registro.id}
                                className={cn(
                                    'flex flex-col gap-4 rounded-2xl border bg-white p-5 transition-colors sm:flex-row sm:items-start sm:justify-between',
                                    editando === registro.id
                                        ? 'border-brand'
                                        : 'border-brand/10',
                                )}
                            >
                                <div className="min-w-0 flex-1">
                                    {resumen(registro)}
                                </div>
                                <div className="flex shrink-0 gap-2">
                                    <button
                                        type="button"
                                        onClick={() => editar(registro)}
                                        className="border-brand/15 hover:bg-brand/5 inline-flex items-center gap-1.5 rounded-full border px-3.5 py-2 text-sm transition-colors"
                                    >
                                        <Pencil className="size-3.5" />
                                        Editar
                                    </button>
                                    <button
                                        type="button"
                                        onClick={() => setEliminando(registro)}
                                        className="border-brand/15 text-destructive hover:bg-destructive/5 inline-flex items-center gap-1.5 rounded-full border px-3.5 py-2 text-sm transition-colors"
                                    >
                                        <Trash2 className="size-3.5" />
                                        Eliminar
                                    </button>
                                </div>
                            </li>
                        ))}
                    </ul>
                )}
            </section>

            <Dialog
                open={eliminando !== null}
                onOpenChange={(abierto) => !abierto && setEliminando(null)}
            >
                <DialogContent>
                    <DialogHeader>
                        <DialogTitle>Eliminar {etiquetaRegistro}</DialogTitle>
                        <DialogDescription>
                            Este registro dejará de formar parte de tu
                            expediente. ¿Deseas continuar?
                        </DialogDescription>
                    </DialogHeader>
                    <DialogFooter>
                        <button
                            type="button"
                            onClick={() => setEliminando(null)}
                            className="border-brand/15 hover:bg-brand/5 rounded-full border px-5 py-2.5 text-sm"
                        >
                            Cancelar
                        </button>
                        <button
                            type="button"
                            disabled={borrando}
                            onClick={confirmarEliminacion}
                            className="bg-destructive inline-flex items-center gap-2 rounded-full px-5 py-2.5 text-sm font-medium text-white disabled:opacity-60"
                        >
                            {borrando && <Spinner />}
                            Eliminar
                        </button>
                    </DialogFooter>
                </DialogContent>
            </Dialog>
        </div>
    );
}
