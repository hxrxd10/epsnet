<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Expediente;
use Symfony\Component\HttpFoundation\StreamedResponse;

class OrdenImpresionController extends Controller
{
    /**
     * Descarga la orden de impresión de un EPS para su revisión.
     */
    public function show(Expediente $expediente): StreamedResponse
    {
        return $expediente->ordenImpresion()->with('contenido')->firstOrFail()->descarga();
    }
}
