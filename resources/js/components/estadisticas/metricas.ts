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
        descripcion:
            'personas beneficiadas, directas e indirectas, con los bienes y servicios',
    },
    {
        clave: 'acciones',
        etiqueta: 'Acciones de transferencia',
        descripcion:
            'capacitaciones, talleres, asesorías y documentos generados',
    },
    {
        clave: 'participantes',
        etiqueta: 'Participantes',
        descripcion:
            'personas que participaron directamente en las acciones de transferencia',
    },
    {
        clave: 'instituciones',
        etiqueta: 'Instituciones aliadas',
        descripcion:
            'instituciones que participaron o cooperaron con los proyectos',
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
