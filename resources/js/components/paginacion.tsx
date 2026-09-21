import { Link } from '@inertiajs/react';
import { ChevronLeft, ChevronRight } from 'lucide-react';

export type Pagina = {
    actual: number;
    ultima: number;
    total: number;
    anterior: string | null;
    siguiente: string | null;
};

export default function Paginacion({ pagina }: { pagina: Pagina }) {
    if (pagina.ultima <= 1) {
        return null;
    }

    const clase =
        'inline-flex items-center gap-1 rounded-md border px-3 py-1.5 text-sm';

    return (
        <nav
            aria-label="Paginación"
            className="flex items-center justify-between gap-4 pt-2 text-sm"
        >
            <span className="text-muted-foreground">
                Página {pagina.actual} de {pagina.ultima} · {pagina.total}{' '}
                resultados
            </span>
            <div className="flex gap-2">
                {pagina.anterior ? (
                    <Link
                        href={pagina.anterior}
                        preserveScroll
                        className={`${clase} hover:bg-accent`}
                    >
                        <ChevronLeft className="size-4" />
                        Anterior
                    </Link>
                ) : (
                    <span
                        className={`${clase} text-muted-foreground opacity-50`}
                    >
                        <ChevronLeft className="size-4" />
                        Anterior
                    </span>
                )}
                {pagina.siguiente ? (
                    <Link
                        href={pagina.siguiente}
                        preserveScroll
                        className={`${clase} hover:bg-accent`}
                    >
                        Siguiente
                        <ChevronRight className="size-4" />
                    </Link>
                ) : (
                    <span
                        className={`${clase} text-muted-foreground opacity-50`}
                    >
                        Siguiente
                        <ChevronRight className="size-4" />
                    </span>
                )}
            </div>
        </nav>
    );
}
