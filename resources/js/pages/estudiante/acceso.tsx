import { Form, Head, Link } from '@inertiajs/react';
import { AlertCircle, ShieldCheck } from 'lucide-react';
import { store } from '@/actions/App/Http/Controllers/Estudiante/AccesoController';
import InputError from '@/components/input-error';
import { Alert, AlertDescription } from '@/components/ui/alert';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import EstudianteLayout from '@/layouts/estudiante-layout';
import { login } from '@/routes';

export default function Acceso({ esInvitado }: { esInvitado: boolean }) {
    return (
        <EstudianteLayout
            paso={1}
            titulo={
                esInvitado
                    ? 'Verifica tu identidad como estudiante'
                    : 'Activa tu cuenta de estudiante'
            }
            descripcion={
                esInvitado
                    ? 'Confirmamos tu identidad una sola vez con tu registro académico y tu DPI, en Registro y Estadística de la USAC. Tu cuenta pasará a ser de estudiante y podrás llenar tu EPS.'
                    : 'Confirmamos tu identidad una sola vez con tu registro académico y tu DPI, en Registro y Estadística de la USAC. Después crearás tu acceso y podrás llenar tu EPS y retomarlo cuando quieras.'
            }
        >
            <Head title="Activar cuenta de estudiante" />

            <div className="border-brand/10 mx-auto max-w-lg rounded-3xl border bg-white p-7 shadow-[0_30px_80px_-40px_rgba(15,16,49,0.35)] sm:p-9">
                <Form
                    {...store.form()}
                    resetOnError={['dpi']}
                    className="flex flex-col gap-6"
                >
                    {({ processing, errors }) => (
                        <>
                            {errors.acceso && (
                                <Alert variant="destructive">
                                    <AlertCircle />
                                    <AlertDescription>
                                        {errors.acceso}
                                    </AlertDescription>
                                </Alert>
                            )}

                            <div className="grid gap-2">
                                <Label htmlFor="registro_academico">
                                    Registro académico
                                </Label>
                                <Input
                                    id="registro_academico"
                                    name="registro_academico"
                                    inputMode="numeric"
                                    autoComplete="off"
                                    autoFocus
                                    required
                                    placeholder="Ej. 201219511"
                                    aria-invalid={!!errors.registro_academico}
                                />
                                <InputError
                                    message={errors.registro_academico}
                                />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="dpi">DPI</Label>
                                <Input
                                    id="dpi"
                                    name="dpi"
                                    inputMode="numeric"
                                    autoComplete="off"
                                    required
                                    maxLength={17}
                                    placeholder="13 dígitos"
                                    aria-invalid={!!errors.dpi}
                                />
                                <InputError message={errors.dpi} />
                            </div>

                            <button
                                type="submit"
                                disabled={processing}
                                className="bg-brand inline-flex items-center justify-center gap-2 rounded-full px-6 py-3.5 text-sm font-medium text-white transition-transform hover:scale-[1.02] active:scale-95 disabled:opacity-60"
                            >
                                {processing && <Spinner />}
                                Verificar mi identidad
                            </button>

                            <p className="text-brand/55 flex items-start gap-2 text-xs leading-relaxed">
                                <ShieldCheck className="mt-0.5 size-4 shrink-0" />
                                Usamos tu DPI únicamente para confirmar tu
                                identidad con la USAC; no lo almacenamos.
                            </p>
                        </>
                    )}
                </Form>

                {!esInvitado && (
                    <p className="border-brand/10 text-brand/60 mt-6 border-t pt-6 text-center text-sm">
                        ¿Ya activaste tu cuenta?{' '}
                        <Link
                            href={login()}
                            className="text-brand font-medium underline underline-offset-4"
                        >
                            Inicia sesión
                        </Link>
                    </p>
                )}
            </div>
        </EstudianteLayout>
    );
}
