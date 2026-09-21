<?php

namespace App\Http\Requests\Estudiante;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class OrdenImpresionRequest extends FormRequest
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
            'orden_impresion' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'orden_impresion.required' => 'Selecciona el archivo de tu orden de impresión.',
            'orden_impresion.mimes' => 'La orden de impresión debe ser un PDF o una imagen (JPG o PNG).',
            'orden_impresion.max' => 'La orden de impresión no puede pesar más de 10 MB.',
            'orden_impresion.uploaded' => 'No se pudo subir el archivo. Verifica que pese menos de 10 MB e inténtalo de nuevo.',
        ];
    }
}
