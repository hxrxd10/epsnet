import { Head } from '@inertiajs/react';
import {
    destroy,
    store,
    update,
} from '@/actions/App/Http/Controllers/Estudiante/TransferenciaConocimientoController';
import PasoCrud from '@/components/estudiante/paso-crud';
import type { Campo } from '@/components/estudiante/paso-crud';
import ResumenRegistro from '@/components/estudiante/resumen-registro';
import { formatearFecha } from '@/lib/fechas';
import EstudianteLayout from '@/layouts/estudiante-layout';
import type { Opcion, PasoProps } from '@/types/estudiante';

type Registro = {
    id: number;
    catalogo_id: number | null;
    tipo_actividad: string;
    tipo_actividad_etiqueta: string;
    actividad: string;
    comunidad: string;
    numero_participantes: number | null;
    fecha: string;
};

type Props = PasoProps & {
    registros: Registro[];
    tipos: Opcion[];
    catalogo: Opcion[];
};

export default function Transferencias({
    expediente,
    eje,
    pasos,
    registros,
    tipos,
    catalogo,
}: Props) {
    const campos: Campo[] = [
        {
            name: 'catalogo_id',
            label: 'Acción del catálogo',
            tipo: 'select',
            opciones: catalogo,
            placeholder: 'Selecciona una o deja "Sin especificar"',
            ayuda: 'Opcional: elige la acción si aparece en la lista.',
            visibleSi: () => catalogo.length > 0,
        },
        {
            name: 'tipo_actividad',
            label: 'Tipo de actividad',
            tipo: 'select',
            requerido: true,
            opciones: tipos,
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
            name: 'actividad',
            label: 'Actividad realizada',
            tipo: 'textarea',
            requerido: true,
            filas: 6,
            ayuda: 'Describe la acción y lo que se transfirió. Si fue un documento o material (una propuesta de ley, una política, un informe, una guía), indica de qué trata y a quién se entregó.',
        },
        {
            name: 'comunidad',
            label: 'Comunidad o grupo beneficiado',
            tipo: 'text',
            requerido: true,
            ancho: 'mitad',
        },
        {
            name: 'numero_participantes',
            label: 'Número de participantes',
            tipo: 'number',
            min: 0,
            ayuda: 'Personas que participaron directamente en la acción (las que asistieron o recibieron el material). Es distinto de los beneficiarios de un bien o servicio.',
            ancho: 'mitad',
        },
    ];

    return (
        <EstudianteLayout
            paso={eje + 2}
            ejes={pasos}
            expediente={expediente}
            titulo="Transferencia de conocimiento"
            descripcion="Registra las capacitaciones, talleres, asesorías y también los documentos y materiales que generaste para compartir conocimiento (leyes, políticas, informes, guías). Si la hiciste junto a otros estudiantes, cada quien registra la suya."
        >
            <Head title="Transferencia de conocimiento" />

            <PasoCrud<Registro>
                etiquetaRegistro="actividad"
                registros={registros}
                campos={campos}
                valoresIniciales={{
                    catalogo_id: '',
                    tipo_actividad: '',
                    fecha: '',
                    actividad: '',
                    comunidad: '',
                    numero_participantes: '',
                }}
                aFormulario={(registro) => ({
                    catalogo_id: registro.catalogo_id?.toString() ?? '',
                    tipo_actividad: registro.tipo_actividad,
                    fecha: registro.fecha,
                    actividad: registro.actividad,
                    comunidad: registro.comunidad,
                    numero_participantes:
                        registro.numero_participantes?.toString() ?? '',
                })}
                resumen={(registro) => (
                    <ResumenRegistro
                        etiqueta={registro.tipo_actividad_etiqueta}
                        titulo={registro.actividad}
                        meta={[
                            registro.comunidad,
                            formatearFecha(registro.fecha),
                            registro.numero_participantes !== null &&
                                `${registro.numero_participantes} participantes`,
                        ]}
                    />
                )}
                urlGuardar={store.url(expediente.id)}
                urlActualizar={(id) =>
                    update.url({ expediente: expediente.id, registro: id })
                }
                urlEliminar={(id) =>
                    destroy.url({ expediente: expediente.id, registro: id })
                }
                vacio="Aún no has registrado actividades de transferencia."
            />
        </EstudianteLayout>
    );
}
