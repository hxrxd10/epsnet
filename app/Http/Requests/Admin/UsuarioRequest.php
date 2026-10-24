<?php

namespace App\Http\Requests\Admin;

use App\Enums\ClaveRol;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
     * Una unidad académica puede tener varios usuarios; cada uno ve toda su unidad o solo las carreras
     * que se le asignen (que deben ser de esa unidad).
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $esUnidad = $this->input('rol') === ClaveRol::UnidadAcademica->value;

        return [
            'rol' => ['required', Rule::enum(ClaveRol::class)->only(self::rolesAsignables())],
            'unidad_academica_id' => [
                Rule::requiredIf($esUnidad),
                'nullable',
                'integer',
                Rule::exists('unidades_academicas', 'id')->whereNull('deleted_at'),
            ],
            'carreras' => ['nullable', 'array'],
            'carreras.*' => [
                'integer',
                'distinct',
                Rule::exists('carreras', 'id')->where('unidad_academica_id', $esUnidad ? (int) $this->input('unidad_academica_id') : 0),
            ],
        ];
    }

    /**
     * Rol, unidad y carreras ya normalizados: solo una unidad académica tiene unidad y carreras.
     *
     * @return array{rol: ClaveRol, unidad_academica_id: int|null, carreras: list<int>}
     */
    public function acceso(): array
    {
        $rol = ClaveRol::from($this->validated('rol'));
        $esUnidad = $rol === ClaveRol::UnidadAcademica;

        return [
            'rol' => $rol,
            'unidad_academica_id' => $esUnidad ? (int) $this->validated('unidad_academica_id') : null,
            'carreras' => $esUnidad ? array_values(array_map('intval', $this->validated('carreras') ?? [])) : [],
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
            'unidad_academica_id.required' => 'Selecciona la unidad académica del usuario.',
            'carreras.*.exists' => 'Las carreras deben ser de la unidad académica elegida.',
        ];
    }
}
