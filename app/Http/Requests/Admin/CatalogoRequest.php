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
                'max:500',
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
            'valor_base' => $catalogo->usaValorBase()
                ? ['nullable', 'numeric', 'min:0', 'max:9999999999.99', 'decimal:0,2']
                : ['prohibited'],
            ...$this->reglasVinculos($catalogo),
            'activo' => ['required', 'boolean'],
        ];
    }

    /**
     * Los bienes y servicios exigen su línea, política, área y ODS, elegidos de su catálogo; los demás
     * catálogos no llevan vínculos.
     *
     * @return array<string, array<mixed>>
     */
    private function reglasVinculos(TipoCatalogo $catalogo): array
    {
        $vinculos = $catalogo->vinculos();

        return collect(TipoCatalogo::BienesServicios->vinculos())
            ->mapWithKeys(fn (TipoCatalogo $origen, string $campo): array => [
                $campo => isset($vinculos[$campo])
                    ? ['required', 'integer', Rule::exists('catalogos', 'id')->where('catalogo', $origen->value)]
                    : ['prohibited'],
            ])
            ->all();
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nombre.unique' => 'Ya existe un elemento con este nombre en el catálogo.',
            'categoria.required' => 'Selecciona la categoría.',
            'linea_intervencion_id.required' => 'Selecciona la línea de intervención.',
            'politica_publica_id.required' => 'Selecciona la política pública.',
            'area_intervencion_id.required' => 'Selecciona el área de intervención.',
            'ods_id.required' => 'Selecciona el ODS.',
            'valor_base.numeric' => 'El valor base debe ser una cantidad en quetzales.',
            'valor_base.min' => 'El valor base no puede ser negativo.',
            'valor_base.decimal' => 'El valor base lleva como máximo dos decimales.',
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
            'valor_base' => 'el valor base',
            'linea_intervencion_id' => 'la línea de intervención',
            'politica_publica_id' => 'la política pública',
            'area_intervencion_id' => 'el área de intervención',
            'ods_id' => 'el ODS',
            'activo' => 'el estado',
        ];
    }
}
