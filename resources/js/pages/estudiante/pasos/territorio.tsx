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
import type { OpcionMunicipio } from '@/components/estudiante/selector-ubicacion';
import EstudianteLayout from '@/layouts/estudiante-layout';
import type { Opcion, PasoProps } from '@/types/estudiante';

type Registro = {
    id: number;
    departamento_id: number;
    departamento: string;
    municipio_id: number | null;
    municipio: string | null;
    comunidad: string | null;
    latitud: string | null;
    longitud: string | null;
    referencia: string | null;
};

type Props = PasoProps & {
    registros: Registro[];
    departamentos: Opcion[];
    municipios: (OpcionMunicipio & {
        latitud: string | null;
        longitud: string | null;
    })[];
    googleMaps: { key: string | null; mapId: string };
};

export default function Territorio({
    expediente,
    eje,
    pasos,
    registros,
    departamentos,
    municipios,
    googleMaps,
}: Props) {
    const campos: Campo[] = [
        {
            name: 'mapa',
            label: 'Ubicación en el mapa',
            tipo: 'text',
            render: ({ valores, establecer }) => {
                const municipio = municipios.find(
                    (item) => item.value === valores.municipio_id,
                );

                return (
                    <SelectorUbicacion
                        apiKey={googleMaps.key}
                        mapId={googleMaps.mapId}
                        latitud={valores.latitud ?? ''}
                        longitud={valores.longitud ?? ''}
                        departamentos={departamentos}
                        municipios={municipios}
                        centro={
                            municipio?.latitud && municipio.longitud
                                ? {
                                      lat: Number(municipio.latitud),
                                      lng: Number(municipio.longitud),
                                  }
                                : null
                        }
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
                );
            },
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
            name: 'municipio_id',
            label: 'Municipio',
            tipo: 'select',
            requerido: true,
            dependeDe: 'departamento_id',
            placeholder: 'Selecciona un municipio',
            opciones: (valores) =>
                municipios.filter(
                    (municipio) =>
                        municipio.departamento_id === valores.departamento_id,
                ),
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
                    municipio_id: '',
                    comunidad: '',
                    latitud: '',
                    longitud: '',
                    referencia: '',
                }}
                aFormulario={(registro) => ({
                    departamento_id: registro.departamento_id.toString(),
                    municipio_id: registro.municipio_id?.toString() ?? '',
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
