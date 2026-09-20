import {
    FileCheck2,
    Fingerprint,
    History,
    Layers,
    MapPin,
    ShieldCheck,
    Sheet,
} from 'lucide-react';
import type { LucideIcon } from 'lucide-react';
import type { ReactNode } from 'react';
import { cn } from '@/lib/utils';
import Reveal from './reveal';

type Feature = {
    icon: LucideIcon;
    title: string;
    text: string;
    className: string;
    inverted?: boolean;
    visual?: ReactNode;
};

const ROLE_TIERS = [
    { role: 'DIGEU', detail: 'Administración central' },
    {
        role: 'Unidad Académica',
        detail: 'Coordinación por facultad, escuela o centro',
    },
    { role: 'Estudiante', detail: 'Registro de su EPS' },
];

const SECURITY_CHIPS = [
    'Autenticación en dos pasos',
    'Correo verificado',
    'Contraseñas cifradas',
    'Permisos por rol',
];

const LOG_LINES = [
    { action: 'crear', target: 'eje 1 · bien o servicio', time: '09:41' },
    { action: 'editar', target: 'formulario de ingreso 03', time: '09:47' },
    { action: 'crear', target: 'eje 3 · transferencia', time: '10:02' },
];

const FEATURES: Feature[] = [
    {
        icon: Fingerprint,
        title: 'Cada quien ve lo que le corresponde',
        text: 'Tres niveles de acceso con permisos por rol. Las cuentas las crea DIGEU para las unidades académicas y cada unidad para sus estudiantes: nunca el usuario final.',
        className: 'lg:col-span-4',
        inverted: true,
        visual: (
            <ol className="mt-8 space-y-2.5">
                {ROLE_TIERS.map((tier, index) => (
                    <li
                        key={tier.role}
                        style={{ marginLeft: `${index * 1.5}rem` }}
                        className="flex items-center gap-4 rounded-2xl border border-white/10 bg-white/[0.06] px-5 py-3.5 backdrop-blur"
                    >
                        <span className="font-mono text-xs text-white/40">
                            0{index + 1}
                        </span>
                        <span className="font-medium">{tier.role}</span>
                        <span className="hidden text-sm text-white/50 sm:inline">
                            {tier.detail}
                        </span>
                    </li>
                ))}
            </ol>
        ),
    },
    {
        icon: ShieldCheck,
        title: 'Seguridad de base',
        text: 'Acceso protegido desde el primer día.',
        className: 'lg:col-span-2',
        visual: (
            <ul className="mt-6 flex flex-wrap gap-2">
                {SECURITY_CHIPS.map((chip) => (
                    <li
                        key={chip}
                        className="border-brand/15 text-brand/80 rounded-full border bg-white px-3 py-1.5 text-xs"
                    >
                        {chip}
                    </li>
                ))}
            </ul>
        ),
    },
    {
        icon: FileCheck2,
        title: 'Seis formularios de ingreso',
        text: 'Guardado parcial, validación por campo y documentos de respaldo adjuntos.',
        className: 'lg:col-span-2',
        visual: (
            <div className="mt-6">
                <div className="flex gap-1.5">
                    {Array.from({ length: 6 }, (_, index) => (
                        <span
                            key={index}
                            className={cn(
                                'h-2 flex-1 rounded-full',
                                index < 4 ? 'bg-brand' : 'bg-brand/15',
                            )}
                        />
                    ))}
                </div>
                <p className="text-brand/50 mt-2 font-mono text-[11px] tracking-wider uppercase">
                    4 de 6 · avance guardado
                </p>
            </div>
        ),
    },
    {
        icon: MapPin,
        title: 'Territorio georreferenciado',
        text: 'Ubicación por mapa o manual, con departamento, municipio y comunidad.',
        className: 'lg:col-span-2',
        visual: (
            <div className="relative mt-6 flex h-16 items-center">
                <span className="bg-brand/25 absolute left-2 size-2 rounded-full" />
                <span className="bg-brand/25 absolute left-14 size-2 rounded-full" />
                <span className="bg-brand/25 absolute left-28 size-2 rounded-full" />
                <span className="absolute left-40 flex size-4 items-center justify-center">
                    <span className="bg-brand/20 absolute size-8 animate-ping rounded-full" />
                    <span className="bg-brand size-3 rounded-full" />
                </span>
            </div>
        ),
    },
    {
        icon: Sheet,
        title: 'Consolidación y reportes',
        text: 'DIGEU filtra por unidad, eje y fechas, y exporta a Excel o PDF.',
        className: 'lg:col-span-2',
        visual: (
            <div className="mt-6 flex h-16 items-end gap-1.5">
                {[38, 62, 45, 80, 58, 96, 70].map((height, index) => (
                    <span
                        key={index}
                        style={{ height: `${height}%` }}
                        className={cn(
                            'flex-1 rounded-t-md',
                            index === 5 ? 'bg-brand' : 'bg-brand/20',
                        )}
                    />
                ))}
            </div>
        ),
    },
    {
        icon: Layers,
        title: 'Un formato, muchas unidades',
        text: 'Unificamos el formato y consolidamos los datos a nivel central sin interferir con los procesos internos de cada unidad académica.',
        className: 'lg:col-span-3',
    },
    {
        icon: History,
        title: 'Bitácora que no se puede editar',
        text: 'Toda creación, edición o eliminación queda registrada con usuario, fecha y módulo.',
        className: 'lg:col-span-3',
        visual: (
            <ul className="mt-6 space-y-1.5 font-mono text-xs">
                {LOG_LINES.map((line) => (
                    <li
                        key={line.target}
                        className="bg-brand/[0.04] text-brand/70 flex items-center gap-3 rounded-lg px-3 py-2"
                    >
                        <span className="text-brand/40">{line.time}</span>
                        <span className="text-brand font-semibold">
                            {line.action}
                        </span>
                        <span className="truncate">{line.target}</span>
                    </li>
                ))}
            </ul>
        ),
    },
];

