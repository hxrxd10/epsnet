import { GraduationCap, HeartHandshake, Microscope } from 'lucide-react';
import Reveal from './reveal';

const PILLARS = [
    {
        icon: GraduationCap,
        name: 'Docencia',
        text: 'Aprender ejerciendo: la formación profesional se completa en la práctica, con supervisión académica.',
    },
    {
        icon: Microscope,
        name: 'Investigación',
        text: 'Conocimiento generado desde el territorio, documentado y publicado con su evidencia.',
    },
    {
        icon: HeartHandshake,
        name: 'Extensión',
        text: 'La Universidad llega a las comunidades con bienes, servicios y transferencia de conocimiento.',
    },
];

const FLOW = [
    {
        actor: 'DIGEU',
        action: 'Crea las unidades académicas y consolida la información de toda la red.',
    },
    {
        actor: 'Unidad Académica',
        action: 'Registra a sus estudiantes y los asigna a EPS facultativo o EPSUM.',
    },
    {
        actor: 'Estudiante',
        action: 'Completa sus formularios y registra su ejercicio en los seis ejes.',
    },
];

const GLOSSARY = [
    { term: 'EPS', meaning: 'Ejercicio Profesional Supervisado' },
    { term: 'EPSUM', meaning: 'EPS Multiprofesional' },
    { term: 'DIGEU', meaning: 'Dirección General de Extensión Universitaria' },
];

export default function DefinitionSection() {
    return (
        <section
            id="definicion"
            className="landing-grain bg-brand relative isolate scroll-mt-24 overflow-hidden py-28 text-white lg:py-36"
        >
            <div
                aria-hidden
                className="landing-grid pointer-events-none absolute inset-0 -z-10 opacity-60"
            />
            <div
                aria-hidden
                className="pointer-events-none absolute -top-40 -left-40 -z-10 size-[520px] rounded-full bg-white/[0.06] blur-3xl"
            />

            <div className="mx-auto grid max-w-7xl gap-16 px-6 lg:grid-cols-[1fr_1fr] lg:gap-20">
                <div className="lg:sticky lg:top-32 lg:self-start">
                    <Reveal>
                        <p className="font-mono text-xs tracking-[0.22em] text-white/50 uppercase">
                            02 — Definición
                        </p>
                        <h2 className="mt-5 text-[clamp(2rem,4.6vw,3.8rem)] leading-[1.02] font-semibold tracking-[-0.04em] text-balance">
                            Una red de estudiantes que devuelve conocimiento al
                            país.
                        </h2>
                    </Reveal>

                    <Reveal delay={100}>
                        <p className="mt-8 text-lg leading-relaxed text-white/65">
                            El Ejercicio Profesional Supervisado es el espacio
                            en que los estudiantes de la USAC ejercen su
                            profesión en comunidades e instituciones del país,
                            bajo supervisión académica.{' '}
                            <strong className="font-semibold text-white">
                                EPSNET
                            </strong>{' '}
                            es la red que los conecta: un sistema único para
                            registrar, dar seguimiento y medir el impacto de
                            cada ejercicio en todo el territorio nacional.
                        </p>
                    </Reveal>

                    <Reveal delay={200}>
                        <dl className="mt-10 grid gap-px overflow-hidden rounded-2xl border border-white/10 bg-white/10 sm:grid-cols-3">
                            {GLOSSARY.map((item) => (
                                <div key={item.term} className="bg-brand p-5">
                                    <dt className="font-mono text-sm font-semibold tracking-wider">
                                        {item.term}
                                    </dt>
                                    <dd className="mt-1.5 text-sm leading-snug text-white/55">
                                        {item.meaning}
                                    </dd>
                                </div>
                            ))}
                        </dl>
                    </Reveal>
                </div>

                <div className="space-y-14">
                    <ul className="space-y-4">
                        {PILLARS.map((pillar, index) => (
                            <Reveal key={pillar.name} delay={index * 100}>
                                <li className="group relative flex gap-5 overflow-hidden rounded-3xl border border-white/10 bg-white/[0.04] p-7 backdrop-blur transition-colors duration-500 hover:border-white/30 hover:bg-white/[0.08]">
                                    <span className="text-brand flex size-12 shrink-0 items-center justify-center rounded-2xl bg-white transition-transform duration-500 group-hover:scale-110 group-hover:-rotate-6">
                                        <pillar.icon className="size-6" />
                                    </span>
                                    <div>
                                        <h3 className="text-2xl font-semibold tracking-tight">
                                            {pillar.name}
                                        </h3>
                                        <p className="mt-2 leading-relaxed text-white/60">
                                            {pillar.text}
                                        </p>
                                    </div>
                                    <span
                                        aria-hidden
                                        className="absolute top-4 right-6 font-mono text-5xl font-semibold text-white/[0.06]"
                                    >
                                        0{index + 1}
                                    </span>
                                </li>
                            </Reveal>
                        ))}
                    </ul>

                    <Reveal>
                        <h3 className="font-mono text-xs tracking-[0.22em] text-white/50 uppercase">
                            Cómo se conecta la red
                        </h3>
                        <ol className="relative mt-6 space-y-7 border-l border-white/15 pl-8">
                            {FLOW.map((step, index) => (
                                <li key={step.actor} className="relative">
                                    <span className="bg-brand absolute top-1 -left-[2.65rem] flex size-6 items-center justify-center rounded-full border border-white/30 font-mono text-[10px]">
                                        {index + 1}
                                    </span>
                                    <p className="font-semibold">
                                        {step.actor}
                                    </p>
                                    <p className="mt-1 leading-relaxed text-white/55">
                                        {step.action}
                                    </p>
                                </li>
                            ))}
                        </ol>
                    </Reveal>
                </div>
            </div>
        </section>
    );
}
