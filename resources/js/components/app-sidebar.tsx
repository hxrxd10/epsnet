import { Link, usePage } from '@inertiajs/react';
import {
    BarChart3,
    BookMarked,
    BookOpen,
    Database,
    ScrollText,
    LayoutGrid,
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
import { index as usuarios } from '@/routes/admin/usuarios';
import { index as estadisticas } from '@/routes/estadisticas';
import { index as estudiantes } from '@/routes/panel/estudiantes';
import { index as repositorio } from '@/routes/repositorio';
import type { NavItem } from '@/types';

const plataforma: NavItem[] = [
    { title: 'Panel', href: dashboard(), icon: LayoutGrid },
    { title: 'Estadísticas', href: estadisticas(), icon: BarChart3 },
    { title: 'Repositorio', href: repositorio(), icon: BookMarked },
    { title: 'Documentación', href: documentacion(), icon: BookOpen },
];

const estudiantesYEps: NavItem = {
    title: 'Estudiantes y EPS',
    href: estudiantes(),
    icon: Users,
};

/** Opciones de administración según el rol: DIGEU ve todas; una unidad académica, a sus estudiantes. */
function opcionesDeAdministracion(rol: string | null): NavItem[] {
    if (rol === 'digeu') {
        return [
            estudiantesYEps,
            { title: 'Usuarios', href: usuarios(), icon: UserCog },
            { title: 'Manejo de datos', href: datos(), icon: Database },
            { title: 'Bitácora', href: bitacora(), icon: ScrollText },
        ];
    }

    return rol === 'unidad_academica' ? [estudiantesYEps] : [];
}

export function AppSidebar() {
    const { rol } = usePage().props;
    const administracion = opcionesDeAdministracion(rol);

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
                <NavMain items={plataforma} />
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
