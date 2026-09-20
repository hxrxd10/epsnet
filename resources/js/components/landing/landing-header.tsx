import { Link } from '@inertiajs/react';
import { ArrowUpRight, Menu, X } from 'lucide-react';
import { useEffect, useState } from 'react';
import { cn } from '@/lib/utils';
import { dashboard, login } from '@/routes';

export const NAV_ITEMS = [
    { label: 'Características', href: '#caracteristicas' },
    { label: 'Definición', href: '#definicion' },
    { label: 'Ejes', href: '#ejes' },
    { label: 'Contacto', href: '#contacto' },
];

export default function LandingHeader({
    isAuthenticated,
}: {
    isAuthenticated: boolean;
}) {
    const [scrolled, setScrolled] = useState(false);
    const [open, setOpen] = useState(false);

    useEffect(() => {
        const onScroll = () => setScrolled(window.scrollY > 24);

        onScroll();
        window.addEventListener('scroll', onScroll, { passive: true });

        return () => window.removeEventListener('scroll', onScroll);
    }, []);

    return (
        <header className="fixed inset-x-0 top-0 z-50 px-4 pt-4">
            <div
                className={cn(
                    'mx-auto max-w-6xl rounded-2xl border transition-all duration-500',
                    scrolled || open
                        ? 'bg-brand/80 border-white/15 shadow-[0_20px_60px_-20px_rgba(0,0,0,0.6)] backdrop-blur-xl'
                        : 'border-transparent bg-transparent',
                )}
            >
                <div className="flex h-16 items-center justify-between gap-6 pr-3 pl-5">
                    <a href="#inicio" className="shrink-0" aria-label="EPSNET">
                        <img
                            src="/images/logo.png"
                            alt="USAC · DIGEU"
                            className="h-9 w-auto"
                        />
                    </a>

                    <nav
                        aria-label="Principal"
                        className="hidden items-center gap-1 md:flex"
                    >
                        {NAV_ITEMS.map((item) => (
                            <a
                                key={item.href}
                                href={item.href}
                                className="rounded-full px-4 py-2 text-sm text-white/70 transition-colors hover:bg-white/10 hover:text-white"
                            >
                                {item.label}
                            </a>
                        ))}
                    </nav>

                    <div className="flex items-center gap-2">
                        <Link
                            href={isAuthenticated ? dashboard() : login()}
                            className="group text-brand inline-flex items-center gap-1.5 rounded-full bg-white px-4 py-2 text-sm font-medium transition-transform hover:scale-[1.03] active:scale-95"
                        >
                            {isAuthenticated ? 'Ir al panel' : 'Ingresar'}
                            <ArrowUpRight className="size-4 transition-transform group-hover:translate-x-0.5 group-hover:-translate-y-0.5" />
                        </Link>
                        <button
                            type="button"
                            onClick={() => setOpen((value) => !value)}
                            aria-expanded={open}
                            aria-label={open ? 'Cerrar menú' : 'Abrir menú'}
                            className="inline-flex size-10 items-center justify-center rounded-full text-white transition-colors hover:bg-white/10 md:hidden"
                        >
                            {open ? (
                                <X className="size-5" />
                            ) : (
                                <Menu className="size-5" />
                            )}
                        </button>
                    </div>
                </div>

                {open && (
                    <nav
                        aria-label="Principal móvil"
                        className="flex flex-col gap-1 border-t border-white/10 p-3 md:hidden"
                    >
                        {NAV_ITEMS.map((item) => (
                            <a
                                key={item.href}
                                href={item.href}
                                onClick={() => setOpen(false)}
                                className="rounded-xl px-4 py-3 text-white/80 transition-colors hover:bg-white/10 hover:text-white"
                            >
                                {item.label}
                            </a>
                        ))}
                    </nav>
                )}
            </div>
        </header>
    );
}
