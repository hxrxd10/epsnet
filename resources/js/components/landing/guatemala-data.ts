import { OUTLINE_POINTS } from './guatemala-geometry';

export type LonLat = [number, number];
export type Point = [number, number];

export type Department = {
    name: string;
    seat: string;
    coordinates: LonLat;
};

/** Caja del mapa real (public/images/mapa.svg) dentro de su lienzo de 2160 px. */
const MAP_BOX = { x: 105.25, y: 37.6, width: 1969.76, height: 2103.58 };

/** Extensión geográfica que cubre el mapa: 4.01° de longitud (ya corregida por latitud) × 4.08° de latitud. */
const LON_WEST = -92.23;
const LAT_NORTH = 17.82;
const LON_SPAN = 4.01;
const LAT_SPAN = 4.08;

const MAP_MARGIN = 10;

export const MAP_VIEWBOX = [
    MAP_BOX.x - MAP_MARGIN,
    MAP_BOX.y - MAP_MARGIN,
    MAP_BOX.width + MAP_MARGIN * 2,
    MAP_BOX.height + MAP_MARGIN * 2,
] as const;

const [VIEWBOX_X, VIEWBOX_Y, MAP_WIDTH, MAP_HEIGHT] = MAP_VIEWBOX;

export { MAP_HEIGHT, MAP_WIDTH };

export function project([lon, lat]: LonLat): Point {
    return [
        MAP_BOX.x + ((lon - LON_WEST) / LON_SPAN) * MAP_BOX.width,
        MAP_BOX.y + ((LAT_NORTH - lat) / LAT_SPAN) * MAP_BOX.height,
    ];
}

export const OUTLINE_PATH = `M${OUTLINE_POINTS.map(([x, y]) => `${x} ${y}`).join('L')}Z`;

function isInside([x, y]: Point, polygon: Point[]): boolean {
    let inside = false;

    for (let i = 0, j = polygon.length - 1; i < polygon.length; j = i++) {
        const [xi, yi] = polygon[i];
        const [xj, yj] = polygon[j];

        if (yi > y !== yj > y && x < ((xj - xi) * (y - yi)) / (yj - yi) + xi) {
            inside = !inside;
        }
    }

    return inside;
}

function buildDotPath(step: number): string {
    const dots: string[] = [];

    for (let y = VIEWBOX_Y + step / 2; y < VIEWBOX_Y + MAP_HEIGHT; y += step) {
        for (
            let x = VIEWBOX_X + step / 2;
            x < VIEWBOX_X + MAP_WIDTH;
            x += step
        ) {
            if (isInside([x, y], OUTLINE_POINTS)) {
                dots.push(`M${x.toFixed(0)} ${y.toFixed(0)}h0`);
            }
        }
    }

    return dots.join('');
}

export const DOT_PATH = buildDotPath(26);

export const DEPARTMENTS: Department[] = [
    {
        name: 'Guatemala',
        seat: 'Ciudad de Guatemala',
        coordinates: [-90.51, 14.63],
    },
    {
        name: 'Sacatepéquez',
        seat: 'Antigua Guatemala',
        coordinates: [-90.73, 14.56],
    },
    {
        name: 'Chimaltenango',
        seat: 'Chimaltenango',
        coordinates: [-90.82, 14.66],
    },
    { name: 'Escuintla', seat: 'Escuintla', coordinates: [-90.79, 14.3] },
    { name: 'Santa Rosa', seat: 'Cuilapa', coordinates: [-90.3, 14.28] },
    { name: 'Jalapa', seat: 'Jalapa', coordinates: [-89.99, 14.63] },
    { name: 'Jutiapa', seat: 'Jutiapa', coordinates: [-89.9, 14.29] },
    { name: 'Chiquimula', seat: 'Chiquimula', coordinates: [-89.54, 14.8] },
    { name: 'Zacapa', seat: 'Zacapa', coordinates: [-89.53, 14.97] },
    { name: 'El Progreso', seat: 'Guastatoya', coordinates: [-90.07, 14.85] },
    { name: 'Baja Verapaz', seat: 'Salamá', coordinates: [-90.32, 15.1] },
    { name: 'Alta Verapaz', seat: 'Cobán', coordinates: [-90.37, 15.47] },
    { name: 'Izabal', seat: 'Puerto Barrios', coordinates: [-88.85, 15.62] },
    { name: 'Petén', seat: 'Flores', coordinates: [-89.89, 16.93] },
    {
        name: 'Quiché',
        seat: 'Santa Cruz del Quiché',
        coordinates: [-91.15, 15.03],
    },
    {
        name: 'Huehuetenango',
        seat: 'Huehuetenango',
        coordinates: [-91.47, 15.32],
    },
    { name: 'San Marcos', seat: 'San Marcos', coordinates: [-91.79, 14.97] },
    {
        name: 'Quetzaltenango',
        seat: 'Quetzaltenango',
        coordinates: [-91.52, 14.83],
    },
    { name: 'Totonicapán', seat: 'Totonicapán', coordinates: [-91.36, 14.91] },
    { name: 'Sololá', seat: 'Sololá', coordinates: [-91.18, 14.77] },
    { name: 'Suchitepéquez', seat: 'Mazatenango', coordinates: [-91.5, 14.53] },
    { name: 'Retalhuleu', seat: 'Retalhuleu', coordinates: [-91.68, 14.54] },
];

