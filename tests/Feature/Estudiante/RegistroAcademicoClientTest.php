<?php

use App\Services\RegistroAcademico\DatosAcademicos;
use App\Services\RegistroAcademico\RegistroAcademicoClient;
use App\Services\RegistroAcademico\RegistroAcademicoNoDisponible;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\Support\RegistroAcademicoFalso;

beforeEach(function () {
    RegistroAcademicoFalso::configurar();
});

it('envía la solicitud con las credenciales de la dependencia y el carné', function () {
    RegistroAcademicoFalso::responder();

    app(RegistroAcademicoClient::class)->consultar(RegistroAcademicoFalso::CARNET);

    Http::assertSent(fn (Request $request): bool => $request->url() === RegistroAcademicoFalso::URL
        && $request->method() === 'POST'
        && $request->hasHeader('Content-Type', 'text/xml; charset=UTF-8')
        && $request->body() === '<SOLICITUD_DATOS_RYE><DEPENDENCIA>epsum</DEPENDENCIA><LOGIN>epsumWS</LOGIN><PWD>clave</PWD><CARNET>201219511</CARNET></SOLICITUD_DATOS_RYE>');
});

it('escapa los caracteres especiales de XML en la solicitud', function () {
    config()->set('services.registro_academico.password', 'a&b<c>"d"');
    RegistroAcademicoFalso::responder();

    app(RegistroAcademicoClient::class)->consultar(RegistroAcademicoFalso::CARNET);

    Http::assertSent(fn (Request $request): bool => str_contains($request->body(), '<PWD>a&amp;b&lt;c&gt;&quot;d&quot;</PWD>'));
});

it('interpreta los datos personales y las carreras de la respuesta', function () {
    RegistroAcademicoFalso::responder();

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
    RegistroAcademicoFalso::responder();

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
]);

it('falla cuando la respuesta no es XML', function (string $cuerpo) {
    RegistroAcademicoFalso::responder($cuerpo);

    app(RegistroAcademicoClient::class)->consultar(RegistroAcademicoFalso::CARNET);
})->with([
    'texto plano' => 'Servicio en mantenimiento',
    'XML mal formado' => '<RESP_CONSULTA_DATOS><CARNET>201219511',
    'vacía' => '',
])->throws(RegistroAcademicoNoDisponible::class);

it('falla cuando el servicio responde con error', function () {
    Http::fake([RegistroAcademicoFalso::URL => Http::response('Error interno', 500)]);

    app(RegistroAcademicoClient::class)->consultar(RegistroAcademicoFalso::CARNET);
})->throws(RegistroAcademicoNoDisponible::class);

it('falla cuando no se puede conectar con el servicio', function () {
    Http::fake([RegistroAcademicoFalso::URL => fn () => throw new ConnectionException('Tiempo de espera agotado')]);

    app(RegistroAcademicoClient::class)->consultar(RegistroAcademicoFalso::CARNET);
})->throws(RegistroAcademicoNoDisponible::class);

it('falla cuando la dirección del servicio no está configurada', function () {
    config()->set('services.registro_academico.url', null);

    app(RegistroAcademicoClient::class)->consultar(RegistroAcademicoFalso::CARNET);
})->throws(RegistroAcademicoNoDisponible::class, 'no está configurado');

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
