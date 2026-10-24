<?php

namespace Tests\Support;

use Illuminate\Testing\TestResponse;
use OpenSpout\Reader\XLSX\Reader;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Lee el Excel que devolvió una respuesta: nombre de cada hoja => filas (listas de valores).
 */
final class LectorExcel
{
    /**
     * @return array<string, list<list<mixed>>>
     */
    public static function hojas(TestResponse $respuesta): array
    {
        $archivo = $respuesta->baseResponse;
        assert($archivo instanceof BinaryFileResponse);

        $lector = new Reader;
        $lector->open($archivo->getFile()->getPathname());
        $hojas = [];

        foreach ($lector->getSheetIterator() as $hoja) {
            foreach ($hoja->getRowIterator() as $fila) {
                $hojas[$hoja->getName()][] = $fila->toArray();
            }
        }

        $lector->close();

        return $hojas;
    }
}