export const HUB = 'Guatemala';

const CONNECTIONS: [string, string][] = [
    ['Guatemala', 'Sacatepéquez'],
    ['Guatemala', 'Chimaltenango'],
    ['Guatemala', 'Escuintla'],
    ['Guatemala', 'Santa Rosa'],
    ['Guatemala', 'Jalapa'],
    ['Guatemala', 'El Progreso'],
    ['Sacatepéquez', 'Chimaltenango'],
    ['Chimaltenango', 'Sololá'],
    ['Chimaltenango', 'Quiché'],
    ['Escuintla', 'Suchitepéquez'],
    ['Suchitepéquez', 'Retalhuleu'],
    ['Suchitepéquez', 'Quetzaltenango'],
    ['Retalhuleu', 'Quetzaltenango'],
    ['Retalhuleu', 'San Marcos'],
    ['San Marcos', 'Quetzaltenango'],
    ['Quetzaltenango', 'Totonicapán'],
    ['Totonicapán', 'Sololá'],
    ['Totonicapán', 'Huehuetenango'],
    ['Huehuetenango', 'Quiché'],
    ['Quiché', 'Baja Verapaz'],
    ['Quiché', 'Alta Verapaz'],
    ['Baja Verapaz', 'Alta Verapaz'],
    ['Baja Verapaz', 'El Progreso'],
    ['El Progreso', 'Zacapa'],
    ['Zacapa', 'Izabal'],
    ['Zacapa', 'Chiquimula'],
    ['Alta Verapaz', 'Izabal'],
    ['Alta Verapaz', 'Petén'],
    ['Izabal', 'Petén'],
    ['Chiquimula', 'Jalapa'],
    ['Jalapa', 'Jutiapa'],
    ['Jutiapa', 'Santa Rosa'],
    ['Santa Rosa', 'Escuintla'],
];

export type NodePosition = Department & { x: number; y: number };

export const NODES: NodePosition[] = DEPARTMENTS.map((department) => {
    const [x, y] = project(department.coordinates);

    return { ...department, x, y };
});

const nodeByName = new Map(NODES.map((node) => [node.name, node]));

export type Edge = { from: string; to: string; path: string };

export const EDGES: Edge[] = CONNECTIONS.map(([from, to], index) => {
    const a = nodeByName.get(from)!;
    const b = nodeByName.get(to)!;
    const dx = b.x - a.x;
    const dy = b.y - a.y;
    const bend = (index % 2 === 0 ? 1 : -1) * 0.14;
    const cx = (a.x + b.x) / 2 - dy * bend;
    const cy = (a.y + b.y) / 2 + dx * bend;

    return {
        from,
        to,
        path: `M${a.x.toFixed(0)} ${a.y.toFixed(0)}Q${cx.toFixed(0)} ${cy.toFixed(0)} ${b.x.toFixed(0)} ${b.y.toFixed(0)}`,
    };
});

export function isNodeInsideBorder(node: NodePosition): boolean {
    return isInside([node.x, node.y], OUTLINE_POINTS);
}
