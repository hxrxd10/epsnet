<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Estadísticas de EPS</title>
    @include('pdf.partials.estilos')
</head>
<body>
    @include('pdf.partials.encabezado', ['titulo' => 'Estadísticas de la red de EPS', 'subtitulo' => 'Sistema Unificado de Registro y Seguimiento del EPS'])
    <div class="contenido">
        @include('pdf.partials.filtros')

        <h2>Totales del país</h2>
        <table class="cajas">
            @foreach (array_chunk($metricas, 4) as $fila)
                <tr>
                    @foreach ($fila as $metrica)
                        <td>
                            <div class="valor">{{ number_format($totales[$metrica->value]) }}</div>
                            <div class="nombre">{{ $metrica->etiqueta() }}</div>
                        </td>
                    @endforeach
                </tr>
            @endforeach
        </table>
        @if ($sinUbicacion['eps'] > 0)
            <p class="apagado">{{ number_format($sinUbicacion['eps']) }} EPS sin ubicación registrada no se asignan a ningún departamento.</p>
        @endif

        <h2>Mapa: {{ $metricaElegida->etiqueta() }} por departamento</h2>
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="width: 300px; vertical-align: top;">
                    <img src="{{ $mapa }}" width="300" alt="Mapa de Guatemala">
                    <p class="apagado" style="font-size: 7.5px;">Más oscuro = más {{ mb_strtolower($metricaElegida->etiqueta()) }}.</p>
                </td>
                <td style="vertical-align: top;">
                    <div class="etiqueta">Departamentos con más {{ mb_strtolower($metricaElegida->etiqueta()) }}</div>
                    <table class="datos">
                        <tr><th>#</th><th>Departamento</th><th class="num">{{ $metricaElegida->etiqueta() }}</th></tr>
                        @foreach ($ranking as $posicion => $departamento)
                            <tr>
                                <td>{{ $posicion + 1 }}</td>
                                <td>{{ $departamento['nombre'] }}</td>
                                <td class="num">{{ number_format($departamento['metricas'][$metricaElegida->value]) }}</td>
                            </tr>
                        @endforeach
                    </table>
                </td>
            </tr>
        </table>

        <h2 style="page-break-before: always; margin-top: 26px;">Detalle por departamento</h2>
        <table class="datos">
            <tr>
                <th>Departamento</th>
                @foreach ($metricas as $metrica)
                    <th class="num">{{ $metrica->etiqueta() }}</th>
                @endforeach
            </tr>
            @foreach ($departamentos as $departamento)
                <tr>
                    <td>{{ $departamento['nombre'] }}</td>
                    @foreach ($metricas as $metrica)
                        <td class="num">{{ number_format($departamento['metricas'][$metrica->value]) }}</td>
                    @endforeach
                </tr>
            @endforeach
            <tr class="total">
                <td>Total del país</td>
                @foreach ($metricas as $metrica)
                    <td class="num">{{ number_format($totales[$metrica->value]) }}</td>
                @endforeach
            </tr>
        </table>

        <h2>Principales bienes y servicios por departamento</h2>
        <table class="datos">
            <tr><th style="width: 110px;">Departamento</th><th>Más frecuentes (registros)</th></tr>
            @forelse ($conBienes as $departamento)
                <tr>
                    <td>{{ $departamento['nombre'] }}</td>
                    <td>{{ collect($departamento['top_bienes_servicios'])->take(3)->map(fn ($item) => $item['nombre'].' ('.number_format($item['cantidad']).')')->implode(' · ') }}</td>
                </tr>
            @empty
                <tr><td colspan="2" class="apagado">Aún no hay bienes ni servicios registrados con estos filtros.</td></tr>
            @endforelse
        </table>
    </div>
    @include('pdf.partials.pie')
</body>
</html>
