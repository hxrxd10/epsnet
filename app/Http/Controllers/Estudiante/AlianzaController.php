<?php

namespace App\Http\Controllers\Estudiante;

use App\Concerns\ArmaPasoDelExpediente;
use App\Enums\Eje;
use App\Http\Controllers\Controller;
use App\Http\Requests\Estudiante\AlianzaRequest;
use App\Models\Expediente;
use Illuminate\Http\RedirectResponse;

class AlianzaController extends Controller
{
    use ArmaPasoDelExpediente;

    public function store(AlianzaRequest $request, Expediente $expediente): RedirectResponse
    {
        $expediente->alianzas()->create($request->validated());

        return $this->guardado($expediente, Eje::Actores, 'Institución aliada registrada.');
    }

    public function update(AlianzaRequest $request, Expediente $expediente, int $registro): RedirectResponse
    {
        $expediente->alianzas()->findOrFail($registro)->update($request->validated());

        return $this->guardado($expediente, Eje::Actores, 'Institución aliada actualizada.');
    }

    public function destroy(Expediente $expediente, int $registro): RedirectResponse
    {
        $expediente->alianzas()->findOrFail($registro)->delete();

        return $this->guardado($expediente, Eje::Actores, 'Institución aliada eliminada.');
    }
}
