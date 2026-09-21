<?php

namespace App\Http\Requests\Estudiante;

use App\Enums\TipoActividadTransferencia;
use App\Enums\TipoCatalogo;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rule;

class TransferenciaConocimientoRequest extends EjeRequest
{
    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'catalogo_id' => ['nullable', 'integer', Rule::exists('catalogos', 'id')->where('catalogo', TipoCatalogo::Acciones->value)],
            'tipo_actividad' => ['required', Rule::enum(TipoActividadTransferencia::class)],
            'actividad' => ['required', 'string', 'max:'.self::LIMITE_TEXTO_LARGO],
            'comunidad' => ['required', 'string', 'max:255'],
            'numero_participantes' => ['nullable', 'integer', 'min:0', 'max:1000000'],
            'fecha' => ['required', 'date', 'before_or_equal:today'],
        ];
    }
}
