import { MapPin } from 'lucide-react';
import { useEffect, useRef, useState } from 'react';
import InputError from '@/components/input-error';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { cargarGoogleMaps } from '@/lib/google-maps';
import type {
    LatLngLiteral,
    MapaGoogle,
    MarcadorGoogle,
} from '@/lib/google-maps';

export type ConfiguracionMapa = { key: string | null; mapId: string };

type Props = {
    googleMaps: ConfiguracionMapa;
    latitud: string;
    longitud: string;
    /** Punto donde se centra el mapa mientras no haya una coordenada elegida. */
    centroInicial?: LatLngLiteral;
    errorLatitud?: string;
    errorLongitud?: string;
    onCambiar: (latitud: string, longitud: string) => void;
};

const CENTRO_GUATEMALA = { lat: 15.5, lng: -90.3 };
const LIMITES_GUATEMALA = {
    north: 18.6,
    south: 13.4,
    west: -92.6,
    east: -87.9,
};

const comoPunto = (latitud: string, longitud: string): LatLngLiteral | null => {
    const lat = Number.parseFloat(latitud);
    const lng = Number.parseFloat(longitud);

    return Number.isFinite(lat) && Number.isFinite(lng) ? { lat, lng } : null;
};

/** Coordenadas de un lugar: se eligen con un clic en el mapa de Google o se escriben a mano. */
export default function SelectorCoordenadas({
    googleMaps,
    latitud,
    longitud,
    centroInicial,
    errorLatitud,
    errorLongitud,
    onCambiar,
}: Props) {
    const contenedor = useRef<HTMLDivElement>(null);
    const mapa = useRef<MapaGoogle | null>(null);
    const marcador = useRef<MarcadorGoogle | null>(null);
    const crearMarcador = useRef<
        ((punto: LatLngLiteral) => MarcadorGoogle) | null
    >(null);
    const alCambiar = useRef(onCambiar);
    const acercado = useRef(false);
    const [estado, setEstado] = useState<'cargando' | 'listo' | 'error'>(
        'cargando',
    );

    useEffect(() => {
        alCambiar.current = onCambiar;
    });

    useEffect(() => {
        if (!googleMaps.key || !contenedor.current) {
            return;
        }

        const elemento = contenedor.current;
        const inicial = centroInicial ?? CENTRO_GUATEMALA;
        let cancelado = false;

        void (async () => {
            try {
                const google = await cargarGoogleMaps(googleMaps.key!);
                const { Map } = await google.importLibrary('maps');
                const { AdvancedMarkerElement } =
                    await google.importLibrary('marker');

                if (cancelado) {
                    return;
                }

                const instancia = new Map(elemento, {
                    center: inicial,
                    zoom: centroInicial ? 11 : 7,
                    mapId: googleMaps.mapId,
                    restriction: {
                        latLngBounds: LIMITES_GUATEMALA,
                        strictBounds: false,
                    },
                    mapTypeControl: false,
                    streetViewControl: false,
                    gestureHandling: 'cooperative',
                });

                instancia.addListener('click', (evento) => {
                    if (evento.latLng) {
                        alCambiar.current(
                            evento.latLng.lat().toFixed(7),
                            evento.latLng.lng().toFixed(7),
                        );
                    }
                });

                crearMarcador.current = (punto) =>
                    new AdvancedMarkerElement({
                        map: instancia,
                        position: punto,
                    });
                mapa.current = instancia;
                setEstado('listo');
            } catch (error) {
                console.error('No se pudo iniciar Google Maps:', error);

                if (!cancelado) {
                    setEstado('error');
                }
            }
        })();

        return () => {
            cancelado = true;
        };
        // El centro solo se usa al crear el mapa.
    }, [googleMaps.key, googleMaps.mapId]);

    useEffect(() => {
        const instancia = mapa.current;

        if (estado !== 'listo' || !instancia || !crearMarcador.current) {
            return;
        }

        const punto = comoPunto(latitud, longitud);

        if (punto === null) {
            if (marcador.current) {
                marcador.current.map = null;
                marcador.current = null;
            }

            return;
        }

        if (marcador.current) {
            marcador.current.position = punto;
        } else {
            marcador.current = crearMarcador.current(punto);
        }

        instancia.setCenter(punto);

        // Al abrir un registro que ya tiene coordenada, el mapa se acerca a ella una sola vez.
        if (!acercado.current) {
            acercado.current = true;
            instancia.setZoom(11);
        }
    }, [estado, latitud, longitud]);

    return (
        <div className="grid gap-3">
            {googleMaps.key ? (
                <div className="relative overflow-hidden rounded-lg border">
                    <div ref={contenedor} className="bg-muted h-56 w-full" />
                    {estado !== 'listo' && (
                        <div className="bg-background/80 text-muted-foreground absolute inset-0 flex items-center justify-center p-4 text-center text-sm">
                            {estado === 'cargando'
                                ? 'Cargando mapa…'
                                : 'No se pudo cargar el mapa. Escribe las coordenadas.'}
                        </div>
                    )}
                </div>
            ) : (
                <p className="text-muted-foreground flex items-start gap-2 rounded-lg border border-dashed p-3 text-sm">
                    <MapPin className="mt-0.5 size-4 shrink-0" />
                    El mapa no está disponible; escribe las coordenadas.
                </p>
            )}

            <div className="grid grid-cols-2 gap-3">
                <div className="grid gap-2">
                    <Label htmlFor="latitud">Latitud</Label>
                    <Input
                        id="latitud"
                        type="number"
                        step="any"
                        value={latitud}
                        placeholder="Ej. 14.6349"
                        aria-invalid={!!errorLatitud}
                        onChange={(evento) =>
                            onCambiar(evento.target.value, longitud)
                        }
                    />
                    <InputError message={errorLatitud} />
                </div>
                <div className="grid gap-2">
                    <Label htmlFor="longitud">Longitud</Label>
                    <Input
                        id="longitud"
                        type="number"
                        step="any"
                        value={longitud}
                        placeholder="Ej. -90.5069"
                        aria-invalid={!!errorLongitud}
                        onChange={(evento) =>
                            onCambiar(latitud, evento.target.value)
                        }
                    />
                    <InputError message={errorLongitud} />
                </div>
            </div>
            {googleMaps.key && (
                <p className="text-muted-foreground -mt-1 text-xs">
                    Haz clic en el mapa para marcar el lugar.
                </p>
            )}
        </div>
    );
}
