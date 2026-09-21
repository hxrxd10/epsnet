<?php

namespace Database\Seeders;

use App\Enums\ClaveRol;
use App\Enums\EstadoExpediente;
use App\Enums\NivelCumplimiento;
use App\Enums\ProgramaEps;
use App\Enums\TipoActividadTransferencia;
use App\Enums\TipoBienServicio;
use App\Enums\TipoCambioBitacora;
use App\Enums\TipoCatalogo;
use App\Enums\TipoPublicacion;
use App\Enums\TipoRegistroSeguimiento;
use App\Enums\TipoUnidadAcademica;
use App\Models\Adjunto;
use App\Models\AdjuntoContenido;
use App\Models\Catalogo;
use App\Models\Departamento;
use App\Models\Estudiante;
use App\Models\Expediente;
use App\Models\InstitucionReceptora;
use App\Models\Municipio;
use App\Models\Rol;
use App\Models\UnidadAcademica;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Datos de demostración para ver el potencial del sistema: unidades académicas, ~320 EPS repartidos por
 * todo el país entre 2023 y 2026, con registros en los seis ejes. Es determinista (siempre genera lo mismo)
 * y no se ejecuta en producción.
 *
 * Uso: php artisan db:seed --class=DatosDemoSeeder
 *
 * Los estudiantes llevan carné "DEMO-…" y las cuentas creadas usan la contraseña "password":
 * admin@epsnet.test (DIGEU), unidad@epsnet.test (Facultad de Humanidades), invitado@epsnet.test y
 * estudiante1@epsnet.test … estudiante8@epsnet.test (estudiantes con su EPS cargado). También deja una bitácora
 * de ejemplo con lo que harían esas cuentas.
 */
class DatosDemoSeeder extends Seeder
{
    private const int TOTAL_EPS = 320;

    private const string PREFIJO_CARNET = 'DEMO-';

    /** Ciudad Universitaria, zona 12. */
    private const array CAMPUS_CENTRAL = [14.5866, -90.5527];

    /** Reparto de EPS por departamento de las unidades que atienden todo el país. */
    private const array PERFIL_NACIONAL = [
        '01' => 12, '03' => 4, '04' => 4, '05' => 3, '06' => 2, '02' => 2, '21' => 2, '22' => 2, '14' => 3,
        '16' => 4, '13' => 4, '12' => 3, '09' => 3, '17' => 2, '18' => 2, '20' => 2, '19' => 1, '10' => 1,
        '11' => 1, '07' => 2, '08' => 2, '15' => 2,
    ];

    /** Perfil de las unidades con vocación rural (más presencia en el norte y el occidente). */
    private const array PERFIL_RURAL = [
        '16' => 6, '17' => 5, '14' => 5, '13' => 5, '15' => 4, '12' => 4, '18' => 3, '20' => 3, '19' => 2,
        '01' => 2, '07' => 2, '08' => 2, '09' => 2, '22' => 2, '21' => 1, '05' => 1,
    ];

