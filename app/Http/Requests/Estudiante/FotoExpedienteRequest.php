<?php

namespace App\Http\Requests\Estudiante;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class FotoExpedienteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * El navegador reduce la foto antes de enviarla; el límite cubre también a quien la suba sin reducir.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'foto' => ['required', 'file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120', 'dimensions:min_width=300,min_height=200'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'foto.required' => 'Selecciona la foto de tu EPS.',
            'foto.image' => 'La foto debe ser una imagen JPG, PNG o WebP.',
            'foto.mimes' => 'La foto debe ser una imagen JPG, PNG o WebP.',
            'foto.max' => 'La foto no puede pesar más de 5 MB.',
            'foto.dimensions' => 'La foto es muy pequeña: debe medir al menos 300 × 200 píxeles.',
            'foto.uploaded' => 'No se pudo subir la foto. Verifica que pese menos de 5 MB e inténtalo de nuevo.',
        ];
    }
}
