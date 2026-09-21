<?php

use App\Enums\ClaveRol;
use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Testing\TestResponse;
use Inertia\Testing\AssertableInertia as Assert;

function registrarse(array $datos = []): TestResponse
{
    return test()->post(route('registro.store'), array_merge([
        'name' => 'Ana López',
        'email' => 'ana@example.com',
        'password' => 'contraseña-segura-1',
        'password_confirmation' => 'contraseña-segura-1',
    ], $datos));
}

it('muestra el formulario de registro a los invitados', function () {
    $this->get(route('registro'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component('auth/register'));
});

it('crea la cuenta con rol de invitado e inicia sesión', function () {
    Notification::fake();

    registrarse(['email' => '  Ana@Example.COM '])->assertRedirect(route('dashboard'));

    $usuario = User::firstWhere('email', 'ana@example.com');

    expect($usuario)->not->toBeNull()
        ->name->toBe('Ana López')
        ->activo->toBeTrue()
        ->email_verified_at->toBeNull()
        ->and($usuario->tieneRol(ClaveRol::Invitado))->toBeTrue()
        ->and($usuario->esEstudiante())->toBeFalse()
        ->and(Hash::check('contraseña-segura-1', $usuario->password))->toBeTrue();

    $this->assertAuthenticatedAs($usuario);
});

it('envía el correo de verificación al registrarse', function () {
    Notification::fake();

    registrarse();

    Notification::assertSentTo(User::firstWhere('email', 'ana@example.com'), VerifyEmail::class);
});

it('pide verificar el correo antes de entrar al panel', function () {
    Notification::fake();
    registrarse();

    $this->get(route('dashboard'))->assertRedirect(route('verification.notice'));
});

it('valida los datos del registro', function (array $datos, array $errores) {
    User::factory()->create(['email' => 'ocupado@example.com']);

    registrarse($datos)->assertSessionHasErrors($errores);

    $this->assertGuest();
    expect(User::count())->toBe(1);
})->with([
    'sin nombre' => [['name' => ''], ['name' => 'Ingresa el nombre.']],
    'correo ocupado' => [['email' => 'ocupado@example.com'], ['email' => 'Ya existe una cuenta con este correo.']],
    'correo inválido' => [['email' => 'no-es-correo'], ['email']],
    'contraseña sin confirmar' => [['password_confirmation' => 'otra'], ['password']],
    'contraseña muy corta' => [['password' => 'corta', 'password_confirmation' => 'corta'], ['password']],
]);

it('limita los registros repetidos desde el mismo lugar', function () {
    Notification::fake();

    foreach (range(1, 6) as $numero) {
        $this->post(route('registro.store'), ['name' => 'X', 'email' => "x{$numero}@example.com"]);
    }

    registrarse()->assertTooManyRequests();
});

it('no muestra el registro a quien ya inició sesión', function () {
    $this->actingAs(User::factory()->invitado()->create())
        ->get(route('registro'))
        ->assertRedirect(route('dashboard'));
});

it('deja a los invitados fuera de las secciones de estudiantes y administradores', function (string $ruta) {
    $this->actingAs(User::factory()->invitado()->create())
        ->get(route($ruta))
        ->assertForbidden();
})->with(['estudiante.carreras', 'admin.datos']);

it('comparte el rol del usuario con la interfaz', function (string $estado, ?string $rol) {
    $usuario = User::factory()->{$estado}()->create();

    $this->actingAs($usuario)
        ->get(route('dashboard'))
        ->assertInertia(fn (Assert $page) => $page->where('rol', $rol));
})->with([
    ['invitado', 'invitado'],
    ['administrador', 'digeu'],
    ['estudiante', 'estudiante'],
]);
