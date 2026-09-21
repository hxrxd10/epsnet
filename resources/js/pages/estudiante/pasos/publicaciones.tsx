import { Head } from '@inertiajs/react';
import {
    destroy,
    store,
    update,
} from '@/actions/App/Http/Controllers/Estudiante/PublicacionInvestigacionController';
import PasoCrud from '@/components/estudiante/paso-crud';
import type { Campo } from '@/components/estudiante/paso-crud';
import ResumenRegistro from '@/components/estudiante/resumen-registro';
import EstudianteLayout from '@/layouts/estudiante-layout';
import type { Opcion, PasoProps } from '@/types/estudiante';

type Registro = {
    id: number;
    titulo: string;
    tipo: string;
    tipo_etiqueta: string;
    autores: string;
    medio_publicacion: string | null;
    resumen: string | null;
    enlace: string | null;
    fecha_publicacion: string | null;
};

type Props = PasoProps & {
    registros: Registro[];
    tipos: Opcion[];
};

export default function Publicaciones({
    expediente,
    eje,
    pasos,
    registros,
    tipos,
}: Props) {
    const campos: Campo[] = [
        {
            name: 'titulo',
            label: 'Título de la investigación o publicación',
            tipo: 'textarea',
            requerido: true,
            filas: 3,
        },
        {
            name: 'tipo',
            label: 'Tipo',
            tipo: 'select',
            requerido: true,
            opciones: tipos,
            ancho: 'mitad',
        },
        {
            name: 'fecha_publicacion',
            label: 'Fecha de publicación',
            tipo: 'date',
            ancho: 'mitad',
        },
        {
            name: 'autores',
            label: 'Autores',
            tipo: 'textarea',
            requerido: true,
            filas: 2,
            ayuda: 'Separa los nombres con comas.',
        },
        {
            name: 'medio_publicacion',
            label: 'Medio de publicación',
            tipo: 'text',
            max: 500,
            placeholder: 'Revista, repositorio, editorial…',
        },
        {
            name: 'enlace',
            label: 'Enlace al documento',
            tipo: 'url',
            placeholder: 'https://',
            ayuda: 'Opcional. Puedes agregarlo después.',
        },
        {
            name: 'resumen',
            label: 'Resumen',
            tipo: 'textarea',
            filas: 7,
        },
    ];

    return (
        <EstudianteLayout
            paso={eje + 2}
            ejes={pasos}
            expediente={expediente}
            titulo="Publicaciones de investigación"
            descripcion="Registra las investigaciones y publicaciones vinculadas a tu ejercicio profesional supervisado."
        >
            <Head title="Publicaciones de investigación" />

            <PasoCrud<Registro>
                etiquetaRegistro="publicación"
                registros={registros}
                campos={campos}
                valoresIniciales={{
                    titulo: '',
                    tipo: '',
                    fecha_publicacion: '',
                    autores: '',
                    medio_publicacion: '',
                    enlace: '',
                    resumen: '',
                }}
                aFormulario={(registro) => ({
                    titulo: registro.titulo,
                    tipo: registro.tipo,
                    fecha_publicacion: registro.fecha_publicacion ?? '',
                    autores: registro.autores,
                    medio_publicacion: registro.medio_publicacion ?? '',
                    enlace: registro.enlace ?? '',
                    resumen: registro.resumen ?? '',
                })}
                resumen={(registro) => (
                    <ResumenRegistro
                        etiqueta={registro.tipo_etiqueta}
                        titulo={registro.titulo}
                        meta={[
                            registro.autores,
                            registro.medio_publicacion,
                            registro.fecha_publicacion,
                        ]}
                        texto={registro.resumen}
                    />
                )}
                urlGuardar={store.url(expediente.id)}
                urlActualizar={(id) =>
                    update.url({ expediente: expediente.id, registro: id })
                }
                urlEliminar={(id) =>
                    destroy.url({ expediente: expediente.id, registro: id })
                }
                vacio="Aún no has registrado publicaciones."
            />
        </EstudianteLayout>
    );
}
