<?php

use App\Models\Estudiante;
use App\Services\RegistroAcademico\DatosAcademicos;
use App\Services\RegistroAcademico\RegistroAcademicoClient;
use App\Services\RegistroAcademico\RegistroAcademicoNoDisponible;
use App\Services\RegistroAcademico\TransporteSoap;
use Illuminate\Support\Facades\Log;
use Tests\Support\RegistroAcademicoFalso;

beforeEach(function () {
    RegistroAcademicoFalso::configurar();
});

it('envía la solicitud con las credenciales de la dependencia y el carné', function () {
    $transporte = RegistroAcademicoFalso::responder();

    app(RegistroAcademicoClient::class)->consultar(RegistroAcademicoFalso::CARNET);

    expect($transporte->solicitudes)->toBe([
        '<SOLICITUD_DATOS_RYE><DEPENDENCIA>epsum</DEPENDENCIA><LOGIN>epsumWS</LOGIN><PWD>clave</PWD><CARNET>201219511</CARNET></SOLICITUD_DATOS_RYE>',
    ]);
});

it('escapa los caracteres especiales de XML en la solicitud', function () {
    config()->set('services.registro_academico.password', 'a&b<c>"d"');
    $transporte = RegistroAcademicoFalso::responder();

    app(RegistroAcademicoClient::class)->consultar(RegistroAcademicoFalso::CARNET);

    expect($transporte->solicitudes[0])->toContain('<PWD>a&amp;b&lt;c&gt;&quot;d&quot;</PWD>');
});

it('interpreta los datos personales y las carreras de la respuesta', function () {
    $datos = app(RegistroAcademicoClient::class)->consultar(RegistroAcademicoFalso::CARNET);

    expect($datos)->toBeInstanceOf(DatosAcademicos::class)
        ->carnet->toBe('201219511')
        ->nombre1->toBe('Madeley')
        ->nombre2->toBeNull()
        ->apellido1->toBe('Morales')
        ->apellido2->toBe('Vivar')
        ->direccion->toBeNull()
        ->cui->toBe('2165914570101')
        ->codigoNacionalidad->toBe('30')
        ->nacionalidad->toBe('Guatemalteca')
        ->carreras->toHaveCount(2);

    expect($datos->carreras[0])
        ->clave()->toBe('07-00-28')
        ->nombreUnidad->toBe('Facultad de Humanidades')
        ->nombreExtension->toBe('Plan Diario')
        ->grado->toBe('Tecnica')
        ->fechaGraduado->toBe('2016-07-05')
        ->cicloActivo->toBeNull()
        ->estaGraduado()->toBeTrue();

    expect($datos->carreras[1])
        ->clave()->toBe('77-00-66')
        ->grado->toBe('Licenciatura')
        ->cicloActivo->toBe('2017')
        ->estaGraduado()->toBeFalse();
});

it('corrige el texto UTF-8 leído dos veces como Latin-1', function () {
    $datos = app(RegistroAcademicoClient::class)->consultar(RegistroAcademicoFalso::CARNET);

    expect($datos->carreras[0]->nombreCarrera)
        ->toBe('Profesorado de Enseñanza  Media en Pedagogía, Ciencias Sociales y Formación Ciudadana')
        ->and($datos->carreras[1]->nombreCarrera)
        ->toBe('Licenciatura en Pedagogía y Administración Educativa');
});

it('conserva el texto correcto y el que no puede repararse', function () {
    RegistroAcademicoFalso::responder(<<<'XML'
    <RESP_CONSULTA_DATOS>
        <CARNET>201219511</CARNET>
        <NOMBRE1>José</NOMBRE1>
        <APELLIDO1>Ãngel “Pérez”</APELLIDO1>
        <CUI>2165914570101</CUI>
    </RESP_CONSULTA_DATOS>
    XML);

    $datos = app(RegistroAcademicoClient::class)->consultar(RegistroAcademicoFalso::CARNET);

    expect($datos->nombre1)->toBe('José')
        ->and($datos->apellido1)->toBe('Ãngel “Pérez”');
});

it('devuelve null cuando el servicio no conoce el carné', function (string $xml) {
    RegistroAcademicoFalso::responder($xml);

    expect(app(RegistroAcademicoClient::class)->consultar(RegistroAcademicoFalso::CARNET))->toBeNull();
})->with([
    'respuesta vacía' => '<RESP_CONSULTA_DATOS></RESP_CONSULTA_DATOS>',
    'carné vacío' => '<RESP_CONSULTA_DATOS><CARNET></CARNET></RESP_CONSULTA_DATOS>',
    'otro elemento raíz' => '<ERROR>Carné no existe</ERROR>',
    'carné distinto al solicitado' => '<RESP_CONSULTA_DATOS><CARNET>999999999</CARNET><CUI>1</CUI></RESP_CONSULTA_DATOS>',
    'estado de carné no encontrado' => '<RESP_CONSULTA_DATOS><STATUS>1</STATUS><MSG>Carné no existe</MSG></RESP_CONSULTA_DATOS>',
]);

