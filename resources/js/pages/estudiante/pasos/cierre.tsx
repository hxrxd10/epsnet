import { Head, Link, router, useForm } from '@inertiajs/react';
import {
    AlertCircle,
    BadgeCheck,
    Download,
    FileText,
    Pencil,
    Trash2,
    Upload,
} from 'lucide-react';
import { useRef, useState } from 'react';
import type { FormEvent } from 'react';
import { store as completar } from '@/actions/App/Http/Controllers/Estudiante/CierreExpedienteController';
import {
    destroy as eliminarOrden,
    store as subirOrden,
} from '@/actions/App/Http/Controllers/Estudiante/OrdenImpresionController';
import InputError from '@/components/input-error';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Spinner } from '@/components/ui/spinner';
import EstudianteLayout from '@/layouts/estudiante-layout';
import type { PasoProps } from '@/types/estudiante';

type ResumenEje = {
    eje: number;
    titulo: string;
    href: string;
    items: string[];
};

type OrdenImpresion = {
    nombre: string;
    mime_type: string | null;
    tamano_bytes: number | null;
    subido_at: string;
    descarga: string;
};

type Props = PasoProps & {
    resumen: ResumenEje[];
    total_registros: number;
    completado_at: string | null;
    orden_impresion: OrdenImpresion | null;
    errors: { registros?: string; orden_impresion?: string };
};

const VISIBLES = 3;

function tamano(bytes: number | null): string {
    if (bytes === null) {
        return '';
    }

    return bytes >= 1_000_000
        ? `${(bytes / 1_000_000).toFixed(1)} MB`
        : `${Math.max(1, Math.round(bytes / 1000))} KB`;
}

const fecha = (iso: string) =>
    new Date(iso).toLocaleDateString('es-GT', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    });

