import { Check, Search } from 'lucide-react';
import { useState } from 'react';
import InputError from '@/components/input-error';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { cn } from '@/lib/utils';
import type { Opcion } from '@/types/estudiante';

type Props = {
    id: string;
    label: string;
    opciones: Opcion[];
    valor: string;
    onChange: (valor: string) => void;
    requerido?: boolean;
    ayuda?: string;
    error?: string;
    vacio?: string;
};

const MAXIMO_VISIBLE = 40;

const normalizar = (texto: string) =>
    texto
        .normalize('NFD')
        .replace(/[̀-ͯ]/g, '')
        .toLowerCase();

/** Lista de opciones con búsqueda: pensada para catálogos largos (instituciones, por ejemplo). */
export default function SelectorBuscable({
    id,
    label,
    opciones,
    valor,
    onChange,
    requerido,
    ayuda,
    error,
    vacio = 'No hay coincidencias.',
}: Props) {
    const [texto, setTexto] = useState('');

    const elegida = opciones.find((opcion) => opcion.value === valor);
    const buscado = normalizar(texto.trim());
    const coincidencias = opciones.filter(
        (opcion) =>
            buscado === '' || normalizar(opcion.label).includes(buscado),
    );
    const visibles = coincidencias.slice(0, MAXIMO_VISIBLE);

    return (
        <div className="grid gap-2">
            <Label htmlFor={id}>
                {label}
                {requerido && (
                    <span aria-hidden className="text-brand/50">
                        {' '}
                        *
                    </span>
                )}
            </Label>

            {elegida && (
                <p className="bg-brand/5 flex items-center gap-2 rounded-xl px-3 py-2 text-sm">
                    <Check className="size-4 shrink-0" />
                    <span className="font-medium">{elegida.label}</span>
                </p>
            )}

            <div className="relative">
                <Search className="text-brand/40 absolute top-2.5 left-3 size-4" />
                <Input
                    id={id}
                    value={texto}
                    onChange={(evento) => setTexto(evento.target.value)}
                    placeholder="Escribe para buscar…"
                    className="pl-9"
                    aria-invalid={!!error}
                    autoComplete="off"
                />
            </div>

            <ul
                role="listbox"
                aria-label={label}
                className="border-brand/15 max-h-56 overflow-y-auto rounded-xl border"
            >
                {visibles.length === 0 ? (
                    <li className="text-brand/55 px-3 py-3 text-sm">{vacio}</li>
                ) : (
                    visibles.map((opcion) => (
                        <li
                            key={opcion.value}
                            role="option"
                            aria-selected={opcion.value === valor}
                        >
                            <button
                                type="button"
                                onClick={() => onChange(opcion.value)}
                                className={cn(
                                    'hover:bg-brand/5 w-full px-3 py-2 text-left text-sm transition-colors',
                                    opcion.value === valor &&
                                        'bg-brand/10 font-medium',
                                )}
                            >
                                {opcion.label}
                            </button>
                        </li>
                    ))
                )}
            </ul>
            {coincidencias.length > MAXIMO_VISIBLE && (
                <p className="text-brand/50 text-xs">
                    Mostrando {MAXIMO_VISIBLE} de {coincidencias.length}:
                    escribe más para acotar la búsqueda.
                </p>
            )}

            {ayuda && !error && (
                <p className="text-brand/50 text-xs">{ayuda}</p>
            )}
            <InputError message={error} />
        </div>
    );
}