    /**
     * Unidades: nombre, siglas, peso (cuántos EPS tiene relativamente), perfil territorial, sede
     * (código de municipio o campus central) y carreras.
     *
     * @var list<array{0: string, 1: string, 2: int, 3: array<string, int>, 4: string, 5: list<string>}>
     */
    private const array UNIDADES = [
        ['Facultad de Humanidades', 'FAHUSAC', 14, self::PERFIL_NACIONAL, 'campus', ['Licenciatura en Pedagogía y Administración Educativa', 'Licenciatura en Letras', 'Licenciatura en Bibliotecología', 'Licenciatura en Filosofía']],
        ['Facultad de Agronomía', 'FAUSAC', 12, self::PERFIL_RURAL, 'campus', ['Ingeniería Agronómica en Sistemas de Producción Agrícola', 'Ingeniería Agronómica en Recursos Naturales Renovables', 'Ingeniería en Gestión Ambiental Local']],
        ['Facultad de Ingeniería', 'FIUSAC', 12, self::PERFIL_NACIONAL, 'campus', ['Ingeniería Civil', 'Ingeniería Industrial', 'Ingeniería en Ciencias y Sistemas']],
        ['Facultad de Ciencias Económicas', 'CCEE', 10, self::PERFIL_NACIONAL, 'campus', ['Contaduría Pública y Auditoría', 'Administración de Empresas', 'Economía']],
        ['Facultad de Ciencias Jurídicas y Sociales', 'DERECHO', 6, self::PERFIL_NACIONAL, 'campus', ['Licenciatura en Ciencias Jurídicas y Sociales, Abogado y Notario']],
        ['Facultad de Ciencias Médicas', 'FCCMM', 8, self::PERFIL_RURAL, 'campus', ['Médico y Cirujano']],
        ['Facultad de Ciencias Químicas y Farmacia', 'CCQQ', 7, self::PERFIL_NACIONAL, 'campus', ['Químico Biólogo', 'Químico Farmacéutico', 'Nutricionista']],
        ['Facultad de Odontología', 'ODONTOLOGIA', 5, self::PERFIL_RURAL, 'campus', ['Cirujano Dentista']],
        ['Facultad de Medicina Veterinaria y Zootecnia', 'FMVZ', 5, self::PERFIL_RURAL, 'campus', ['Médico Veterinario', 'Zootecnista']],
        ['Facultad de Arquitectura', 'FARUSAC', 3, self::PERFIL_NACIONAL, 'campus', ['Arquitectura', 'Diseño Gráfico']],
        ['Escuela de Trabajo Social', 'ETS', 8, self::PERFIL_NACIONAL, 'campus', ['Licenciatura en Trabajo Social']],
        ['Escuela de Ciencias Psicológicas', 'CUM', 7, self::PERFIL_NACIONAL, 'campus', ['Licenciatura en Psicología']],
        ['Escuela de Formación de Profesores de Enseñanza Media', 'EFPEM', 5, self::PERFIL_NACIONAL, 'campus', ['Licenciatura en Enseñanza de la Matemática y la Física', 'Licenciatura en Educación y Aprendizaje']],
        ['Escuela de Ciencias de la Comunicación', 'ECC', 3, self::PERFIL_NACIONAL, 'campus', ['Licenciatura en Ciencias de la Comunicación']],
        ['Centro Universitario de Occidente', 'CUNOC', 12, ['09' => 6, '12' => 4, '13' => 3, '08' => 3, '07' => 2, '10' => 1, '11' => 1], '0901', ['Licenciatura en Trabajo Social', 'Ingeniería Civil', 'Licenciatura en Pedagogía', 'Licenciatura en Administración de Empresas']],
        ['Centro Universitario de Oriente', 'CUNORI', 9, ['20' => 5, '19' => 4, '18' => 3, '02' => 2, '21' => 1, '22' => 1], '2001', ['Licenciatura en Zootecnia', 'Ingeniería en Gestión Ambiental Local', 'Licenciatura en Trabajo Social']],
        ['Centro Universitario del Norte', 'CUNOR', 9, ['16' => 6, '15' => 4, '17' => 1, '14' => 1], '1601', ['Ingeniería Agronómica en Sistemas de Producción Agrícola', 'Licenciatura en Trabajo Social', 'Licenciatura en Pedagogía']],
        ['Centro Universitario de Sur Occidente', 'CUNSUROC', 6, ['05' => 4, '10' => 4, '11' => 3, '09' => 1], '0502', ['Ingeniería en Alimentos', 'Licenciatura en Pedagogía', 'Licenciatura en Trabajo Social']],
        ['Centro Universitario de Petén', 'CUDEP', 5, ['17' => 8], '1701', ['Licenciatura en Administración de Empresas', 'Licenciatura en Pedagogía', 'Ingeniería en Gestión Ambiental Local']],
        ['Centro Universitario de Zacapa', 'CUNZAC', 3, ['19' => 5, '20' => 2, '02' => 1], '1901', ['Licenciatura en Trabajo Social', 'Licenciatura en Administración de Empresas']],
        ['Centro Universitario de Izabal', 'CUNIZAB', 3, ['18' => 6, '19' => 1], '1801', ['Licenciatura en Administración de Empresas', 'Ingeniería en Gestión Ambiental Local']],
        ['Centro Universitario de Jutiapa', 'CUNJUT', 3, ['22' => 5, '21' => 2, '06' => 1], '2201', ['Licenciatura en Pedagogía', 'Licenciatura en Trabajo Social']],
    ];

    /**
     * Bienes y servicios: nombre, categoría y peso (los primeros son los más frecuentes).
     *
     * @var list<array{0: string, 1: string, 2: int}>
     */
    private const array BIENES_SERVICIOS = [
        ['Jornada de salud comunitaria', 'servicio', 10],
        ['Refuerzo escolar', 'servicio', 9],
        ['Huertos familiares', 'bien', 8],
        ['Biblioteca comunitaria', 'bien', 7],
        ['Guía didáctica', 'bien', 7],
        ['Asesoría legal gratuita', 'servicio', 6],
        ['Alfabetización de adultos', 'servicio', 6],
        ['Filtros de agua', 'bien', 5],
        ['Mobiliario escolar', 'bien', 5],
        ['Campaña de salud bucal', 'servicio', 5],
        ['Estufas ahorradoras de leña', 'bien', 4],
        ['Plan de gestión de riesgo', 'bien', 3],
        ['Diagnóstico comunitario', 'servicio', 4],
        ['Material de nutrición infantil', 'bien', 3],
        ['Atención psicológica', 'servicio', 3],
        ['Vacunación de animales de traspatio', 'servicio', 2],
    ];

    /**
     * Acciones de transferencia: nombre, tipo de actividad.
     *
     * @var list<array{0: string, 1: TipoActividadTransferencia}>
     */
    private const array ACCIONES = [
        ['Taller de lectura comprensiva', TipoActividadTransferencia::Taller],
        ['Capacitación en huertos escolares', TipoActividadTransferencia::Capacitacion],
        ['Taller de higiene y salud preventiva', TipoActividadTransferencia::Taller],
        ['Asesoría a organizaciones comunitarias', TipoActividadTransferencia::Asesoria],
        ['Capacitación en manejo de residuos', TipoActividadTransferencia::Capacitacion],
        ['Taller de emprendimiento juvenil', TipoActividadTransferencia::Taller],
        ['Asesoría a docentes', TipoActividadTransferencia::Asesoria],
        ['Charla de prevención de la violencia', TipoActividadTransferencia::Otro],
        ['Capacitación en primeros auxilios', TipoActividadTransferencia::Capacitacion],
        ['Taller de nutrición familiar', TipoActividadTransferencia::Taller],
    ];

    private const array TEMAS = [
        'Seguridad alimentaria', 'Alfabetización de adultos', 'Manejo de residuos sólidos', 'Salud materno infantil',
        'Prevención de la violencia intrafamiliar', 'Producción de hortalizas', 'Acceso a agua segura', 'Deserción escolar',
        'Uso de plantas medicinales', 'Salud bucal escolar', 'Organización comunitaria', 'Emprendimiento juvenil',
        'Nutrición infantil', 'Saneamiento ambiental', 'Gestión de riesgo', 'Educación bilingüe intercultural',
    ];

