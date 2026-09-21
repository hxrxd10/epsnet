import { Head, Link, usePage } from '@inertiajs/react';
import { ArrowRight } from 'lucide-react';
import { dashboard, documentacion } from '@/routes';
import { datos } from '@/routes/admin';
import { index as bitacora } from '@/routes/admin/bitacora';
import { index as usuarios } from '@/routes/admin/usuarios';
import { index as estadisticas } from '@/routes/estadisticas';
import { acceso, carreras } from '@/routes/estudiante';
import { index as estudiantes } from '@/routes/panel/estudiantes';
import { index as repositorio } from '@/routes/repositorio';

type Seccion = {
    titulo: string;
    pasos: string[];
};

type Acceso = {
    titulo: string;
    href: string;
};

type Guia = {
    titulo: string;
    resumen: string;
    secciones: Seccion[];
    accesos: Acceso[];
};

const GUIAS: Record<string, Guia> = {
    invitado: {
        titulo: 'Invitado',
        resumen:
            'Tu cuenta te permite consultar la información consolidada de los EPS de todo el país.',
        secciones: [
            {
                titulo: 'Consultar las estadísticas',
                pasos: [
                    'Entra a "Estadísticas": el mapa de Guatemala muestra lo que ha hecho la red de EPS en cada departamento.',
                    'Elige en la barra de la derecha qué quieres ver: investigaciones, bienes y servicios, beneficiarios, acciones, participantes, instituciones, estudiantes o EPS.',
                    'Filtra por año (el de la orden de impresión), por unidad académica y por carrera. Al elegir una unidad, solo verás las carreras de esa unidad.',
                    'Haz clic en un departamento para ver sus investigaciones y sus principales bienes y servicios; con "Ver más" abres el detalle organizado por municipio.',
                    'Con "Generar PDF" descargas lo que estás viendo, con los filtros aplicados. También puedes generar el PDF de un departamento.',
                ],
            },
            {
                titulo: 'Consultar el repositorio',
                pasos: [
                    'En "Repositorio" están los EPS aprobados por su unidad académica, con una descripción de lo que se hizo en cada uno.',
                    'Busca por estudiante o carrera y filtra por unidad; abre un EPS para ver todo lo registrado.',
                ],
            },
            {
                titulo: 'Si eres estudiante en EPS',
                pasos: [
                    'Verifica tu identidad una sola vez con tu registro académico y tu DPI. El DPI no se almacena.',
                    'Al verificarte, tu cuenta pasa a ser de estudiante y podrás llenar tu EPS.',
                ],
            },
        ],
        accesos: [
            { titulo: 'Estadísticas', href: estadisticas().url },
            { titulo: 'Repositorio', href: repositorio().url },
            { titulo: 'Soy estudiante', href: acceso().url },
        ],
    },
    estudiante: {
        titulo: 'Estudiante',
        resumen:
            'Documentas tu EPS en seis ejes, paso a paso. Todo se guarda al instante y puedes volver a corregirlo mientras tu unidad no lo apruebe.',
        secciones: [
            {
                titulo: 'Empezar',
                pasos: [
                    'Verifica tu identidad una sola vez con tu registro académico y tu DPI.',
                    'Elige la carrera en la que realizas tu EPS: cada carrera tiene su propio expediente y su unidad académica se toma automáticamente.',
                    'En el primer paso indica si tu EPS se realiza dentro del EPSUM (Programa de EPS Multiprofesional) con el interruptor "Programa de tu EPS". Si no lo activas, se registra como EPS normal.',
                ],
            },
            {
                titulo: 'Llenar los seis ejes',
                pasos: [
                    'Bienes y servicios: lo que generaste para la comunidad (elige del catálogo, indica los beneficiarios y la fecha).',
                    'Publicaciones de investigación: título, tipo, autores, medio y, si lo tienes, el enlace.',
                    'Transferencia de conocimiento: capacitaciones, talleres o asesorías, con la comunidad y los participantes.',
                    'Territorio: elige el departamento y el municipio de la lista y, si quieres, marca el punto exacto en el mapa; el mapa sugiere el departamento y el municipio. La comunidad y la referencia son opcionales.',
                    'Actores y participantes: la institución receptora (puedes reutilizar una ya registrada), tu contraparte y la comunidad beneficiada.',
                    'Seguimiento e impacto: avances parciales durante el EPS y la evaluación de impacto al finalizar.',
                    'En cada eje puedes registrar varios elementos, editarlos o eliminarlos.',
                ],
            },
            {
                titulo: 'Cerrar tu EPS',
                pasos: [
                    'En el último paso revisa el resumen de lo que registraste.',
                    'Sube tu orden de impresión (PDF o imagen de hasta 10 MB) y guarda: tu EPS pasa a "Completo" y cuenta para las estadísticas.',
                    'Tu unidad académica lo revisará y, al aprobarlo, aparecerá en el repositorio.',
                ],
            },
        ],
        accesos: [
            { titulo: 'Mis carreras y EPS', href: carreras().url },
            { titulo: 'Estadísticas', href: estadisticas().url },
            { titulo: 'Repositorio', href: repositorio().url },
        ],
    },
    unidad_academica: {
        titulo: 'Unidad académica',
        resumen:
            'Revisas y apruebas los EPS de los estudiantes de la unidad que administras.',
        secciones: [
            {
                titulo: 'Revisar a tus estudiantes',
                pasos: [
                    'DIGEU te asigna la unidad académica que administras.',
                    'En "Estudiantes y EPS" ves a todos los estudiantes de tu unidad y el avance de cada EPS. Busca por nombre, carné o carrera y filtra por estado.',
                    'Los EPS que se realizan dentro del EPSUM llevan una etiqueta que lo indica.',
                    'Abre un EPS para revisar todo lo registrado en los seis ejes y descargar la orden de impresión.',
                ],
            },
            {
                titulo: 'Aprobar un EPS',
                pasos: [
                    'Cuando el EPS esté completo, usa "Aprobar EPS".',
                    'Debes marcar el acepto: confirmas que los bienes y servicios y todo lo descrito en el EPS está comprobado y que se ejecutó. Sin el acepto no se puede aprobar.',
                    'El EPS queda verificado y aparece en el repositorio. La aprobación, con quién la hizo y su acepto, queda anotada en la bitácora.',
                    'Puedes retirar la aprobación si hace falta: el EPS vuelve a "Completo".',
                ],
            },
            {
                titulo: 'Consultar los resultados',
                pasos: [
                    'En "Estadísticas" ves el mapa consolidado de todo el país y puedes filtrar por tu unidad y tus carreras.',
                    'Con "Generar PDF" descargas el reporte con los filtros aplicados.',
                ],
            },
        ],
        accesos: [
            { titulo: 'Estudiantes y EPS', href: estudiantes().url },
            { titulo: 'Estadísticas', href: estadisticas().url },
            { titulo: 'Repositorio', href: repositorio().url },
        ],
    },
    digeu: {
        titulo: 'DIGEU (administrador)',
        resumen:
            'Administras el sistema y consolidas la información de todas las unidades académicas.',
        secciones: [
            {
                titulo: 'Usuarios y estudiantes',
                pasos: [
                    'En "Usuarios" das el rol de DIGEU o de unidad académica a quienes se registraron como invitados; a una unidad académica le asignas la unidad que administra (una persona por unidad).',
                    'En "Estudiantes y EPS" ves a todos los estudiantes de todas las unidades, con filtros por unidad y estado, y puedes aprobar EPS (con el acepto de que lo descrito se ejecutó).',
                ],
            },
            {
                titulo: 'Manejo de datos',
                pasos: [
                    'Catálogos del EPS: bienes y servicios, y acciones de transferencia. Lo que ya está en uso se desactiva en lugar de eliminarse.',
                    'Departamentos y municipios: los estudiantes eligen su ubicación de estos catálogos. Cada uno lleva una coordenada de referencia que puedes marcar en el mapa.',
                    'Unidades académicas: facultades, escuelas y centros, con la ubicación de su edificio. Las unidades también se crean solas cuando un estudiante registra su carrera; aquí completas sus datos.',
                    'Un municipio o departamento con EPS registrados, y una unidad con EPS o con administrador, no se eliminan: se desactivan.',
                ],
            },
            {
                titulo: 'Estadísticas y reportes',
                pasos: [
                    'El mapa consolida todo el país. Filtra por año (el de la orden de impresión), unidad académica y carrera, y elige qué mostrar.',
                    'Cada departamento tiene su página con todo organizado por municipio: bienes y servicios e investigaciones.',
                    'Con "Generar PDF" descargas el reporte del país o de un departamento, con los filtros aplicados.',
                ],
            },
            {
                titulo: 'Bitácora',
                pasos: [
                    'La bitácora anota sola quién creó, editó o eliminó cualquier registro, con su correo, rol, fecha, módulo y el detalle del cambio (valor anterior y nuevo).',
                    'También anota los inicios y cierres de sesión, las aprobaciones de EPS y los PDF generados.',
                    'Filtra por usuario o correo, módulo, tipo de acción y rango de fechas. Nadie puede editarla ni eliminarla.',
                ],
            },
        ],
        accesos: [
            { titulo: 'Usuarios', href: usuarios().url },
            { titulo: 'Manejo de datos', href: datos().url },
            { titulo: 'Bitácora', href: bitacora().url },
            { titulo: 'Estadísticas', href: estadisticas().url },
        ],
    },
};

