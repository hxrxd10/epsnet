<?php

namespace App\Http\Requests\Admin;

use App\Concerns\ValidaCoordenadas;
use App\Models\Departamento;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MunicipioRequest extends FormRequest
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
        $municipio = $this->route('municipio');

        return [
            'departamento_id' => ['required', 'integer', 'exists:departamentos,id'],
            'codigo' => [
                'required',
                'string',
                'regex:/^\d{4}$/',
                Rule::unique('municipios', 'codigo')->ignore($municipio),
                function (string $atributo, mixed $valor, Closure $fallar): void {
                    $departamento = Departamento::find($this->input('departamento_id'));

                    if ($departamento !== null && ! str_starts_with((string) $valor, $departamento->codigo)) {
                        $fallar("El código debe empezar con el del departamento ({$departamento->codigo}).");
                    }
                },
            ],
            'nombre' => [
                'required',
                'string',
                'max:150',
                Rule::unique('municipios', 'nombre')->where('departamento_id', $this->input('departamento_id'))->ignore($municipio),
            ],
            ...$this->reglasCoordenadas(),
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'codigo.regex' => 'El código son cuatro dígitos: el del departamento y el del municipio, por ejemplo 0101.',
            'codigo.unique' => 'Ya existe un municipio con este código.',
            'nombre.unique' => 'Este departamento ya tiene un municipio con ese nombre.',
            ...$this->mensajesCoordenadas(),
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return ['departamento_id' => 'el departamento', 'codigo' => 'el código', 'nombre' => 'el nombre'];
    }
}
