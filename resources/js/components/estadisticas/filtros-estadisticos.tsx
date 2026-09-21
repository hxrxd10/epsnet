import { router } from '@inertiajs/react';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import type {
    FiltrosEstadisticos,
    OpcionesFiltros,
} from '@/types/estadisticas';

const TODOS = '__todos__';

type Props = OpcionesFiltros & {
    filtros: FiltrosEstadisticos;
    /** Ruta a la que se envían los filtros (el mapa o la página de un departamento). */
    url: string;
};

export default function FiltrosEstadisticosBarra({
    filtros,
    anios,
    unidades,
    carreras,
    url,
}: Props) {
    function aplicar(nuevos: Partial<FiltrosEstadisticos>) {
        const combinados = { ...filtros, ...nuevos };

        // Las carreras dependen de la unidad: al cambiarla se vuelve a "todas".
        if ('unidad' in nuevos) {
            combinados.carrera = '';
        }

        router.get(
            url,
            Object.fromEntries(
                Object.entries(combinados).filter(([, valor]) => valor !== ''),
            ),
            { preserveState: true, preserveScroll: true, replace: true },
        );
    }

    return (
        <div className="flex flex-wrap items-center gap-3">
            <Select
                value={filtros.anio || TODOS}
                onValueChange={(valor) =>
                    aplicar({ anio: valor === TODOS ? '' : valor })
                }
            >
                <SelectTrigger className="w-44" aria-label="Año">
                    <SelectValue />
                </SelectTrigger>
                <SelectContent>
                    <SelectItem value={TODOS}>Todos los años</SelectItem>
                    {anios.map((anio) => (
                        <SelectItem key={anio} value={String(anio)}>
                            {anio}
                        </SelectItem>
                    ))}
                </SelectContent>
            </Select>

            <Select
                value={filtros.unidad || TODOS}
                onValueChange={(valor) =>
                    aplicar({ unidad: valor === TODOS ? '' : valor })
                }
            >
                <SelectTrigger className="w-64" aria-label="Unidad académica">
                    <SelectValue />
                </SelectTrigger>
                <SelectContent>
                    <SelectItem value={TODOS}>Todas las unidades</SelectItem>
                    {unidades.map((unidad) => (
                        <SelectItem key={unidad.value} value={unidad.value}>
                            {unidad.label}
                        </SelectItem>
                    ))}
                </SelectContent>
            </Select>

            <Select
                value={filtros.carrera || TODOS}
                onValueChange={(valor) =>
                    aplicar({ carrera: valor === TODOS ? '' : valor })
                }
            >
                <SelectTrigger className="w-72" aria-label="Carrera">
                    <SelectValue />
                </SelectTrigger>
                <SelectContent>
                    <SelectItem value={TODOS}>Todas las carreras</SelectItem>
                    {carreras.map((carrera) => (
                        <SelectItem key={carrera.value} value={carrera.value}>
                            {carrera.label}
                        </SelectItem>
                    ))}
                </SelectContent>
            </Select>
        </div>
    );
}
