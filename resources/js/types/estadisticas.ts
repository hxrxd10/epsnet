import type { Opcion } from '@/types/estudiante';

export type ClaveMetrica =
    | 'eps'
    | 'estudiantes'
    | 'bienes_servicios'
    | 'beneficiarios'
    | 'acciones'
    | 'participantes'
    | 'investigaciones'
    | 'instituciones';

export type Metricas = Record<ClaveMetrica, number>;

export type ItemBienServicio = {
    nombre: string;
    tipo: 'bien' | 'servicio';
    cantidad: number;
    beneficiarios: number;
};

export type DepartamentoEstadistico = {
    codigo: string;
    nombre: string;
    cabecera: string;
    metricas: Metricas;
    top_bienes_servicios: ItemBienServicio[];
};

export type Investigacion = {
    id: number;
    titulo: string;
    tipo: string;
    autores: string;
    medio: string | null;
    fecha: string | null;
    enlace: string | null;
    carrera: string;
    unidad: string;
};

export type MunicipioEstadistico = {
    nombre: string;
    metricas: Metricas;
    bienes_servicios: ItemBienServicio[];
    investigaciones: Investigacion[];
};

export type FiltrosEstadisticos = {
    anio: string;
    unidad: string;
    carrera: string;
};

export type OpcionesFiltros = {
    anios: number[];
    unidades: Opcion[];
    carreras: Opcion[];
};
