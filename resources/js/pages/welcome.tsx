import { Head, usePage } from '@inertiajs/react';
import AxesSection from '@/components/landing/axes-section';
import ContactSection from '@/components/landing/contact-section';
import DefinitionSection from '@/components/landing/definition-section';
import FeaturesSection from '@/components/landing/features-section';
import HeroSection from '@/components/landing/hero-section';
import LandingHeader from '@/components/landing/landing-header';

export default function Welcome() {
    const { auth } = usePage().props;
    const isAuthenticated = Boolean(auth?.user);

    return (
        <>
            <Head title="Red nacional de EPS">
                <meta
                    name="description"
                    content="EPSNET es la red de estudiantes en Ejercicio Profesional Supervisado de la USAC: docencia, investigación y extensión conectadas en todo el territorio de Guatemala."
                />
            </Head>

            <div className="bg-brand selection:text-brand text-white antialiased selection:bg-white">
                <LandingHeader isAuthenticated={isAuthenticated} />
                <main>
                    <HeroSection isAuthenticated={isAuthenticated} />
                    <FeaturesSection />
                    <DefinitionSection />
                    <AxesSection />
                    <ContactSection isAuthenticated={isAuthenticated} />
                </main>
            </div>
        </>
    );
}
