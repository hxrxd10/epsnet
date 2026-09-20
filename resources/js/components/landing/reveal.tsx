import { useEffect, useRef, useState } from 'react';
import type { ReactNode } from 'react';
import { cn } from '@/lib/utils';

type RevealProps = {
    children: ReactNode;
    className?: string;
    delay?: number;
};

export default function Reveal({
    children,
    className,
    delay = 0,
}: RevealProps) {
    const ref = useRef<HTMLDivElement>(null);
    const [visible, setVisible] = useState(false);

    useEffect(() => {
        const element = ref.current;

        if (!element) {
            return;
        }

        const prefersReducedMotion = window.matchMedia(
            '(prefers-reduced-motion: reduce)',
        ).matches;

        if (prefersReducedMotion || !('IntersectionObserver' in window)) {
            setVisible(true);

            return;
        }

        const observer = new IntersectionObserver(
            ([entry]) => {
                if (entry.isIntersecting) {
                    setVisible(true);
                    observer.disconnect();
                }
            },
            { threshold: 0.12, rootMargin: '0px 0px -6% 0px' },
        );

        observer.observe(element);

        return () => observer.disconnect();
    }, []);

    return (
        <div
            ref={ref}
            data-visible={visible}
            style={{ transitionDelay: `${delay}ms` }}
            className={cn(
                'translate-y-8 opacity-0 transition-[opacity,transform] duration-700 ease-out data-[visible=true]:translate-y-0 data-[visible=true]:opacity-100',
                className,
            )}
        >
            {children}
        </div>
    );
}
