import { router } from '@inertiajs/react';
import { useState } from 'react';
import { update } from '@/actions/App/Http/Controllers/Estudiante/ProgramaExpedienteController';
import { cn } from '@/lib/utils';

type Props = {
    expedienteId: number;
    esEpsum: boolean;
};

/** Interruptor: ¿el EPS se realiza dentro del EPSUM (Programa de EPS Multiprofesional)? */
export default function ProgramaEps({ expedienteId, esEpsum }: Props) {
    const [guardando, setGuardando] = useState(false);

    function cambiar() {
        router.put(
            update.url(expedienteId),
            { epsum: !esEpsum },
            {
                preserveScroll: true,
                onStart: () => setGuardando(true),
                onFinish: () => setGuardando(false),
            },
        );
    }

    return (
        <section className="border-brand/10 mb-6 flex items-center justify-between gap-6 rounded-3xl border bg-white p-6 shadow-[0_30px_80px_-40px_rgba(15,16,49,0.35)]">
            <div>
                <h2 className="text-lg font-semibold tracking-tight">
                    Programa de tu EPS
                </h2>
                <p className="text-brand/60 mt-1 max-w-xl text-sm">
                    Activa esta opción si tu EPS se realiza dentro del EPSUM
                    (Programa de Ejercicio Profesional Supervisado
                    Multiprofesional). Si no, se registra como EPS normal.
                </p>
            </div>
            <div className="flex shrink-0 items-center gap-3">
                <span className="text-brand/70 text-sm font-medium">
                    {esEpsum ? 'EPSUM' : 'EPS normal'}
                </span>
                <button
                    type="button"
                    role="switch"
                    aria-checked={esEpsum}
                    aria-label="Mi EPS pertenece al EPSUM"
                    disabled={guardando}
                    onClick={cambiar}
                    className={cn(
                        'focus-visible:ring-brand relative inline-flex h-7 w-12 shrink-0 items-center rounded-full transition-colors focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:outline-none disabled:opacity-60',
                        esEpsum ? 'bg-brand' : 'bg-brand/20',
                    )}
                >
                    <span
                        className={cn(
                            'inline-block size-5 rounded-full bg-white shadow transition-transform',
                            esEpsum ? 'translate-x-6' : 'translate-x-1',
                        )}
                    />
                </button>
            </div>
        </section>
    );
}
