import { Badge } from '@/components/ui/badge';

export type RegistroDetalle = {
    etiqueta: string | null;
    titulo: string;
    meta: string[];
    textos: { etiqueta: string; valor: string }[];
};

export type EjeDetalle = {
    eje: number;
    titulo: string;
    registros: RegistroDetalle[];
};

/** Lo que el estudiante registró en cada eje, en modo de solo lectura. */
export default function DetalleEjes({ ejes }: { ejes: EjeDetalle[] }) {
    return (
        <div className="space-y-8">
            {ejes.map((eje) => (
                <section key={eje.eje}>
                    <div className="flex items-baseline gap-3">
                        <h2 className="text-lg font-semibold tracking-tight">
                            {eje.titulo}
                        </h2>
                        <span className="text-muted-foreground font-mono text-xs">
                            {eje.registros.length}{' '}
                            {eje.registros.length === 1
                                ? 'registro'
                                : 'registros'}
                        </span>
                    </div>

                    {eje.registros.length === 0 ? (
                        <p className="text-muted-foreground mt-2 text-sm">
                            Sin registros.
                        </p>
                    ) : (
                        <ul className="mt-3 space-y-3">
                            {eje.registros.map((registro, indice) => (
                                <li
                                    key={indice}
                                    className="border-sidebar-border/70 rounded-xl border p-4"
                                >
                                    {registro.etiqueta && (
                                        <Badge
                                            variant="secondary"
                                            className="mb-2"
                                        >
                                            {registro.etiqueta}
                                        </Badge>
                                    )}
                                    <p className="font-medium">
                                        {registro.titulo}
                                    </p>
                                    {registro.meta.length > 0 && (
                                        <p className="text-muted-foreground mt-1 text-sm">
                                            {registro.meta.join(' · ')}
                                        </p>
                                    )}
                                    {registro.textos.map((texto) => (
                                        <div
                                            key={texto.etiqueta}
                                            className="mt-3"
                                        >
                                            <p className="text-muted-foreground font-mono text-[11px] tracking-wider uppercase">
                                                {texto.etiqueta}
                                            </p>
                                            <p className="mt-1 text-sm leading-relaxed whitespace-pre-line">
                                                {texto.valor}
                                            </p>
                                        </div>
                                    ))}
                                </li>
                            ))}
                        </ul>
                    )}
                </section>
            ))}
        </div>
    );
}
