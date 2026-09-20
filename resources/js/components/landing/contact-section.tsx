import { Link } from '@inertiajs/react';
import { ArrowUpRight, Building2, Mail, Phone } from 'lucide-react';
import { dashboard, login } from '@/routes';
import GuatemalaMap from './guatemala-map';
import { NAV_ITEMS } from './landing-header';
import Reveal from './reveal';

/**
 * Canales de contacto institucional. Los que estén vacíos no se muestran;
 * completar con los datos oficiales de DIGEU.
 */
const CONTACT = {
    institution: 'Dirección General de Extensión Universitaria',
    university: 'Universidad de San Carlos de Guatemala',
    email: '',
    phone: '',
};

export default function ContactSection({
    isAuthenticated,
}: {
    isAuthenticated: boolean;
}) {
    const channels = [
        {
            icon: Mail,
            label: 'Correo',
            value: CONTACT.email,
            href: `mailto:${CONTACT.email}`,
        },
        {
            icon: Phone,
            label: 'Teléfono',
            value: CONTACT.phone,
            href: `tel:${CONTACT.phone}`,
        },
    ].filter((channel) => channel.value !== '');

    return (
        <section
            id="contacto"
            className="landing-grain bg-brand relative isolate scroll-mt-24 overflow-hidden pt-28 text-white lg:pt-36"
        >
            <div
                aria-hidden
                className="pointer-events-none absolute top-1/2 -right-32 -z-10 hidden w-[620px] -translate-y-1/2 opacity-25 lg:block"
            >
                <GuatemalaMap interactive={false} />
            </div>
            <div
                aria-hidden
                className="landing-grid pointer-events-none absolute inset-0 -z-10 opacity-50"
            />

            <div className="mx-auto max-w-7xl px-6">
                <Reveal className="max-w-3xl">
                    <p className="font-mono text-xs tracking-[0.22em] text-white/50 uppercase">
                        04 — Contacto
                    </p>
                    <h2 className="mt-5 text-[clamp(2.4rem,6vw,5rem)] leading-[0.98] font-semibold tracking-[-0.045em] text-balance">
                        Súmate a la red.{' '}
                        <span className="bg-gradient-to-b from-white to-white/35 bg-clip-text text-transparent">
                            Conecta tu unidad académica.
                        </span>
                    </h2>
                    <p className="mt-8 max-w-xl text-lg leading-relaxed text-white/65">
                        ¿Tu facultad, escuela o centro universitario aún no
                        forma parte de EPSNET? DIGEU crea las cuentas de las
                        unidades académicas, y cada unidad registra a sus
                        estudiantes.
                    </p>
                </Reveal>

                <Reveal delay={150} className="mt-12 flex flex-wrap gap-3">
                    <Link
                        href={isAuthenticated ? dashboard() : login()}
                        className="group text-brand inline-flex items-center gap-2 rounded-full bg-white px-6 py-3.5 text-sm font-medium transition-transform hover:scale-[1.03] active:scale-95"
                    >
                        {isAuthenticated
                            ? 'Ir al panel'
                            : 'Ingresar al sistema'}
                        <ArrowUpRight className="size-4 transition-transform group-hover:translate-x-0.5 group-hover:-translate-y-0.5" />
                    </Link>
                    {channels.map((channel) => (
                        <a
                            key={channel.label}
                            href={channel.href}
                            className="inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/5 px-6 py-3.5 text-sm font-medium backdrop-blur transition-colors hover:bg-white/10"
                        >
                            <channel.icon className="size-4" />
                            {channel.value}
                        </a>
                    ))}
                </Reveal>

                <Reveal delay={250} className="mt-16">
                    <div className="flex items-start gap-4 rounded-3xl border border-white/10 bg-white/[0.04] p-6 backdrop-blur sm:max-w-md">
                        <span className="text-brand flex size-11 shrink-0 items-center justify-center rounded-2xl bg-white">
                            <Building2 className="size-5" />
                        </span>
                        <div>
                            <p className="font-semibold">
                                {CONTACT.institution}
                            </p>
                            <p className="mt-1 text-sm text-white/55">
                                {CONTACT.university}
                            </p>
                        </div>
                    </div>
                </Reveal>
            </div>

            <footer className="mx-auto mt-28 max-w-7xl border-t border-white/10 px-6 py-8">
                <div className="flex flex-col items-start justify-between gap-6 md:flex-row md:items-center">
                    <img
                        src="/images/logo.png"
                        alt="USAC · DIGEU"
                        className="h-9 w-auto"
                    />
                    <nav
                        aria-label="Pie de página"
                        className="flex flex-wrap gap-x-6 gap-y-2 text-sm text-white/55"
                    >
                        {NAV_ITEMS.map((item) => (
                            <a
                                key={item.href}
                                href={item.href}
                                className="transition-colors hover:text-white"
                            >
                                {item.label}
                            </a>
                        ))}
                    </nav>
                </div>
                <p className="mt-8 text-xs text-white/40">
                    © {new Date().getFullYear()} EPSNET · Sistema Unificado de
                    Registro y Seguimiento del Ejercicio Profesional
                    Supervisado.
                </p>
            </footer>
        </section>
    );
}