    private const array ENFOQUES = ['diagnóstico participativo', 'estudio de caso', 'sistematización de la experiencia', 'propuesta de intervención', 'evaluación de impacto'];

    private const array NOMBRES = ['José', 'María', 'Juan', 'Ana', 'Luis', 'Carmen', 'Carlos', 'Rosa', 'Pedro', 'Lucía', 'Miguel', 'Sofía', 'Jorge', 'Elena', 'Marvin', 'Karla', 'Erick', 'Mónica', 'Byron', 'Daniela', 'Oscar', 'Andrea', 'Selvin', 'Gabriela', 'Kevin', 'Brenda', 'Edwin', 'Wendy', 'Josué', 'Alejandra'];

    private const array APELLIDOS = ['López', 'García', 'Pérez', 'Morales', 'Hernández', 'Ajú', 'Xicay', 'Tzoc', 'Cifuentes', 'Ramírez', 'Choc', 'Caal', 'Coy', 'Méndez', 'Juárez', 'Batz', 'Ixcoy', 'Girón', 'Castillo', 'Sac', 'Tojín', 'Maldonado', 'Chub', 'Us', 'Cú', 'Aguilar', 'Orozco', 'Ordóñez', 'Tuyuc', 'Yat'];

    private const array COMUNIDADES = ['Aldea El Progreso', 'Caserío La Esperanza', 'Colonia Nueva Vida', 'Aldea San José', 'Barrio El Calvario', 'Aldea Las Flores', 'Caserío El Rosario', 'Colonia Santa Rita', 'Aldea Chuacruz', 'Cantón Central', 'Aldea Pachaj', 'Caserío Buena Vista'];

    private const string PDF_DEMO = "%PDF-1.4\n1 0 obj<</Type/Catalog/Pages 2 0 R>>endobj\n2 0 obj<</Type/Pages/Count 1/Kids[3 0 R]>>endobj\n3 0 obj<</Type/Page/Parent 2 0 R/MediaBox[0 0 200 100]>>endobj\ntrailer<</Root 1 0 R>>\n%%EOF";

    /** @var array<string, int> */
    private array $departamentos = [];

    /** @var array<int, list<Municipio>> */
    private array $municipios = [];

    /** @var array<string, int> */
    private array $instituciones = [];

    /** @var array<string, list<array<string, mixed>>> */
    private array $filas = [];

    private CarbonInterface $hoy;

    public function run(): void
    {
        if (app()->isProduction()) {
            $this->command?->error('Los datos de demostración no se siembran en producción.');

            return;
        }

        if (Estudiante::where('carnet', 'like', self::PREFIJO_CARNET.'%')->exists()) {
            $this->command?->warn('Los datos de demostración ya están sembrados.');

            return;
        }

        $this->call([RolSeeder::class, DepartamentoSeeder::class, MunicipioSeeder::class]);

        mt_srand(2026);
        $this->hoy = now();
        $this->departamentos = Departamento::pluck('id', 'codigo')->all();
        $this->municipios = Municipio::orderBy('codigo')->get()->groupBy('departamento_id')->map(fn ($grupo) => $grupo->all())->all();

        DB::transaction(function (): void {
            $unidades = $this->sembrarUnidades();
            $catalogoBienes = $this->sembrarCatalogo(TipoCatalogo::BienesServicios, self::BIENES_SERVICIOS);
            $catalogoAcciones = $this->sembrarCatalogo(TipoCatalogo::Acciones, array_map(fn (array $accion): array => [$accion[0], null, 1], self::ACCIONES));

            $this->sembrarUsuarios($unidades);
            $this->sembrarExpedientes($unidades, $catalogoBienes, $catalogoAcciones);
            $this->volcarFilas();
            $this->sembrarBitacora();
        });

        $this->command?->info(sprintf(
            'Datos de demostración: %d EPS, %d bienes y servicios, %d publicaciones, %d municipios con actividad.',
            Expediente::count(),
            DB::table('bienes_servicios')->count(),
            DB::table('publicaciones_investigacion')->count(),
            DB::table('ubicaciones_territoriales')->distinct()->count('municipio_id'),
        ));
    }

    /**
     * @return list<array{unidad: UnidadAcademica, peso: int, perfil: array<string, int>, carreras: list<string>, indice: int}>
     */
    private function sembrarUnidades(): array
    {
        $unidades = [];

        foreach (self::UNIDADES as $indice => [$nombre, $siglas, $peso, $perfil, $sede, $carreras]) {
            [$latitud, $longitud] = $sede === 'campus'
                ? self::CAMPUS_CENTRAL
                : $this->coordenadaDeMunicipio($sede);

            $unidades[] = [
                'unidad' => UnidadAcademica::updateOrCreate(['nombre' => $nombre], [
                    'siglas' => $siglas,
                    'tipo' => TipoUnidadAcademica::desdeNombre($nombre),
                    'latitud' => $latitud,
                    'longitud' => $longitud,
                    'activa' => true,
                ]),
                'peso' => $peso,
                'perfil' => $perfil,
                'carreras' => $carreras,
                'indice' => $indice + 1,
            ];
        }

        return $unidades;
    }

    /**
     * @return array{0: float, 1: float}
     */
    private function coordenadaDeMunicipio(string $codigo): array
    {
        $municipio = Municipio::where('codigo', $codigo)->firstOrFail();

        return [(float) $municipio->latitud, (float) $municipio->longitud];
    }

