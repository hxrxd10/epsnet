import { Head } from '@inertiajs/react';
import {
    destroy,
    store,
    update,
} from '@/actions/App/Http/Controllers/Estudiante/SeguimientoImpactoController';
import PasoCrud from '@/components/estudiante/paso-crud';
import type { Campo } from '@/components/estudiante/paso-crud';
import ResumenRegistro from '@/components/estudiante/resumen-registro';
import EstudianteLayout from '@/layouts/estudiante-layout';
import type { Opcion, PasoProps } from '@/types/estudiante';

type Registro = {
    id: number;
    tipo_registro: string;
    tipo_registro_etiqueta: string;
    indicador: string;
    avance: string | null;
    porcentaje_avance: number | null;
    cumplimiento: string | null;
    cumplimiento_etiqueta: string | null;
    observaciones: string | null;
    evaluacion_impacto: string | null;
    fecha: string;
};

type Props = PasoProps & {
    registros: Registro[];
    tiposRegistro: Opcion[];
    nivelesCumplimiento: Opcion[];
};

export default function Seguimiento({
    expediente,
    eje,
    pasos,
    registros,
    tiposRegistro,
    nivelesCumplimiento,
}: Props) {
    const campos: Campo[] = [
        {
            name: 'tipo_registro',
            label: 'Tipo de registro',
            tipo: 'select',
            requerido: true,
            opciones: tiposRegistro,
            ayuda: 'Usa "Avance parcial" durante el EPS y la evaluación final al terminarlo.',
            ancho: 'mitad',
        },
        {
            name: 'fecha',
            label: 'Fecha',
            tipo: 'date',
            requerido: true,
            ancho: 'mitad',
        },
        {
            name: 'indicador',
            label: 'Indicador',
            tipo: 'text',
            requerido: true,
            max: 500,
            placeholder: 'Ej. Talleres impartidos en la comunidad',
        },
        {
            name: 'avance',
            label: 'Avance',
            tipo: 'textarea',
            filas: 5,
        },
        {
            name: 'porcentaje_avance',
            label: 'Porcentaje de avance',
            tipo: 'number',
            min: 0,
            maxNumero: 100,
            ancho: 'mitad',
        },
        {
            name: 'cumplimiento',
            label: 'Cumplimiento',
            tipo: 'select',
            opciones: nivelesCumplimiento,
            ancho: 'mitad',
        },
        {
            name: 'observaciones',
            label: 'Observaciones',
            tipo: 'textarea',
            filas: 4,
        },
        {
            name: 'evaluacion_impacto',
            label: 'Evaluación de impacto',
            tipo: 'textarea',
            requerido: true,
            filas: 8,
            ayuda: 'Describe el impacto de tu ejercicio en la comunidad y en tu formación.',
            visibleSi: (valores) =>
                valores.tipo_registro === 'evaluacion_final',
        },
    ];

    return (
        <EstudianteLayout
            paso={eje + 2}
            ejes={pasos}
            expediente={expediente}
            titulo="Seguimiento e impacto"
            descripcion="Registra los indicadores de avance y cumplimiento durante tu EPS, y al finalizarlo la evaluación de su impacto. Puedes repetirlo cuantas veces necesites."
        >
            <Head title="Seguimiento e impacto" />

            <PasoCrud<Registro>
                etiquetaRegistro="seguimiento"
                registros={registros}
                campos={campos}
                valoresIniciales={{
                    tipo_registro: '',
                    fecha: '',
                    indicador: '',
                    avance: '',
                    porcentaje_avance: '',
                    cumplimiento: '',
                    observaciones: '',
                    evaluacion_impacto: '',
                }}
                aFormulario={(registro) => ({
                    tipo_registro: registro.tipo_registro,
                    fecha: registro.fecha,
                    indicador: registro.indicador,
                    avance: registro.avance ?? '',
                    porcentaje_avance:
                        registro.porcentaje_avance?.toString() ?? '',
                    cumplimiento: registro.cumplimiento ?? '',
                    observaciones: registro.observaciones ?? '',
                    evaluacion_impacto: registro.evaluacion_impacto ?? '',
                })}
                resumen={(registro) => (
                    <ResumenRegistro
                        etiqueta={registro.tipo_registro_etiqueta}
                        titulo={registro.indicador}
                        meta={[
                            registro.fecha,
                            registro.porcentaje_avance !== null &&
                                `${registro.porcentaje_avance}% de avance`,
                            registro.cumplimiento_etiqueta,
                        ]}
                        texto={registro.evaluacion_impacto ?? registro.avance}
                    />
                )}
                urlGuardar={store.url(expediente.id)}
                urlActualizar={(id) =>
                    update.url({ expediente: expediente.id, registro: id })
                }
                urlEliminar={(id) =>
                    destroy.url({ expediente: expediente.id, registro: id })
                }
                vacio="Aún no has registrado seguimientos."
            />
        </EstudianteLayout>
    );
}
