import { Link, usePage } from '@inertiajs/react';
import { ArrowLeft, ArrowRight, LogOut } from 'lucide-react';
import type { ReactNode } from 'react';
import PasosStepper from '@/components/estudiante/pasos-stepper';
import { home, logout } from '@/routes';
import { carreras } from '@/routes/estudiante';
import type { ExpedienteResumen, PasoEje } from '@/types/estudiante';

type EstudianteLayoutProps = {
    /** Paso actual, de 1 (acceso) a 9 (resumen y orden de impresión). */
    paso: number;
    titulo: string;
    descripcion?: string;
    expediente?: ExpedienteResumen;
    ejes?: PasoEje[];
    children: ReactNode;
};

export default function EstudianteLayout({
    paso,
    titulo,
    descripcion,
    expediente,
    ejes,
    children,
}: EstudianteLayoutProps) {
    const { estudiante } = usePage().props;
    const eje = paso - 2;
    const anterior = ejes?.find((item) => item.eje === eje - 1);
    const siguiente = ejes?.find((item) => item.eje === eje + 1);

    return (
        <div className="text-brand min-h-svh bg-white">
            <header className="landing-grain bg-brand relative isolate overflow-hidden pb-28 text-white">
                <div
                    aria-hidden
                    className="landing-grid pointer-events-none absolute inset-0 -z-10 opacity-60"
                />
                <div className="mx-auto flex max-w-5xl items-center justify-between gap-4 px-6 py-5">
                    <Link href={home()} aria-label="Ir al inicio">
                        <img
                            src="/images/logo.png"
                            alt="USAC · DIGEU"
                            className="h-9 w-auto"
                        />
                    </Link>

                    {estudiante && (
                        <div className="flex items-center gap-3 text-sm">
                            <div className="hidden text-right sm:block">
                                <p className="font-medium">
                                    {estudiante.nombre_completo}
                                </p>
                                <p className="font-mono text-xs text-white/50">
                                    {estudiante.carnet}
                                </p>
                            </div>
                            <Link
                                href={logout()}
                                as="button"
                                className="inline-flex items-center gap-2 rounded-full border border-white/20 px-4 py-2 text-white/80 transition-colors hover:bg-white/10 hover:text-white"
                            >
                                <LogOut className="size-4" />
                                Salir
                            </Link>
                        </div>
                    )}
                </div>

                <div className="mx-auto max-w-5xl px-6">
                    <PasosStepper paso={paso} ejes={ejes} />

                    <div className="mt-10 max-w-3xl">
                        {expediente && (
                            <p className="mb-3 flex flex-wrap items-center gap-x-3 gap-y-2 font-mono text-xs tracking-[0.18em] text-white/50 uppercase">
                                <span>
                                    {expediente.nombre_carrera} ·{' '}
                                    {expediente.nombre_unidad}
                                </span>
                                <span className="rounded-full border border-white/25 px-2.5 py-0.5 text-[10px] tracking-wider text-white/80">
                                    {expediente.estado_etiqueta}
                                </span>
                            </p>
                        )}
                        <h1 className="text-[clamp(1.8rem,4vw,2.8rem)] leading-[1.05] font-semibold tracking-[-0.035em] text-balance">
                            {titulo}
                        </h1>
                        {descripcion && (
                            <p className="mt-4 max-w-2xl text-base leading-relaxed text-white/65">
                                {descripcion}
                            </p>
                        )}
                    </div>
                </div>
            </header>

            <main className="relative z-10 mx-auto -mt-16 max-w-5xl px-6 pb-20">
                {children}

                {ejes && (
                    <div className="border-brand/10 mt-10 flex items-center justify-between gap-3 border-t pt-6">
                        {anterior ? (
                            <Link
                                href={anterior.href}
                                className="border-brand/15 hover:bg-brand/5 inline-flex items-center gap-2 rounded-full border px-5 py-2.5 text-sm font-medium transition-colors"
                            >
                                <ArrowLeft className="size-4" />
                                {anterior.titulo}
                            </Link>
                        ) : (
                            <Link
                                href={carreras()}
                                className="border-brand/15 hover:bg-brand/5 inline-flex items-center gap-2 rounded-full border px-5 py-2.5 text-sm font-medium transition-colors"
                            >
                                <ArrowLeft className="size-4" />
                                Mis carreras
                            </Link>
                        )}

                        {siguiente ? (
                            <Link
                                href={siguiente.href}
                                className="bg-brand inline-flex items-center gap-2 rounded-full px-5 py-2.5 text-sm font-medium text-white transition-transform hover:scale-[1.03]"
                            >
                                {siguiente.titulo}
                                <ArrowRight className="size-4" />
                            </Link>
                        ) : (
                            <Link
                                href={carreras()}
                                className="bg-brand inline-flex items-center gap-2 rounded-full px-5 py-2.5 text-sm font-medium text-white transition-transform hover:scale-[1.03]"
                            >
                                Terminar por ahora
                                <ArrowRight className="size-4" />
                            </Link>
                        )}
                    </div>
                )}
            </main>
        </div>
    );
}
