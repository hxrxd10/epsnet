<?php

namespace App\Http\Requests\Estudiante;

use App\Enums\TipoPublicacion;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rule;

class PublicacionInvestigacionRequest extends EjeRequest
{
    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'titulo' => ['required', 'string', 'max:'.self::LIMITE_TEXTO_LARGO],
            'tipo' => ['required', Rule::enum(TipoPublicacion::class)],
            'autores' => ['required', 'string', 'max:'.self::LIMITE_TEXTO_LARGO],
            'medio_publicacion' => ['nullable', 'string', 'max:500'],
            'resumen' => ['nullable', 'string', 'max:'.self::LIMITE_TEXTO_LARGO],
            'enlace' => ['nullable', 'url', 'max:2048'],
            'fecha_publicacion' => ['nullable', 'date', 'before_or_equal:today'],
        ];
    }
}
