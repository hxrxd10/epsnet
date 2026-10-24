<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rule;

/**
 * Cuenta que crea DIGEU (p. ej. un supervisor de EPS de una unidad académica): además del acceso,
 * el nombre y el correo al que se envía el enlace para definir la contraseña.
 */
class CrearUsuarioRequest extends UsuarioRequest
{
    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users', 'email')],
            ...parent::rules(),
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            ...parent::messages(),
            'name.required' => 'Escribe el nombre de la persona.',
            'email.required' => 'Escribe el correo de la persona.',
            'email.unique' => 'Ya existe una cuenta con este correo. Búscala en la lista y edita su acceso.',
        ];
    }
}
