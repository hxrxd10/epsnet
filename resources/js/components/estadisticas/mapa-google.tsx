import { useEffect, useMemo, useRef, useState } from 'react';
import { cargarGoogleMaps } from '@/lib/google-maps';
import type { LatLngLiteral } from '@/lib/google-maps';
import { cn } from '@/lib/utils';
import MapaEstadistico from './mapa-estadistico';
import type { ValorDepartamento } from './mapa-estadistico';
import { formatearNumero } from './metricas';

type Props = {
    apiKey: string | null;
    departamentos: ValorDepartamento[];
    seleccionado: string | null;
    onSeleccionar: (codigo: string | null) => void;
    /** Nombre de lo que se cuenta, para el texto accesible y el tooltip. */
    etiqueta: string;
    /** Cómo se escribe cada valor (por defecto, como número entero). */
    formato?: (valor: number, compacto?: boolean) => string;
};

/** Límites oficiales de los departamentos (COD-AB 2019), simplificados para la web. */
const GEOJSON = '/mapas/departamentos-gt.geojson';

const LIMITES_GUATEMALA = {
    north: 17.82,
    south: 13.73,
    west: -92.24,
    east: -88.22,
};

/** Escala secuencial de un solo tono (del azul de la marca), de menos a más. */
const ESCALA = ['#dfe3f4', '#aab3e0', '#6f7cc4', '#3a4796', '#0f1031'];
const SIN_DATOS = '#f1f1f4';

/** Estilo «Silver» de Google, sin puntos de interés para no competir con los datos. */
const ESTILO_SILVER = [
    { elementType: 'geometry', stylers: [{ color: '#f5f5f5' }] },
    { elementType: 'labels.icon', stylers: [{ visibility: 'off' }] },
    { elementType: 'labels.text.fill', stylers: [{ color: '#616161' }] },
    { elementType: 'labels.text.stroke', stylers: [{ color: '#f5f5f5' }] },
    { featureType: 'poi', stylers: [{ visibility: 'off' }] },
    { featureType: 'transit', stylers: [{ visibility: 'off' }] },
    {
        featureType: 'administrative.land_parcel',
        stylers: [{ visibility: 'off' }],
    },
    {
        featureType: 'road',
        elementType: 'geometry',
        stylers: [{ color: '#ffffff' }],
    },
    {
        featureType: 'road.highway',
        elementType: 'geometry',
        stylers: [{ color: '#dadada' }],
    },
    {
        featureType: 'road',
        elementType: 'labels',
        stylers: [{ visibility: 'off' }],
    },
    {
        featureType: 'water',
        elementType: 'geometry',
        stylers: [{ color: '#c9c9c9' }],
    },
    {
        featureType: 'water',
        elementType: 'labels.text.fill',
        stylers: [{ color: '#9e9e9e' }],
    },
];

type Rasgo = { getProperty(nombre: string): unknown };

type EventoRasgo = {
    feature: Rasgo;
    domEvent?: { clientX: number; clientY: number };
};

type MapaConDatos = {
    fitBounds(limites: typeof LIMITES_GUATEMALA, relleno?: number): void;
    data: {
        addGeoJson(
            geojson: unknown,
            opciones: { idPropertyName: string },
        ): unknown;
        setStyle(estilo: (rasgo: Rasgo) => Record<string, unknown>): void;
        addListener(
            evento: 'click' | 'mouseover' | 'mousemove' | 'mouseout',
            manejador: (evento: EventoRasgo) => void,
        ): void;
    };
};

type Etiqueta = { centro: LatLngLiteral; texto: string; oscuro: boolean };

type CapaEtiquetas = {
    setMap(mapa: unknown): void;
    mostrar(etiquetas: Etiqueta[]): void;
};

type Departamento = { codigo: string; nombre: string; centro: LatLngLiteral };

/** Tono de la escala para un valor (la raíz suaviza los departamentos con valores extremos). */
function claseDe(valor: number, maximo: number): number {
    if (valor <= 0 || maximo <= 0) {
        return -1;
    }

    return Math.min(
        ESCALA.length - 1,
        Math.floor(Math.sqrt(valor / maximo) * ESCALA.length),
    );
}