    /**
     * @param  list<array{0: string, 1: string|null, 2: int}>  $elementos
     * @return list<array{id: int, nombre: string, categoria: string|null, peso: int}>
     */
    private function sembrarCatalogo(TipoCatalogo $tipo, array $elementos): array
    {
        return array_map(function (array $elemento) use ($tipo): array {
            $catalogo = Catalogo::firstOrCreate(
                ['catalogo' => $tipo->value, 'nombre' => $elemento[0]],
                ['categoria' => $elemento[1], 'activo' => true],
            );

            return ['id' => $catalogo->id, 'nombre' => $catalogo->nombre, 'categoria' => $catalogo->categoria, 'peso' => $elemento[2]];
        }, $elementos);
    }

    /**
     * @param  list<array{unidad: UnidadAcademica, peso: int, perfil: array<string, int>, carreras: list<string>, indice: int}>  $unidades
     */
    private function sembrarUsuarios(array $unidades): void
    {
        $cuentas = [
            ['admin@epsnet.test', 'Administrador DIGEU', ClaveRol::Digeu],
            ['unidad@epsnet.test', 'Unidad Humanidades', ClaveRol::UnidadAcademica],
            ['invitado@epsnet.test', 'Invitado de prueba', ClaveRol::Invitado],
        ];

        foreach ($cuentas as [$correo, $nombre, $rol]) {
            $usuario = User::where('email', $correo)->first() ?? User::factory()->create(['name' => $nombre, 'email' => $correo]);
            $usuario->forceFill(['rol_id' => Rol::delSistema($rol)->id])->save();

            if ($rol === ClaveRol::UnidadAcademica && UnidadAcademica::where('administrador_id', $usuario->id)->doesntExist()) {
                $unidades[0]['unidad']->update(['administrador_id' => $usuario->id]);
            }
        }
    }

    /**
     * @param  list<array{unidad: UnidadAcademica, peso: int, perfil: array<string, int>, carreras: list<string>, indice: int}>  $unidades
     * @param  list<array{id: int, nombre: string, categoria: string|null, peso: int}>  $catalogoBienes
     * @param  list<array{id: int, nombre: string, categoria: string|null, peso: int}>  $catalogoAcciones
     */
    private function sembrarExpedientes(array $unidades, array $catalogoBienes, array $catalogoAcciones): void
    {
        $pesosUnidades = array_map(fn (array $unidad): int => $unidad['peso'], $unidades);
        $administrador = User::where('email', 'admin@epsnet.test')->value('id');

        $codigos = array_keys($this->departamentos);

        for ($numero = 1; $numero <= self::TOTAL_EPS; $numero++) {
            // Los primeros EPS recorren los departamentos para que ninguno quede vacío en el mapa; los atiende
            // una unidad que opere allí.
            $forzado = $codigos[$numero - 1] ?? null;
            $candidatas = $forzado === null
                ? $pesosUnidades
                : array_filter($pesosUnidades, fn (int $peso, int $indice): bool => isset($unidades[$indice]['perfil'][$forzado]), ARRAY_FILTER_USE_BOTH);
            $unidad = $unidades[$this->ponderado($candidatas)];
            $carrera = $unidad['carreras'][array_rand($unidad['carreras'])];
            $estado = $this->estado();
            $orden = $this->fechaDeOrden();
            $fin = $orden->copy()->subDays(mt_rand(15, 60));
            $inicio = $fin->copy()->subDays(mt_rand(150, 240));

            $estudiante = Estudiante::create([
                'carnet' => self::PREFIJO_CARNET.str_pad((string) $numero, 5, '0', STR_PAD_LEFT),
                'nombre1' => self::NOMBRES[array_rand(self::NOMBRES)],
                'apellido1' => self::APELLIDOS[array_rand(self::APELLIDOS)],
                'apellido2' => self::APELLIDOS[array_rand(self::APELLIDOS)],
                'codigo_nacionalidad' => '30',
                'nacionalidad' => 'Guatemalteca',
                'ultima_consulta_at' => $this->hoy,
            ]);

            $expediente = Expediente::create([
                'estudiante_id' => $estudiante->id,
                'unidad_academica_id' => $unidad['unidad']->id,
                'programa' => mt_rand(1, 100) <= 20 ? ProgramaEps::Epsum : ProgramaEps::EpsFacultativo,
                'codigo_unidad' => str_pad((string) $unidad['indice'], 2, '0', STR_PAD_LEFT),
                'codigo_extension' => '00',
                'codigo_carrera' => str_pad((string) (array_search($carrera, $unidad['carreras'], true) + 1), 2, '0', STR_PAD_LEFT),
                'nombre_unidad' => $unidad['unidad']->nombre,
                'nombre_extension' => 'Plan Diario',
                'nombre_carrera' => $carrera,
                'nivel_academico' => 'Licenciatura',
                'eje_actual' => $estado === EstadoExpediente::Activo ? mt_rand(1, 5) : 6,
                'estado_expediente' => $estado,
                'completado_at' => $estado === EstadoExpediente::Activo ? null : $orden,
                'verificado_at' => $estado === EstadoExpediente::Verificado ? $orden->copy()->addDays(mt_rand(3, 30)) : null,
                'verificado_por' => $estado === EstadoExpediente::Verificado ? $administrador : null,
                'fecha_inicio_eps' => $inicio,
                'fecha_fin_eps' => $fin,
            ]);

            $ubicaciones = $this->registrarUbicaciones($expediente, $forzado, $unidad['perfil'], $inicio);
            $lugar = $ubicaciones[0];
            $completo = $estado !== EstadoExpediente::Activo;

            $this->registrarBienesServicios($expediente->id, $catalogoBienes, $lugar, $inicio, $fin, $completo);
            $this->registrarTransferencias($expediente->id, $catalogoAcciones, $lugar, $inicio, $fin, $completo);
            $this->registrarPublicaciones($expediente->id, $estudiante, $lugar, $inicio, $fin, $completo);
            $this->registrarActores($expediente->id, $lugar);
            $this->registrarSeguimientos($expediente->id, $lugar, $inicio, $fin, $completo);

            if ($completo) {
                $this->registrarOrdenImpresion($expediente->id, $orden, $administrador);
            }
        }
    }

