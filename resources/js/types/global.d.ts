import type { Auth } from '@/types/auth';
import type { EstudianteSesion } from '@/types/estudiante';

declare module 'react' {
    interface InputHTMLAttributes<T> {
        passwordrules?: string;
    }
}

declare module '@inertiajs/core' {
    export interface InertiaConfig {
        sharedPageProps: {
            name: string;
            auth: Auth;
            rol: string | null;
            estudiante: EstudianteSesion | null;
            solicitudesPendientes: number;
            /** Para una unidad académica: lo que ve en las consultas. */
            alcanceConsulta: string | null;
            sidebarOpen: boolean;
            [key: string]: unknown;
        };
    }
}
