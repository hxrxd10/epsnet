import { Head } from '@inertiajs/react';
import {
    destroy,
    store,
    update,
} from '@/actions/App/Http/Controllers/Estudiante/BienServicioController';
import PasoCrud from '@/components/estudiante/paso-crud';
import InputError from '@/components/input-error';
import { Checkbox } from '@/components/ui/checkbox';
import ProgramaEps from '@/components/estudiante/programa-eps';
import type { Campo } from '@/components/estudiante/paso-crud';
import ResumenRegistro from '@/components/estudiante/resumen-registro';
import { formatearFecha } from '@/lib/fechas';
import EstudianteLayout from '@/layouts/estudiante-layout';
import type { Opcion, PasoProps } from '@/types/estudiante';

type Registro = {
    id: number;
    catalogo_id: number | null;
    tipo: string;
    tipo_etiqueta: string;
    descripcion: string;
    cantidad: number | null;
    beneficiarios: string | null;
    cantidad_beneficiarios: number | null;
    poblaciones: { id: number; nombre: string }[];
    fecha: string;
};

type OpcionCatalogo = Opcion & { linea: string | null };

type Props = PasoProps & {
    registros: Registro[];
    tipos: Opcion[];
    catalogo: OpcionCatalogo[];
    lineas: Opcion[];
    poblaciones: Opcion[];
};