export default function Cierre({
    expediente,
    eje,
    pasos,
    resumen,
    total_registros,
    completado_at,
    orden_impresion,
    errors,
}: Props) {
    const entrada = useRef<HTMLInputElement>(null);
    const formulario = useForm<{ orden_impresion: File | null }>({
        orden_impresion: null,
    });
    const [confirmandoBorrado, setConfirmandoBorrado] = useState(false);
    const [finalizando, setFinalizando] = useState(false);
    const [borrando, setBorrando] = useState(false);

    const completo = expediente.estado !== 'activo';

    function subir(evento: FormEvent) {
        evento.preventDefault();

        formulario.post(subirOrden.url(expediente.id), {
            forceFormData: true,
            preserveScroll: true,
            onSuccess: () => {
                formulario.reset();

                if (entrada.current) {
                    entrada.current.value = '';
                }
            },
        });
    }

    function finalizar() {
        router.post(
            completar.url(expediente.id),
            {},
            {
                preserveScroll: true,
                onStart: () => setFinalizando(true),
                onFinish: () => setFinalizando(false),
            },
        );
    }

    function borrar() {
        router.delete(eliminarOrden.url(expediente.id), {
            preserveScroll: true,
            onStart: () => setBorrando(true),
            onFinish: () => {
                setBorrando(false);
                setConfirmandoBorrado(false);
            },
        });
    }

    return (
        <EstudianteLayout
            paso={eje + 2}
            ejes={pasos}
            expediente={expediente}
            titulo="Resumen y orden de impresión"
            descripcion="Revisa lo que registraste y corrige lo que necesites. Al subir tu orden de impresión y guardar, tu EPS queda completo y sus datos se usan en las estadísticas."
        >
            <Head title="Resumen y orden de impresión" />

            <div className="space-y-6">
                {completo && (
                    <div className="border-brand flex items-start gap-4 rounded-3xl border-2 bg-white p-6 shadow-[0_30px_80px_-40px_rgba(15,16,49,0.35)]">
                        <BadgeCheck className="mt-0.5 size-7 shrink-0" />
                        <div>
                            <p className="text-lg font-semibold">
                                {expediente.estado === 'verificado'
                                    ? 'Tu EPS está completo y verificado por tu unidad académica'
                                    : 'Tu EPS está completo'}
                            </p>
                            <p className="text-brand/65 mt-1 text-sm">
                                {completado_at &&
                                    `Guardado el ${fecha(completado_at)}. `}
                                Sus datos cuentan para las estadísticas. Puedes
                                seguir corrigiendo tu información cuando
                                quieras.
                            </p>
                        </div>
                    </div>
                )}

                <section className="border-brand/10 rounded-3xl border bg-white p-6 shadow-[0_30px_80px_-40px_rgba(15,16,49,0.35)] sm:p-8">
                    <h2 className="text-xl font-semibold tracking-tight">
                        Resumen de tu EPS
                    </h2>
                    <p className="text-brand/60 mt-1 text-sm">
                        {total_registros === 0
                            ? 'Aún no has registrado nada en los ejes.'
                            : `${total_registros} ${total_registros === 1 ? 'registro' : 'registros'} en total.`}
                    </p>
                    <InputError message={errors.registros} className="mt-3" />

                    <ul className="mt-6 grid gap-4 md:grid-cols-2">
                        {resumen.map((seccion) => (
                            <li
                                key={seccion.eje}
                                className="border-brand/10 flex flex-col rounded-2xl border p-5"
                            >
                                <div className="flex items-start justify-between gap-3">
                                    <h3 className="font-medium tracking-tight">
                                        {seccion.titulo}
                                    </h3>
                                    <span className="bg-brand/[0.06] rounded-full px-2.5 py-0.5 font-mono text-xs">
                                        {seccion.items.length}
                                    </span>
                                </div>

                                {seccion.items.length === 0 ? (
                                    <p className="text-brand/45 mt-3 text-sm">
                                        Sin registros.
                                    </p>
                                ) : (
                                    <ul className="text-brand/70 mt-3 space-y-1.5 text-sm">
                                        {seccion.items
                                            .slice(0, VISIBLES)
                                            .map((item, indice) => (
                                                <li
                                                    key={indice}
                                                    className="line-clamp-2"
                                                >
                                                    · {item}
                                                </li>
                                            ))}
                                        {seccion.items.length > VISIBLES && (
                                            <li className="text-brand/45">
                                                y{' '}
                                                {seccion.items.length -
                                                    VISIBLES}{' '}
                                                más
                                            </li>
                                        )}
                                    </ul>
                                )}

                                <Link
                                    href={seccion.href}
                                    className="mt-4 inline-flex items-center gap-1.5 self-start pt-1 text-sm font-medium underline-offset-4 hover:underline"
                                >
                                    <Pencil className="size-3.5" />
                                    Revisar y editar
                                </Link>
                            </li>
                        ))}
                    </ul>
                </section>

                <section className="border-brand/10 rounded-3xl border bg-white p-6 shadow-[0_30px_80px_-40px_rgba(15,16,49,0.35)] sm:p-8">
                    <h2 className="text-xl font-semibold tracking-tight">
                        Orden de impresión
                    </h2>
                    <p className="text-brand/60 mt-1 text-sm leading-relaxed">
                        Es la evidencia que valida tu EPS: sin ella, lo que
                        registraste no se cuenta en las estadísticas. Sube el
                        archivo en PDF o imagen (JPG o PNG) de hasta 10 MB.
                    </p>

                    {orden_impresion && (
                        <div className="border-brand/15 bg-brand/[0.03] mt-6 flex flex-col gap-4 rounded-2xl border p-5 sm:flex-row sm:items-center sm:justify-between">
                            <div className="flex min-w-0 items-center gap-3">
                                <FileText className="text-brand/60 size-8 shrink-0" />
                                <div className="min-w-0">
                                    <p className="truncate font-medium">
                                        {orden_impresion.nombre}
                                    </p>
                                    <p className="text-brand/55 text-xs">
                                        {tamano(orden_impresion.tamano_bytes)} ·
                                        subida el{' '}
                                        {fecha(orden_impresion.subido_at)}
                                    </p>
                                </div>
                            </div>
                            <div className="flex shrink-0 gap-2">
                                <a
                                    href={orden_impresion.descarga}
                                    className="border-brand/15 hover:bg-brand/5 inline-flex items-center gap-1.5 rounded-full border px-3.5 py-2 text-sm transition-colors"
                                >
                                    <Download className="size-3.5" />
                                    Descargar
                                </a>
                                <button
                                    type="button"
                                    onClick={() => setConfirmandoBorrado(true)}
                                    className="border-brand/15 text-destructive hover:bg-destructive/5 inline-flex items-center gap-1.5 rounded-full border px-3.5 py-2 text-sm transition-colors"
                                >
                                    <Trash2 className="size-3.5" />
                                    Eliminar
                                </button>
                            </div>
                        </div>
                    )}

                    <form onSubmit={subir} className="mt-6">
                        <label
                            htmlFor="orden_impresion"
                            className="text-sm font-medium"
                        >
                            {orden_impresion
                                ? 'Reemplazar el archivo'
                                : 'Archivo de la orden de impresión'}
                        </label>
                        <div className="mt-2 flex flex-col gap-3 sm:flex-row sm:items-center">
                            <input
                                ref={entrada}
                                id="orden_impresion"
                                type="file"
                                accept=".pdf,.jpg,.jpeg,.png,application/pdf,image/jpeg,image/png"
                                onChange={(evento) =>
                                    formulario.setData(
                                        'orden_impresion',
                                        evento.target.files?.[0] ?? null,
                                    )
                                }
                                className="border-input file:bg-brand block w-full flex-1 rounded-md border text-sm file:mr-4 file:h-9 file:border-0 file:px-4 file:text-sm file:font-medium file:text-white"
                            />
                            <button
                                type="submit"
                                disabled={
                                    formulario.processing ||
                                    formulario.data.orden_impresion === null
                                }
                                className="bg-brand inline-flex items-center justify-center gap-2 rounded-full px-5 py-2.5 text-sm font-medium text-white transition-transform hover:scale-[1.03] disabled:opacity-50"
                            >
                                {formulario.processing ? (
                                    <Spinner />
                                ) : (
                                    <Upload className="size-4" />
                                )}
                                {orden_impresion
                                    ? 'Reemplazar'
                                    : 'Subir archivo'}
                            </button>
                        </div>
                        <InputError
                            message={formulario.errors.orden_impresion}
                            className="mt-2"
                        />
                    </form>
                </section>

                <section className="border-brand/10 rounded-3xl border bg-white p-6 shadow-[0_30px_80px_-40px_rgba(15,16,49,0.35)] sm:p-8">
                    <h2 className="text-xl font-semibold tracking-tight">
                        Completar mi EPS
                    </h2>
                    <p className="text-brand/60 mt-1 text-sm">
                        {completo
                            ? 'Tu EPS ya está completo. Si cambiaste algo, tus datos actualizados ya cuentan.'
                            : 'Cuando estés conforme con el resumen y hayas subido tu orden de impresión, guarda para completar tu EPS.'}
                    </p>

                    {errors.orden_impresion &&
                        !formulario.errors.orden_impresion && (
                            <p className="text-destructive mt-4 flex items-start gap-2 text-sm">
                                <AlertCircle className="mt-0.5 size-4 shrink-0" />
                                {errors.orden_impresion}
                            </p>
                        )}

                    {!completo && (
                        <button
                            type="button"
                            onClick={finalizar}
                            disabled={finalizando}
                            className="bg-brand mt-6 inline-flex items-center gap-2 rounded-full px-6 py-3 text-sm font-medium text-white transition-transform hover:scale-[1.03] disabled:opacity-60"
                        >
                            {finalizando ? (
                                <Spinner />
                            ) : (
                                <BadgeCheck className="size-4" />
                            )}
                            Guardar y completar mi EPS
                        </button>
                    )}
                </section>
            </div>

            <Dialog
                open={confirmandoBorrado}
                onOpenChange={setConfirmandoBorrado}
            >
                <DialogContent>
                    <DialogHeader>
                        <DialogTitle>
                            Eliminar la orden de impresión
                        </DialogTitle>
                        <DialogDescription>
                            Sin la orden de impresión tu EPS vuelve a estar en
                            progreso y deja de contar en las estadísticas hasta
                            que subas una nueva.
                        </DialogDescription>
                    </DialogHeader>
                    <DialogFooter>
                        <button
                            type="button"
                            onClick={() => setConfirmandoBorrado(false)}
                            className="border-brand/15 hover:bg-brand/5 rounded-full border px-5 py-2.5 text-sm"
                        >
                            Cancelar
                        </button>
                        <button
                            type="button"
                            disabled={borrando}
                            onClick={borrar}
                            className="bg-destructive inline-flex items-center gap-2 rounded-full px-5 py-2.5 text-sm font-medium text-white disabled:opacity-60"
                        >
                            {borrando && <Spinner />}
                            Eliminar
                        </button>
                    </DialogFooter>
                </DialogContent>
            </Dialog>
        </EstudianteLayout>
    );
}
