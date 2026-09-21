import { Head, Link, usePage } from '@inertiajs/react';
import { ArrowRight } from 'lucide-react';
import { dashboard, documentacion } from '@/routes';
import { datos } from '@/routes/admin';
import { index as bitacora } from '@/routes/admin/bitacora';
import { index as usuarios } from '@/routes/admin/usuarios';
import { index as estadisticas } from '@/routes/estadisticas';
import { acceso, carreras } from '@/routes/estudiante';
import { index as bandeja } from '@/routes/panel/bandeja';
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
                    'Filtra por año (el de la orden de impresión o, si no la tiene, el de su aprobación), por unidad académica y por carrera. Al elegir una unidad, solo verás las carreras de esa unidad.',
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
                    'Actores y participantes, primera parte: escribe la institución receptora (donde realizas tu EPS, puede ser cualquiera), tu contraparte y la comunidad beneficiada.',
                    'Actores y participantes, segunda parte: agrega las instituciones aliadas, es decir, las que participaron o cooperaron con tu proyecto (un ministerio, una ONG, un socio). Las eliges del catálogo de DIGEU (si no aparece, pide que la agreguen) y, si quieres, describes su aporte.',
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
            {
                titulo: 'Si no tienes orden de impresión',
                pasos: [
                    'Puede pasar que no haya un informe escrito y, por eso, no tengas orden de impresión. En ese caso no puedes completar tu EPS, pero sí enviarlo a aprobación.',
                    'En el último paso usa "Enviar a aprobación de mi unidad". Necesitas haber registrado al menos un elemento en los ejes.',
                    'Tu EPS llega a la bandeja de solicitudes de tu unidad académica, que las revisa en orden de llegada. El sistema te dice el lugar que ocupa tu solicitud.',
                    'Cuando tu unidad lo aprueba, tu EPS queda verificado, aparece en el repositorio y cuenta para las estadísticas.',
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
                titulo: 'Bandeja de solicitudes',
                pasos: [
                    'Cuando un estudiante sin orden de impresión envía su EPS a aprobación, le llega a tu bandeja de solicitudes. El menú muestra cuántas esperan.',
                    'Las solicitudes van en orden de llegada: la que llegó primero está arriba, con su número de turno, la fecha y hora de envío y los días que lleva esperando.',
                    'Abre una para revisar todo lo registrado y apruébala con el acepto; al aprobar vuelves a la bandeja para seguir con la siguiente.',
                    'DIGEU ve las solicitudes de todas las unidades y puede filtrarlas por unidad.',
                ],
            },
            {
                titulo: 'Aprobar un EPS',
                pasos: [
                    'Cuando el EPS esté completo, usa "Aprobar EPS". Si el estudiante no tiene orden de impresión (por ejemplo, porque no hay un informe escrito), también puedes aprobarlo: basta con que tenga información registrada y con tu acepto.',
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
            { titulo: 'Bandeja de solicitudes', href: bandeja().url },
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
                    'En "Bandeja de solicitudes" ves los EPS que los estudiantes enviaron a aprobación sin orden de impresión, de todas las unidades y en orden de llegada, para revisarlos y aprobarlos.',
                ],
            },
            {
                titulo: 'Manejo de datos',
                pasos: [
                    'Catálogos del EPS: bienes y servicios, y acciones de transferencia. Lo que ya está en uso se desactiva en lugar de eliminarse.',
                    'Instituciones aliadas: ministerios, ONG y socios que participaron o cooperaron con los proyectos. Los estudiantes las eligen de este catálogo y no pueden crearlas; son las que se cuantifican en las estadísticas. Una que ya figura en algún EPS no se elimina.',
                    'Departamentos y municipios: los estudiantes eligen su ubicación de estos catálogos. Cada uno lleva una coordenada de referencia que puedes marcar en el mapa.',
                    'Unidades académicas: facultades, escuelas y centros, con la ubicación de su edificio. Las unidades también se crean solas cuando un estudiante registra su carrera; aquí completas sus datos.',
                    'Un municipio o departamento con EPS registrados, y una unidad con EPS o con administrador, no se eliminan: se desactivan.',
                ],
            },
            {
                titulo: 'Estadísticas y reportes',
                pasos: [
                    'El mapa consolida todo el país. Filtra por año (el de la orden de impresión o, si no la tiene, el de su aprobación), unidad académica y carrera, y elige qué mostrar.',
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
            { titulo: 'Bandeja de solicitudes', href: bandeja().url },
            { titulo: 'Usuarios', href: usuarios().url },
            { titulo: 'Manejo de datos', href: datos().url },
            { titulo: 'Bitácora', href: bitacora().url },
            { titulo: 'Estadísticas', href: estadisticas().url },
        ],
    },
};

