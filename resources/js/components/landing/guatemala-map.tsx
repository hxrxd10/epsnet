import { useId, useState } from 'react';
import { cn } from '@/lib/utils';
import {
    DOT_PATH,
    EDGES,
    HUB,
    MAP_HEIGHT,
    MAP_VIEWBOX,
    MAP_WIDTH,
    NODES,
    OUTLINE_PATH,
} from './guatemala-data';
import { MAP_PATH } from './guatemala-geometry';

type GuatemalaMapProps = {
    className?: string;
    interactive?: boolean;
};

const [VIEWBOX_X, VIEWBOX_Y] = MAP_VIEWBOX;
const SCAN_HEIGHT = 520;

export default function GuatemalaMap({
    className,
    interactive = true,
}: GuatemalaMapProps) {
    const uid = useId();
    const clipId = `${uid}-clip`;
    const scanId = `${uid}-scan`;
    const fillId = `${uid}-fill`;
    const [activeName, setActiveName] = useState<string | null>(null);

    const active = NODES.find((node) => node.name === activeName) ?? null;
    const hub = NODES.find((node) => node.name === HUB)!;
    const tooltipBelow = active !== null && active.y < VIEWBOX_Y + 340;

    return (
        <div
            className={cn('relative w-full', className)}
            style={{ aspectRatio: `${MAP_WIDTH} / ${MAP_HEIGHT}` }}
            onPointerLeave={() => setActiveName(null)}
        >
            <svg
                viewBox={MAP_VIEWBOX.join(' ')}
                className="size-full overflow-visible"
                role="img"
                aria-label="Mapa de Guatemala con la red de EPS conectando los 22 departamentos"
            >
                <defs>
                    <linearGradient id={scanId} x1="0" y1="0" x2="0" y2="1">
                        <stop offset="0" stopColor="#fff" stopOpacity="0" />
                        <stop
                            offset="0.85"
                            stopColor="#fff"
                            stopOpacity="0.22"
                        />
                        <stop offset="1" stopColor="#fff" stopOpacity="0.45" />
                    </linearGradient>
                    <radialGradient id={fillId} cx="50%" cy="45%" r="60%">
                        <stop offset="0" stopColor="#fff" stopOpacity="0.1" />
                        <stop offset="1" stopColor="#fff" stopOpacity="0.02" />
                    </radialGradient>
                    <clipPath id={clipId}>
                        <path d={OUTLINE_PATH} />
                    </clipPath>
                </defs>

                {/* Capa 1: puntos de fondo */}
                <path d={OUTLINE_PATH} fill={`url(#${fillId})`} />
                <path
                    d={DOT_PATH}
                    stroke="#fff"
                    strokeOpacity={0.3}
                    strokeWidth={10}
                    strokeLinecap="round"
                    fill="none"
                />
                <g clipPath={`url(#${clipId})`}>
                    <rect
                        className="map-scan"
                        x={VIEWBOX_X}
                        y={VIEWBOX_Y - SCAN_HEIGHT}
                        width={MAP_WIDTH}
                        height={SCAN_HEIGHT}
                        fill={`url(#${scanId})`}
                        style={{ transformBox: 'fill-box' }}
                    />
                </g>

                {/* Capa 2: mapa real con la división departamental */}
                <path d={MAP_PATH} fill="#fff" fillOpacity={0.6} />

                {/* Capa 3: red de departamentos */}
                <g fill="none" strokeLinecap="round">
                    {EDGES.map((edge) => {
                        const highlighted =
                            active !== null &&
                            (edge.from === active.name ||
                                edge.to === active.name);

                        return (
                            <path
                                key={`${edge.from}-${edge.to}`}
                                d={edge.path}
                                stroke="#fff"
                                strokeOpacity={highlighted ? 0.95 : 0.42}
                                strokeWidth={highlighted ? 6 : 3.5}
                                className="transition-[stroke-opacity,stroke-width] duration-300"
                            />
                        );
                    })}
                    {EDGES.map((edge, index) => (
                        <path
                            key={`flow-${edge.from}-${edge.to}`}
                            d={edge.path}
                            pathLength={1}
                            stroke="#fff"
                            strokeOpacity={0.95}
                            strokeWidth={6.5}
                            strokeDasharray="0.09 0.91"
                            className="net-flow"
                            style={{
                                animationDelay: `${-(index * 0.37) % 5}s`,
                                animationDuration: `${4 + (index % 4)}s`,
                            }}
                        />
                    ))}
                </g>

                {NODES.map((node) => {
                    const isHub = node.name === HUB;
                    const isActive = node.name === activeName;

                    return (
                        <g
                            key={node.name}
                            transform={`translate(${node.x} ${node.y})`}
                            tabIndex={interactive ? 0 : -1}
                            className={cn(
                                'outline-none',
                                interactive && 'cursor-pointer',
                            )}
                            onPointerEnter={() =>
                                interactive && setActiveName(node.name)
                            }
                            onFocus={() =>
                                interactive && setActiveName(node.name)
                            }
                            onBlur={() => setActiveName(null)}
                        >
                            <title>{`${node.name} · ${node.seat}`}</title>
                            {(isHub || isActive) && (
                                <circle
                                    r={20}
                                    fill="#fff"
                                    className="node-ping"
                                />
                            )}
                            <circle r={60} fill="transparent" />
                            <circle
                                r={isHub ? 26 : isActive ? 21 : 15}
                                fill="#0f1031"
                                stroke="#fff"
                                strokeWidth={isHub ? 9 : 7}
                                className="transition-[r] duration-300"
                            />
                            <circle r={isHub ? 9 : 5.5} fill="#fff" />
                        </g>
                    );
                })}
                <text
                    x={hub.x}
                    y={hub.y + 78}
                    textAnchor="middle"
                    fill="#fff"
                    fillOpacity={0.8}
                    className="font-mono"
                    fontSize={32}
                    letterSpacing={8}
                >
                    DIGEU
                </text>
            </svg>

            {interactive && active && (
                <div
                    className={cn(
                        'bg-brand/80 pointer-events-none absolute z-10 -translate-x-1/2 rounded-xl border border-white/15 px-3 py-2 text-left whitespace-nowrap shadow-2xl backdrop-blur-md',
                        tooltipBelow
                            ? 'translate-y-[14px]'
                            : '-translate-y-[calc(100%+14px)]',
                    )}
                    style={{
                        left: `${((active.x - VIEWBOX_X) / MAP_WIDTH) * 100}%`,
                        top: `${((active.y - VIEWBOX_Y) / MAP_HEIGHT) * 100}%`,
                    }}
                >
                    <p className="font-mono text-[10px] tracking-[0.18em] text-white/50 uppercase">
                        {active.name === HUB
                            ? 'Nodo central · DIGEU'
                            : 'Nodo departamental'}
                    </p>
                    <p className="text-sm font-medium text-white">
                        {active.name}
                        {active.seat !== active.name && (
                            <span className="text-white/50">
                                {' '}
                                · {active.seat}
                            </span>
                        )}
                    </p>
                </div>
            )}
        </div>
    );
}
