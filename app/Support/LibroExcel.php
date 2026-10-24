<?php

namespace App\Support;

use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Writer\XLSX\Entity\SheetView;
use OpenSpout\Writer\XLSX\Writer;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Libro de Excel (.xlsx) con una o varias hojas: encabezado resaltado y fijo, y formato según el tipo
 * de cada columna (texto, entero, quetzales, porcentaje). Se escribe en un archivo temporal que se
 * elimina al terminar la descarga.
 */
final class LibroExcel
{
    private const string AZUL = '0F1031';

    private Writer $escritor;

    private string $ruta;

    private bool $sinHojas = true;

    public function __construct()
    {
        $this->ruta = (string) tempnam(sys_get_temp_dir(), 'sirse-xlsx-');
        $this->escritor = new Writer;
        $this->escritor->openToFile($this->ruta);
    }

    /**
     * Agrega una hoja. Las columnas indican su título y su tipo; cada fila trae los valores en el mismo orden.
     *
     * @param  list<array{0: string, 1?: 'texto'|'entero'|'quetzales'|'decimal'|'porcentaje'}>  $columnas
     * @param  iterable<array<int, string|int|float|null>>  $filas
     * @param  list<array{0: string, 1: string}>  $encabezado  Pares «etiqueta: valor» antes de la tabla (p. ej. los filtros).
     */
    public function hoja(string $nombre, array $columnas, iterable $filas, array $encabezado = []): self
    {
        $hoja = $this->sinHojas ? $this->escritor->getCurrentSheet() : $this->escritor->addNewSheetAndMakeItCurrent();
        $this->sinHojas = false;
        $hoja->setName(self::nombreDeHoja($nombre));

        $negrita = (new Style)->withFontBold(true);

        foreach ($encabezado as [$etiqueta, $valor]) {
            $this->escritor->addRow(Row::fromValuesWithStyles([$etiqueta, $valor], [$negrita]));
        }

        if ($encabezado !== []) {
            $this->escritor->addRow(Row::fromValues([]));
        }

        $titulo = (new Style)->withFontBold(true)->withFontColor('FFFFFF')->withBackgroundColor(self::AZUL)->withShouldWrapText(true);
        $this->escritor->addRow(Row::fromValuesWithStyles(
            array_map(fn (array $columna): string => $columna[0], $columnas),
            array_fill(0, count($columnas), $titulo),
        ));
        $hoja->setSheetView(new SheetView(freezeRow: count($encabezado) + ($encabezado === [] ? 0 : 1) + 2));

        $estilos = array_map(fn (array $columna): ?Style => match ($columna[1] ?? 'texto') {
            'entero' => (new Style)->withFormat('#,##0'),
            'quetzales' => (new Style)->withFormat('"Q"#,##0.00'),
            'decimal' => (new Style)->withFormat('#,##0.00'),
            'porcentaje' => (new Style)->withFormat('0%'),
            default => null,
        }, $columnas);

        foreach ($filas as $fila) {
            $this->escritor->addRow(Row::fromValuesWithStyles(array_values($fila), array_filter($estilos)));
        }

        foreach ($columnas as $indice => $columna) {
            $hoja->setColumnWidth(($columna[1] ?? 'texto') === 'texto' ? 38 : 16, $indice + 1);
        }

        return $this;
    }

    /**
     * Cierra el libro y lo entrega como descarga.
     */
    public function descargar(string $nombreArchivo): BinaryFileResponse
    {
        $this->escritor->close();

        return response()
            ->download($this->ruta, $nombreArchivo, ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'])
            ->deleteFileAfterSend();
    }

    /**
     * Excel no admite más de 31 caracteres ni los signos [ ] : * ? / \ en el nombre de una hoja.
     */
    private static function nombreDeHoja(string $nombre): string
    {
        return mb_substr(trim((string) preg_replace('/[\[\]:*?\/\\\\]+/', ' ', $nombre)), 0, 31);
    }
}
