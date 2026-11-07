import { usePage } from '@inertiajs/react';
import AppLogoIcon from '@/components/app-logo-icon';

/** Emblema de SIRSE USAC y logo de USAC · DIGEU; con el menú contraído solo queda el emblema. */
export default function AppLogo() {
    const { name } = usePage().props;

    return (
        <>
            <AppLogoIcon claro alt={name} className="size-8 shrink-0" />
            <span
                aria-hidden
                className="h-7 w-px shrink-0 bg-white/20 group-data-[collapsible=icon]:hidden"
            />
            <img
                src="/images/logo.png"
                alt="USAC · DIGEU"
                className="h-8 w-auto min-w-0 object-contain object-left group-data-[collapsible=icon]:hidden"
            />
        </>
    );
}
