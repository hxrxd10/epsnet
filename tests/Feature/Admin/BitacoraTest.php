<?php

use App\Enums\ClaveRol;
use App\Enums\EstadoExpediente;
use App\Enums\TipoCambioBitacora;
use App\Models\Bitacora;
use App\Models\Departamento;
use App\Models\Estudiante;
use App\Models\Expediente;
use App\Models\Municipio;
use App\Models\Rol;
use App\Models\UnidadAcademica;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\Support\RegistroAcademicoFalso;

beforeEach(function () {
    RegistroAcademicoFalso::configurar();
});

/**
 * Un estudiante con sesión iniciada y su expediente.
 *
 * @return array{0: Estudiante, 1: Expediente}
 */
function estudianteConExpediente(): array
{
    $estudiante = Estudiante::factory()->create(['nombre1' => 'Madeley', 'apellido1' => 'Morales', 'apellido2' => 'Vivar']);
    RegistroAcademicoFalso::iniciarSesion($estudiante);
    $expediente = Expediente::factory()->for($estudiante)->create(['nombre_carrera' => 'Licenciatura en Pedagogía']);

    return [$estudiante, $expediente];
}

/**
 * @return array<string, mixed>
 */
function bienValido(array $cambios = []): array
{
    return [
        'tipo' => 'servicio',
        'descripcion' => 'Jornada de alfabetización para adultos',
        'beneficiarios' => 'Adultos de la comunidad',
        'cantidad_beneficiarios' => 40,
        'fecha' => '2026-03-10',
        ...$cambios,
    ];
}

