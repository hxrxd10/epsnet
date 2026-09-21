import { Head } from '@inertiajs/react';
import {
    destroy,
    store,
    update,
} from '@/actions/App/Http/Controllers/Estudiante/ActorParticipanteController';
import PasoCrud from '@/components/estudiante/paso-crud';
import type { Campo, Valores } from '@/components/estudiante/paso-crud';
import ResumenRegistro from '@/components/estudiante/resumen-registro';
import EstudianteLayout from '@/layouts/estudiante-layout';
import type { Opcion, PasoProps } from '@/types/estudiante';

type Registro = {
    id: number;
    institucion_receptora_id: number;
    institucion: string;
    contraparte: string;
    comunidad_beneficiada: string;
};

type Props = PasoProps & {
    registros: Registro[];
    instituciones: Opcion[];
};

const NUEVA = 'nueva';

const esNueva = (valores: Valores) =>
    valores.institucion_receptora_id === NUEVA;

export default function Actores({
    expediente,
    eje,
    pasos,
    registros,
    instituciones,
}: Props) {
    const campos: Campo[] = [
        {
            name: 'institucion_receptora_id',
            label: 'Institución receptora',
            tipo: 'select',
            requerido: true,
            opciones: [
                { value: NUEVA, label: '+ Registrar una institución nueva' },
                ...instituciones,
            ],
            ayuda: 'Si otra persona ya registró la institución, selecciónala de la lista.',
        },
        {
            name: 'institucion_nombre',
            label: 'Nombre de la institución',
            tipo: 'text',
            requerido: true,
            visibleSi: esNueva,
        },
        {
            name: 'institucion_tipo',
            label: 'Tipo de institución',
            tipo: 'text',
            max: 100,
            placeholder: 'Escuela, centro de salud, municipalidad…',
            ancho: 'mitad',
            visibleSi: esNueva,
        },
        {
            name: 'institucion_nombre_contacto',
            label: 'Persona de contacto',
            tipo: 'text',
            ancho: 'mitad',
            visibleSi: esNueva,
        },
        {
            name: 'institucion_correo_contacto',
            label: 'Correo de contacto',
            tipo: 'email',
            ancho: 'mitad',
            visibleSi: esNueva,
        },
        {
            name: 'institucion_telefono_contacto',
            label: 'Teléfono de contacto',
            tipo: 'text',
            max: 30,
            ancho: 'mitad',
            visibleSi: esNueva,
        },
        {
            name: 'contraparte',
            label: 'Contraparte',
            tipo: 'text',
            requerido: true,
            ayuda: 'Persona de la institución que acompaña tu EPS.',
            ancho: 'mitad',
        },
        {
            name: 'comunidad_beneficiada',
            label: 'Comunidad beneficiada',
            tipo: 'text',
            requerido: true,
            ancho: 'mitad',
        },
    ];

    return (
        <EstudianteLayout
            paso={eje + 2}
            ejes={pasos}
            expediente={expediente}
            titulo="Actores y participantes"
            descripcion="Registra la institución que te recibe, tu contraparte y la comunidad que se beneficia con tu ejercicio."
        >
            <Head title="Actores y participantes" />

            <PasoCrud<Registro>
                etiquetaRegistro="actor"
                registros={registros}
                campos={campos}
                valoresIniciales={{
                    institucion_receptora_id: '',
                    institucion_nombre: '',
                    institucion_tipo: '',
                    institucion_nombre_contacto: '',
                    institucion_correo_contacto: '',
                    institucion_telefono_contacto: '',
                    contraparte: '',
                    comunidad_beneficiada: '',
                }}
                aFormulario={(registro) => ({
                    institucion_receptora_id:
                        registro.institucion_receptora_id.toString(),
                    institucion_nombre: '',
                    institucion_tipo: '',
                    institucion_nombre_contacto: '',
                    institucion_correo_contacto: '',
                    institucion_telefono_contacto: '',
                    contraparte: registro.contraparte,
                    comunidad_beneficiada: registro.comunidad_beneficiada,
                })}
                antesDeEnviar={(valores) =>
                    esNueva(valores)
                        ? { ...valores, institucion_receptora_id: '' }
                        : {
                              ...valores,
                              institucion_nombre: '',
                              institucion_tipo: '',
                              institucion_nombre_contacto: '',
                              institucion_correo_contacto: '',
                              institucion_telefono_contacto: '',
                          }
                }
                resumen={(registro) => (
                    <ResumenRegistro
                        etiqueta="Institución receptora"
                        titulo={registro.institucion}
                        meta={[
                            `Contraparte: ${registro.contraparte}`,
                            `Comunidad: ${registro.comunidad_beneficiada}`,
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
                vacio="Aún no has registrado actores."
            />
        </EstudianteLayout>
    );
}