export default function BienesServicios({
    expediente,
    eje,
    pasos,
    registros,
    tipos,
    catalogo,
    lineas,
    poblaciones,
}: Props) {
    const lineaDe = (catalogoId: string): string =>
        catalogo.find((opcion) => opcion.value === catalogoId)?.linea ?? '';

    const campos: Campo[] = [
        {
            name: 'linea',
            label: 'Línea de intervención',
            tipo: 'select',
            opciones: lineas,
            placeholder: 'Todas las líneas',
            ayuda: 'Elige la línea de intervención para encontrar más rápido el bien o servicio.',
            visibleSi: () => lineas.length > 0,
        },
        {
            name: 'catalogo_id',
            label: 'Bien o servicio del catálogo',
            tipo: 'select',
            dependeDe: 'linea',
            opciones: (valores) =>
                valores.linea
                    ? catalogo.filter(
                          (opcion) => opcion.linea === valores.linea,
                      )
                    : catalogo,
            placeholder: 'Selecciona uno o deja "Sin especificar"',
            ayuda: 'Si no aparece en la lista, déjalo sin especificar e indica el tipo.',
            visibleSi: () => catalogo.length > 0,
        },
        {
            name: 'tipo',
            label: 'Tipo',
            tipo: 'select',
            requerido: true,
            opciones: tipos,
            ancho: 'mitad',
            visibleSi: (valores) => !valores.catalogo_id,
        },
        {
            name: 'fecha',
            label: 'Fecha',
            tipo: 'date',
            requerido: true,
            ancho: 'mitad',
        },
        {
            name: 'descripcion',
            label: 'Descripción del bien o servicio',
            tipo: 'textarea',
            requerido: true,
            filas: 6,
            ayuda: 'Describe qué generaste durante tu EPS y el resultado obtenido.',
        },
        {
            name: 'cantidad',
            label: 'Cantidad entregada',
            tipo: 'number',
            min: 1,
            ayuda: 'Cuántas unidades del bien o servicio entregaste (por ejemplo, 40 pupitres o 12 jornadas). Si lo dejas vacío, cuenta como una.',
            ancho: 'mitad',
        },
        {
            name: 'beneficiarios',
            label: 'Beneficiarios',
            tipo: 'textarea',
            filas: 3,
            ayuda: 'Quiénes se beneficiaron: comunidad, institución, grupo, etc.',
        },
        {
            name: 'cantidad_beneficiarios',
            label: 'Cantidad de beneficiarios',
            tipo: 'number',
            min: 0,
            ayuda: 'Total estimado de personas que se benefician con este bien o servicio, directas e indirectas.',
            ancho: 'mitad',
        },
        {
            name: 'poblaciones',
            label: 'Poblaciones vulnerables incluidas',
            tipo: 'text',
            visibleSi: () => poblaciones.length > 0,
            render: ({ valores, establecer, errores }) => {
                const marcadas = valores.poblaciones
                    ? valores.poblaciones.split(',')
                    : [];
                const error = Object.entries(errores).find(([campo]) =>
                    campo.startsWith('poblaciones'),
                )?.[1];

                return (
                    <fieldset className="grid gap-2">
                        <legend className="text-sm font-medium">
                            Poblaciones vulnerables incluidas
                        </legend>
                        <p className="text-brand/55 text-xs">
                            Marca los grupos en situación de vulnerabilidad que
                            estuvieron entre los beneficiarios.
                        </p>
                        <div className="grid gap-2 sm:grid-cols-2">
                            {poblaciones.map((poblacion) => (
                                <label
                                    key={poblacion.value}
                                    className="flex items-center gap-2 text-sm"
                                >
                                    <Checkbox
                                        checked={marcadas.includes(
                                            poblacion.value,
                                        )}
                                        onCheckedChange={(valor) =>
                                            establecer({
                                                poblaciones: (valor === true
                                                    ? [
                                                          ...marcadas,
                                                          poblacion.value,
                                                      ]
                                                    : marcadas.filter(
                                                          (otra) =>
                                                              otra !==
                                                              poblacion.value,
                                                      )
                                                ).join(','),
                                            })
                                        }
                                    />
                                    {poblacion.label}
                                </label>
                            ))}
                        </div>
                        <InputError message={error} />
                    </fieldset>
                );
            },
        },
    ];

    return (
        <EstudianteLayout
            paso={eje + 2}
            ejes={pasos}
            expediente={expediente}
            titulo="Bienes y servicios generados"
            descripcion="Registra lo que produjiste para las comunidades durante tu ejercicio: materiales, obras, servicios, atención directa y más."
        >
            <Head title="Bienes y servicios generados" />

            <ProgramaEps
                expedienteId={expediente.id}
                esEpsum={expediente.es_epsum}
            />

            <PasoCrud<Registro>
                etiquetaRegistro="bien o servicio"
                registros={registros}
                campos={campos}
                valoresIniciales={{
                    linea: '',
                    catalogo_id: '',
                    tipo: '',
                    fecha: '',
                    descripcion: '',
                    cantidad: '',
                    beneficiarios: '',
                    cantidad_beneficiarios: '',
                    poblaciones: '',
                }}
                aFormulario={(registro) => ({
                    linea: lineaDe(registro.catalogo_id?.toString() ?? ''),
                    catalogo_id: registro.catalogo_id?.toString() ?? '',
                    tipo: registro.tipo,
                    fecha: registro.fecha,
                    descripcion: registro.descripcion,
                    cantidad: registro.cantidad?.toString() ?? '',
                    beneficiarios: registro.beneficiarios ?? '',
                    cantidad_beneficiarios:
                        registro.cantidad_beneficiarios?.toString() ?? '',
                    poblaciones: registro.poblaciones
                        .map((poblacion) => poblacion.id)
                        .join(','),
                })}
                resumen={(registro) => (
                    <ResumenRegistro
                        etiqueta={registro.tipo_etiqueta}
                        titulo={registro.descripcion}
                        meta={[
                            formatearFecha(registro.fecha),
                            registro.cantidad !== null &&
                                `${registro.cantidad} ${registro.cantidad === 1 ? 'unidad' : 'unidades'}`,
                            registro.cantidad_beneficiarios !== null &&
                                `${registro.cantidad_beneficiarios} beneficiarios`,
                        ]}
                        texto={
                            registro.poblaciones.length > 0
                                ? [
                                      registro.beneficiarios,
                                      `Poblaciones vulnerables: ${registro.poblaciones.map((poblacion) => poblacion.nombre).join(', ')}`,
                                  ]
                                      .filter(Boolean)
                                      .join(' · ')
                                : registro.beneficiarios
                        }
                    />
                )}
                antesDeEnviar={({ linea: _linea, ...valores }) => valores}
                urlGuardar={store.url(expediente.id)}
                urlActualizar={(id) =>
                    update.url({ expediente: expediente.id, registro: id })
                }
                urlEliminar={(id) =>
                    destroy.url({ expediente: expediente.id, registro: id })
                }
                vacio="Aún no has registrado bienes o servicios."
            />
        </EstudianteLayout>
    );
}
