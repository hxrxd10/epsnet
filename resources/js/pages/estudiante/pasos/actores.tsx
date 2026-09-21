import { Head } from '@inertiajs/react';
import {
    destroy,
    store,
    update,
} from '@/actions/App/Http/Controllers/Estudiante/ActorParticipanteController';
import {
    destroy as destroyAlianza,
    store as storeAlianza,
    update as updateAlianza,
} from '@/actions/App/Http/Controllers/Estudiante/AlianzaController';
import PasoCrud from '@/components/estudiante/paso-crud';
import type { Campo } from '@/components/estudiante/paso-crud';
import ResumenRegistro from '@/components/estudiante/resumen-registro';
import SelectorBuscable from '@/components/estudiante/selector-buscable';
import EstudianteLayout from '@/layouts/estudiante-layout';
import type { Opcion, PasoProps } from '@/types/estudiante';

type Registro = {
    id: number;
    institucion_receptora: string;
    contraparte: string;
    comunidad_beneficiada: string;
};

type Alianza = {
    id: number;
    institucion_aliada_id: number;
    institucion: string;
    tipo: string | null;
    aporte: string | null;
};

type Props = PasoProps & {
    registros: Registro[];
    alianzas: Alianza[];
    instituciones: Opcion[];
};

export default function Actores({
    expediente,
    eje,
    pasos,
    registros,
    alianzas,
    instituciones,
}: Props) {
    const camposReceptora: Campo[] = [
        {
            name: 'institucion_receptora',
            label: 'Institución receptora',
            tipo: 'text',
            requerido: true,
            max: 255,
            ayuda: 'La institución donde realizas tu EPS. Escríbela como la conoces.',
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

    const camposAliadas: Campo[] = [
        {
            name: 'institucion_aliada_id',
            label: 'Institución aliada',
            tipo: 'text',
            requerido: true,
            render: ({ valores, errores, establecer }) => (
                <SelectorBuscable
                    id="institucion_aliada_id"
                    label="Institución aliada"
                    requerido
                    opciones={instituciones}
                    valor={valores.institucion_aliada_id ?? ''}
                    onChange={(valor) =>
                        establecer({ institucion_aliada_id: valor })
                    }
                    error={errores.institucion_aliada_id}
                    ayuda="Busca y elige la institución. Si no aparece, pide a DIGEU que la agregue al catálogo."
                    vacio="No hay instituciones que coincidan. Pide a DIGEU que la agregue."
                />
            ),
        },
        {
            name: 'aporte',
            label: 'Aporte o cooperación',
            tipo: 'textarea',
            filas: 4,
            ayuda: 'Opcional: en qué consistió su participación (acompañamiento técnico, materiales, financiamiento, gestión…).',
        },
    ];

    return (
        <EstudianteLayout
            paso={eje + 2}
            ejes={pasos}
            expediente={expediente}
            titulo="Actores y participantes"
            descripcion="Registra la institución donde realizas tu EPS y las instituciones aliadas: las que participaron o cooperaron con tu proyecto, como un ministerio o una ONG."
        >
            <Head title="Actores y participantes" />

            <div className="space-y-12">
                <div>
                    <div className="mb-4">
                        <h2 className="text-xl font-semibold tracking-tight">
                            Institución receptora
                        </h2>
                        <p className="text-brand/60 mt-1 max-w-2xl text-sm">
                            Es donde realizas tu EPS. La defines tú: puede ser
                            cualquier institución u organización.
                        </p>
                    </div>
                    <PasoCrud<Registro>
                        etiquetaRegistro="institución receptora"
                        registros={registros}
                        campos={camposReceptora}
                        valoresIniciales={{
                            institucion_receptora: '',
                            contraparte: '',
                            comunidad_beneficiada: '',
                        }}
                        aFormulario={(registro) => ({
                            institucion_receptora:
                                registro.institucion_receptora,
                            contraparte: registro.contraparte,
                            comunidad_beneficiada:
                                registro.comunidad_beneficiada,
                        })}
                        resumen={(registro) => (
                            <ResumenRegistro
                                etiqueta="Institución receptora"
                                titulo={registro.institucion_receptora}
                                meta={[
                                    `Contraparte: ${registro.contraparte}`,
                                    `Comunidad: ${registro.comunidad_beneficiada}`,
                                ]}
                            />
                        )}
                        urlGuardar={store.url(expediente.id)}
                        urlActualizar={(id) =>
                            update.url({
                                expediente: expediente.id,
                                registro: id,
                            })
                        }
                        urlEliminar={(id) =>
                            destroy.url({
                                expediente: expediente.id,
                                registro: id,
                            })
                        }
                        vacio="Aún no has registrado tu institución receptora."
                    />
                </div>

                <div>
                    <div className="mb-4">
                        <h2 className="text-xl font-semibold tracking-tight">
                            Instituciones aliadas
                        </h2>
                        <p className="text-brand/60 mt-1 max-w-2xl text-sm">
                            Instituciones que participaron o cooperaron con tu
                            proyecto: un ministerio, una ONG, un socio. Se
                            eligen del catálogo de DIGEU y se cuantifican en las
                            estadísticas.
                        </p>
                    </div>
                    <PasoCrud<Alianza>
                        etiquetaRegistro="institución aliada"
                        registros={alianzas}
                        campos={camposAliadas}
                        valoresIniciales={{
                            institucion_aliada_id: '',
                            aporte: '',
                        }}
                        aFormulario={(alianza) => ({
                            institucion_aliada_id:
                                alianza.institucion_aliada_id.toString(),
                            aporte: alianza.aporte ?? '',
                        })}
                        resumen={(alianza) => (
                            <ResumenRegistro
                                etiqueta={alianza.tipo ?? 'Institución aliada'}
                                titulo={alianza.institucion}
                                texto={alianza.aporte}
                            />
                        )}
                        urlGuardar={storeAlianza.url(expediente.id)}
                        urlActualizar={(id) =>
                            updateAlianza.url({
                                expediente: expediente.id,
                                registro: id,
                            })
                        }
                        urlEliminar={(id) =>
                            destroyAlianza.url({
                                expediente: expediente.id,
                                registro: id,
                            })
                        }
                        vacio="Aún no has registrado instituciones aliadas."
                    />
                </div>
            </div>
        </EstudianteLayout>
    );
}
