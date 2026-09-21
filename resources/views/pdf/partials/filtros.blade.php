<table class="filtros">
    <tr>
        @foreach ($filtros as $nombre => $valor)
            <td><div class="clave">{{ $nombre }}</div>{{ $valor }}</td>
        @endforeach
    </tr>
</table>