describe('registro automático', function () {
    it('anota quién creó un bien o servicio, con su correo, cuándo y dónde', function () {
        [, $expediente] = estudianteConExpediente();
        $usuario = auth()->user();

        $this->post(route('estudiante.expedientes.bienes-servicios.store', $expediente), bienValido())->assertSessionHasNoErrors();

        $registro = Bitacora::where('modulo', 'Bienes y servicios')->sole();

        expect($registro)
            ->usuario_id->toBe($usuario->id)
            ->usuario_nombre->toBe($usuario->name)
            ->usuario_correo->toBe($usuario->email)
            ->usuario_rol->toBe('estudiante')
            ->tipo_cambio->toBe(TipoCambioBitacora::Creacion)
            ->entidad_tipo->toBe('BienServicio')
            ->entidad_id->toBe($expediente->bienesServicios()->sole()->id)
            ->ip->not->toBeNull()
            ->and($registro->detalle)->toContain('Creó el servicio «Jornada de alfabetización para adultos»')
            ->and($registro->detalle)->toContain('EPS de Madeley Morales Vivar (Licenciatura en Pedagogía)')
            ->and($registro->valores_nuevos['descripcion'])->toBe('Jornada de alfabetización para adultos')
            ->and($registro->fecha_hora->isToday())->toBeTrue();
    });

    it('anota lo que cambió con su valor anterior y el nuevo', function () {
        [, $expediente] = estudianteConExpediente();
        $bien = $expediente->bienesServicios()->create(bienValido());

        $this->put(route('estudiante.expedientes.bienes-servicios.update', [$expediente, $bien->id]), bienValido(['cantidad_beneficiarios' => 75]))
            ->assertSessionHasNoErrors();

        $registro = Bitacora::where('tipo_cambio', TipoCambioBitacora::Edicion)->sole();

        expect($registro->valores_anteriores)->toBe(['cantidad_beneficiarios' => 40])
            ->and($registro->valores_nuevos)->toBe(['cantidad_beneficiarios' => 75])
            ->and($registro->detalle)->toContain('Editó')->toContain('Campos: cantidad_beneficiarios');
    });

    it('anota lo que se eliminó', function () {
        [, $expediente] = estudianteConExpediente();
        $bien = $expediente->bienesServicios()->create(bienValido());

        $this->delete(route('estudiante.expedientes.bienes-servicios.destroy', [$expediente, $bien->id]));

        $registro = Bitacora::where('tipo_cambio', TipoCambioBitacora::Eliminacion)->sole();

        expect($registro->detalle)->toContain('Eliminó el servicio')
            ->and($registro->valores_anteriores['descripcion'])->toBe('Jornada de alfabetización para adultos')
            ->and($registro->valores_nuevos)->toBeNull();
    });

    it('anota el resto de los ejes con su propio módulo', function () {
        [, $expediente] = estudianteConExpediente();
        $municipio = Municipio::factory()->for(Departamento::factory()->create(['nombre' => 'Sacatepéquez']))->create(['nombre' => 'Antigua Guatemala']);

        $this->post(route('estudiante.expedientes.territorio.store', $expediente), ['departamento_id' => $municipio->departamento_id, 'municipio_id' => $municipio->id])
            ->assertSessionHasNoErrors();

        $registro = Bitacora::where('modulo', 'Territorio y geolocalización')->sole();

        expect($registro->detalle)->toContain('Creó la ubicación Antigua Guatemala, Sacatepéquez');
    });

    it('no anota cambios que solo tocan las fechas de la fila ni los que están en la lista de omitidos', function () {
        [, $expediente] = estudianteConExpediente();
        $bien = $expediente->bienesServicios()->create(bienValido());
        Bitacora::query()->getQuery()->delete();

        $bien->touch();
        $expediente->update(['eje_actual' => 4]);

        expect(Bitacora::count())->toBe(0);
    });

    it('nunca guarda contraseñas ni datos secretos', function () {
        $usuario = User::factory()->invitado()->create();
        $this->actingAs($usuario);
        Bitacora::query()->getQuery()->delete();

        $usuario->update(['password' => 'otra-clave-muy-larga-123', 'remember_token' => 'abc']);
        expect(Bitacora::count())->toBe(0);

        $usuario->update(['name' => 'Nombre Nuevo', 'password' => 'otra-mas-larga-456']);

        $registro = Bitacora::sole();

        expect($registro->valores_nuevos)->toBe(['name' => 'Nombre Nuevo'])
            ->and(json_encode($registro->getAttributes()))->not->toContain('otra-mas');
    });

    it('recorta los valores muy largos', function () {
        [, $expediente] = estudianteConExpediente();

        $this->post(route('estudiante.expedientes.bienes-servicios.store', $expediente), bienValido(['descripcion' => str_repeat('a', 5000)]));

        expect(mb_strlen(Bitacora::where('modulo', 'Bienes y servicios')->sole()->valores_nuevos['descripcion']))->toBeLessThanOrEqual(300);
    });

    it('anota el cambio de rol con el nombre del rol, no con su id', function () {
        $administrador = User::factory()->administrador()->create();
        $invitado = User::factory()->invitado()->create();
        $this->actingAs($administrador);

        $this->put(route('admin.usuarios.update', $invitado), ['rol' => 'digeu']);

        $registro = Bitacora::where('modulo', 'Usuarios')->where('tipo_cambio', TipoCambioBitacora::Edicion)->sole();

        expect($registro->usuario_correo)->toBe($administrador->email)
            ->and($registro->usuario_rol)->toBe('digeu')
            ->and($registro->valores_anteriores)->toBe(['rol' => 'invitado'])
            ->and($registro->valores_nuevos)->toBe(['rol' => 'digeu']);
    });

    it('anota el registro de una cuenta nueva a nombre de quien se registra', function () {
        $this->post(route('registro.store'), [
            'name' => 'Persona Nueva',
            'email' => 'nueva@example.com',
            'password' => 'una-clave-larga-123',
            'password_confirmation' => 'una-clave-larga-123',
        ])->assertSessionHasNoErrors();

        $registro = Bitacora::where('modulo', 'Usuarios')->where('tipo_cambio', TipoCambioBitacora::Creacion)->sole();

        expect($registro->usuario_correo)->toBe('nueva@example.com')
            ->and($registro->valores_nuevos)->not->toHaveKey('password');
    });

    it('anota el inicio y el cierre de sesión', function () {
        $usuario = User::factory()->administrador()->create(['email' => 'digeu@example.com']);

        $this->post(route('login.store'), ['email' => 'digeu@example.com', 'password' => 'password']);
        $this->post(route('logout'));

        expect(Bitacora::where('modulo', 'Acceso al sistema')->orderBy('id')->get()->map(fn (Bitacora $r): array => [$r->tipo_cambio, $r->detalle, $r->usuario_correo])->all())
            ->toBe([
                [TipoCambioBitacora::Acceso, 'Inició sesión', 'digeu@example.com'],
                [TipoCambioBitacora::Acceso, 'Cerró sesión', 'digeu@example.com'],
            ])
            ->and($usuario->exists)->toBeTrue();
    });

    it('no anota lo que ocurre sin nadie a quien atribuirlo, como las siembras', function () {
        Expediente::factory()->create();
        UnidadAcademica::factory()->create();

        expect(Bitacora::count())->toBe(0);
    });
});