/**
 * Tonos de la leyenda con su límite superior. Se omiten los que no pueden tener ningún departamento
 * (con cifras pequeñas, dos tonos seguidos caerían en el mismo número entero).
 */
function leyenda(maximo: number): { color: string; hasta: number }[] {
    const limites = ESCALA.map((_, indice) =>
        Math.floor(maximo * ((indice + 1) / ESCALA.length) ** 2),
    );

    return ESCALA.flatMap((color, indice) => {
        const hasta = indice === ESCALA.length - 1 ? maximo : limites[indice];
        const anterior = indice === 0 ? 0 : limites[indice - 1];

        return hasta > anterior ? [{ color, hasta }] : [];
    });
}

/**
 * Capa con el valor de cada departamento sobre su centro. Se crea después de cargar Google Maps
 * porque extiende su OverlayView.
 */
function crearCapaEtiquetas(
    OverlayView: new () => {
        getPanes(): { floatPane: HTMLElement } | null;
        getProjection():
            | {
                  fromLatLngToDivPixel(punto: LatLngLiteral): {
                      x: number;
                      y: number;
                  } | null;
              }
            | null
            | undefined;
        setMap(mapa: unknown): void;
    },
): new () => CapaEtiquetas {
    return class extends OverlayView implements CapaEtiquetas {
        private contenedor = document.createElement('div');

        private etiquetas: Etiqueta[] = [];

        onAdd(): void {
            this.contenedor.style.position = 'absolute';
            this.contenedor.style.pointerEvents = 'none';
            this.getPanes()?.floatPane.appendChild(this.contenedor);
        }

        onRemove(): void {
            this.contenedor.remove();
        }

        mostrar(etiquetas: Etiqueta[]): void {
            this.etiquetas = etiquetas;
            this.draw();
        }

        draw(): void {
            const proyeccion = this.getProjection();

            if (!proyeccion) {
                return;
            }

            this.contenedor.replaceChildren(
                ...this.etiquetas.flatMap((etiqueta) => {
                    const punto = proyeccion.fromLatLngToDivPixel(
                        etiqueta.centro,
                    );

                    if (punto === null) {
                        return [];
                    }

                    const elemento = document.createElement('span');
                    elemento.textContent = etiqueta.texto;
                    elemento.className = cn(
                        'absolute -translate-x-1/2 -translate-y-1/2 font-mono text-[11px] font-semibold whitespace-nowrap',
                        etiqueta.oscuro
                            ? 'text-white [text-shadow:0_0_3px_#0f1031,0_0_2px_#0f1031]'
                            : 'text-[#0f1031] [text-shadow:0_0_3px_#fff,0_0_2px_#fff]',
                    );
                    elemento.style.left = `${punto.x}px`;
                    elemento.style.top = `${punto.y}px`;

                    return [elemento];
                }),
            );
        }
    };
}

/**
 * Mapa de las estadísticas sobre Google Maps (estilo «Silver») con la división oficial por
 * departamentos. Si no hay clave o Google Maps no carga, se usa el mapa de puntos propio.
 */