it('trata "usuario no autorizado" como carné no reconocido y lo registra', function (string $xml) {
    Log::spy();
    RegistroAcademicoFalso::responder($xml);

    expect(app(RegistroAcademicoClient::class)->consultar(RegistroAcademicoFalso::CARNET))->toBeNull();

    Log::shouldHaveReceived('warning')->once();
})->with([
    'por estado' => '<RESP_CONSULTA_DATOS><STATUS>3</STATUS><MSG>usuario no autorizado</MSG></RESP_CONSULTA_DATOS>',
    'otro estado' => '<RESP_CONSULTA_DATOS><STATUS>9</STATUS><MSG>Usuario No Autorizado</MSG></RESP_CONSULTA_DATOS>',
]);

it('lee el nombre cuando el servicio lo entrega en un solo campo', function (string $nombre, array $esperado) {
    RegistroAcademicoFalso::responder("<RESP_CONSULTA_DATOS><CARNET>201219511</CARNET><NOMBRE>{$nombre}</NOMBRE><CUI>2165914570101</CUI></RESP_CONSULTA_DATOS>");

    $datos = app(RegistroAcademicoClient::class)->consultar(RegistroAcademicoFalso::CARNET);

    expect([$datos->nombre1, $datos->nombre2, $datos->nombre3, $datos->apellido1, $datos->apellido2])->toBe($esperado);
})->with([
    'cuatro palabras' => ['María José Pérez López', ['María', 'José', null, 'Pérez', 'López']],
    'tres palabras' => ['Juan Pérez López', ['Juan', null, null, 'Pérez', 'López']],
    'cinco palabras' => ['María José Fernanda Pérez López', ['María', 'José', 'Fernanda', 'Pérez', 'López']],
    'dos palabras' => ['Juan Pérez', ['Juan', null, null, 'Pérez', null]],
    'una palabra' => ['Juan', ['Juan', null, null, '', null]],
    'con texto mal codificado' => ['MarÃ­a JosÃ© PÃ©rez LÃ³pez', ['María', 'José', null, 'Pérez', 'López']],
]);

it('conserva el orden del nombre completo del servicio', function () {
    RegistroAcademicoFalso::responder('<RESP_CONSULTA_DATOS><CARNET>201219511</CARNET><NOMBRE>María José Pérez López</NOMBRE><CUI>2165914570101</CUI></RESP_CONSULTA_DATOS>');

    $datos = app(RegistroAcademicoClient::class)->consultar(RegistroAcademicoFalso::CARNET);
    $estudiante = new Estudiante(['nombre1' => $datos->nombre1, 'nombre2' => $datos->nombre2, 'apellido1' => $datos->apellido1, 'apellido2' => $datos->apellido2]);

    expect($estudiante->nombre_completo)->toBe('María José Pérez López');
});

it('acepta una respuesta con estado 0 que trae los datos del estudiante', function () {
    RegistroAcademicoFalso::responder('<RESP_CONSULTA_DATOS><STATUS>0</STATUS><CARNET>201219511</CARNET><NOMBRE1>Madeley</NOMBRE1><APELLIDO1>Morales</APELLIDO1><CUI>2165914570101</CUI></RESP_CONSULTA_DATOS>');

    expect(app(RegistroAcademicoClient::class)->consultar(RegistroAcademicoFalso::CARNET))->nombre1->toBe('Madeley');
});

it('falla cuando la respuesta no es XML', function (string $cuerpo) {
    RegistroAcademicoFalso::responder($cuerpo);

    app(RegistroAcademicoClient::class)->consultar(RegistroAcademicoFalso::CARNET);
})->with([
    'texto plano' => 'Servicio en mantenimiento',
    'XML mal formado' => '<RESP_CONSULTA_DATOS><CARNET>201219511',
    'vacía' => '',
])->throws(RegistroAcademicoNoDisponible::class);

it('propaga la falla cuando el transporte no puede comunicarse con el servicio', function () {
    RegistroAcademicoFalso::fallar();

    app(RegistroAcademicoClient::class)->consultar(RegistroAcademicoFalso::CARNET);
})->throws(RegistroAcademicoNoDisponible::class);

it('compara el CUI ignorando espacios y guiones', function (string $ingresado, bool $coincide) {
    $datos = new DatosAcademicos('1', 'A', null, null, 'B', null, null, '2165914570101', null, null, []);

    expect($datos->coincideCui($ingresado))->toBe($coincide);
})->with([
    'exacto' => ['2165914570101', true],
    'con espacios' => ['2165 91457 0101', true],
    'con guiones' => ['2165-91457-0101', true],
    'distinto' => ['2165914570102', false],
    'vacío' => ['', false],
    'solo letras' => ['abc', false],
]);

describe('transporte SOAP', function () {
    it('falla cuando la dirección del WSDL no está configurada', function () {
        config()->set('services.registro_academico.url', null);

        app(TransporteSoap::class)->datosGenerales('<SOLICITUD_DATOS_RYE/>');
    })->throws(RegistroAcademicoNoDisponible::class, 'no está configurado');

});
