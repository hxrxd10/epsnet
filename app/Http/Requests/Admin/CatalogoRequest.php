<?php

namespace App\Http\Requests\Admin;

use App\Enums\TipoCatalogo;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CatalogoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        /** @var TipoCatalogo $catalogo */
        $catalogo = $this->route('catalogo');

        return [
            'nombre' => [
                'required',
                'string',
                'max:255',
                Rule::unique('catalogos', 'nombre')
                    ->where('catalogo', $catalogo->value)
                    ->ignore($this->route('item')),
            ],
            'descripcion' => ['nullable', 'string', 'max:5000'],
            'categoria' => [
                Rule::requiredIf($catalogo->usaCategoria()),
                'nullable',
                Rule::in(array_column($catalogo->categorias(), 'value')),
            ],
            'activo' => ['required', 'boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nombre.unique' => 'Ya existe un elemento con este nombre en el catálogo.',
            'categoria.required' => 'Selecciona la categoría.',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'nombre' => 'el nombre',
            'descripcion' => 'la descripción',
            'categoria' => 'la categoría',
            'activo' => 'el estado',
        ];
    }
}
