<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Estadísticas de {{ $departamento->nombre }}</title>
    @include('pdf.partials.estilos')
</head>
<body>
    @include('pdf.partials.encabezado', ['titulo' => $departamento->nombre, 'subtitulo' => 'Estadísticas del departamento · Cabecera: '.$departamento->cabecera])
    <div class="contenido">
        @include('pdf.partials.filtros')

        <table style="width: 100%; border-collapse: collapse; margin-top: 4px;">
            <tr>
                <td style="vertical-align: top;">
                    <h2>Totales del departamento</h2>
                    <table class="cajas" style="margin: 0 -5px;">
                        @foreach (array_chunk($metricas, 2) as $fila)
                            <tr>
                                @foreach ($fila as $metrica)
                                    <td style="width: 50%;">
                                        <div class="valor">{{ number_format($totalesDepartamento[$metrica->value]) }}</div>
                                        <div class="nombre">{{ $metrica->etiqueta() }}</div>
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </table>
                </td>
                <td style="width: 170px; vertical-align: top; text-align: right; padding-top: 20px;">
                    <img src="{{ $mapa }}" width="150" alt="Ubicación en el mapa">
                </td>
            </tr>
        </table>

        @if (count($municipios) === 0)
            <p class="apagado" style="margin-top: 18px;">No hay EPS con ubicación en este departamento para los filtros elegidos.</p>
        @endif

        @foreach ($municipios as $municipio)
            <div class="municipio">
                <h2>
                    {{ $municipio['nombre'] }}
                    <span class="apagado" style="font-size: 8.5px; font-weight: normal;">
                        · {{ number_format($municipio['metricas']['eps']) }} EPS
                        · {{ number_format($municipio['metricas']['beneficiarios']) }} beneficiarios
                        · {{ number_format($municipio['metricas']['acciones']) }} acciones
                    </span>
                </h2>

                <div class="etiqueta">Bienes y servicios</div>
                @if (count($municipio['bienes_servicios']) === 0)
                    <p class="apagado">No hay bienes ni servicios registrados.</p>
                @else
                    <table class="datos">
                        <tr><th>Bien o servicio</th><th>Tipo</th><th class="num">Registros</th><th class="num">Beneficiarios</th></tr>
                        @foreach ($municipio['bienes_servicios'] as $item)
                            <tr>
                                <td>{{ $item['nombre'] }}</td>
                                <td>{{ $item['tipo'] === 'bien' ? 'Bien' : 'Servicio' }}</td>
                                <td class="num">{{ number_format($item['cantidad']) }}</td>
                                <td class="num">{{ number_format($item['beneficiarios']) }}</td>
                            </tr>
                        @endforeach
                    </table>
                @endif

                <h3>Investigaciones ({{ count($municipio['investigaciones']) }})</h3>
                @forelse ($municipio['investigaciones'] as $investigacion)
                    <div class="invest">
                        <div class="titulo">{{ $investigacion['titulo'] }} <span class="insignia">{{ $investigacion['tipo'] }}</span></div>
                        <div class="apagado">
                            {{ $investigacion['autores'] }}
                            @if ($investigacion['medio']) · {{ $investigacion['medio'] }} @endif
                            @if ($investigacion['fecha']) · {{ \Illuminate\Support\Carbon::parse($investigacion['fecha'])->format('d-m-Y') }} @endif
                        </div>
                        <div class="apagado">{{ $investigacion['carrera'] }} · {{ $investigacion['unidad'] }}</div>
                    </div>
                @empty
                    <p class="apagado">No hay investigaciones registradas.</p>
                @endforelse

                <h3>Instituciones aliadas ({{ count($municipio['instituciones']) }})</h3>
                @forelse ($municipio['instituciones'] as $institucion)
                    <div style="margin: 0 0 3px 0;">
                        {{ $institucion['nombre'] }}
                        @if ($institucion['tipo']) <span class="apagado">· {{ $institucion['tipo'] }}</span> @endif
                        <span class="apagado">· {{ $institucion['eps'] }} EPS</span>
                    </div>
                @empty
                    <p class="apagado">No hay instituciones aliadas registradas.</p>
                @endforelse
            </div>
        @endforeach
    </div>
    @include('pdf.partials.pie')
</body>
</html>