describe('aprobación', function () {
    it('anota quién aprobó el EPS y la constancia de que lo descrito se ejecutó', function () {
        $unidad = UnidadAcademica::factory()->create();
        $coordinador = User::factory()->create(['email' => 'coordinador@usac.example']);
        $coordinador->forceFill(['rol_id' => Rol::delSistema(ClaveRol::UnidadAcademica)->id])->save();
        $unidad->update(['administrador_id' => $coordinador->id]);
        $expediente = Expediente::factory()->for(Estudiante::factory()->create(['nombre1' => 'Ana', 'apellido1' => 'López', 'apellido2' => 'Pérez']))
            ->create(['unidad_academica_id' => $unidad->id, 'estado_expediente' => EstadoExpediente::Completo, 'nombre_carrera' => 'Psicología']);
        $this->actingAs($coordinador);

        $this->post(route('panel.estudiantes.verificacion.store', $expediente), ['acepto' => true]);
        $this->delete(route('panel.estudiantes.verificacion.destroy', $expediente));

        $registros = Bitacora::where('modulo', 'Expedientes (EPS)')->orderBy('id')->get();

        expect($registros)->toHaveCount(2)
            ->and($registros[0])->tipo_cambio->toBe(TipoCambioBitacora::Aprobacion)->usuario_correo->toBe('coordinador@usac.example')
            ->and($registros[0]->detalle)->toContain('Aprobó el EPS de Ana López Pérez (Psicología)')->toContain('está comprobado y que se ejecutó')
            ->and($registros[1])->tipo_cambio->toBe(TipoCambioBitacora::RetiroAprobacion);
    });
});

describe('inmutabilidad', function () {
    it('no permite editar ni eliminar un registro de la bitácora', function () {
        $registro = Bitacora::create([
            'modulo' => 'Prueba',
            'tipo_cambio' => TipoCambioBitacora::Creacion,
            'detalle' => 'Original',
            'fecha_hora' => now(),
        ]);

        expect(fn () => $registro->update(['detalle' => 'Alterado']))->toThrow(LogicException::class)
            ->and(fn () => $registro->delete())->toThrow(LogicException::class)
            ->and($registro->fresh()->detalle)->toBe('Original');
    });

    it('no ofrece ninguna ruta para modificarla', function () {
        $rutas = collect(app('router')->getRoutes()->getRoutes())->filter(fn ($ruta) => str_contains($ruta->uri(), 'bitacora'));

        expect($rutas->flatMap->methods()->unique()->values()->sort()->values()->all())->toBe(['GET', 'HEAD']);
    });
});