const ESTADOS = [
    ['En progreso', 'El estudiante todavía está llenando su información.'],
    [
        'Completo',
        'El estudiante revisó su resumen y subió su orden de impresión: sus datos ya cuentan para las estadísticas.',
    ],
    [
        'Verificado',
        'La unidad académica lo aprobó, confirmando que lo descrito se ejecutó: aparece en el repositorio.',
    ],
];

const GLOSARIO = [
    ['EPS', 'Ejercicio Profesional Supervisado.'],
    ['EPSUM', 'Programa de EPS Multiprofesional.'],
    ['DIGEU', 'Dirección General de Extensión Universitaria.'],
    [
        'Unidad académica',
        'Facultad, escuela o centro universitario de la USAC.',
    ],
    ['Eje', 'Cada una de las seis áreas en que se documenta un EPS.'],
    ['Orden de impresión', 'Documento con el que el estudiante valida su EPS.'],
];

export default function Documentacion() {
    const { rol } = usePage().props;
    const guia = rol ? GUIAS[rol] : undefined;

    return (
        <>
            <Head title="Documentación" />
            <div className="flex flex-1 flex-col gap-8 p-4">
                <div>
                    <h1 className="text-2xl font-semibold tracking-tight">
                        Documentación
                    </h1>
                    <p className="text-muted-foreground mt-1 max-w-2xl text-sm">
                        EPSNET es el sistema unificado de registro y seguimiento
                        del Ejercicio Profesional Supervisado de la USAC. Esta
                        es la guía de uso para tu rol.
                    </p>
                </div>

                {guia === undefined ? (
                    <p className="border-sidebar-border/70 text-muted-foreground rounded-xl border border-dashed p-10 text-center text-sm">
                        Tu cuenta aún no tiene un rol asignado. Cuando DIGEU te
                        lo asigne, aquí verás la guía de uso que te corresponde.
                    </p>
                ) : (
                    <>
                        <section className="border-foreground rounded-xl border p-6">
                            <p className="text-muted-foreground font-mono text-[11px] tracking-wider uppercase">
                                Tu rol
                            </p>
                            <h2 className="mt-1 text-xl font-semibold tracking-tight">
                                {guia.titulo}
                            </h2>
                            <p className="text-muted-foreground mt-2 max-w-2xl text-sm">
                                {guia.resumen}
                            </p>
                            <div className="mt-4 flex flex-wrap gap-2">
                                {guia.accesos.map((enlace) => (
                                    <Link
                                        key={enlace.titulo}
                                        href={enlace.href}
                                        className="hover:bg-accent inline-flex items-center gap-1.5 rounded-full border px-3.5 py-1.5 text-sm transition-colors"
                                    >
                                        {enlace.titulo}
                                        <ArrowRight className="size-3.5" />
                                    </Link>
                                ))}
                            </div>
                        </section>

                        <div className="grid gap-4 lg:grid-cols-2">
                            {guia.secciones.map((seccion) => (
                                <section
                                    key={seccion.titulo}
                                    className="border-sidebar-border/70 rounded-xl border p-6"
                                >
                                    <h3 className="font-semibold">
                                        {seccion.titulo}
                                    </h3>
                                    <ol className="text-muted-foreground mt-3 list-decimal space-y-2 pl-5 text-sm leading-relaxed">
                                        {seccion.pasos.map((paso) => (
                                            <li key={paso}>{paso}</li>
                                        ))}
                                    </ol>
                                </section>
                            ))}
                        </div>
                    </>
                )}

                <div className="grid gap-4 lg:grid-cols-2">
                    <section className="border-sidebar-border/70 rounded-xl border p-6">
                        <h2 className="font-semibold">Estados de un EPS</h2>
                        <dl className="mt-3 space-y-3 text-sm">
                            {ESTADOS.map(([estado, texto]) => (
                                <div key={estado}>
                                    <dt className="font-medium">{estado}</dt>
                                    <dd className="text-muted-foreground">
                                        {texto}
                                    </dd>
                                </div>
                            ))}
                        </dl>
                    </section>

                    <section className="border-sidebar-border/70 rounded-xl border p-6">
                        <h2 className="font-semibold">Glosario</h2>
                        <dl className="mt-3 space-y-3 text-sm">
                            {GLOSARIO.map(([termino, texto]) => (
                                <div key={termino}>
                                    <dt className="font-medium">{termino}</dt>
                                    <dd className="text-muted-foreground">
                                        {texto}
                                    </dd>
                                </div>
                            ))}
                        </dl>
                    </section>
                </div>
            </div>
        </>
    );
}

Documentacion.layout = {
    breadcrumbs: [
        { title: 'Panel', href: dashboard() },
        { title: 'Documentación', href: documentacion() },
    ],
};
