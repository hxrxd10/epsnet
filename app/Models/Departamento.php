<?php

namespace App\Models;

use Database\Factories\DepartamentoFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $codigo
 * @property string $nombre
 * @property string $cabecera
 * @property string|null $latitud
 * @property string|null $longitud
 */
#[Fillable(['codigo', 'nombre', 'cabecera', 'latitud', 'longitud'])]
class Departamento extends Model
{
    /** @use HasFactory<DepartamentoFactory> */
    use HasFactory;
}