    /**
     * @param  array<string, int>  $perfil
     * @return non-empty-list<array{municipio: Municipio, comunidad: string}>
     */
    private function registrarUbicaciones(Expediente $expediente, ?string $forzado, array $perfil, CarbonInterface $fecha): array
    {
        $lugares = [];
        $cantidad = mt_rand(1, 100) <= 15 ? 2 : 1;
        $departamentoId = $this->departamentos[$forzado ?? $this->ponderado($perfil)];

        for ($i = 0; $i < $cantidad; $i++) {
            $municipios = $this->municipios[$departamentoId];
            $pesos = array_map(fn (Municipio $municipio): int => str_ends_with($municipio->codigo, '01') ? 4 : 1, $municipios);
            $municipio = $municipios[$this->ponderado($pesos)];
            $comunidad = self::COMUNIDADES[array_rand(self::COMUNIDADES)];

            $lugares[] = ['municipio' => $municipio, 'comunidad' => $comunidad];
            $this->filas['ubicaciones_territoriales'][] = [
                'expediente_id' => $expediente->id,
                'departamento_id' => $departamentoId,
                'municipio_id' => $municipio->id,
                'comunidad' => $comunidad,
                'latitud' => round((float) $municipio->latitud + (mt_rand(-300, 300) / 10000), 7),
                'longitud' => round((float) $municipio->longitud + (mt_rand(-300, 300) / 10000), 7),
                'referencia' => "A pocos minutos del centro de {$municipio->nombre}",
                ...$this->marcas($fecha),
            ];
        }

        return $lugares;
    }

    /**
     * @param  list<array{id: int, nombre: string, categoria: string|null, peso: int}>  $catalogo
     * @param  array{municipio: Municipio, comunidad: string}  $lugar
     */
    private function registrarBienesServicios(int $expedienteId, array $catalogo, array $lugar, CarbonInterface $inicio, CarbonInterface $fin, bool $completo): void
    {
        $pesos = array_map(fn (array $item): int => $item['peso'], $catalogo);

        for ($i = mt_rand($completo ? 2 : 0, $completo ? 7 : 2); $i > 0; $i--) {
            $item = $catalogo[$this->ponderado($pesos)];
            $beneficiarios = (int) round(exp(mt_rand(20, 60) / 10));
            $tipo = $item['categoria'] === TipoBienServicio::Bien->value ? TipoBienServicio::Bien : TipoBienServicio::Servicio;
            $fecha = $this->fechaEntre($inicio, $fin);

            $this->filas['bienes_servicios'][] = [
                'expediente_id' => $expedienteId,
                'catalogo_id' => $item['id'],
                'tipo' => $tipo->value,
                'descripcion' => "{$item['nombre']} en {$lugar['comunidad']}, {$lugar['municipio']->nombre}: actividad realizada durante el EPS para fortalecer las capacidades de la comunidad.",
                'beneficiarios' => "Familias y estudiantes de {$lugar['comunidad']}",
                'cantidad_beneficiarios' => $beneficiarios,
                'fecha' => $fecha->toDateString(),
                ...$this->marcas($fecha),
            ];
        }
    }

    /**
     * @param  list<array{id: int, nombre: string, categoria: string|null, peso: int}>  $catalogo
     * @param  array{municipio: Municipio, comunidad: string}  $lugar
     */
    private function registrarTransferencias(int $expedienteId, array $catalogo, array $lugar, CarbonInterface $inicio, CarbonInterface $fin, bool $completo): void
    {
        for ($i = mt_rand($completo ? 1 : 0, $completo ? 4 : 1); $i > 0; $i--) {
            $indice = array_rand($catalogo);
            $fecha = $this->fechaEntre($inicio, $fin);

            $this->filas['transferencias_conocimiento'][] = [
                'expediente_id' => $expedienteId,
                'catalogo_id' => $catalogo[$indice]['id'],
                'tipo_actividad' => self::ACCIONES[$indice][1]->value,
                'actividad' => "{$catalogo[$indice]['nombre']} dirigido a la comunidad de {$lugar['comunidad']}.",
                'comunidad' => $lugar['comunidad'],
                'numero_participantes' => mt_rand(8, 80),
                'fecha' => $fecha->toDateString(),
                ...$this->marcas($fecha),
            ];
        }
    }

