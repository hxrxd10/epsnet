<?php

use App\Services\RegistroAcademico\RegistroAcademicoClient;
use App\Services\RegistroAcademico\TransporteRegistroAcademico;
use App\Services\RegistroAcademico\TransporteSoap;

/*
 * Prueba de integración contra el servicio REAL de Registro y Estadística.
 *
 * Usa las credenciales de .env (REGISTRO_ACADEMICO_*), no las del código. Solo corre si en .env
 * están REGISTRO_ACADEMICO_PRUEBA_CARNET y, para verificar el DPI, REGISTRO_ACADEMICO_PRUEBA_DPI.
 * No escribas contraseñas, carnés ni DPI en este archivo: se suben al repositorio.
 */
$sinConfiguracion = fn (): bool => blank(config('services.registro_academico.url'))
    || blank(config('services.registro_academico.password'))
    || blank(config('services.registro_academico.prueba_carnet'));

beforeEach(function () {
    app()->bind(TransporteRegistroAcademico::class, TransporteSoap::class);
});

it('consulta al servicio real y reconoce al estudiante de prueba', function () {
    $carnet = (string) config('services.registro_academico.prueba_carnet');

    $datos = app(RegistroAcademicoClient::class)->consultar($carnet);

    expect($datos)->not->toBeNull('El servicio no devolvió datos para el carné de prueba (revisa las credenciales y el carné).')
        ->and($datos->carnet)->toBe($carnet)
        ->and($datos->nombre1)->not->toBe('')
        ->and($datos->apellido1)->not->toBe('')
        ->and($datos->cui)->not->toBe('')
        ->and($datos->carreras)->not->toBeEmpty();
})->skip($sinConfiguracion, 'Define REGISTRO_ACADEMICO_PRUEBA_CARNET (y las credenciales) en .env.');

it('el DPI de prueba coincide con el CUI que devuelve el servicio real', function () {
    $datos = app(RegistroAcademicoClient::class)->consultar((string) config('services.registro_academico.prueba_carnet'));

    expect($datos?->coincideCui((string) config('services.registro_academico.prueba_dpi')))->toBeTrue();
})->skip(
    fn (): bool => $sinConfiguracion() || blank(config('services.registro_academico.prueba_dpi')),
    'Define REGISTRO_ACADEMICO_PRUEBA_DPI en .env.',
);

it('no reconoce un carné inexistente en el servicio real', function () {
    expect(app(RegistroAcademicoClient::class)->consultar('100000001'))->toBeNull();
})->skip($sinConfiguracion, 'Define REGISTRO_ACADEMICO_PRUEBA_CARNET (y las credenciales) en .env.');
