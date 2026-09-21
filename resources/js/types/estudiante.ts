export type EstudianteSesion = {
    carnet: string;
    nombre_completo: string;
};

export type Opcion = {
    value: string;
    label: string;
};

export type EstadoExpediente = 'activo' | 'completo' | 'verificado';

export type ExpedienteResumen = {
    id: number;
    nombre_carrera: string;
    nombre_unidad: string;
    nivel_academico: string | null;
    estado: EstadoExpediente;
    estado_etiqueta: string;
};

export type PasoEje = {
    eje: number;
    titulo: string;
    href: string;
    registros: number;
};

/** Props comunes que el servidor envía a cada página de eje del asistente. */
export type PasoProps = {
    expediente: ExpedienteResumen;
    eje: number;
    pasos: PasoEje[];
};

export type CarreraEstudiante = {
    clave: string;
    nombre_carrera: string;
    nombre_unidad: string;
    nombre_extension: string | null;
    grado: string | null;
    ciclo_activo: string | null;
    graduado: boolean;
    fecha_graduado: string | null;
    expediente: {
        id: number;
        eje_actual: number;
        estado: EstadoExpediente;
        estado_etiqueta: string;
        registros: number;
    } | null;
};
