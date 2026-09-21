<?php

namespace Tests\Support;

use App\Models\Estudiante;
use App\Models\User;
use App\Services\RegistroAcademico\DetalleAcademico;
use Illuminate\Support\Facades\Http;

/**
 * Doble del servicio web de Registro y Estadística basado en una respuesta real de ejemplo.
 */
class RegistroAcademicoFalso
{
    public const string URL = 'https://rye.test/consulta';

    public const string CARNET = '201219511';

    public const string CUI = '2165914570101';

    /**
     * Configura el cliente para que apunte al servicio falso.
     */
    public static function configurar(): void
    {
        Http::preventStrayRequests();

        // Con public/hot presente, Inertia intentaría renderizar en el servidor de Vite.
        config()->set('inertia.ssr.enabled', false);

        config()->set('services.registro_academico', [
            'url' => self::URL,
            'dependencia' => 'epsum',
            'login' => 'epsumWS',
            'password' => 'clave',
            'timeout' => 5,
        ]);
    }

    /**
     * Responde a cualquier consulta con la respuesta de ejemplo.
     */
    public static function responder(?string $xml = null): void
    {
        Http::fake([self::URL => Http::response($xml ?? self::xml())]);
    }

    public static function xml(string $carnet = self::CARNET, string $cui = self::CUI): string
    {
        return <<<XML
        <RESP_CONSULTA_DATOS>
            <CARNET>{$carnet}</CARNET>
            <NOMBRE1>Madeley</NOMBRE1>
            <NOMBRE2></NOMBRE2>
            <NOMBRE3></NOMBRE3>
            <APELLIDO1>Morales</APELLIDO1>
            <APELLIDO2>Vivar</APELLIDO2>
            <DIRECCION></DIRECCION>
            <CUI>{$cui}</CUI>
            <COD_NAC>30</COD_NAC>
            <NOM_NAC>Guatemalteca</NOM_NAC>
            <DETALLE_ACADEMICO>
                <UNIDAD>07</UNIDAD>
                <EXTENSION>00</EXTENSION>
                <CARRERA>28</CARRERA>
                <NOMBRE_UNIDAD>Facultad de Humanidades</NOMBRE_UNIDAD>
                <NOMBRE_EXTENSION>Plan Diario</NOMBRE_EXTENSION>
                <NOMBRE_CARRERA>Profesorado de EnseÃ±anza  Media en PedagogÃ­a, Ciencias Sociales y FormaciÃ³n Ciudadana</NOMBRE_CARRERA>
                <NIVEL_ACADEMICO>
                    <NIVEL>2</NIVEL>
                    <GRADO>Tecnica</GRADO>
                </NIVEL_ACADEMICO>
                <ESTADO>3</ESTADO>
                <CLASIFICACION>2</CLASIFICACION>
                <CICLO_ACTIVO></CICLO_ACTIVO>
                <SEMESTRE>0</SEMESTRE>
                <FECHA_INSCRITO></FECHA_INSCRITO>
                <FECHA_RETIRO_MATRICULA></FECHA_RETIRO_MATRICULA>
                <FECHA_CIERRE>2015-11-30</FECHA_CIERRE>
                <FECHA_GRADUADO>2016-07-05</FECHA_GRADUADO>
            </DETALLE_ACADEMICO>
            <DETALLE_ACADEMICO>
                <UNIDAD>77</UNIDAD>
                <EXTENSION>00</EXTENSION>
                <CARRERA>66</CARRERA>
                <NOMBRE_UNIDAD>Facultad de Humanidades</NOMBRE_UNIDAD>
                <NOMBRE_EXTENSION>Sede  Guatemala. -Plan diario-</NOMBRE_EXTENSION>
                <NOMBRE_CARRERA>Licenciatura en PedagogÃ­a y AdministraciÃ³n Educativa</NOMBRE_CARRERA>
                <NIVEL_ACADEMICO>
                    <NIVEL>1</NIVEL>
                    <GRADO>Licenciatura</GRADO>
                </NIVEL_ACADEMICO>
                <ESTADO>0</ESTADO>
                <CLASIFICACION>2</CLASIFICACION>
                <CICLO_ACTIVO>2017</CICLO_ACTIVO>
                <SEMESTRE>1</SEMESTRE>
                <FECHA_INSCRITO>2016-11-25</FECHA_INSCRITO>
                <FECHA_RETIRO_MATRICULA></FECHA_RETIRO_MATRICULA>
                <FECHA_CIERRE></FECHA_CIERRE>
                <FECHA_GRADUADO></FECHA_GRADUADO>
            </DETALLE_ACADEMICO>
        </RESP_CONSULTA_DATOS>
        XML;
    }

    /**
     * Las dos carreras de la respuesta de ejemplo, tal como se guardan en el perfil del estudiante.
     *
     * @return list<array<string, string|null>>
     */
    public static function carreras(): array
    {
        return [
            (new DetalleAcademico('07', '00', '28', 'Facultad de Humanidades', 'Plan Diario', 'Profesorado de Enseñanza  Media en Pedagogía, Ciencias Sociales y Formación Ciudadana', '2', 'Tecnica', '3', null, null, '2015-11-30', '2016-07-05'))->toArray(),
            (new DetalleAcademico('77', '00', '66', 'Facultad de Humanidades', 'Sede  Guatemala. -Plan diario-', 'Licenciatura en Pedagogía y Administración Educativa', '1', 'Licenciatura', '0', '2017', '2016-11-25', null, null))->toArray(),
        ];
    }

    /**
     * Inicia sesión como el usuario del estudiante (rol estudiante), con sus carreras ya consultadas.
     */
    public static function iniciarSesion(Estudiante $estudiante): User
    {
        $usuario = User::factory()->estudiante()->create();

        $estudiante->forceFill([
            'usuario_id' => $usuario->id,
            'carreras' => self::carreras(),
            'ultima_consulta_at' => now(),
        ])->save();

        test()->actingAs($usuario);

        return $usuario;
    }
}
