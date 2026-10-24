import { Check, Minus } from 'lucide-react';
import { Badge } from '@/components/ui/badge';
import { cn } from '@/lib/utils';

export type ArticulacionEps = {
    nivel: number;
    total: number;
    porcentaje: number;
    etiqueta: string;
    funciones: {
        clave: string;
        etiqueta: string;
        evidencia: string;
        presente: boolean;
    }[];
};

/** Indicador compacto para listas: «Articulación 3/4 · Articulada». */
export function ArticulacionInsignia({
    articulacion,
}: {
    articulacion: ArticulacionEps;
}) {
    return (
        <Badge
            variant="outline"
            title={articulacion.funciones
                .map(
                    (funcion) =>
                        `${funcion.presente ? '✓' : '—'} ${funcion.etiqueta}`,
                )
                .join('\n')}
        >
            Articulación {articulacion.nivel}/{articulacion.total} ·{' '}
            {articulacion.etiqueta}
        </Badge>
    );
}

/**
 * Detalle del indicador: qué funciones universitarias combina el EPS y con qué registros se
 * demuestra cada una.
 */
export default function Articulacion({
    articulacion,
    descripcion = 'Cuántas funciones universitarias combina este EPS además de la docencia.',
    className,
}: {
    articulacion: ArticulacionEps;
    descripcion?: string;
    className?: string;
}) {
    return (
        <section
            className={cn(
                'border-sidebar-border/70 rounded-xl border p-5',
                className,
            )}
        >
            <div className="flex flex-wrap items-baseline justify-between gap-2">
                <h2 className="font-semibold">Indicador de articulación</h2>
                <p className="font-mono text-sm tabular-nums">
                    <span className="text-2xl font-semibold">
                        {articulacion.nivel}
                    </span>
                    <span className="text-muted-foreground">
                        {' '}
                        de {articulacion.total} · {articulacion.etiqueta}
                    </span>
                </p>
            </div>
            <p className="text-muted-foreground mt-1 text-sm">{descripcion}</p>

            <div
                className="mt-3 flex gap-1"
                role="img"
                aria-label={`${articulacion.nivel} de ${articulacion.total} funciones articuladas`}
            >
                {articulacion.funciones.map((funcion) => (
                    <span
                        key={funcion.clave}
                        className={cn(
                            'h-1.5 flex-1 rounded-full',
                            funcion.presente ? 'bg-brand' : 'bg-muted',
                        )}
                    />
                ))}
            </div>

            <ul className="mt-4 grid gap-2 sm:grid-cols-2">
                {articulacion.funciones.map((funcion) => (
                    <li key={funcion.clave} className="flex items-start gap-2">
                        <span
                            className={cn(
                                'mt-0.5 flex size-5 shrink-0 items-center justify-center rounded-full',
                                funcion.presente
                                    ? 'bg-brand text-white'
                                    : 'bg-muted text-muted-foreground',
                            )}
                        >
                            {funcion.presente ? (
                                <Check className="size-3" />
                            ) : (
                                <Minus className="size-3" />
                            )}
                        </span>
                        <span className="text-sm">
                            <span
                                className={cn(
                                    'font-medium',
                                    !funcion.presente &&
                                        'text-muted-foreground',
                                )}
                            >
                                {funcion.etiqueta}
                            </span>
                            <span className="text-muted-foreground block text-xs">
                                {funcion.evidencia}
                            </span>
                        </span>
                    </li>
                ))}
            </ul>
        </section>
    );
}
