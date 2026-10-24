<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Carreras que DIGEU agrega desde la consulta de un carné: se identifican por su clave
 * «unidad-extensión-carrera» y sus datos se vuelven a tomar del registro académico.
 */
class CarreraRequest extends FormRequest
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
        return [
            'carnet' => ['required', 'string', 'regex:/^\d{4,15}$/'],
            'claves' => ['required', 'array', 'min:1'],
            'claves.*' => ['string', 'distinct', 'max:40'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'carnet.required' => 'Escribe el carné que consultaste.',
            'carnet.regex' => 'El carné solo lleva números.',
            'claves.required' => 'Selecciona al menos una carrera para agregar.',
            'claves.min' => 'Selecciona al menos una carrera para agregar.',
        ];
    }
}
