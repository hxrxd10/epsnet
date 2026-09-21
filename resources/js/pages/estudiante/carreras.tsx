import { Form, Head, Link } from '@inertiajs/react';
import { ArrowRight, GraduationCap } from 'lucide-react';
import { store } from '@/actions/App/Http/Controllers/Estudiante/ExpedienteController';
import InputError from '@/components/input-error';
import { Badge } from '@/components/ui/badge';
import { Spinner } from '@/components/ui/spinner';
import EstudianteLayout from '@/layouts/estudiante-layout';
import { show } from '@/routes/estudiante/expedientes';
import type { CarreraEstudiante } from '@/types/estudiante';

type Props = {
    carreras: CarreraEstudiante[];
    errors: { carrera?: string };
};

export default function Carreras({ carreras, errors }: Props) {
    return (
        <EstudianteLayout
            paso={2}
            titulo="¿En qué carrera realizas tu EPS?"
            descripcion="Puedes hacer un EPS por cada carrera. Al elegirla, tomamos su unidad académica automáticamente y guardamos tu avance por separado."
        >
            <Head title="Tus carreras" />

            {errors.carrera && (
                <div className="border-destructive/30 mb-4 rounded-2xl border bg-white p-4">
                    <InputError message={errors.carrera} />
                </div>
            )}

            {carreras.length === 0 ? (
                <div className="border-brand/10 rounded-3xl border bg-white p-10 text-center shadow-[0_30px_80px_-40px_rgba(15,16,49,0.35)]">
                    <GraduationCap className="text-brand/40 mx-auto size-10" />
                    <p className="mt-4 font-medium">
                        No encontramos carreras asociadas a tu registro
                        académico.
                    </p>
                    <p className="text-brand/60 mt-1 text-sm">
                        Comunícate con tu unidad académica para verificar tu
                        información.
                    </p>
                </div>
            ) : (
                <ul className="grid gap-4 md:grid-cols-2">
                    {carreras.map((carrera) => (
                        <li
                            key={carrera.clave}
                            className="border-brand/10 flex flex-col rounded-3xl border bg-white p-6 shadow-[0_30px_80px_-40px_rgba(15,16,49,0.35)]"
                        >
                            <div className="flex flex-wrap gap-2">
                                {carrera.grado && (
                                    <Badge variant="secondary">
                                        {carrera.grado}
                                    </Badge>
                                )}
                                {carrera.graduado ? (
                                    <Badge variant="outline">Graduado</Badge>
                                ) : (
                                    carrera.ciclo_activo && (
                                        <Badge variant="outline">
                                            Ciclo {carrera.ciclo_activo}
                                        </Badge>
                                    )
                                )}
                            </div>

                            <h2 className="mt-4 text-lg leading-snug font-semibold tracking-tight">
                                {carrera.nombre_carrera}
                            </h2>
                            <p className="text-brand/60 mt-2 text-sm">
                                {carrera.nombre_unidad}
                                {carrera.nombre_extension &&
                                    ` · ${carrera.nombre_extension}`}
                            </p>

                            <div className="mt-auto pt-6">
                                {carrera.expediente ? (
                                    <>
                                        <p className="text-brand/50 mb-3 font-mono text-xs tracking-wider uppercase">
                                            {carrera.expediente.estado ===
                                            'activo'
                                                ? `Paso ${carrera.expediente.eje_actual} de 6`
                                                : carrera.expediente
                                                      .estado_etiqueta}{' '}
                                            · {carrera.expediente.registros}{' '}
                                            {carrera.expediente.registros === 1
                                                ? 'registro'
                                                : 'registros'}
                                        </p>
                                        <Link
                                            href={show(carrera.expediente.id)}
                                            className="bg-brand inline-flex items-center gap-2 rounded-full px-5 py-2.5 text-sm font-medium text-white transition-transform hover:scale-[1.03]"
                                        >
                                            {carrera.expediente.estado ===
                                            'activo'
                                                ? 'Continuar mi EPS'
                                                : 'Ver mi EPS'}
                                            <ArrowRight className="size-4" />
                                        </Link>
                                    </>
                                ) : (
                                    <Form {...store.form()}>
                                        {({ processing }) => (
                                            <>
                                                <input
                                                    type="hidden"
                                                    name="carrera"
                                                    value={carrera.clave}
                                                />
                                                <button
                                                    type="submit"
                                                    disabled={processing}
                                                    className="bg-brand inline-flex items-center gap-2 rounded-full px-5 py-2.5 text-sm font-medium text-white transition-transform hover:scale-[1.03] disabled:opacity-60"
                                                >
                                                    {processing ? (
                                                        <Spinner />
                                                    ) : (
                                                        <ArrowRight className="size-4" />
                                                    )}
                                                    Comenzar EPS en esta carrera
                                                </button>
                                            </>
                                        )}
                                    </Form>
                                )}
                            </div>
                        </li>
                    ))}
                </ul>
            )}
        </EstudianteLayout>
    );
}
