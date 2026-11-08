import { Link, usePage } from '@inertiajs/react';
import {
    BarChart3,
    BookMarked,
    BookOpen,
    Database,
    FlaskConical,
    GraduationCap,
    Inbox,
    LayoutGrid,
    ScrollText,
    Target,
    UserCog,
    Users,
} from 'lucide-react';
import AppLogo from '@/components/app-logo';
import { NavMain } from '@/components/nav-main';
import { NavUser } from '@/components/nav-user';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { dashboard, documentacion } from '@/routes';
import { datos } from '@/routes/admin';
import { index as bitacora } from '@/routes/admin/bitacora';
import { index as carreras } from '@/routes/admin/carreras';
import { index as usuarios } from '@/routes/admin/usuarios';
import { index as estadisticas } from '@/routes/estadisticas';
import { index as bandeja } from '@/routes/panel/bandeja';
import { index as estudiantes } from '@/routes/panel/estudiantes';
import { index as investigaciones } from '@/routes/investigaciones';
import { index as ods } from '@/routes/ods';
import { index as repositorio } from '@/routes/repositorio';
import type { NavItem } from '@/types';

const consultas: NavItem[] = [
    { title: 'Estadísticas', href: estadisticas(), icon: BarChart3 },
    { title: 'ODS', href: ods(), icon: Target },
    { title: 'Repositorio', href: repositorio(), icon: BookMarked },
    { title: 'Investigaciones', href: investigaciones(), icon: FlaskConical },
];

/** Las consultas son para DIGEU y las unidades académicas; los estudiantes solo ven su propio EPS. */
const ROLES_CON_CONSULTAS = ['digeu', 'unidad_academica'];

function opcionesDePlataforma(rol: string | null): NavItem[] {
    return [
        { title: 'Panel', href: dashboard(), icon: LayoutGrid },
        ...(rol !== null && ROLES_CON_CONSULTAS.includes(rol) ? consultas : []),
        { title: 'Documentación', href: documentacion(), icon: BookOpen },
    ];
}

const estudiantesYEps: NavItem = {
    title: 'Estudiantes y EPS',
    href: estudiantes(),
    icon: Users,
};

/**
 * Opciones de administración según el rol: DIGEU ve todas; una unidad académica, a sus estudiantes y,
 * si es la cuenta principal de la unidad, también a sus supervisores y carreras.
 */
function opcionesDeAdministracion(
    rol: string | null,
    solicitudes: number,
    gestionaSupervisores: boolean,
): NavItem[] {
    const bandejaDeSolicitudes: NavItem = {
        title: 'Bandeja de solicitudes',
        href: bandeja(),
        icon: Inbox,
        badge: solicitudes,
    };

    if (rol === 'digeu') {
        return [
            bandejaDeSolicitudes,
            estudiantesYEps,
            { title: 'Usuarios', href: usuarios(), icon: UserCog },
            { title: 'Manejo de datos', href: datos(), icon: Database },
            { title: 'Bitácora', href: bitacora(), icon: ScrollText },
        ];
    }

    if (rol !== 'unidad_academica') {
        return [];
    }

    return gestionaSupervisores
        ? [
              bandejaDeSolicitudes,
              estudiantesYEps,
              { title: 'Supervisores', href: usuarios(), icon: UserCog },
              { title: 'Carreras', href: carreras(), icon: GraduationCap },
          ]
        : [bandejaDeSolicitudes, estudiantesYEps];
}

export function AppSidebar() {
    const { rol, solicitudesPendientes, gestionaSupervisores } =
        usePage().props;
    const administracion = opcionesDeAdministracion(
        rol,
        solicitudesPendientes,
        gestionaSupervisores,
    );

    return (
        <Sidebar collapsible="icon" variant="inset">
            <SidebarHeader>
                <SidebarMenu>
                    <SidebarMenuItem>
                        <SidebarMenuButton size="lg" asChild>
                            <Link href={dashboard()} prefetch>
                                <AppLogo />
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarHeader>

            <SidebarContent>
                <NavMain items={opcionesDePlataforma(rol)} />
                {administracion.length > 0 && (
                    <NavMain items={administracion} label="Administración" />
                )}
            </SidebarContent>

            <SidebarFooter>
                <NavUser />
            </SidebarFooter>
        </Sidebar>
    );
}
