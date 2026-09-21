import { MapPin } from 'lucide-react';
import { useEffect, useRef, useState } from 'react';
import { cargarGoogleMaps } from '@/lib/google-maps';
import type {
    LatLngLiteral,
    MapaGoogle,
    MarcadorGoogle,
    ResultadoGeocodificacion,
} from '@/lib/google-maps';
import type { Opcion } from '@/types/estudiante';

export type SeleccionUbicacion = {
    latitud: string;
    longitud: string;
    departamento_id?: string;
    municipio_id?: string;
};

export type OpcionMunicipio = Opcion & { departamento_id: string };

type SelectorUbicacionProps = {
    apiKey: string | null;
    mapId: string;
    latitud: string;
    longitud: string;
    departamentos: Opcion[];
    municipios: OpcionMunicipio[];
    /** Punto donde se centra el mapa mientras no haya una coordenada marcada (p. ej. el municipio elegido). */
    centro?: LatLngLiteral | null;
    onSeleccionar: (seleccion: SeleccionUbicacion) => void;
};

const CENTRO_GUATEMALA = { lat: 15.5, lng: -90.3 };
const LIMITES_GUATEMALA = {
    north: 18.6,
    south: 13.4,
    west: -92.6,
    east: -87.9,
};

const normalizar = (texto: string) =>
    texto
        .normalize('NFD')
        .replace(/[̀-ͯ]/g, '')
        .toLowerCase()
        .replace(/^(departamento|department)( de| of)? /, '')
        .replace(/ (department|departamento)$/, '')
        .trim();

/** Deduce departamento y municipio del catálogo a partir del resultado de la geocodificación inversa. */
function deducirDivision(
    resultados: ResultadoGeocodificacion[],
    departamentos: Opcion[],
    municipios: OpcionMunicipio[],
): Pick<SeleccionUbicacion, 'departamento_id' | 'municipio_id'> {
    const componentes = resultados.flatMap((r) => r.address_components);
    const nivel = (tipo: string) =>
        componentes.find((c) => c.types.includes(tipo))?.long_name;

    const nombreDepartamento = nivel('administrative_area_level_1');
    const nombreMunicipio = (
        nivel('administrative_area_level_2') ?? nivel('locality')
    )?.replace(/^Municipio de /i, '');
    const departamento = nombreDepartamento
        ? departamentos.find(
              (d) => normalizar(d.label) === normalizar(nombreDepartamento),
          )
        : undefined;

    const municipio =
        departamento && nombreMunicipio
            ? municipios.find(
                  (m) =>
                      m.departamento_id === departamento.value &&
                      normalizar(m.label) === normalizar(nombreMunicipio),
              )
            : undefined;

    return {
        departamento_id: departamento?.value,
        // Sin coincidencia se vacía, para no dejar un municipio de otro departamento.
        municipio_id: municipio?.value ?? (departamento ? '' : undefined),
    };
}