export default function MapaGoogle({
    apiKey,
    departamentos,
    seleccionado,
    onSeleccionar,
    etiqueta,
    formato = formatearNumero,
}: Props) {
    const contenedor = useRef<HTMLDivElement>(null);
    const mapa = useRef<MapaConDatos | null>(null);
    const capa = useRef<CapaEtiquetas | null>(null);
    const [estado, setEstado] = useState<'cargando' | 'listo' | 'error'>(
        apiKey ? 'cargando' : 'error',
    );
    const [geografia, setGeografia] = useState<Departamento[]>([]);
    const [activo, setActivo] = useState<{
        codigo: string;
        x: number;
        y: number;
    } | null>(null);

    const valores = useMemo(
        () => new Map(departamentos.map((d) => [d.codigo, d])),
        [departamentos],
    );
    const maximo = Math.max(0, ...departamentos.map((d) => d.valor));

    // Los manejadores de Google se registran una sola vez: leen lo actual por referencia.
    const actual = useRef({ seleccionado, onSeleccionar, formato });
    useEffect(() => {
        actual.current = { seleccionado, onSeleccionar, formato };
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
                const biblioteca = (await google.importLibrary(
                    'maps',
                )) as unknown as {
                    Map: new (
                        elemento: HTMLElement,
                        opciones: Record<string, unknown>,
                    ) => MapaConDatos;
                    OverlayView: Parameters<typeof crearCapaEtiquetas>[0];
                };
                const geojson = (await fetch(GEOJSON).then((respuesta) =>
                    respuesta.ok ? respuesta.json() : Promise.reject(),
                )) as {
                    features: {
                        properties: {
                            codigo: string;
                            nombre: string;
                            centro: [number, number];
                        };
                    }[];
                };

                if (cancelado) {
                    return;
                }

                const instancia = new biblioteca.Map(elemento, {
                    center: { lat: 15.7, lng: -90.3 },
                    zoom: 7,
                    styles: ESTILO_SILVER,
                    backgroundColor: '#f5f5f5',
                    isFractionalZoomEnabled: true,
                    disableDefaultUI: true,
                    zoomControl: true,
                    gestureHandling: 'cooperative',
                    clickableIcons: false,
                    restriction: {
                        latLngBounds: {
                            north: 19,
                            south: 12.5,
                            west: -94,
                            east: -86.5,
                        },
                        strictBounds: false,
                    },
                });

                instancia.fitBounds(LIMITES_GUATEMALA, 4);
                instancia.data.addGeoJson(geojson, {
                    idPropertyName: 'codigo',
                });

                const codigoDe = (rasgo: Rasgo) =>
                    String(rasgo.getProperty('codigo'));
                const posicion = (evento: EventoRasgo) => {
                    const caja = elemento.getBoundingClientRect();

                    return {
                        x: (evento.domEvent?.clientX ?? 0) - caja.left,
                        y: (evento.domEvent?.clientY ?? 0) - caja.top,
                    };
                };

                instancia.data.addListener('mouseover', (evento) =>
                    setActivo({
                        codigo: codigoDe(evento.feature),
                        ...posicion(evento),
                    }),
                );
                instancia.data.addListener('mousemove', (evento) =>
                    setActivo({
                        codigo: codigoDe(evento.feature),
                        ...posicion(evento),
                    }),
                );
                instancia.data.addListener('mouseout', () => setActivo(null));
                instancia.data.addListener('click', (evento) => {
                    const codigo = codigoDe(evento.feature);
                    const { seleccionado: ahora, onSeleccionar: elegir } =
                        actual.current;

                    elegir(ahora === codigo ? null : codigo);
                });

                const Capa = crearCapaEtiquetas(biblioteca.OverlayView);
                const etiquetas = new Capa();
                etiquetas.setMap(instancia);

                mapa.current = instancia;
                capa.current = etiquetas;
                setGeografia(
                    geojson.features.map(({ properties }) => ({
                        codigo: properties.codigo,
                        nombre: properties.nombre,
                        centro: {
                            lat: properties.centro[1],
                            lng: properties.centro[0],
                        },
                    })),
                );
                setEstado('listo');
            } catch {
                if (!cancelado) {
                    setEstado('error');
                }
            }
        })();

        return () => {
            cancelado = true;
            capa.current?.setMap(null);
        };
    }, [apiKey]);

    // Colores, selección y cifras: se actualizan con cada cambio de métrica o filtro.
    useEffect(() => {
        if (estado !== 'listo' || mapa.current === null) {
            return;
        }

        mapa.current.data.setStyle((rasgo) => {
            const codigo = String(rasgo.getProperty('codigo'));
            const clase = claseDe(valores.get(codigo)?.valor ?? 0, maximo);
            const esSeleccionado = codigo === seleccionado;
            const esActivo = codigo === activo?.codigo;

            return {
                fillColor: clase < 0 ? SIN_DATOS : ESCALA[clase],
                fillOpacity: clase < 0 ? 0.55 : 0.82,
                strokeColor: esSeleccionado ? '#f59e0b' : '#ffffff',
                strokeOpacity: 1,
                strokeWeight: esSeleccionado ? 3 : esActivo ? 2 : 1,
                zIndex: esSeleccionado ? 3 : esActivo ? 2 : 1,
                cursor: 'pointer',
            };
        });

        capa.current?.mostrar(
            geografia.flatMap((departamento) => {
                const valor = valores.get(departamento.codigo)?.valor ?? 0;

                return valor > 0
                    ? [
                          {
                              centro: departamento.centro,
                              texto: actual.current.formato(valor, true),
                              oscuro: claseDe(valor, maximo) >= 2,
                          },
                      ]
                    : [];
            }),
        );
    }, [estado, valores, maximo, seleccionado, activo?.codigo, geografia]);

    if (estado === 'error') {
        return (
            <MapaEstadistico
                departamentos={departamentos}
                seleccionado={seleccionado}
                onSeleccionar={onSeleccionar}
                etiqueta={etiqueta}
                formato={formato}
            />
        );
    }

    const datosActivo = activo ? valores.get(activo.codigo) : undefined;
    const tonos = leyenda(maximo);

    return (
        <div className="flex flex-col gap-3">
            <div className="relative overflow-hidden rounded-xl ring-1 ring-white/10">
                <div
                    ref={contenedor}
                    className="aspect-square max-h-[560px] min-h-[340px] w-full bg-[#f5f5f5]"
                    aria-hidden
                />

                {estado === 'cargando' && (
                    <div className="absolute inset-0 animate-pulse bg-[#e9e9ec]" />
                )}

                {activo && datosActivo && (
                    <div
                        className="bg-brand/90 pointer-events-none absolute z-10 -translate-x-1/2 -translate-y-[calc(100%+14px)] rounded-xl border border-white/15 px-3 py-2 text-left whitespace-nowrap text-white shadow-2xl backdrop-blur-md"
                        style={{ left: activo.x, top: activo.y }}
                    >
                        <p className="text-sm font-medium">
                            {datosActivo.nombre}
                        </p>
                        <p className="font-mono text-xs text-white/70">
                            {formato(datosActivo.valor)} {etiqueta}
                        </p>
                    </div>
                )}
            </div>

            {maximo > 0 && (
                <div
                    className="flex flex-wrap items-center gap-x-3 gap-y-1.5 text-[11px] text-white/70"
                    aria-label={`Escala: ${etiqueta} por departamento`}
                >
                    <span className="flex items-center gap-1.5">
                        <span
                            className="size-3 rounded-sm ring-1 ring-white/30"
                            style={{ backgroundColor: SIN_DATOS }}
                        />
                        Sin datos
                    </span>
                    {tonos.map(({ color, hasta }) => (
                        <span
                            key={color}
                            className="flex items-center gap-1.5 font-mono"
                        >
                            <span
                                className="size-3 rounded-sm ring-1 ring-white/30"
                                style={{ backgroundColor: color }}
                            />
                            hasta {formato(hasta, true)}
                        </span>
                    ))}
                </div>
            )}

            {/* El mapa de Google no se recorre con el teclado: estos botones dan el mismo acceso. */}
            <ul className="sr-only">
                {departamentos.map((departamento) => (
                    <li key={departamento.codigo}>
                        <button
                            type="button"
                            aria-pressed={seleccionado === departamento.codigo}
                            onClick={() =>
                                onSeleccionar(
                                    seleccionado === departamento.codigo
                                        ? null
                                        : departamento.codigo,
                                )
                            }
                        >
                            {departamento.nombre}: {formato(departamento.valor)}{' '}
                            {etiqueta}
                        </button>
                    </li>
                ))}
            </ul>
        </div>
    );
}
