import { Link } from '@inertiajs/react';
import { ArrowDown, ArrowUpRight } from 'lucide-react';
import type { PointerEvent } from 'react';
import { dashboard, login } from '@/routes';
import GuatemalaMap from './guatemala-map';
import Reveal from './reveal';

const STATS = [
    { value: '22', label: 'departamentos' },
    { value: '13', label: 'módulos' },
    { value: '6', label: 'ejes de datos' },
    { value: '3', label: 'niveles de acceso' },
];

const TICKER = [
    'Docencia',
    'Investigación',
    'Extensión',
    'Territorio',
    'Comunidad',
    'Impacto',
];

function trackPointer(event: PointerEvent<HTMLElement>) {
    const bounds = event.currentTarget.getBoundingClientRect();

    event.currentTarget.style.setProperty(
        '--mx',
        `${event.clientX - bounds.left}px`,
    );
    event.currentTarget.style.setProperty(
        '--my',
        `${event.clientY - bounds.top}px`,
    );
}

export default function HeroSection({
    isAuthenticated,
}: {
    isAuthenticated: boolean;
}) {
    return (
        <section
            id="inicio"
            onPointerMove={trackPointer}
            className="landing-grain bg-brand relative isolate overflow-hidden text-white"
        >
            <div
                aria-hidden
                className="pointer-events-none absolute inset-0 -z-10"
                style={{
                    background:
                        'radial-gradient(640px circle at var(--mx, 72%) var(--my, 30%), rgb(255 255 255 / 0.11), transparent 60%)',
                }}
            />
            <div
                aria-hidden
                className="landing-grid pointer-events-none absolute inset-0 -z-10"
            />

            <div className="mx-auto grid max-w-7xl items-center gap-12 px-6 pt-36 pb-32 lg:grid-cols-[1.05fr_0.95fr] lg:gap-6 lg:pt-40">
                <div>
                    <Reveal>
                        <span className="inline-flex items-center gap-2.5 rounded-full border border-white/15 bg-white/5 py-1.5 pr-4 pl-2 text-xs text-white/75 backdrop-blur">
                            <span className="relative flex size-5 items-center justify-center">
                                <span className="absolute inline-flex size-full animate-ping rounded-full bg-white/40" />
                                <span className="relative size-2 rounded-full bg-white" />
                            </span>
                            <span className="font-mono tracking-[0.16em] uppercase">
                                Red nacional de EPS · USAC
                            </span>
                        </span>
                    </Reveal>

                    <Reveal delay={100}>
                        <h1 className="mt-8 text-[clamp(2.5rem,5.2vw,4.8rem)] leading-[0.95] font-semibold tracking-[-0.045em] text-balance">
                            Docencia, investigación y extensión,{' '}
                            <span className="bg-gradient-to-b from-white to-white/35 bg-clip-text text-transparent">
                                conectadas en todo el país.
                            </span>
                        </h1>
                    </Reveal>

                    <Reveal delay={200}>
                        <p className="mt-8 max-w-xl text-lg leading-relaxed text-white/65">
                            <strong className="font-semibold text-white">
                                EPSNET
                            </strong>{' '}
                            es la red que une a los estudiantes en Ejercicio
                            Profesional Supervisado de la Universidad de San
                            Carlos, desde Petén hasta el Pacífico, en un solo
                            sistema unificado.
                        </p>
                    </Reveal>

                    <Reveal delay={300} className="mt-10 flex flex-wrap gap-3">
                        <Link
                            href={isAuthenticated ? dashboard() : login()}
                            className="group text-brand inline-flex items-center gap-2 rounded-full bg-white px-6 py-3.5 text-sm font-medium transition-transform hover:scale-[1.03] active:scale-95"
                        >
                            {isAuthenticated
                                ? 'Ir al panel'
                                : 'Ingresar al sistema'}
                            <ArrowUpRight className="size-4 transition-transform group-hover:translate-x-0.5 group-hover:-translate-y-0.5" />
                        </Link>
                        <a
                            href="#definicion"
                            className="group inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/5 px-6 py-3.5 text-sm font-medium text-white backdrop-blur transition-colors hover:bg-white/10"
                        >
                            Conocer la red
                            <ArrowDown className="size-4 transition-transform group-hover:translate-y-0.5" />
                        </a>
                    </Reveal>

                    <Reveal delay={400}>
                        <dl className="mt-14 grid max-w-xl grid-cols-2 gap-y-6 sm:grid-cols-4">
                            {STATS.map((stat) => (
                                <div
                                    key={stat.label}
                                    className="border-l border-white/15 pl-4"
                                >
                                    <dt className="sr-only">{stat.label}</dt>
                                    <dd>
                                        <span className="block text-4xl font-semibold tracking-tight tabular-nums">
                                            {stat.value}
                                        </span>
                                        <span className="mt-1 block text-xs text-white/50">
                                            {stat.label}
                                        </span>
                                    </dd>
                                </div>
                            ))}
                        </dl>
                    </Reveal>
                </div>

                <Reveal
                    delay={250}
                    className="relative mx-auto w-full max-w-[560px]"
                >
                    <div
                        aria-hidden
                        className="absolute inset-[-12%] -z-10 rounded-full bg-white/[0.07] blur-3xl"
                    />
                    <div className="animate-float motion-reduce:animate-none">
                        <GuatemalaMap />
                    </div>
                    <p className="mt-4 text-center font-mono text-[11px] tracking-[0.2em] text-white/40 uppercase">
                        Pasa el cursor por los nodos · 22 cabeceras
                        departamentales
                    </p>
                </Reveal>
            </div>

            <div
                aria-hidden
                className="bg-brand/60 absolute inset-x-0 bottom-0 overflow-hidden border-t border-white/10 py-4 backdrop-blur"
            >
                <div className="animate-marquee flex w-max gap-10 whitespace-nowrap motion-reduce:animate-none">
                    {[...TICKER, ...TICKER, ...TICKER, ...TICKER].map(
                        (word, index) => (
                            <span
                                key={index}
                                className="flex items-center gap-10 text-sm tracking-[0.3em] text-white/45 uppercase"
                            >
                                {word}
                                <span className="size-1.5 rotate-45 bg-white/30" />
                            </span>
                        ),
                    )}
                </div>
            </div>
        </section>
    );
}
