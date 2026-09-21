import { Head, Link } from '@inertiajs/react';
import { ArrowRight, Database } from 'lucide-react';
import { dashboard } from '@/routes';
import { datos } from '@/routes/admin';

type CatalogoResumen = {
    clave: string;
    etiqueta: string;
    descripcion: string;
    total: number;
    activos: number;
    href: string;
};

export default function Datos({ catalogos }: { catalogos: CatalogoResumen[] }) {
    return (
        <>
            <Head title="Manejo de datos" />
            <div className="flex flex-1 flex-col gap-6 p-4">
                <div>
                    <h1 className="text-2xl font-semibold tracking-tight">
                        Manejo de datos
                    </h1>
                    <p className="text-muted-foreground mt-1 max-w-2xl text-sm">
                        Administra los catálogos que los estudiantes usan al
                        llenar su EPS. Los cambios se reflejan de inmediato en
                        sus formularios.
                    </p>
                </div>

                <ul className="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                    {catalogos.map((catalogo) => (
                        <li key={catalogo.clave}>
                            <Link
                                href={catalogo.href}
                                className="border-sidebar-border/70 hover:bg-accent group flex h-full flex-col rounded-xl border p-6 transition-colors"
                            >
                                <Database className="text-muted-foreground size-6" />
                                <h2 className="mt-4 font-semibold">
                                    {catalogo.etiqueta}
                                </h2>
                                <p className="text-muted-foreground mt-1 text-sm">
                                    {catalogo.descripcion}
                                </p>
                                <div className="mt-auto flex items-center justify-between pt-6 text-sm">
                                    <span className="text-muted-foreground">
                                        {catalogo.total}{' '}
                                        {catalogo.total === 1
                                            ? 'elemento'
                                            : 'elementos'}{' '}
                                        · {catalogo.activos} activos
                                    </span>
                                    <ArrowRight className="size-4 transition-transform group-hover:translate-x-0.5" />
                                </div>
                            </Link>
                        </li>
                    ))}
                </ul>
            </div>
        </>
    );
}

Datos.layout = {
    breadcrumbs: [
        { title: 'Panel', href: dashboard() },
        { title: 'Manejo de datos', href: datos() },
    ],
};
