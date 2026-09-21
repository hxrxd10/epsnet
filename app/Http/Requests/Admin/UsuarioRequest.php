<?php

namespace App\Http\Requests\Admin;

use App\Enums\ClaveRol;
use App\Models\UnidadAcademica;
use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UsuarioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Roles que DIGEU puede asignar; el de estudiante solo se obtiene al verificar la identidad.
     *
     * @return list<ClaveRol>
     */
    public static function rolesAsignables(): array
    {
        return [ClaveRol::Invitado, ClaveRol::UnidadAcademica, ClaveRol::Digeu];
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'rol' => ['required', Rule::enum(ClaveRol::class)->only(self::rolesAsignables())],
            'unidad_academica_id' => [
                Rule::requiredIf($this->input('rol') === ClaveRol::UnidadAcademica->value),
                'nullable',
                'integer',
                Rule::exists('unidades_academicas', 'id')->whereNull('deleted_at'),
            ],
        ];
    }

    /**
     * Una unidad académica tiene un solo administrador.
     *
     * @return list<callable(Validator): void>
     */
    public function after(): array
    {
        return [
            function (Validator $validador): void {
                if ($validador->errors()->isNotEmpty() || $this->input('rol') !== ClaveRol::UnidadAcademica->value) {
                    return;
                }

                /** @var User $usuario */
                $usuario = $this->route('usuario');
                $administrador = UnidadAcademica::find($this->input('unidad_academica_id'))?->administrador;

                if ($administrador !== null && $administrador->id !== $usuario->id) {
                    $validador->errors()->add('unidad_academica_id', "Esa unidad ya la administra {$administrador->name}. Cámbiale o quítale la unidad primero.");
                }
            },
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'rol.required' => 'Selecciona el rol.',
            'rol.enum' => 'El rol elegido no se puede asignar.',
            'unidad_academica_id.required' => 'Selecciona la unidad académica que administrará.',
        ];
    }
}