export default function FeaturesSection() {
    return (
        <section
            id="caracteristicas"
            className="text-brand relative scroll-mt-24 overflow-hidden bg-white py-28 lg:py-36"
        >
            <div
                aria-hidden
                className="landing-grid-dark pointer-events-none absolute inset-0"
            />

            <div className="relative mx-auto max-w-7xl px-6">
                <Reveal className="max-w-3xl">
                    <p className="text-brand/50 font-mono text-xs tracking-[0.22em] uppercase">
                        01 — Características
                    </p>
                    <h2 className="mt-5 text-[clamp(2rem,4.6vw,3.8rem)] leading-[1.02] font-semibold tracking-[-0.04em] text-balance">
                        Todo el ejercicio profesional supervisado, en un solo
                        sistema.
                    </h2>
                    <p className="text-brand/60 mt-6 text-lg leading-relaxed">
                        Del ingreso del estudiante a la consolidación nacional:
                        una plataforma pensada para que registrar, dar
                        seguimiento y reportar sea simple para cada nivel.
                    </p>
                </Reveal>

                <div className="mt-16 grid gap-4 lg:grid-cols-6">
                    {FEATURES.map((feature, index) => (
                        <Reveal
                            key={feature.title}
                            delay={(index % 3) * 90}
                            className={feature.className}
                        >
                            <article
                                className={cn(
                                    'group relative flex h-full flex-col overflow-hidden rounded-3xl border p-7 transition-all duration-500 hover:-translate-y-1',
                                    feature.inverted
                                        ? 'border-brand bg-brand text-white hover:shadow-[0_30px_80px_-30px_rgba(15,16,49,0.7)]'
                                        : 'border-brand/10 bg-brand/[0.025] hover:border-brand/30 hover:bg-white hover:shadow-[0_30px_80px_-40px_rgba(15,16,49,0.35)]',
                                )}
                            >
                                <span
                                    className={cn(
                                        'flex size-11 items-center justify-center rounded-2xl',
                                        feature.inverted
                                            ? 'text-brand bg-white'
                                            : 'bg-brand text-white',
                                    )}
                                >
                                    <feature.icon className="size-5" />
                                </span>
                                <h3 className="mt-6 text-xl font-semibold tracking-tight">
                                    {feature.title}
                                </h3>
                                <p
                                    className={cn(
                                        'mt-2 max-w-md leading-relaxed',
                                        feature.inverted
                                            ? 'text-white/60'
                                            : 'text-brand/60',
                                    )}
                                >
                                    {feature.text}
                                </p>
                                <div className="mt-auto">{feature.visual}</div>
                            </article>
                        </Reveal>
                    ))}
                </div>
            </div>
        </section>
    );
}
