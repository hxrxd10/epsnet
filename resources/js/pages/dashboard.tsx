import { Head, Link, usePage } from '@inertiajs/react';
import {
    BarChart3,
    BookMarked,
    BookOpen,
    Database,
    GraduationCap,
    ScrollText,
    UserCog,
    Users,
} from 'lucide-react';
import type { LucideIcon } from 'lucide-react';
import { dashboard, documentacion } from '@/routes';
import { datos } from '@/routes/admin';
import { index as bitacora } from '@/routes/admin/bitacora';
import { index as usuarios } from '@/routes/admin/usuarios';
import { index as estadisticas } from '@/routes/estadisticas';
import { acceso } from '@/routes/estudiante';
import { index as estudiantes } from '@/routes/panel/estudiantes';
import { index as repositorio } from '@/routes/repositorio';

type Tarjeta = {
    icono: LucideIcon;
    titulo: string;
    texto: string;
    href?: ReturnType<typeof dashboard>;
};

export default function Dashboard() {
    const { rol, auth } = usePage().props;

    const tarjetas: Tarjeta[] = [
        {
            icono: BarChart3,
            titulo: 'Estadísticas',
            texto: 'Explora en el mapa lo que la red de EPS ha hecho en cada departamento.',
            href: estadisticas(),
        },
        {
            icono: BookMarked,
            titulo: 'Repositorio',
            texto: 'Consulta los EPS aprobados y lo que se hizo en cada uno.',
            href: repositorio(),
        },
    ];

    if (rol === 'digeu') {
        tarjetas.push(
            {
                icono: Users,
                titulo: 'Estudiantes y EPS',
                texto: 'Todos los estudiantes y sus EPS, de todas las unidades académicas.',
                href: estudiantes(),
            },
            {
                icono: UserCog,
                titulo: 'Usuarios',
                texto: 'Da el rol de DIGEU o de unidad académica a quienes se registraron.',
                href: usuarios(),
            },
            {
                icono: Database,
                titulo: 'Manejo de datos',
                texto: 'Administra los catálogos que usan los estudiantes en sus formularios.',
                href: datos(),
            },
            {
                icono: ScrollText,
                titulo: 'Bitácora',
                texto: 'Quién creó, editó o eliminó cada registro, con su correo, fecha y módulo.',
                href: bitacora(),
            },
        );
    }

    if (rol === 'unidad_academica') {
        tarjetas.push({
            icono: Users,
            titulo: 'Estudiantes y EPS',
            texto: 'Los estudiantes de tu unidad académica y sus EPS: revísalos y apruébalos.',
            href: estudiantes(),
        });
    }

    if (rol === 'invitado') {
        tarjetas.push({
            icono: GraduationCap,
            titulo: '¿Eres estudiante en EPS?',
            texto: 'Verifica tu identidad con tu registro académico y tu DPI para registrar tu EPS.',
            href: acceso(),
        });
    }

    tarjetas.push({
        icono: BookOpen,
        titulo: 'Documentación',
        texto: 'Guía de uso de EPSNET según tu rol.',
        href: documentacion(),
    });

    return (
        <>
            <Head title="Panel" />
            <div className="flex h-full flex-1 flex-col gap-6 overflow-x-auto rounded-xl p-4">
                <div>
                    <h1 className="text-2xl font-semibold tracking-tight">
                        Hola, {auth.user.name}
                    </h1>
                    <p className="text-muted-foreground mt-1 text-sm">
                        Bienvenido a EPSNET.
                    </p>
                </div>

                <div className="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                    {tarjetas.map((tarjeta) => {
                        const contenido = (
                            <>
                                <tarjeta.icono className="text-muted-foreground size-6" />
                                <h2 className="mt-4 font-semibold">
                                    {tarjeta.titulo}
                                </h2>
                                <p className="text-muted-foreground mt-1 text-sm">
                                    {tarjeta.texto}
                                </p>
                            </>
                        );
                        const clase =
                            'border-sidebar-border/70 flex flex-col rounded-xl border p-6';

                        return tarjeta.href ? (
                            <Link
                                key={tarjeta.titulo}
                                href={tarjeta.href}
                                className={`${clase} hover:bg-accent transition-colors`}
                            >
                                {contenido}
                            </Link>
                        ) : (
                            <section key={tarjeta.titulo} className={clase}>
                                {contenido}
                            </section>
                        );
                    })}
                </div>
            </div>
        </>
    );
}

Dashboard.layout = {
    breadcrumbs: [
        {
            title: 'Panel',
            href: dashboard(),
        },
    ],
};
