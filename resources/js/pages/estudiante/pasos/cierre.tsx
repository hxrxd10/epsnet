import { Head, Link, router, useForm } from '@inertiajs/react';
import {
    BadgeCheck,
    Download,
    Inbox,
    Send,
    FileText,
    Pencil,
    Trash2,
    Upload,
} from 'lucide-react';
import { useRef, useState } from 'react';
import type { FormEvent } from 'react';
import {
    destroy as eliminarOrden,
    store as subirOrden,
} from '@/actions/App/Http/Controllers/Estudiante/OrdenImpresionController';
import { store as solicitarAprobacion } from '@/actions/App/Http/Controllers/Estudiante/SolicitudAprobacionController';
import FotoEps from '@/components/estudiante/foto-eps';
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
import { formatearFecha, formatearFechaHora } from '@/lib/fechas';
import EstudianteLayout from '@/layouts/estudiante-layout';
import type { PasoProps } from '@/types/estudiante';
import Articulacion from '@/components/expediente/articulacion';
import type { ArticulacionEps } from '@/components/expediente/articulacion';

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
    foto: string | null;
    articulacion: ArticulacionEps;
    puede_solicitar: boolean;
    solicitud: { enviada_at: string; posicion: number; total: number } | null;
    errors: {
        registros?: string;
        orden_impresion?: string;
        solicitud?: string;
    };
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

export default function Cierre({
    expediente,
    eje,
    pasos,
    resumen,
    total_registros,
    completado_at,
    orden_impresion,
    foto,
    articulacion,
    puede_solicitar,
    solicitud,
    errors,
}: Props) {
    const entrada = useRef<HTMLInputElement>(null);
    const formulario = useForm<{ orden_impresion: File | null }>({
        orden_impresion: null,
    });
    const [confirmandoBorrado, setConfirmandoBorrado] = useState(false);
    const [borrando, setBorrando] = useState(false);
    const [enviando, setEnviando] = useState(false);

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

    function enviarAAprobacion() {
        router.post(
            solicitarAprobacion.url(expediente.id),
            {},
            {
                preserveScroll: true,
                onStart: () => setEnviando(true),
                onFinish: () => setEnviando(false),
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
            titulo="Resumen y envío a aprobación"
            descripcion="Revisa lo que registraste y corrige lo que necesites. Todo EPS necesita la aprobación de supervisión de tu unidad académica: cuando lo aprueben, sus datos cuentan en las estadísticas."
        >
            <Head title="Resumen y envío a aprobación" />

            <div className="space-y-6">
                {completo && (
                    <div className="border-brand flex items-start gap-4 rounded-3xl border-2 bg-white p-6 shadow-[0_30px_80px_-40px_rgba(15,16,49,0.35)]">
                        <BadgeCheck className="mt-0.5 size-7 shrink-0" />
                        <div>
                            <p className="text-lg font-semibold">
                                {expediente.estado === 'verificado'
                                    ? 'Tu EPS está aprobado por la supervisión de tu unidad académica'
                                    : 'Tu EPS está en revisión'}
                            </p>
                            <p className="text-brand/65 mt-1 text-sm">
                                {completado_at &&
                                    `Enviado el ${formatearFecha(completado_at)}. `}
                                {expediente.estado === 'verificado'
                                    ? 'Sus datos cuentan para las estadísticas y aparece en el repositorio.'
                                    : 'Cuenta para las estadísticas cuando la supervisión lo apruebe.'}{' '}
                                Puedes seguir corrigiendo tu información cuando
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

                <Articulacion
                    articulacion={articulacion}
                    descripcion="Cuántas funciones universitarias combina tu EPS además de la docencia. Registra publicaciones, acciones de transferencia o instituciones aliadas para articular más."
                    className="rounded-3xl bg-white p-6 sm:p-8"
                />

                <FotoEps expedienteId={expediente.id} foto={foto} />

                <section className="border-brand/10 rounded-3xl border bg-white p-6 shadow-[0_30px_80px_-40px_rgba(15,16,49,0.35)] sm:p-8">
                    <h2 className="text-xl font-semibold tracking-tight">
                        Orden de impresión{' '}
                        <span className="text-brand/45 text-base font-normal">
                            (opcional)
                        </span>
                    </h2>
                    <p className="text-brand/60 mt-1 text-sm leading-relaxed">
                        Si tienes tu orden de impresión, súbela para que la
                        supervisión la revise junto con tu EPS. Sube el archivo
                        en PDF o imagen (JPG o PNG) de hasta 10 MB. Por sí sola
                        no aprueba tu EPS: siempre necesitas la aprobación de
                        supervisión.
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
                                        {formatearFecha(
                                            orden_impresion.subido_at,
                                        )}
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

                {(solicitud || puede_solicitar) && (
                    <section className="border-brand/10 rounded-3xl border bg-white p-6 shadow-[0_30px_80px_-40px_rgba(15,16,49,0.35)] sm:p-8">
                        <h2 className="flex items-center gap-2 text-xl font-semibold tracking-tight">
                            <Inbox className="size-5" />
                            Aprobación de supervisión
                        </h2>

                        {solicitud ? (
                            <div className="mt-3 text-sm leading-relaxed">
                                <p className="text-brand/70">
                                    Enviaste tu EPS el{' '}
                                    {formatearFechaHora(solicitud.enviada_at)}.
                                    Está en la bandeja de tu unidad académica,
                                    que las revisa en orden de llegada.
                                </p>
                                <p className="bg-brand/[0.05] mt-4 inline-block rounded-full px-4 py-1.5 font-medium">
                                    Tu solicitud es la {solicitud.posicion} de{' '}
                                    {solicitud.total} en espera
                                </p>
                            </div>
                        ) : (
                            <>
                                <p className="text-brand/60 mt-1 text-sm leading-relaxed">
                                    Cuando estés conforme con el resumen, envía
                                    tu EPS a la supervisión de tu unidad
                                    académica: le llegará a su bandeja de
                                    solicitudes y lo revisará en orden de
                                    llegada. Necesitas haber registrado al menos
                                    un elemento en los ejes.
                                </p>
                                <InputError
                                    message={
                                        errors.solicitud ?? errors.registros
                                    }
                                    className="mt-3"
                                />
                                <button
                                    type="button"
                                    onClick={enviarAAprobacion}
                                    disabled={enviando}
                                    className="bg-brand mt-6 inline-flex items-center gap-2 rounded-full px-6 py-3 text-sm font-medium text-white transition-transform hover:scale-[1.03] disabled:opacity-60"
                                >
                                    {enviando ? (
                                        <Spinner />
                                    ) : (
                                        <Send className="size-4" />
                                    )}
                                    Enviar mi EPS a aprobación
                                </button>
                            </>
                        )}
                    </section>
                )}
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
                            Se eliminará el archivo de tu orden de impresión. Si
                            tu EPS ya estaba aprobado, volverá a la bandeja de
                            supervisión para aprobarse de nuevo.
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