    /**
     * @param  array{municipio: Municipio, comunidad: string}  $lugar
     */
    private function registrarPublicaciones(int $expedienteId, Estudiante $estudiante, array $lugar, CarbonInterface $inicio, CarbonInterface $fin, bool $completo): void
    {
        if (! $completo || mt_rand(1, 100) > 38) {
            return;
        }

        $departamento = Departamento::find($lugar['municipio']->departamento_id)->nombre;

        for ($i = mt_rand(1, 100) <= 20 ? 2 : 1; $i > 0; $i--) {
            $tema = self::TEMAS[array_rand(self::TEMAS)];
            $enfoque = self::ENFOQUES[array_rand(self::ENFOQUES)];
            $tipo = [TipoPublicacion::Articulo, TipoPublicacion::Informe, TipoPublicacion::Tesis, TipoPublicacion::Ponencia][mt_rand(0, 3)];
            $fecha = $this->fechaEntre($inicio, $fin);

            $this->filas['publicaciones_investigacion'][] = [
                'expediente_id' => $expedienteId,
                'titulo' => "{$tema} en {$lugar['municipio']->nombre}, {$departamento}: {$enfoque}",
                'tipo' => $tipo->value,
                'autores' => "{$estudiante->nombre1} {$estudiante->apellido1} {$estudiante->apellido2}",
                'medio_publicacion' => ['Revista de la USAC', 'Repositorio DIGEU', 'Congreso Universitario de Extensión', 'Informe final de EPS'][mt_rand(0, 3)],
                'resumen' => "Estudio sobre {$tema} en la comunidad de {$lugar['comunidad']}, con base en el trabajo de campo realizado durante el EPS.",
                'enlace' => null,
                'fecha_publicacion' => $fecha->toDateString(),
                ...$this->marcas($fecha),
            ];
        }
    }

    /**
     * @param  array{municipio: Municipio, comunidad: string}  $lugar
     */
    private function registrarActores(int $expedienteId, array $lugar): void
    {
        $tipos = ['Municipalidad de', 'Centro de Salud de', 'Escuela Oficial Urbana Mixta de', 'Instituto Nacional de Educación Básica de'];

        for ($i = mt_rand(1, 2); $i > 0; $i--) {
            $prefijo = $tipos[array_rand($tipos)];
            $nombre = "{$prefijo} {$lugar['municipio']->nombre}";

            $this->instituciones[$nombre] ??= InstitucionReceptora::firstOrCreate(
                ['nombre' => $nombre],
                ['tipo' => str_starts_with($prefijo, 'Municipalidad') ? 'Gobierno local' : (str_starts_with($prefijo, 'Centro') ? 'Salud' : 'Educación')],
            )->id;

            $this->filas['actores_participantes'][] = [
                'expediente_id' => $expedienteId,
                'institucion_receptora_id' => $this->instituciones[$nombre],
                'contraparte' => self::NOMBRES[array_rand(self::NOMBRES)].' '.self::APELLIDOS[array_rand(self::APELLIDOS)],
                'comunidad_beneficiada' => $lugar['comunidad'],
                ...$this->marcas($this->hoy),
            ];
        }
    }

    /**
     * @param  array{municipio: Municipio, comunidad: string}  $lugar
     */
    private function registrarSeguimientos(int $expedienteId, array $lugar, CarbonInterface $inicio, CarbonInterface $fin, bool $completo): void
    {
        $avances = mt_rand(1, 3);

        for ($i = 1; $i <= $avances; $i++) {
            $porcentaje = (int) round(100 * $i / ($avances + 1));
            $fecha = $inicio->copy()->addDays((int) ($inicio->diffInDays($fin) * $i / ($avances + 1)));

            $this->filas['seguimientos_impacto'][] = [
                'expediente_id' => $expedienteId,
                'registrado_por' => null,
                'tipo_registro' => TipoRegistroSeguimiento::Avance->value,
                'indicador' => "Actividades ejecutadas en {$lugar['comunidad']}",
                'avance' => "Se ha ejecutado el {$porcentaje}% de las actividades planificadas.",
                'porcentaje_avance' => $porcentaje,
                'cumplimiento' => NivelCumplimiento::Parcial->value,
                'observaciones' => null,
                'evaluacion_impacto' => null,
                'fecha' => $fecha->toDateString(),
                ...$this->marcas($fecha),
            ];
        }

        if (! $completo) {
            return;
        }

        $this->filas['seguimientos_impacto'][] = [
            'expediente_id' => $expedienteId,
            'registrado_por' => null,
            'tipo_registro' => TipoRegistroSeguimiento::EvaluacionFinal->value,
            'indicador' => 'Cumplimiento del plan de EPS',
            'avance' => 'Se completaron las actividades del plan.',
            'porcentaje_avance' => 100,
            'cumplimiento' => (mt_rand(1, 10) <= 8 ? NivelCumplimiento::Cumplido : NivelCumplimiento::Parcial)->value,
            'observaciones' => null,
            'evaluacion_impacto' => "La comunidad de {$lugar['comunidad']}, en {$lugar['municipio']->nombre}, fortaleció sus capacidades y cuenta ahora con recursos que le permiten dar continuidad al trabajo iniciado durante el EPS.",
            'fecha' => $fin->toDateString(),
            ...$this->marcas($fin),
        ];
    }

    private function registrarOrdenImpresion(int $expedienteId, CarbonInterface $fecha, ?int $subidoPor): void
    {
        // El tipo y el id de la entidad no son asignables en masa.
        $adjunto = (new Adjunto)->forceFill([
            'categoria' => Adjunto::ORDEN_IMPRESION,
            'entidad_tipo' => 'expediente',
            'entidad_id' => $expedienteId,
            'subido_por' => $subidoPor,
            'nombre_original' => 'orden-de-impresion.pdf',
            'mime_type' => 'application/pdf',
            'tamano_bytes' => strlen(self::PDF_DEMO),
            'sha256' => hash('sha256', self::PDF_DEMO),
            'fecha_subida' => $fecha,
        ]);
        $adjunto->save();

        AdjuntoContenido::create(['adjunto_id' => $adjunto->id, 'contenido' => base64_encode(self::PDF_DEMO)]);
    }

