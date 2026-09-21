<?php

namespace App\Http\Requests\Estudiante;

use App\Enums\TipoBienServicio;
use App\Enums\TipoCatalogo;
use App\Models\Catalogo;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class BienServicioRequest extends EjeRequest
{
    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'catalogo_id' => ['nullable', 'integer', Rule::exists('catalogos', 'id')->where('catalogo', TipoCatalogo::BienesServicios->value)],
            'tipo' => ['required_without:catalogo_id', 'nullable', Rule::enum(TipoBienServicio::class)],
            'descripcion' => ['required', 'string', 'max:'.self::LIMITE_TEXTO_LARGO],
            'beneficiarios' => ['nullable', 'string', 'max:'.self::LIMITE_TEXTO_LARGO],
            'cantidad_beneficiarios' => ['nullable', 'integer', 'min:0', 'max:100000000'],
            'fecha' => ['required', 'date', 'before_or_equal:today'],
        ];
    }

    /**
     * Un elemento del catálogo sin categoría exige indicar si es un bien o un servicio.
     *
     * @return list<callable(Validator): void>
     */
    public function after(): array
    {
        return [
            function (Validator $validador): void {
                if ($validador->errors()->isNotEmpty() || blank($this->input('catalogo_id')) || filled($this->input('tipo'))) {
                    return;
                }

                if (Catalogo::find($this->input('catalogo_id'))?->categoria === null) {
                    $validador->errors()->add('tipo', 'Selecciona si es un bien o un servicio.');
                }
            },
        ];
    }

    /**
     * Datos a guardar: si se eligió un elemento del catálogo, su categoría define el tipo.
     *
     * @return array<string, mixed>
     */
    public function datos(): array
    {
        $datos = $this->validated();

        if (filled($datos['catalogo_id'] ?? null)) {
            $categoria = Catalogo::find($datos['catalogo_id'])?->categoria;

            $datos['tipo'] = $categoria ?? $datos['tipo'];
        }

        return $datos;
    }
}
