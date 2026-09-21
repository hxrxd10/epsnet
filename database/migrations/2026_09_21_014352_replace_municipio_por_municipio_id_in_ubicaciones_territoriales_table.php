<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * El municipio deja de ser texto libre y pasa a ser una referencia al catálogo. Los textos ya
     * capturados se enlazan por nombre (sin distinguir mayúsculas ni acentos) dentro de su departamento;
     * los que no coinciden quedan sin municipio.
     *
     * El índice nuevo se crea antes de borrar el viejo: la llave foránea de `departamento_id` necesita
     * siempre un índice que empiece por esa columna (MySQL/MariaDB no permiten quitar el último).
     */
    public function up(): void
    {
        if (! Schema::hasColumn('ubicaciones_territoriales', 'municipio_id')) {
            Schema::table('ubicaciones_territoriales', function (Blueprint $table) {
                $table->foreignId('municipio_id')->nullable()->after('departamento_id')->constrained('municipios')->restrictOnDelete();
                $table->index(['departamento_id', 'municipio_id']);
            });
        }

        if (Schema::hasColumn('ubicaciones_territoriales', 'municipio')) {
            $this->enlazarMunicipiosEscritos();

            Schema::table('ubicaciones_territoriales', function (Blueprint $table) {
                $table->dropIndex(['departamento_id', 'municipio']);
            });

            Schema::table('ubicaciones_territoriales', function (Blueprint $table) {
                $table->dropColumn('municipio');
            });
        }
    }

    public function down(): void
    {
        Schema::table('ubicaciones_territoriales', function (Blueprint $table) {
            $table->string('municipio', 150)->default('')->after('departamento_id');
            $table->index(['departamento_id', 'municipio']);
        });

        DB::table('ubicaciones_territoriales')->whereNotNull('municipio_id')->get(['id', 'municipio_id'])->each(function (object $ubicacion): void {
            DB::table('ubicaciones_territoriales')->where('id', $ubicacion->id)->update([
                'municipio' => DB::table('municipios')->where('id', $ubicacion->municipio_id)->value('nombre'),
            ]);
        });

        Schema::table('ubicaciones_territoriales', function (Blueprint $table) {
            $table->dropIndex(['departamento_id', 'municipio_id']);
        });

        Schema::table('ubicaciones_territoriales', function (Blueprint $table) {
            $table->dropConstrainedForeignId('municipio_id');
        });
    }

    private function enlazarMunicipiosEscritos(): void
    {
        $catalogo = DB::table('municipios')->get(['id', 'departamento_id', 'nombre'])
            ->mapWithKeys(fn (object $municipio): array => [$municipio->departamento_id.'|'.$this->clave($municipio->nombre) => $municipio->id]);

        DB::table('ubicaciones_territoriales')->get(['id', 'departamento_id', 'municipio'])->each(function (object $ubicacion) use ($catalogo): void {
            $municipioId = $catalogo[$ubicacion->departamento_id.'|'.$this->clave($ubicacion->municipio)] ?? null;

            if ($municipioId !== null) {
                DB::table('ubicaciones_territoriales')->where('id', $ubicacion->id)->update(['municipio_id' => $municipioId]);
            }
        });
    }

    private function clave(string $nombre): string
    {
        return Str::lower(Str::ascii(Str::squish($nombre)));
    }
};
