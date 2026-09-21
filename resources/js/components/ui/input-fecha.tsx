import { useState } from 'react';
import type { ComponentProps } from 'react';
import { Input } from '@/components/ui/input';
import { enmascararFecha, isoATexto, textoAIso } from '@/lib/fechas';

type Props = Omit<
    ComponentProps<typeof Input>,
    'value' | 'onChange' | 'type' | 'placeholder'
> & {
    /** Fecha en formato aaaa-mm-dd (o vacío). */
    value: string;
    /** Recibe aaaa-mm-dd cuando la fecha está completa y es válida; si no, cadena vacía. */
    onChange: (iso: string) => void;
};

/** Campo de fecha que se escribe y se muestra como dd-mm-aaaa. */
export function InputFecha({ value, onChange, ...props }: Props) {
    const [texto, setTexto] = useState(isoATexto(value));
    const [valorAnterior, setValorAnterior] = useState(value);

    // Si el valor cambia desde fuera (p. ej. al editar otro registro), el texto lo sigue.
    if (value !== valorAnterior) {
        setValorAnterior(value);

        if (textoAIso(texto) !== value) {
            setTexto(isoATexto(value));
        }
    }

    function cambiar(entrada: string) {
        const enmascarado = enmascararFecha(entrada);
        const iso = textoAIso(enmascarado) ?? '';

        setTexto(enmascarado);
        setValorAnterior(iso);

        if (iso !== value) {
            onChange(iso);
        }
    }

    return (
        <Input
            {...props}
            type="text"
            inputMode="numeric"
            autoComplete="off"
            placeholder="dd-mm-aaaa"
            maxLength={10}
            value={texto}
            onChange={(evento) => cambiar(evento.target.value)}
            aria-invalid={
                props['aria-invalid'] ||
                (texto.length === 10 && textoAIso(texto) === null)
            }
        />
    );
}
