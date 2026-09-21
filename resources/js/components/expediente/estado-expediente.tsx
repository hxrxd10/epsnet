import { Badge } from '@/components/ui/badge';
import type { EstadoExpediente } from '@/types/estudiante';

export default function EstadoBadge({
    estado,
    etiqueta,
}: {
    estado: EstadoExpediente;
    etiqueta: string;
}) {
    return (
        <Badge
            variant={
                estado === 'verificado'
                    ? 'default'
                    : estado === 'completo'
                      ? 'secondary'
                      : 'outline'
            }
        >
            {etiqueta}
        </Badge>
    );
}