const CONCEPTOS = [
    [
        'Acción de transferencia',
        'Cualquier forma de compartir conocimiento con una comunidad: capacitaciones, talleres, asesorías y también los documentos o materiales que generaste con ese fin (propuestas de ley, políticas, informes técnicos). Se registran en el eje de Transferencia de conocimiento; para un documento elige el tipo "Documento o material generado".',
    ],
    [
        'Dónde va cada cosa',
        'Lo que entregas o prestas a la comunidad (una biblioteca, filtros de agua, una jornada de salud) va en Bienes y servicios. Los documentos y materiales con los que transfieres conocimiento van en Transferencia. Una investigación (artículo, tesis, informe de investigación) va en Publicaciones de investigación.',
    ],
    [
        'Participantes',
        'Personas que participaron directamente en una acción de transferencia: las que asistieron al taller o capacitación, o recibieron el material. Se cuentan por actividad; quien asiste a dos actividades cuenta dos veces.',
    ],
    [
        'Beneficiarios',
        'Total estimado de personas que se benefician con un bien o servicio, directas e indirectas (por ejemplo, todas las familias de una comunidad que usan la biblioteca). Es el alcance del bien o servicio, no la asistencia a una actividad.',
    ],
    [
        'Participantes y beneficiarios',
        'Son conteos distintos y no se suman entre sí: los participantes salen de las acciones de transferencia (eje 3) y los beneficiarios, de los bienes y servicios (eje 1). Una misma persona puede aparecer en ambos.',
    ],
    [
        'Institución receptora',
        'La institución donde el estudiante realiza su EPS. Es una sola por EPS y la escribe el estudiante libremente; no forma parte de ningún catálogo ni se cuantifica.',
    ],
    [
        'Instituciones aliadas',
        'Las instituciones que participaron o cooperaron con el proyecto: un ministerio, una ONG, un socio. Se eligen de un catálogo que administra DIGEU y son las que se cuentan en las estadísticas (instituciones distintas). Si una no aparece, hay que pedir que la agreguen.',
    ],
];

const ESTADOS = [
    ['En progreso', 'El estudiante todavía está llenando su información.'],
    [
        'Completo',
        'El estudiante revisó su resumen y subió su orden de impresión: sus datos ya cuentan para las estadísticas.',
    ],
    [
        'Verificado',
        'La unidad académica lo aprobó, confirmando que lo descrito se ejecutó: aparece en el repositorio. Se puede aprobar aun sin orden de impresión (por ejemplo, si no hay un informe escrito).',
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

                <section className="border-sidebar-border/70 rounded-xl border p-6">
                    <h2 className="font-semibold">
                        Conceptos que conviene tener claros
                    </h2>
                    <dl className="mt-3 grid gap-x-8 gap-y-4 text-sm lg:grid-cols-2">
                        {CONCEPTOS.map(([termino, texto]) => (
                            <div key={termino}>
                                <dt className="font-medium">{termino}</dt>
                                <dd className="text-muted-foreground mt-0.5 leading-relaxed">
                                    {texto}
                                </dd>
                            </div>
                        ))}
                    </dl>
                </section>

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
