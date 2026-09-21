/**
 * Fechas de la aplicación: se muestran siempre como dd-mm-aaaa (y dd-mm-aaaa HH:mm con hora); hacia el servidor
 * viajan como aaaa-mm-dd.
 */

const SOLO_FECHA = /^(\d{4})-(\d{2})-(\d{2})$/;
const FECHA_HORA_SIN_ZONA =
    /^(\d{4})-(\d{2})-(\d{2})[ T](\d{2}):(\d{2})(?::\d{2}(?:\.\d+)?)?$/;

const dos = (numero: number): string => String(numero).padStart(2, '0');

/** "2026-03-10", "2026-03-10 09:15:00" o un ISO con zona horaria → "10-03-2026". */
export function formatearFecha(valor: string | null | undefined): string {
    if (!valor) {
        return '';
    }

    const fecha = SOLO_FECHA.exec(valor) ?? FECHA_HORA_SIN_ZONA.exec(valor);

    if (fecha) {
        return `${fecha[3]}-${fecha[2]}-${fecha[1]}`;
    }

    const instante = new Date(valor);

    return Number.isNaN(instante.getTime())
        ? valor
        : `${dos(instante.getDate())}-${dos(instante.getMonth() + 1)}-${instante.getFullYear()}`;
}

/** Igual que `formatearFecha`, con la hora: "10-03-2026 09:15". */
export function formatearFechaHora(valor: string | null | undefined): string {
    if (!valor) {
        return '';
    }

    const local = FECHA_HORA_SIN_ZONA.exec(valor);

    if (local) {
        return `${local[3]}-${local[2]}-${local[1]} ${local[4]}:${local[5]}`;
    }

    const instante = new Date(valor);

    return Number.isNaN(instante.getTime())
        ? formatearFecha(valor)
        : `${formatearFecha(valor)} ${dos(instante.getHours())}:${dos(instante.getMinutes())}`;
}

/** Cambia cualquier fecha aaaa-mm-dd que aparezca dentro de un texto a dd-mm-aaaa. */
export function formatearFechasEnTexto(texto: string): string {
    return texto.replace(/\b(\d{4})-(\d{2})-(\d{2})\b/g, '$3-$2-$1');
}

/** "aaaa-mm-dd" → "dd-mm-aaaa" (vacío si no es una fecha completa). */
export function isoATexto(iso: string): string {
    const fecha = SOLO_FECHA.exec(iso);

    return fecha ? `${fecha[3]}-${fecha[2]}-${fecha[1]}` : '';
}

/** "dd-mm-aaaa" → "aaaa-mm-dd", o null si el texto no es una fecha real. */
export function textoAIso(texto: string): string | null {
    const partes = /^(\d{2})-(\d{2})-(\d{4})$/.exec(texto);

    if (!partes) {
        return null;
    }

    const [, dia, mes, anio] = partes;
    const fecha = new Date(Number(anio), Number(mes) - 1, Number(dia));

    return fecha.getFullYear() === Number(anio) &&
        fecha.getMonth() === Number(mes) - 1 &&
        fecha.getDate() === Number(dia)
        ? `${anio}-${mes}-${dia}`
        : null;
}

/** Da forma de dd-mm-aaaa a lo que se va escribiendo: solo dígitos y los guiones en su lugar. */
export function enmascararFecha(entrada: string): string {
    const digitos = entrada.replace(/\D/g, '').slice(0, 8);

    return [digitos.slice(0, 2), digitos.slice(2, 4), digitos.slice(4, 8)]
        .filter((parte) => parte !== '')
        .join('-');
}
