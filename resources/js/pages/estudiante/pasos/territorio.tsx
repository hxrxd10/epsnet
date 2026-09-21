import { Head } from '@inertiajs/react';
import {
    destroy,
    store,
    update,
} from '@/actions/App/Http/Controllers/Estudiante/UbicacionTerritorialController';
import PasoCrud from '@/components/estudiante/paso-crud';
import type { Campo } from '@/components/estudiante/paso-crud';
import ResumenRegistro from '@/components/estudiante/resumen-registro';
import SelectorUbicacion from '@/components/estudiante/selector-ubicacion';
import EstudianteLayout from '@/layouts/estudiante-layout';
import type { Opcion, PasoProps } from '@/types/estudiante';

type Registro = {
    id: number;
    departamento_id: number;
    departamento: string;
    municipio: string;
    comunidad: string | null;
    latitud: string | null;
    longitud: string | null;
    referencia: string | null;
};

type Props = PasoProps & {
    registros: Registro[];
    departamentos: Opcion[];
    googleMaps: { key: string | null; mapId: string };
};

export default function Territorio({
    expediente,
    eje,
    pasos,
    registros,
    departamentos,
    googleMaps,
}: Props) {
    const campos: Campo[] = [
        {
            name: 'mapa',
            label: 'Ubicación en el mapa',
            tipo: 'text',
            render: ({ valores, establecer }) => (
                <SelectorUbicacion
                    apiKey={googleMaps.key}
                    mapId={googleMaps.mapId}
                    latitud={valores.latitud ?? ''}
                    longitud={valores.longitud ?? ''}
                    departamentos={departamentos}
                    onSeleccionar={(seleccion) =>
                        establecer(
                            Object.fromEntries(
                                Object.entries(seleccion).filter(
                                    ([, valor]) => valor !== undefined,
                                ),
                            ) as Record<string, string>,
                        )
                    }
                />
            ),
        },
        {
            name: 'departamento_id',
            label: 'Departamento',
            tipo: 'select',
            requerido: true,
            opciones: departamentos,
            ancho: 'mitad',
        },
        {
            name: 'municipio',
            label: 'Municipio',
            tipo: 'text',
            requerido: true,
            max: 150,
            ancho: 'mitad',
        },
        {
            name: 'comunidad',
            label: 'Comunidad',
            tipo: 'text',
            ayuda: 'Aldea, caserío, barrio o colonia donde realizas tu EPS.',
        },
        {
            name: 'latitud',
            label: 'Latitud',
            tipo: 'decimal',
            placeholder: 'Ej. 14.6349',
            ancho: 'mitad',
        },
        {
            name: 'longitud',
            label: 'Longitud',
            tipo: 'decimal',
            placeholder: 'Ej. -90.5069',
            ancho: 'mitad',
        },
        {
            name: 'referencia',
            label: 'Referencia territorial',
            tipo: 'textarea',
            filas: 3,
            ayuda: 'Si no tienes la ubicación exacta, indica la referencia más cercana.',
        },
    ];

    return (
        <EstudianteLayout
            paso={eje + 2}
            ejes={pasos}
            expediente={expediente}
            titulo="Territorio y geolocalización"
            descripcion="Marca en el mapa dónde realizas tu EPS y confirma el departamento y el municipio. Si no encuentras el lugar exacto, basta con el departamento y el municipio."
        >
            <Head title="Territorio y geolocalización" />

            <PasoCrud<Registro>
                etiquetaRegistro="ubicación"
                registros={registros}
                campos={campos}
                valoresIniciales={{
                    departamento_id: '',
                    municipio: '',
                    comunidad: '',
                    latitud: '',
                    longitud: '',
                    referencia: '',
                }}
                aFormulario={(registro) => ({
                    departamento_id: registro.departamento_id.toString(),
                    municipio: registro.municipio,
                    comunidad: registro.comunidad ?? '',
                    latitud: registro.latitud ?? '',
                    longitud: registro.longitud ?? '',
                    referencia: registro.referencia ?? '',
                })}
                resumen={(registro) => (
                    <ResumenRegistro
                        etiqueta={registro.departamento}
                        titulo={[registro.municipio, registro.comunidad]
                            .filter(Boolean)
                            .join(' · ')}
                        meta={[
                            registro.latitud !== null &&
                                registro.longitud !== null &&
                                `${registro.latitud}, ${registro.longitud}`,
                        ]}
                        texto={registro.referencia}
                    />
                )}
                urlGuardar={store.url(expediente.id)}
                urlActualizar={(id) =>
                    update.url({ expediente: expediente.id, registro: id })
                }
                urlEliminar={(id) =>
                    destroy.url({ expediente: expediente.id, registro: id })
                }
                vacio="Aún no has registrado tu ubicación."
            />
        </EstudianteLayout>
    );
}
