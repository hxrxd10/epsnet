import type { ReactNode } from 'react';
import { Badge } from '@/components/ui/badge';

type ResumenRegistroProps = {
    etiqueta?: string | null;
    titulo: ReactNode;
    meta?: (string | number | null | undefined | false)[];
    texto?: string | null;
};

/** Presentación común de un registro guardado en la lista de un eje. */
export default function ResumenRegistro({
    etiqueta,
    titulo,
    meta = [],
    texto,
}: ResumenRegistroProps) {
    const datos = meta.filter(
        (dato): dato is string | number =>
            dato !== null &&
            dato !== undefined &&
            dato !== false &&
            dato !== '',
    );

    return (
        <div>
            {etiqueta && (
                <Badge variant="secondary" className="mb-2">
                    {etiqueta}
                </Badge>
            )}
            <p className="line-clamp-2 leading-snug font-medium">{titulo}</p>
            {datos.length > 0 && (
                <p className="text-brand/55 mt-1 text-sm">
                    {datos.join(' · ')}
                </p>
            )}
            {texto && (
                <p className="text-brand/70 mt-2 line-clamp-3 text-sm leading-relaxed whitespace-pre-line">
                    {texto}
                </p>
            )}
        </div>
    );
}
