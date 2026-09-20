import { usePage } from '@inertiajs/react';

export default function AppLogo() {
    const { name } = usePage().props;

    return (
        <img
            src="/images/logo.png"
            alt={name}
            className="h-9 w-auto object-contain object-left group-data-[collapsible=icon]:hidden"
        />
    );
}