export default function SelectorUbicacion({
    apiKey,
    mapId,
    latitud,
    longitud,
    departamentos,
    municipios,
    centro,
    onSeleccionar,
}: SelectorUbicacionProps) {
    const contenedor = useRef<HTMLDivElement>(null);
    const mapa = useRef<MapaGoogle | null>(null);
    const marcador = useRef<MarcadorGoogle | null>(null);
    const crearMarcador = useRef<
        ((posicion: LatLngLiteral) => MarcadorGoogle) | null
    >(null);
    const alSeleccionar = useRef(onSeleccionar);
    const departamentosActuales = useRef(departamentos);
    const municipiosActuales = useRef(municipios);
    const [estado, setEstado] = useState<'cargando' | 'listo' | 'error'>(
        'cargando',
    );

    useEffect(() => {
        alSeleccionar.current = onSeleccionar;
        departamentosActuales.current = departamentos;
        municipiosActuales.current = municipios;
    });

    useEffect(() => {
        if (!apiKey || !contenedor.current) {
            return;
        }

        const elemento = contenedor.current;
        let cancelado = false;

        void (async () => {
            try {
                const google = await cargarGoogleMaps(apiKey);
                const { Map } = await google.importLibrary('maps');
                const { AdvancedMarkerElement } =
                    await google.importLibrary('marker');

                if (cancelado) {
                    return;
                }

                const instancia = new Map(elemento, {
                    center: CENTRO_GUATEMALA,
                    zoom: 7,
                    mapId,
                    restriction: {
                        latLngBounds: LIMITES_GUATEMALA,
                        strictBounds: false,
                    },
                    mapTypeControl: false,
                    streetViewControl: false,
                    gestureHandling: 'cooperative',
                });

                instancia.addListener('click', async (evento) => {
                    if (!evento.latLng) {
                        return;
                    }

                    const punto = {
                        lat: evento.latLng.lat(),
                        lng: evento.latLng.lng(),
                    };
                    const seleccion: SeleccionUbicacion = {
                        latitud: punto.lat.toFixed(7),
                        longitud: punto.lng.toFixed(7),
                    };

                    alSeleccionar.current(seleccion);

                    try {
                        const { Geocoder } =
                            await google.importLibrary('geocoding');
                        const { results } = await new Geocoder().geocode({
                            location: punto,
                            language: 'es',
                        });

                        alSeleccionar.current({
                            ...seleccion,
                            ...deducirDivision(
                                results,
                                departamentosActuales.current,
                                municipiosActuales.current,
                            ),
                        });
                    } catch {
                        // Sin geocodificación, el estudiante elige municipio y departamento a mano.
                    }
                });

                crearMarcador.current = (posicion) =>
                    new AdvancedMarkerElement({
                        map: instancia,
                        position: posicion,
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
    }, [apiKey, mapId]);

    useEffect(() => {
        const instancia = mapa.current;

        if (estado !== 'listo' || !instancia || !crearMarcador.current) {
            return;
        }

        const lat = Number.parseFloat(latitud);
        const lng = Number.parseFloat(longitud);

        if (!Number.isFinite(lat) || !Number.isFinite(lng)) {
            if (marcador.current) {
                marcador.current.map = null;
                marcador.current = null;
            }

            return;
        }

        const posicion = { lat, lng };

        if (marcador.current) {
            marcador.current.position = posicion;
        } else {
            marcador.current = crearMarcador.current(posicion);
        }

        instancia.setCenter(posicion);
    }, [estado, latitud, longitud]);

    const centroLat = centro?.lat;
    const centroLng = centro?.lng;

    useEffect(() => {
        const instancia = mapa.current;

        if (
            estado !== 'listo' ||
            !instancia ||
            centroLat === undefined ||
            centroLng === undefined ||
            Number.isFinite(Number.parseFloat(latitud))
        ) {
            return;
        }

        instancia.setCenter({ lat: centroLat, lng: centroLng });
        instancia.setZoom(11);
        // Solo se recentra al cambiar el municipio; una marca ya puesta manda sobre el centro.
    }, [estado, centroLat, centroLng]);

    if (!apiKey) {
        return (
            <p className="border-brand/25 text-brand/65 flex items-start gap-2 rounded-2xl border border-dashed p-4 text-sm">
                <MapPin className="mt-0.5 size-4 shrink-0" />
                El mapa no está disponible en este momento. Ingresa las
                coordenadas manualmente o describe la referencia territorial.
            </p>
        );
    }

    return (
        <div>
            <div className="border-brand/15 relative overflow-hidden rounded-2xl border">
                <div
                    ref={contenedor}
                    className="bg-brand/5 h-72 w-full sm:h-96"
                />
                {estado !== 'listo' && (
                    <div className="text-brand/65 absolute inset-0 flex items-center justify-center bg-white/80 p-6 text-center text-sm">
                        {estado === 'cargando'
                            ? 'Cargando mapa…'
                            : 'No se pudo cargar el mapa. Ingresa las coordenadas manualmente.'}
                    </div>
                )}
            </div>
            <p className="text-brand/55 mt-2 text-xs">
                Haz clic en el mapa para marcar el lugar de tu EPS: tomamos las
                coordenadas y sugerimos el departamento y el municipio, que
                puedes corregir.
            </p>
        </div>
    );
}
