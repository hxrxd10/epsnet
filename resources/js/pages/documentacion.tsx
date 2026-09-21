import { Head, usePage } from '@inertiajs/react';
import { dashboard, documentacion } from '@/routes';

type Guia = {
    rol: string;
    titulo: string;
    pasos: string[];
};

const GUIAS: Guia[] = [
    {
        rol: 'invitado',
        titulo: 'Invitado',
        pasos: [
            'Crea tu cuenta con tu correo y confirma tu correo con el enlace que te enviamos.',
            'Consulta las estadísticas de los EPS y el repositorio de EPS aprobados.',
            'Si eres estudiante en EPS, verifica tu identidad con tu registro académico y tu DPI desde el panel: tu cuenta pasará a ser de estudiante.',
        ],
    },
    {
        rol: 'estudiante',
        titulo: 'Estudiante',
        pasos: [
            'Verifica tu identidad una sola vez con tu registro académico y tu DPI. El DPI no se almacena.',
            'Elige la carrera en la que realizas tu EPS; cada carrera tiene su propio expediente y su unidad académica se toma automáticamente.',
            'Llena los seis ejes: bienes y servicios, publicaciones, transferencia de conocimiento, territorio (marcando el punto en el mapa), actores y seguimiento. Puedes registrar varios elementos en cada eje y todo se guarda al instante.',
            'En el último paso revisa el resumen, sube tu orden de impresión (PDF o imagen de hasta 10 MB) y guarda para completar tu EPS.',
            'Tu unidad académica revisará y aprobará tu EPS; entonces aparecerá en el repositorio. Puedes seguir corrigiendo tu información mientras tanto.',
        ],
    },
    {
        rol: 'unidad_academica',
        titulo: 'Unidad académica',
        pasos: [
            'DIGEU te asigna la unidad académica que administras.',
            'En "Estudiantes y EPS" ves a todos los estudiantes de tu unidad y el avance de cada EPS; puedes buscar por nombre, carné o carrera y filtrar por estado.',
            'Abre un EPS para revisar todo lo registrado y descargar la orden de impresión.',
            'Cuando el EPS esté completo, apruébalo: queda verificado y se publica en el repositorio. Puedes retirar la aprobación si hace falta.',
        ],
    },
    {
        rol: 'digeu',
        titulo: 'DIGEU (administrador)',
        pasos: [
            'En "Usuarios" das el rol de DIGEU o de unidad académica a quienes se registraron como invitados; a una unidad académica le asignas la unidad que administra (una persona por unidad).',
            'En "Estudiantes y EPS" ves a todos los estudiantes de todas las unidades, con filtros por unidad y estado.',
            'En "Manejo de datos" mantienes los catálogos que los estudiantes usan en sus formularios (bienes y servicios, acciones). Lo que ya está en uso se desactiva en lugar de eliminarse.',
            'También puedes aprobar EPS y consultar el repositorio.',
        ],
    },
];

const ESTADOS = [
    ['En progreso', 'El estudiante todavía está llenando su información.'],
    [
        'Completo',
        'El estudiante revisó su resumen y subió su orden de impresión: sus datos ya cuentan para las estadísticas.',
    ],
    [
        'Verificado',
        'La unidad académica lo aprobó (doble verificación): aparece en el repositorio.',
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
    const ordenadas = [
        ...GUIAS.filter((guia) => guia.rol === rol),
        ...GUIAS.filter((guia) => guia.rol !== rol),
    ];

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
                        es la guía de uso según cada rol.
                    </p>
                </div>

                <div className="grid gap-4 lg:grid-cols-2">
                    {ordenadas.map((guia) => (
                        <section
                            key={guia.rol}
                            className={`rounded-xl border p-6 ${
                                guia.rol === rol
                                    ? 'border-foreground'
                                    : 'border-sidebar-border/70'
                            }`}
                        >
                            <h2 className="font-semibold">
                                {guia.titulo}
                                {guia.rol === rol && (
                                    <span className="text-muted-foreground ml-2 text-xs font-normal">
                                        (tu rol)
                                    </span>
                                )}
                            </h2>
                            <ol className="text-muted-foreground mt-3 list-decimal space-y-2 pl-5 text-sm leading-relaxed">
                                {guia.pasos.map((paso) => (
                                    <li key={paso}>{paso}</li>
                                ))}
                            </ol>
                        </section>
                    ))}
                </div>

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
