import type { ClaveMetrica } from '@/types/estadisticas';

export type DefinicionMetrica = {
    clave: ClaveMetrica;
    etiqueta: string;
    descripcion: string;
};

/** Lo que se puede consultar en el mapa, en el orden en que se ofrece. */
export const METRICAS: DefinicionMetrica[] = [
    {
        clave: 'investigaciones',
        etiqueta: 'Investigaciones',
        descripcion: 'publicaciones de investigación',
    },
    {
        clave: 'bienes_servicios',
        etiqueta: 'Bienes y servicios',
        descripcion: 'bienes y servicios generados',
    },
    {
        clave: 'beneficiarios',
        etiqueta: 'Beneficiarios',
        descripcion: 'personas beneficiadas con bienes y servicios',
    },
    {
        clave: 'acciones',
        etiqueta: 'Acciones de transferencia',
        descripcion: 'capacitaciones, talleres y asesorías',
    },
    {
        clave: 'participantes',
        etiqueta: 'Participantes',
        descripcion: 'personas que participaron en las acciones',
    },
    {
        clave: 'instituciones',
        etiqueta: 'Instituciones',
        descripcion: 'instituciones receptoras distintas',
    },
    {
        clave: 'estudiantes',
        etiqueta: 'Estudiantes',
        descripcion: 'estudiantes con EPS',
    },
    { clave: 'eps', etiqueta: 'EPS', descripcion: 'EPS completos o aprobados' },
];

export const formatearNumero = (valor: number): string =>
    valor.toLocaleString('es-GT');
