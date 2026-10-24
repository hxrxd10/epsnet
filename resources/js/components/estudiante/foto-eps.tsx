import { router, useForm } from '@inertiajs/react';
import { ImagePlus, Trash2 } from 'lucide-react';
import { useRef, useState } from 'react';
import {
    destroy as eliminarFoto,
    store as subirFoto,
} from '@/actions/App/Http/Controllers/Estudiante/FotoExpedienteController';
import InputError from '@/components/input-error';
import { Spinner } from '@/components/ui/spinner';
import { reducirImagen } from '@/lib/imagenes';

/**
 * La mejor foto del EPS: el estudiante la elige, se reduce en el navegador y se guarda en la base
 * de datos. Se muestra en el repositorio cuando el EPS está aprobado.
 */
export default function FotoEps({
    expedienteId,
    foto,
}: {
    expedienteId: number;
    foto: string | null;
}) {
    const entrada = useRef<HTMLInputElement>(null);
    const formulario = useForm<{ foto: File | null }>({ foto: null });
    const [preparando, setPreparando] = useState(false);
    const [borrando, setBorrando] = useState(false);

    async function elegir(archivo: File | undefined) {
        if (!archivo) {
            return;
        }

        setPreparando(true);
        const reducida = await reducirImagen(archivo);
        setPreparando(false);

        formulario.transform(() => ({ foto: reducida }));
        formulario.post(subirFoto.url(expedienteId), {
            forceFormData: true,
            preserveScroll: true,
            onFinish: () => {
                if (entrada.current) {
                    entrada.current.value = '';
                }
            },
        });
    }

    function borrar() {
        router.delete(eliminarFoto.url(expedienteId), {
            preserveScroll: true,
            onStart: () => setBorrando(true),
            onFinish: () => setBorrando(false),
        });
    }

    const ocupado = preparando || formulario.processing;

    return (
        <section className="border-brand/10 rounded-3xl border bg-white p-6 shadow-[0_30px_80px_-40px_rgba(15,16,49,0.35)] sm:p-8">
            <h2 className="text-xl font-semibold tracking-tight">
                La mejor foto de tu EPS
            </h2>
            <p className="text-brand/60 mt-1 text-sm leading-relaxed">
                Elige la foto que mejor muestre tu trabajo. Aparecerá en el
                repositorio cuando tu EPS esté aprobado. JPG, PNG o WebP; la
                reducimos antes de subirla.
            </p>

            <div className="mt-6 flex flex-col gap-5 sm:flex-row sm:items-start">
                <div className="border-brand/15 bg-brand/[0.03] flex aspect-[4/3] w-full shrink-0 items-center justify-center overflow-hidden rounded-2xl border sm:w-72">
                    {foto ? (
                        <img
                            src={foto}
                            alt="Foto del EPS"
                            className="size-full object-cover"
                        />
                    ) : (
                        <ImagePlus className="text-brand/30 size-10" />
                    )}
                </div>

                <div className="flex flex-col gap-3">
                    <input
                        ref={entrada}
                        id="foto_eps"
                        type="file"
                        accept="image/jpeg,image/png,image/webp"
                        className="sr-only"
                        onChange={(evento) => elegir(evento.target.files?.[0])}
                    />
                    <label
                        htmlFor="foto_eps"
                        aria-disabled={ocupado}
                        className="bg-brand inline-flex cursor-pointer items-center justify-center gap-2 self-start rounded-full px-5 py-2.5 text-sm font-medium text-white transition-transform hover:scale-[1.03] aria-disabled:pointer-events-none aria-disabled:opacity-60"
                    >
                        {ocupado ? (
                            <Spinner />
                        ) : (
                            <ImagePlus className="size-4" />
                        )}
                        {foto ? 'Cambiar foto' : 'Elegir foto'}
                    </label>
                    {foto && (
                        <button
                            type="button"
                            onClick={borrar}
                            disabled={borrando}
                            className="border-brand/15 text-destructive hover:bg-destructive/5 inline-flex items-center gap-1.5 self-start rounded-full border px-3.5 py-2 text-sm transition-colors disabled:opacity-60"
                        >
                            {borrando ? (
                                <Spinner />
                            ) : (
                                <Trash2 className="size-3.5" />
                            )}
                            Quitar foto
                        </button>
                    )}
                    <InputError message={formulario.errors.foto} />
                </div>
            </div>
        </section>
    );
}
