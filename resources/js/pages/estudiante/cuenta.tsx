import { Form, Head } from '@inertiajs/react';
import { BadgeCheck } from 'lucide-react';
import { store } from '@/actions/App/Http/Controllers/Estudiante/CuentaController';
import InputError from '@/components/input-error';
import PasswordInput from '@/components/password-input';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import EstudianteLayout from '@/layouts/estudiante-layout';
import type { EstudianteSesion } from '@/types/estudiante';

export default function Cuenta({ perfil }: { perfil: EstudianteSesion }) {
    return (
        <EstudianteLayout
            paso={1}
            titulo="Crea tu acceso"
            descripcion="Tu identidad quedó confirmada. Elige el correo y la contraseña con los que ingresarás a EPSNET a partir de ahora."
        >
            <Head title="Crear cuenta de estudiante" />

            <div className="border-brand/10 mx-auto max-w-lg rounded-3xl border bg-white p-7 shadow-[0_30px_80px_-40px_rgba(15,16,49,0.35)] sm:p-9">
                <div className="bg-brand/[0.04] mb-7 flex items-center gap-3 rounded-2xl p-4">
                    <BadgeCheck className="text-brand size-6 shrink-0" />
                    <div className="min-w-0">
                        <p className="truncate font-medium">
                            {perfil.nombre_completo}
                        </p>
                        <p className="text-brand/55 font-mono text-xs">
                            Registro académico {perfil.carnet}
                        </p>
                    </div>
                </div>

                <Form
                    {...store.form()}
                    resetOnSuccess={['password', 'password_confirmation']}
                    className="flex flex-col gap-6"
                >
                    {({ processing, errors }) => (
                        <>
                            <div className="grid gap-2">
                                <Label htmlFor="email">
                                    Correo electrónico
                                </Label>
                                <Input
                                    id="email"
                                    type="email"
                                    name="email"
                                    required
                                    autoFocus
                                    autoComplete="email"
                                    placeholder="correo@ejemplo.com"
                                    aria-invalid={!!errors.email}
                                />
                                <InputError message={errors.email} />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="password">Contraseña</Label>
                                <PasswordInput
                                    id="password"
                                    name="password"
                                    required
                                    autoComplete="new-password"
                                    placeholder="Contraseña"
                                />
                                <InputError message={errors.password} />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="password_confirmation">
                                    Confirma tu contraseña
                                </Label>
                                <PasswordInput
                                    id="password_confirmation"
                                    name="password_confirmation"
                                    required
                                    autoComplete="new-password"
                                    placeholder="Repite la contraseña"
                                />
                                <InputError
                                    message={errors.password_confirmation}
                                />
                            </div>

                            <button
                                type="submit"
                                disabled={processing}
                                className="bg-brand inline-flex items-center justify-center gap-2 rounded-full px-6 py-3.5 text-sm font-medium text-white transition-transform hover:scale-[1.02] active:scale-95 disabled:opacity-60"
                            >
                                {processing && <Spinner />}
                                Crear cuenta y continuar
                            </button>
                        </>
                    )}
                </Form>
            </div>
        </EstudianteLayout>
    );
}
