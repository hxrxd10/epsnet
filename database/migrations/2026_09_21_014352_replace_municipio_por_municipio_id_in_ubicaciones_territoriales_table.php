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
     */
    public function up(): void
    {
        Schema::table('ubicaciones_territoriales', function (Blueprint $table) {
            $table->dropIndex(['departamento_id', 'municipio']);
            $table->foreignId('municipio_id')->nullable()->after('departamento_id')->constrained('municipios')->restrictOnDelete();
        });

        $catalogo = DB::table('municipios')->get(['id', 'departamento_id', 'nombre'])
            ->mapWithKeys(fn (object $municipio): array => [$municipio->departamento_id.'|'.$this->clave($municipio->nombre) => $municipio->id]);

        DB::table('ubicaciones_territoriales')->get(['id', 'departamento_id', 'municipio'])->each(function (object $ubicacion) use ($catalogo): void {
            $municipioId = $catalogo[$ubicacion->departamento_id.'|'.$this->clave($ubicacion->municipio)] ?? null;

            if ($municipioId !== null) {
                DB::table('ubicaciones_territoriales')->where('id', $ubicacion->id)->update(['municipio_id' => $municipioId]);
            }
        });

        Schema::table('ubicaciones_territoriales', function (Blueprint $table) {
            $table->dropColumn('municipio');
            $table->index(['departamento_id', 'municipio_id']);
        });
    }

    public function down(): void
    {
        Schema::table('ubicaciones_territoriales', function (Blueprint $table) {
            $table->dropIndex(['departamento_id', 'municipio_id']);
            $table->string('municipio', 150)->default('')->after('departamento_id');
        });

        DB::table('ubicaciones_territoriales')->whereNotNull('municipio_id')->get(['id', 'municipio_id'])->each(function (object $ubicacion): void {
            DB::table('ubicaciones_territoriales')->where('id', $ubicacion->id)->update([
                'municipio' => DB::table('municipios')->where('id', $ubicacion->municipio_id)->value('nombre'),
            ]);
        });

        Schema::table('ubicaciones_territoriales', function (Blueprint $table) {
            $table->dropConstrainedForeignId('municipio_id');
            $table->index(['departamento_id', 'municipio']);
        });
    }

    private function clave(string $nombre): string
    {
        return Str::lower(Str::ascii(Str::squish($nombre)));
    }
};
