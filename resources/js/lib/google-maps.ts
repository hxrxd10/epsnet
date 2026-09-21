/**
 * Carga de la API de Google Maps (JavaScript) sin dependencias adicionales.
 * Solo se declara la parte de la API que usa la aplicación.
 */

export type LatLngLiteral = { lat: number; lng: number };

export type ComponenteDireccion = {
    long_name: string;
    types: string[];
};

export type ResultadoGeocodificacion = {
    address_components: ComponenteDireccion[];
};

export type MapaGoogle = {
    setCenter(centro: LatLngLiteral): void;
    setZoom(zoom: number): void;
    addListener(
        evento: 'click',
        manejador: (evento: {
            latLng: { lat(): number; lng(): number } | null;
        }) => void,
    ): void;
};

export type MarcadorGoogle = {
    position: LatLngLiteral | null;
    map: MapaGoogle | null;
};

export type BibliotecaMapas = {
    Map: new (
        elemento: HTMLElement,
        opciones: Record<string, unknown>,
    ) => MapaGoogle;
};

export type BibliotecaMarcadores = {
    AdvancedMarkerElement: new (opciones: {
        map: MapaGoogle;
        position: LatLngLiteral;
    }) => MarcadorGoogle;
};

export type BibliotecaGeocodificacion = {
    Geocoder: new () => {
        geocode(solicitud: {
            location: LatLngLiteral;
            language?: string;
        }): Promise<{ results: ResultadoGeocodificacion[] }>;
    };
};

type GoogleMaps = {
    importLibrary(nombre: 'maps'): Promise<BibliotecaMapas>;
    importLibrary(nombre: 'marker'): Promise<BibliotecaMarcadores>;
    importLibrary(nombre: 'geocoding'): Promise<BibliotecaGeocodificacion>;
};

declare global {
    interface Window {
        google?: { maps?: Partial<GoogleMaps> };
    }
}

let carga: Promise<GoogleMaps> | null = null;

const RETORNO = '__epsnetGoogleMapsListo';

export function cargarGoogleMaps(apiKey: string): Promise<GoogleMaps> {
    const yaCargada = window.google?.maps;

    if (yaCargada?.importLibrary) {
        return Promise.resolve(yaCargada as GoogleMaps);
    }

    carga ??= new Promise<GoogleMaps>((resolve, reject) => {
        const fallar = (mensaje: string) => {
            carga = null;
            reject(new Error(mensaje));
        };

        // Con loading=async, importLibrary solo existe cuando Google invoca este retorno.
        Object.assign(window, {
            [RETORNO]: () => {
                const maps = window.google?.maps;

                if (maps?.importLibrary) {
                    resolve(maps as GoogleMaps);
                } else {
                    fallar('Google Maps no está disponible.');
                }
            },
        });

        const script = document.createElement('script');

        script.src = `https://maps.googleapis.com/maps/api/js?key=${encodeURIComponent(apiKey)}&v=weekly&loading=async&language=es&region=GT&callback=${RETORNO}`;
        script.async = true;
        script.onerror = () => fallar('No se pudo cargar Google Maps.');

        document.head.appendChild(script);
    });

    return carga;
}
