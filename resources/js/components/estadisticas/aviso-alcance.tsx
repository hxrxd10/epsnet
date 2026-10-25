import { usePage } from '@inertiajs/react';
import { Filter } from 'lucide-react';

/**
 * Para una unidad académica: recuerda que las consultas solo muestran su unidad (y sus carreras).
 */
export default function AvisoAlcance() {
    const { alcanceConsulta } = usePage().props;

    if (!alcanceConsulta) {
        return null;
    }

    return (
        <p className="border-sidebar-border/70 bg-muted/50 text-muted-foreground flex items-start gap-2 rounded-lg border px-3 py-2 text-sm">
            <Filter className="mt-0.5 size-4 shrink-0" />
            <span>
                Solo ves los EPS de{' '}
                <span className="text-foreground font-medium">
                    {alcanceConsulta}
                </span>
                .
            </span>
        </p>
    );
}
