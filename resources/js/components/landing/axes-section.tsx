import {
    BookOpenText,
    Handshake,
    MapPinned,
    Package,
    Presentation,
    TrendingUp,
} from 'lucide-react';
import type { LucideIcon } from 'lucide-react';
import { useState } from 'react';
import { cn } from '@/lib/utils';
import Reveal from './reveal';

type Axis = {
    icon: LucideIcon;
    name: string;
    summary: string;
    records: string[];
};

const AXES: Axis[] = [
    {
        icon: Package,
        name: 'Bienes y Servicios Generados',
        summary:
            'Todo lo que cada EPS produce para las comunidades durante su ejercicio.',
        records: [
            'Tipo de bien o servicio',
            'Descripción',
            'Beneficiarios',
            'Fecha',
            'Evidencia adjunta',
        ],
    },
    {
        icon: BookOpenText,
        name: 'Publicaciones de Investigación',
        summary:
            'Investigaciones y publicaciones vinculadas al ejercicio profesional.',
        records: [
            'Título',
            'Tipo de publicación',
            'Autores',
            'Medio de publicación',
            'Documento o enlace',
        ],
    },
    {
        icon: Presentation,
        name: 'Transferencia de Conocimiento',
        summary:
            'Capacitaciones, talleres y asesorías que llevan saber útil a las comunidades.',
        records: [
            'Actividad realizada',
            'Comunidad o grupo beneficiado',
            'Fecha',
            'Participación por estudiante',
        ],
    },
    {
        icon: MapPinned,
        name: 'Territorio y Geolocalización',
        summary:
            'Dónde ocurre cada EPS: la distribución territorial de la red en el mapa del país.',
        records: [
            'Ubicación en mapa o manual',
            'Departamento',
            'Municipio',
            'Comunidad',
        ],
    },
    {
        icon: Handshake,
        name: 'Actores y Participantes',
        summary:
            'Las instituciones y personas con quienes se construye cada ejercicio.',
        records: [
            'Institución receptora',
            'Contraparte',
            'Comunidad beneficiada',
            'Instituciones aliadas y su aporte',
        ],
    },
    {
        icon: TrendingUp,
        name: 'Seguimiento e Impacto',
        summary:
            'Indicadores durante el EPS y evaluación de impacto al finalizarlo.',
        records: [
            'Avance y cumplimiento',
            'Observaciones',
            'Avances parciales',
            'Evaluación de impacto',
        ],
    },
];

export default function AxesSection() {
    const [selected, setSelected] = useState(0);
    const axis = AXES[selected];

    return (
        <section
            id="ejes"
            className="text-brand relative scroll-mt-24 overflow-hidden bg-white py-28 lg:py-36"
        >
            <div
                aria-hidden
                className="landing-grid-dark pointer-events-none absolute inset-0"
            />

            <div className="relative mx-auto max-w-7xl px-6">
                <Reveal className="max-w-3xl">
                    <p className="text-brand/50 font-mono text-xs tracking-[0.22em] uppercase">
                        03 — Ejes
                    </p>
                    <h2 className="mt-5 text-[clamp(2rem,4.6vw,3.8rem)] leading-[1.02] font-semibold tracking-[-0.04em] text-balance">
                        Seis ejes de datos, una sola mirada del impacto.
                    </h2>
                    <p className="text-brand/60 mt-6 text-lg leading-relaxed">
                        Cada EPS se documenta bajo la misma estructura, de modo
                        que DIGEU pueda consolidar y comparar la información de
                        todas las unidades académicas.
                    </p>
                </Reveal>

                <Reveal delay={100} className="mt-16">
                    <div className="grid gap-6 lg:grid-cols-[minmax(0,0.9fr)_minmax(0,1.1fr)]">
                        <div
                            role="tablist"
                            aria-label="Ejes de datos"
                            className="flex flex-col gap-2"
                        >
                            {AXES.map((item, index) => {
                                const isSelected = index === selected;

                                return (
                                    <button
                                        key={item.name}
                                        type="button"
                                        role="tab"
                                        aria-selected={isSelected}
                                        onClick={() => setSelected(index)}
                                        onMouseEnter={() => setSelected(index)}
                                        className={cn(
                                            'group focus-visible:ring-brand flex items-center gap-5 rounded-2xl border px-5 py-4 text-left transition-all duration-300 focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:outline-none',
                                            isSelected
                                                ? 'border-brand bg-brand text-white shadow-[0_24px_60px_-30px_rgba(15,16,49,0.8)] lg:translate-x-2'
                                                : 'border-brand/10 bg-brand/[0.025] hover:border-brand/30',
                                        )}
                                    >
                                        <span
                                            className={cn(
                                                'font-mono text-sm tabular-nums',
                                                isSelected
                                                    ? 'text-white/50'
                                                    : 'text-brand/40',
                                            )}
                                        >
                                            0{index + 1}
                                        </span>
                                        <span className="flex-1 text-lg font-medium tracking-tight">
                                            {item.name}
                                        </span>
                                        <item.icon
                                            className={cn(
                                                'size-5 transition-transform duration-300',
                                                isSelected
                                                    ? 'scale-110'
                                                    : 'text-brand/40',
                                            )}
                                        />
                                    </button>
                                );
                            })}
                        </div>

                        <div
                            role="tabpanel"
                            key={axis.name}
                            className="animate-in border-brand bg-brand fade-in slide-in-from-bottom-3 relative flex min-h-[26rem] flex-col overflow-hidden rounded-3xl border p-8 text-white duration-500 lg:p-10"
                        >
                            <div
                                aria-hidden
                                className="landing-grid pointer-events-none absolute inset-0 opacity-70"
                            />
                            <span
                                aria-hidden
                                className="pointer-events-none absolute -right-4 -bottom-10 font-mono text-[12rem] leading-none font-semibold text-white/[0.05] select-none"
                            >
                                0{selected + 1}
                            </span>

                            <div className="relative">
                                <span className="text-brand flex size-14 items-center justify-center rounded-2xl bg-white">
                                    <axis.icon className="size-7" />
                                </span>
                                <p className="mt-8 font-mono text-xs tracking-[0.22em] text-white/50 uppercase">
                                    Eje {selected + 1} de 6
                                </p>
                                <h3 className="mt-3 text-3xl font-semibold tracking-[-0.03em] text-balance">
                                    {axis.name}
                                </h3>
                                <p className="mt-4 max-w-md leading-relaxed text-white/65">
                                    {axis.summary}
                                </p>
                            </div>

                            <div className="relative mt-auto pt-10">
                                <p className="font-mono text-xs tracking-[0.22em] text-white/50 uppercase">
                                    Qué se registra
                                </p>
                                <ul className="mt-4 flex flex-wrap gap-2">
                                    {axis.records.map((record) => (
                                        <li
                                            key={record}
                                            className="rounded-full border border-white/15 bg-white/[0.07] px-3.5 py-1.5 text-sm text-white/85 backdrop-blur"
                                        >
                                            {record}
                                        </li>
                                    ))}
                                </ul>
                            </div>
                        </div>
                    </div>
                </Reveal>
            </div>
        </section>
    );
}
