<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * La institución receptora (donde el estudiante hace su EPS) deja de ser un catálogo: la escribe el
     * estudiante. Se conservan los nombres que ya tenían los EPS y el catálogo anterior se elimina; el
     * catálogo de DIGEU ahora es el de instituciones aliadas.
     */
    public function up(): void
    {
        Schema::table('actores_participantes', function (Blueprint $table) {
            $table->string('institucion_receptora')->nullable()->after('expediente_id');
        });

        DB::table('actores_participantes')->get(['id', 'institucion_receptora_id'])->each(function (object $actor): void {
            DB::table('actores_participantes')->where('id', $actor->id)->update([
                'institucion_receptora' => DB::table('instituciones_receptoras')->where('id', $actor->institucion_receptora_id)->value('nombre') ?? 'Sin nombre',
            ]);
        });

        Schema::table('actores_participantes', function (Blueprint $table) {
            $table->dropConstrainedForeignId('institucion_receptora_id');
        });

        Schema::table('actores_participantes', function (Blueprint $table) {
            $table->string('institucion_receptora')->nullable(false)->change();
        });

        Schema::dropIfExists('instituciones_receptoras');
    }

    public function down(): void
    {
        Schema::create('instituciones_receptoras', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique();
            $table->string('tipo', 100)->nullable();
            $table->string('nombre_contacto')->nullable();
            $table->string('correo_contacto')->nullable();
            $table->string('telefono_contacto', 30)->nullable();
            $table->string('direccion', 500)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('actores_participantes', function (Blueprint $table) {
            $table->foreignId('institucion_receptora_id')->nullable()->after('expediente_id')->constrained('instituciones_receptoras')->restrictOnDelete();
        });

        DB::table('actores_participantes')->distinct()->pluck('institucion_receptora')->each(function (string $nombre): void {
            $id = DB::table('instituciones_receptoras')->insertGetId(['nombre' => $nombre, 'created_at' => now(), 'updated_at' => now()]);

            DB::table('actores_participantes')->where('institucion_receptora', $nombre)->update(['institucion_receptora_id' => $id]);
        });

        Schema::table('actores_participantes', function (Blueprint $table) {
            $table->dropColumn('institucion_receptora');
        });
    }
};
