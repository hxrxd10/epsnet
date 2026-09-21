<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ClaveRol;
use App\Enums\TipoCambioBitacora;
use App\Http\Controllers\Controller;
use App\Models\Bitacora;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class BitacoraController extends Controller
{
    /**
     * Consulta de la bitácora: solo lectura, con filtros por usuario, módulo, tipo de cambio y fechas.
     */
    public function index(Request $request): Response
    {
        $filtros = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'modulo' => ['nullable', 'string', 'max:100'],
            'tipo' => ['nullable', Rule::enum(TipoCambioBitacora::class)],
            'desde' => ['nullable', 'date'],
            'hasta' => ['nullable', 'date', 'after_or_equal:desde'],
        ]);

        $registros = Bitacora::query()
            ->when($filtros['q'] ?? null, fn (Builder $consulta, string $texto) => $consulta->where(
                fn (Builder $consulta) => $consulta
                    ->where('usuario_nombre', 'like', "%{$texto}%")
                    ->orWhere('usuario_correo', 'like', "%{$texto}%")
                    ->orWhere('detalle', 'like', "%{$texto}%"),
            ))
            ->when($filtros['modulo'] ?? null, fn (Builder $consulta, string $modulo) => $consulta->where('modulo', $modulo))
            ->when($filtros['tipo'] ?? null, fn (Builder $consulta, string $tipo) => $consulta->where('tipo_cambio', $tipo))
            ->when($filtros['desde'] ?? null, fn (Builder $consulta, string $desde) => $consulta->where('fecha_hora', '>=', "{$desde} 00:00:00"))
            ->when($filtros['hasta'] ?? null, fn (Builder $consulta, string $hasta) => $consulta->where('fecha_hora', '<=', "{$hasta} 23:59:59"))
            ->orderByDesc('fecha_hora')
            ->orderByDesc('id')
            ->paginate(25)
            ->withQueryString();

        return Inertia::render('admin/bitacora', [
            'registros' => $registros->getCollection()->map(fn (Bitacora $registro): array => [
                'id' => $registro->id,
                'fecha' => $registro->fecha_hora->format('Y-m-d H:i:s'),
                'usuario' => $registro->usuario_nombre,
                'correo' => $registro->usuario_correo,
                'rol' => $registro->usuario_rol === null ? null : (ClaveRol::tryFrom($registro->usuario_rol)?->etiqueta() ?? $registro->usuario_rol),
                'tipo' => $registro->tipo_cambio->value,
                'tipo_etiqueta' => $registro->tipo_cambio->etiqueta(),
                'modulo' => $registro->modulo,
                'detalle' => $registro->detalle,
                'anteriores' => $registro->valores_anteriores,
                'nuevos' => $registro->valores_nuevos,
                'ip' => $registro->ip,
            ])->values(),
            'pagina' => [
                'actual' => $registros->currentPage(),
                'ultima' => $registros->lastPage(),
                'total' => $registros->total(),
                'anterior' => $registros->previousPageUrl(),
                'siguiente' => $registros->nextPageUrl(),
            ],
            'filtros' => [
                'q' => $filtros['q'] ?? '',
                'modulo' => $filtros['modulo'] ?? '',
                'tipo' => $filtros['tipo'] ?? '',
                'desde' => $filtros['desde'] ?? '',
                'hasta' => $filtros['hasta'] ?? '',
            ],
            'modulos' => Bitacora::query()->distinct()->orderBy('modulo')->pluck('modulo'),
            'tipos' => TipoCambioBitacora::opciones(),
        ]);
    }
}