    /**
     * Bitácora de ejemplo: estudiantes con cuenta que registran su EPS, aprobaciones, catálogos y exportaciones.
     */
    private function sembrarBitacora(): void
    {
        $admin = User::where('email', 'admin@epsnet.test')->firstOrFail();
        $coordinador = User::where('email', 'unidad@epsnet.test')->firstOrFail();
        $invitado = User::where('email', 'invitado@epsnet.test')->firstOrFail();
        $filas = [];

        $anotar = function (User $usuario, ClaveRol $rol, string $modulo, TipoCambioBitacora $tipo, string $detalle, CarbonInterface $fecha, ?Model $entidad = null, ?array $antes = null, ?array $despues = null) use (&$filas): void {
            $filas[] = [
                'usuario_id' => $usuario->id,
                'estudiante_id' => Estudiante::where('usuario_id', $usuario->id)->value('id'),
                'usuario_nombre' => $usuario->name,
                'usuario_correo' => $usuario->email,
                'usuario_rol' => $rol->value,
                'modulo' => $modulo,
                'tipo_cambio' => $tipo->value,
                'entidad_tipo' => $entidad === null ? null : class_basename($entidad),
                'entidad_id' => $entidad?->getKey(),
                'detalle' => $detalle,
                'valores_anteriores' => $antes === null ? null : json_encode($antes, JSON_UNESCAPED_UNICODE),
                'valores_nuevos' => $despues === null ? null : json_encode($despues, JSON_UNESCAPED_UNICODE),
                'ip' => '192.168.'.mt_rand(0, 20).'.'.mt_rand(2, 250),
                'fecha_hora' => $fecha->format('Y-m-d H:i:s'),
            ];
        };

        $inicio = $this->hoy->copy()->subYears(3);

        foreach (UnidadAcademica::orderBy('id')->get() as $unidad) {
            $anotar($admin, ClaveRol::Digeu, 'Unidades académicas', TipoCambioBitacora::Creacion, "Creó la unidad académica «{$unidad->nombre}»", $inicio->copy()->addMinutes($unidad->id * 3), $unidad, null, ['nombre' => $unidad->nombre, 'siglas' => $unidad->siglas]);
        }

        foreach (Catalogo::orderBy('id')->get() as $elemento) {
            $anotar($admin, ClaveRol::Digeu, 'Catálogo: '.$elemento->catalogo->etiqueta(), TipoCambioBitacora::Creacion, "Creó el elemento «{$elemento->nombre}»", $inicio->copy()->addHours(2)->addMinutes($elemento->id * 4), $elemento, null, ['nombre' => $elemento->nombre, 'categoria' => $elemento->categoria]);
        }

        foreach (Municipio::whereIn('codigo', ['1601', '0901', '1701', '2001'])->get() as $municipio) {
            $anotar($admin, ClaveRol::Digeu, 'Municipios', TipoCambioBitacora::Edicion, "Editó el municipio «{$municipio->nombre}» ({$municipio->codigo}) · Campos: latitud, longitud", $inicio->copy()->addDays(3), $municipio, ['latitud' => (string) $municipio->latitud], ['latitud' => (string) round((float) $municipio->latitud + 0.0123, 7)]);
        }

        foreach (Expediente::validos()->where('estado_expediente', EstadoExpediente::Verificado)->with('estudiante')->orderBy('id')->limit(70)->get() as $indice => $expediente) {
            $aprobador = $expediente->nombre_unidad === 'Facultad de Humanidades' ? $coordinador : $admin;
            $rol = $aprobador->is($coordinador) ? ClaveRol::UnidadAcademica : ClaveRol::Digeu;

            $anotar($aprobador, $rol, 'Expedientes (EPS)', TipoCambioBitacora::Aprobacion, "Aprobó el {$expediente->resumenBitacora()} · ".Expediente::CONSTANCIA_APROBACION, $expediente->verificado_at, $expediente);
        }

        $estudiantes = Estudiante::where('carnet', 'like', self::PREFIJO_CARNET.'%')->orderBy('id')->limit(8)->get();

        foreach ($estudiantes as $indice => $estudiante) {
            $usuario = User::factory()->create(['name' => $estudiante->nombre_completo, 'email' => 'estudiante'.($indice + 1).'@epsnet.test']);
            $usuario->forceFill(['rol_id' => Rol::delSistema(ClaveRol::Estudiante)->id])->save();
            $estudiante->forceFill(['usuario_id' => $usuario->id])->save();

            $expediente = $estudiante->expedientes()->first();
            $contexto = $expediente->resumenBitacora();
            $fecha = $expediente->fecha_inicio_eps->copy()->addDays(2)->setTime(9, 15);

            $anotar($usuario, ClaveRol::Estudiante, 'Acceso al sistema', TipoCambioBitacora::Acceso, 'Inició sesión', $fecha);
            $anotar($usuario, ClaveRol::Estudiante, 'Expedientes (EPS)', TipoCambioBitacora::Creacion, "Creó el {$contexto}", $fecha->copy()->addMinutes(3), $expediente);

            foreach ($expediente->bienesServicios as $bien) {
                $anotar($usuario, ClaveRol::Estudiante, 'Bienes y servicios', TipoCambioBitacora::Creacion, "Creó el {$bien->tipo->value} «".mb_strimwidth($bien->descripcion, 0, 70, '…')."» del {$contexto}", $bien->created_at, $bien, null, ['tipo' => $bien->tipo->value, 'cantidad_beneficiarios' => $bien->cantidad_beneficiarios, 'fecha' => $bien->fecha->toDateString()]);
            }

            if ($primero = $expediente->bienesServicios->first()) {
                $anotar($usuario, ClaveRol::Estudiante, 'Bienes y servicios', TipoCambioBitacora::Edicion, "Editó el {$primero->tipo->value} «".mb_strimwidth($primero->descripcion, 0, 70, '…')."» del {$contexto} · Campos: cantidad_beneficiarios", $primero->created_at->copy()->addDays(2), $primero, ['cantidad_beneficiarios' => max(1, $primero->cantidad_beneficiarios - 12)], ['cantidad_beneficiarios' => $primero->cantidad_beneficiarios]);
            }

            foreach ($expediente->transferencias as $accion) {
                $anotar($usuario, ClaveRol::Estudiante, 'Transferencia de conocimiento', TipoCambioBitacora::Creacion, 'Creó la actividad «'.mb_strimwidth($accion->actividad, 0, 70, '…')."» del {$contexto}", $accion->created_at, $accion, null, ['actividad' => $accion->actividad, 'numero_participantes' => $accion->numero_participantes]);
            }

            foreach ($expediente->publicaciones as $publicacion) {
                $anotar($usuario, ClaveRol::Estudiante, 'Publicaciones de investigación', TipoCambioBitacora::Creacion, "Creó la publicación «{$publicacion->titulo}» del {$contexto}", $publicacion->created_at, $publicacion, null, ['titulo' => $publicacion->titulo, 'tipo' => $publicacion->tipo->value]);
            }

            if ($indice < 3) {
                $anotar($usuario, ClaveRol::Estudiante, 'Expedientes (EPS)', TipoCambioBitacora::Edicion, "Editó el {$contexto} · Campos: programa", $fecha->copy()->addMinutes(10), $expediente, ['programa' => null], ['programa' => $expediente->programa->value]);
            }

            $anotar($usuario, ClaveRol::Estudiante, 'Acceso al sistema', TipoCambioBitacora::Acceso, 'Cerró sesión', $fecha->copy()->addHours(1));
        }

        $anotar($invitado, ClaveRol::Invitado, 'Acceso al sistema', TipoCambioBitacora::Acceso, 'Inició sesión', $this->hoy->copy()->subDays(1));
        $anotar($invitado, ClaveRol::Invitado, 'Estadísticas', TipoCambioBitacora::Exportacion, 'Generó el PDF de estadísticas del país (Investigaciones) · Año (orden de impresión): 2025 · Unidad académica: Todas las unidades · Carrera: Todas las carreras', $this->hoy->copy()->subDays(1)->addMinutes(8));
        $anotar($admin, ClaveRol::Digeu, 'Estadísticas', TipoCambioBitacora::Exportacion, 'Generó el PDF del departamento Alta Verapaz · Año (orden de impresión): Todos los años · Unidad académica: Centro Universitario del Norte · Carrera: Todas las carreras', $this->hoy->copy()->subHours(5));

        usort($filas, fn (array $a, array $b): int => $a['fecha_hora'] <=> $b['fecha_hora']);

        foreach (array_chunk($filas, 300) as $lote) {
            DB::table('bitacoras')->insert($lote);
        }
    }

