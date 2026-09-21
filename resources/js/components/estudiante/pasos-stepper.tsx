import { Link } from '@inertiajs/react';
import { Check } from 'lucide-react';
import { useEffect, useRef } from 'react';
import { cn } from '@/lib/utils';
import { carreras } from '@/routes/estudiante';
import type { PasoEje } from '@/types/estudiante';

const ETIQUETAS = [
    'Acceso',
    'Carrera',
    'Bienes y servicios',
    'Publicaciones',
    'Transferencia',
    'Territorio',
    'Actores',
    'Seguimiento',
    'Cierre',
];

type PasosStepperProps = {
    /** Paso actual, de 1 (acceso) a 9 (resumen y orden de impresión). */
    paso: number;
    ejes?: PasoEje[];
};

export default function PasosStepper({ paso, ejes }: PasosStepperProps) {
    const lista = useRef<HTMLOListElement>(null);

    useEffect(() => {
        lista.current
            ?.querySelector('[aria-current="step"]')
            ?.scrollIntoView({ inline: 'center', block: 'nearest' });
    }, [paso]);

    const items = ETIQUETAS.map((etiqueta, indice) => {
        const numero = indice + 1;
        const eje = ejes?.find((item) => item.eje + 2 === numero);

        return {
            numero,
            etiqueta,
            href: numero === 2 && paso > 1 ? carreras.url() : eje?.href,
            completo: numero < 3 ? numero < paso : (eje?.registros ?? 0) > 0,
        };
    });

    return (
        <nav
            aria-label="Pasos del asistente"
            className="-mx-6 overflow-x-auto px-6"
        >
            <ol ref={lista} className="flex min-w-max items-center gap-2">
                {items.map((item, indice) => {
                    const actual = item.numero === paso;
                    const contenido = (
                        <>
                            <span
                                className={cn(
                                    'flex size-7 shrink-0 items-center justify-center rounded-full border text-xs font-medium transition-colors',
                                    actual
                                        ? 'text-brand border-white bg-white'
                                        : item.completo
                                          ? 'border-white/40 bg-white/20 text-white'
                                          : 'border-white/20 text-white/50',
                                )}
                            >
                                {item.completo && !actual ? (
                                    <Check className="size-3.5" />
                                ) : (
                                    item.numero
                                )}
                            </span>
                            <span
                                className={cn(
                                    'text-sm whitespace-nowrap',
                                    actual
                                        ? 'font-medium text-white'
                                        : 'text-white/60',
                                )}
                            >
                                {item.etiqueta}
                            </span>
                        </>
                    );

                    return (
                        <li
                            key={item.numero}
                            className="flex items-center gap-2"
                        >
                            {item.href && !actual ? (
                                <Link
                                    href={item.href}
                                    className="flex items-center gap-2 rounded-full py-1 pr-2 transition-opacity hover:opacity-80"
                                >
                                    {contenido}
                                </Link>
                            ) : (
                                <span
                                    aria-current={actual ? 'step' : undefined}
                                    className="flex items-center gap-2 py-1 pr-2"
                                >
                                    {contenido}
                                </span>
                            )}
                            {indice < items.length - 1 && (
                                <span
                                    aria-hidden
                                    className="h-px w-4 bg-white/20"
                                />
                            )}
                        </li>
                    );
                })}
            </ol>
        </nav>
    );
}
