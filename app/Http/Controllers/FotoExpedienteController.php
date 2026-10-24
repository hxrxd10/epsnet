<?php

namespace App\Http\Controllers;

use App\Models\Expediente;
use Illuminate\Http\Response;

class FotoExpedienteController extends Controller
{
    /**
     * Muestra la foto del EPS (la autorización la da la ruta: `can:verFoto`).
     */
    public function show(Expediente $expediente): Response
    {
        return $expediente->foto()->with('contenido')->firstOrFail()->enLinea();
    }
}