    private function volcarFilas(): void
    {
        foreach ($this->filas as $tabla => $filas) {
            foreach (array_chunk($filas, 400) as $lote) {
                DB::table($tabla)->insert($lote);
            }
        }
    }

    private function estado(): EstadoExpediente
    {
        $azar = mt_rand(1, 100);

        return match (true) {
            $azar <= 66 => EstadoExpediente::Verificado,
            $azar <= 90 => EstadoExpediente::Completo,
            default => EstadoExpediente::Activo,
        };
    }

    /**
     * Fecha de la orden de impresión: reparte los EPS entre 2023 y lo que va del año actual.
     */
    private function fechaDeOrden(): CarbonInterface
    {
        $anio = $this->ponderado([2023 => 20, 2024 => 30, 2025 => 35, 2026 => 15]);
        $inicio = Carbon::create($anio, 1, 1);
        $fin = $anio === $this->hoy->year ? $this->hoy->copy()->subDays(3) : Carbon::create($anio, 12, 20);

        return $inicio->copy()->addDays(mt_rand(0, max(1, (int) $inicio->diffInDays($fin))))->setTime(mt_rand(8, 16), mt_rand(0, 59));
    }

    private function fechaEntre(CarbonInterface $inicio, CarbonInterface $fin): CarbonInterface
    {
        return $inicio->copy()->addDays(mt_rand(0, max(1, (int) $inicio->diffInDays($fin))));
    }

    /**
     * @return array{created_at: CarbonInterface, updated_at: CarbonInterface}
     */
    private function marcas(CarbonInterface $fecha): array
    {
        return ['created_at' => $fecha, 'updated_at' => $fecha];
    }

    /**
     * Índice elegido al azar según los pesos dados.
     *
     * @param  array<int|string, int>  $pesos
     */
    private function ponderado(array $pesos): int|string
    {
        $azar = mt_rand(1, array_sum($pesos));

        foreach ($pesos as $clave => $peso) {
            if (($azar -= $peso) <= 0) {
                return $clave;
            }
        }

        return array_key_last($pesos);
    }
}