describe('consulta (DIGEU)', function () {
    it('envía a los invitados a iniciar sesión y niega el acceso a los demás roles', function (string $estado) {
        $this->get(route('admin.bitacora.index'))->assertRedirect(route('login'));

        $this->actingAs(User::factory()->{$estado}()->create());
        $this->get(route('admin.bitacora.index'))->assertForbidden();
    })->with(['invitado', 'estudiante']);

    it('lista los registros más recientes primero, con quién, qué y dónde', function () {
        $administrador = User::factory()->administrador()->create(['name' => 'Ana Admin', 'email' => 'ana@digeu.example']);
        $this->actingAs($administrador);
        Bitacora::query()->getQuery()->delete();
        Bitacora::create(['usuario_nombre' => 'Viejo', 'modulo' => 'Municipios', 'tipo_cambio' => TipoCambioBitacora::Creacion, 'detalle' => 'Creó el municipio «A»', 'fecha_hora' => '2026-01-01 10:00:00']);
        Bitacora::create([
            'usuario_nombre' => 'Ana Admin', 'usuario_correo' => 'ana@digeu.example', 'usuario_rol' => 'digeu', 'modulo' => 'Departamentos',
            'tipo_cambio' => TipoCambioBitacora::Edicion, 'detalle' => 'Editó el departamento «B»', 'valores_anteriores' => ['cabecera' => 'X'],
            'valores_nuevos' => ['cabecera' => 'Y'], 'ip' => '10.0.0.1', 'fecha_hora' => '2026-02-01 11:30:00',
        ]);

        $this->get(route('admin.bitacora.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('admin/bitacora')
                ->has('registros', 2)
                ->where('registros.0.usuario', 'Ana Admin')
                ->where('registros.0.correo', 'ana@digeu.example')
                ->where('registros.0.rol', 'DIGEU')
                ->where('registros.0.tipo_etiqueta', 'Editó')
                ->where('registros.0.modulo', 'Departamentos')
                ->where('registros.0.fecha', '2026-02-01 11:30:00')
                ->where('registros.0.anteriores', ['cabecera' => 'X'])
                ->where('registros.1.usuario', 'Viejo')
                ->where('modulos', ['Departamentos', 'Municipios']));
    });

    it('filtra por usuario o correo, módulo, tipo y rango de fechas', function () {
        $this->actingAs(User::factory()->administrador()->create());
        Bitacora::query()->getQuery()->delete();
        $crear = fn (string $nombre, string $correo, string $modulo, TipoCambioBitacora $tipo, string $fecha) => Bitacora::create([
            'usuario_nombre' => $nombre, 'usuario_correo' => $correo, 'modulo' => $modulo, 'tipo_cambio' => $tipo, 'detalle' => "$nombre $modulo", 'fecha_hora' => $fecha,
        ]);
        $crear('Ana', 'ana@example.com', 'Municipios', TipoCambioBitacora::Creacion, '2026-03-01 08:00:00');
        $crear('Luis', 'luis@example.com', 'Municipios', TipoCambioBitacora::Eliminacion, '2026-03-10 23:59:00');
        $crear('Luis', 'luis@example.com', 'Departamentos', TipoCambioBitacora::Edicion, '2026-04-01 00:00:00');

        $consulta = fn (array $filtros) => $this->get(route('admin.bitacora.index', $filtros));

        $consulta(['q' => 'ana@'])->assertInertia(fn (Assert $page) => $page->has('registros', 1)->where('registros.0.usuario', 'Ana'));
        $consulta(['q' => 'luis'])->assertInertia(fn (Assert $page) => $page->has('registros', 2));
        $consulta(['modulo' => 'Municipios'])->assertInertia(fn (Assert $page) => $page->has('registros', 2));
        $consulta(['tipo' => 'edicion'])->assertInertia(fn (Assert $page) => $page->has('registros', 1)->where('registros.0.modulo', 'Departamentos'));
        $consulta(['desde' => '2026-03-05', 'hasta' => '2026-03-10'])->assertInertia(fn (Assert $page) => $page->has('registros', 1)->where('registros.0.usuario', 'Luis'));
        $consulta(['q' => 'nadie'])->assertInertia(fn (Assert $page) => $page->has('registros', 0));
    });

    it('rechaza filtros inválidos', function () {
        $this->actingAs(User::factory()->administrador()->create());

        $this->get(route('admin.bitacora.index', ['tipo' => 'inventado', 'desde' => '2026-03-10', 'hasta' => '2026-03-01']))
            ->assertSessionHasErrors(['tipo', 'hasta']);
    });

    it('pagina de 25 en 25', function () {
        $this->actingAs(User::factory()->administrador()->create());
        Bitacora::query()->getQuery()->delete();
        foreach (range(1, 30) as $numero) {
            Bitacora::create(['modulo' => 'Prueba', 'tipo_cambio' => TipoCambioBitacora::Creacion, 'detalle' => "Registro $numero", 'fecha_hora' => now()]);
        }

        $this->get(route('admin.bitacora.index'))
            ->assertInertia(fn (Assert $page) => $page->has('registros', 25)->where('pagina.total', 30)->where('pagina.ultima', 2));
    });
});
