<?php

namespace App\Http\Requests\Estudiante;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Base de las solicitudes de los ejes. El acceso al expediente ya lo controla la ruta
 * (can:view,expediente), por lo que aquí solo se valida el contenido.
 */
abstract class EjeRequest extends FormRequest
{
    /**
     * Máximo de caracteres para campos de contenido: unas 6 000 palabras, con holgura sobre las
     * 4 000 previstas y dentro de la capacidad de una columna TEXT (65 535 bytes).
     */
    protected const int LIMITE_TEXTO_LARGO = 40000;

    public function authorize(): bool
    {
        return true;
    }
}
