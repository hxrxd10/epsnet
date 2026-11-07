import type { ImgHTMLAttributes } from 'react';
import { cn } from '@/lib/utils';

type Props = Omit<ImgHTMLAttributes<HTMLImageElement>, 'src'> & {
    /** Siempre en blanco, para fondos oscuros (p. ej. el azul de la marca). */
    claro?: boolean;
};

/**
 * Emblema de SIRSE USAC: en azul sobre fondos claros y en blanco en el modo oscuro, o siempre en
 * blanco con `claro`.
 */
export default function AppLogoIcon({
    claro = false,
    className,
    alt = '',
    ...props
}: Props) {
    if (claro) {
        return (
            <img
                {...props}
                src="/images/sirse-emblema-blanco.png"
                alt={alt}
                className={cn('object-contain', className)}
            />
        );
    }

    return (
        <>
            <img
                {...props}
                src="/images/sirse-emblema.png"
                alt={alt}
                className={cn('object-contain dark:hidden', className)}
            />
            <img
                {...props}
                src="/images/sirse-emblema-blanco.png"
                alt={alt}
                className={cn('hidden object-contain dark:block', className)}
            />
        </>
    );
}
