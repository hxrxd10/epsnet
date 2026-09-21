<?php

namespace App\Http\Requests\Estudiante;

use App\Services\RegistroAcademico\DatosAcademicos;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class AccesoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * El registro académico y el DPI se aceptan con espacios o guiones.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'registro_academico' => DatosAcademicos::soloDigitos((string) $this->input('registro_academico')),
            'dpi' => DatosAcademicos::soloDigitos((string) $this->input('dpi')),
        ]);
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'registro_academico' => ['required', 'string', 'regex:/^\d{6,12}$/'],
            'dpi' => ['required', 'string', 'digits:13'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'registro_academico.regex' => 'El registro académico solo debe contener números (entre 6 y 12 dígitos).',
            'dpi.digits' => 'El DPI debe tener 13 dígitos.',
        ];
    }
}
