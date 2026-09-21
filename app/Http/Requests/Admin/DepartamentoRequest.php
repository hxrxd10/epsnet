<?php

namespace App\Http\Requests\Admin;

use App\Concerns\ValidaCoordenadas;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DepartamentoRequest extends FormRequest
{
    use ValidaCoordenadas;

    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $departamento = $this->route('departamento');

        return [
            'codigo' => ['required', 'string', 'regex:/^\d{2}$/', Rule::unique('departamentos', 'codigo')->ignore($departamento)],
            'nombre' => ['required', 'string', 'max:100', Rule::unique('departamentos', 'nombre')->ignore($departamento)],
            'cabecera' => ['required', 'string', 'max:100'],
            ...$this->reglasCoordenadas(),
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'codigo.regex' => 'El código son dos dígitos, por ejemplo 01.',
            'codigo.unique' => 'Ya existe un departamento con este código.',
            'nombre.unique' => 'Ya existe un departamento con este nombre.',
            ...$this->mensajesCoordenadas(),
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return ['codigo' => 'el código', 'nombre' => 'el nombre', 'cabecera' => 'la cabecera'];
    }
}
