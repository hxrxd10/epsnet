<?php

use App\Enums\ClaveRol;
use App\Enums\EstadoExpediente;
use App\Enums\TipoCambioBitacora;
use App\Models\Adjunto;
use App\Models\Bitacora;
use App\Models\Estudiante;
use App\Models\Expediente;
use App\Models\Rol;
use App\Models\UnidadAcademica;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

function usuarioDeUnidad(?UnidadAcademica $unidad = null): User
{
    $usuario = User::factory()->create();
    $usuario->forceFill(['rol_id' => Rol::delSistema(ClaveRol::UnidadAcademica)->id])->save();
    $unidad?->forceFill(['administrador_id' => $usuario->id])->save();

    return $usuario;
}

function expedienteDeUnidad(UnidadAcademica $unidad, array $atributos = []): Expediente
{
    return Expediente::factory()
        ->for(Estudiante::factory()->create(['nombre1' => 'Madeley', 'apellido1' => 'Morales']))
        ->create(['unidad_academica_id' => $unidad->id, ...$atributos]);
}

describe('acceso', function () {
    it('envía a los invitados a iniciar sesión', function () {
        $this->get(route('panel.estudiantes.index'))->assertRedirect(route('login'));
    });

    it('niega el acceso a invitados y estudiantes', function (string $estado) {
        $expediente = Expediente::factory()->create();
        $this->actingAs(User::factory()->{$estado}()->create());

        $this->get(route('panel.estudiantes.index'))->assertForbidden();
        $this->get(route('panel.estudiantes.show', $expediente))->assertForbidden();
    })->with(['invitado', 'estudiante']);
});

describe('listado', function () {
    it('muestra a DIGEU todos los EPS', function () {
        $this->actingAs(User::factory()->administrador()->create());
        expedienteDeUnidad(UnidadAcademica::factory()->create());
        expedienteDeUnidad(UnidadAcademica::factory()->create());

        $this->get(route('panel.estudiantes.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('estudiantes/index')
                ->has('expedientes', 2)
                ->where('ambito', 'Todas las unidades académicas')
                ->has('unidades', 2));
    });

    it('muestra a una unidad académica solo los EPS de su unidad', function () {
        $propia = UnidadAcademica::factory()->create(['nombre' => 'Facultad de Humanidades']);
        $otra = UnidadAcademica::factory()->create();
        expedienteDeUnidad($propia);
        expedienteDeUnidad($propia);
        expedienteDeUnidad($otra);
        $this->actingAs(usuarioDeUnidad($propia));

        $this->get(route('panel.estudiantes.index'))
            ->assertInertia(fn (Assert $page) => $page
                ->has('expedientes', 2)
                ->where('ambito', 'Facultad de Humanidades')
                ->where('unidades', [])
                ->where('sinUnidad', false));
    });

    it('no muestra nada a una unidad académica sin unidad asignada', function () {
        expedienteDeUnidad(UnidadAcademica::factory()->create());
        $this->actingAs(usuarioDeUnidad());

        $this->get(route('panel.estudiantes.index'))
            ->assertInertia(fn (Assert $page) => $page->has('expedientes', 0)->where('sinUnidad', true));
    });

    it('filtra por nombre, carné, estado y unidad', function () {
        $this->actingAs(User::factory()->administrador()->create());
        $humanidades = UnidadAcademica::factory()->create();
        $ingenieria = UnidadAcademica::factory()->create();
        $ana = Expediente::factory()->for(Estudiante::factory()->create(['nombre1' => 'Ana', 'apellido1' => 'Pérez', 'carnet' => '201100001']))->create(['unidad_academica_id' => $humanidades->id, 'estado_expediente' => EstadoExpediente::Completo]);
        Expediente::factory()->for(Estudiante::factory()->create(['nombre1' => 'Luis', 'apellido1' => 'Gómez', 'carnet' => '201100002']))->create(['unidad_academica_id' => $ingenieria->id]);

        $this->get(route('panel.estudiantes.index', ['q' => 'Pérez']))->assertInertia(fn (Assert $page) => $page->has('expedientes', 1)->where('expedientes.0.id', $ana->id));
        $this->get(route('panel.estudiantes.index', ['q' => '201100002']))->assertInertia(fn (Assert $page) => $page->has('expedientes', 1)->where('expedientes.0.carnet', '201100002'));
        $this->get(route('panel.estudiantes.index', ['estado' => 'completo']))->assertInertia(fn (Assert $page) => $page->has('expedientes', 1)->where('expedientes.0.id', $ana->id));
        $this->get(route('panel.estudiantes.index', ['unidad' => $ingenieria->id]))->assertInertia(fn (Assert $page) => $page->has('expedientes', 1));
    });

    it('ignora el filtro de unidad para una unidad académica', function () {
        $propia = UnidadAcademica::factory()->create();
        $otra = UnidadAcademica::factory()->create();
        expedienteDeUnidad($propia);
        expedienteDeUnidad($otra);
        $this->actingAs(usuarioDeUnidad($propia));

        $this->get(route('panel.estudiantes.index', ['unidad' => $otra->id]))
            ->assertInertia(fn (Assert $page) => $page->has('expedientes', 1));
    });

    it('pagina los resultados', function () {
        $this->actingAs(User::factory()->administrador()->create());
        Expediente::factory()->count(17)->create();

        $this->get(route('panel.estudiantes.index'))
            ->assertInertia(fn (Assert $page) => $page->has('expedientes', 15)->where('pagina.total', 17)->where('pagina.ultima', 2));
    });
});

describe('detalle', function () {
    it('muestra a la unidad lo que su estudiante registró', function () {
        $unidad = UnidadAcademica::factory()->create();
        $expediente = expedienteDeUnidad($unidad, ['estado_expediente' => EstadoExpediente::Completo]);
        $expediente->bienesServicios()->create(['tipo' => 'servicio', 'descripcion' => 'Jornada de alfabetización', 'fecha' => '2026-03-10']);
        Adjunto::factory()->for($expediente, 'entidad')->conContenido()->create();
        $this->actingAs(usuarioDeUnidad($unidad));

        $this->get(route('panel.estudiantes.show', $expediente))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('estudiantes/show')
                ->where('expediente.estudiante', 'Madeley Morales '.$expediente->estudiante->apellido2)
                ->where('expediente.estado', 'completo')
                ->where('puedeVerificar', true)
                ->where('orden_impresion.nombre', 'orden-de-impresion.pdf')
                ->has('ejes', 6)
                ->where('ejes.0.registros.0.textos.0', ['etiqueta' => 'Descripción', 'valor' => 'Jornada de alfabetización']));
    });

    it('permite a DIGEU ver cualquier EPS', function () {
        $expediente = expedienteDeUnidad(UnidadAcademica::factory()->create());
        $this->actingAs(User::factory()->administrador()->create());

        $this->get(route('panel.estudiantes.show', $expediente))->assertOk();
    });

    it('oculta a una unidad los EPS de otras unidades', function () {
        $ajeno = expedienteDeUnidad(UnidadAcademica::factory()->create(), ['estado_expediente' => EstadoExpediente::Completo]);
        Adjunto::factory()->for($ajeno, 'entidad')->conContenido()->create();
        $this->actingAs(usuarioDeUnidad(UnidadAcademica::factory()->create()));

        $this->get(route('panel.estudiantes.show', $ajeno))->assertNotFound();
        $this->get(route('panel.estudiantes.orden-impresion', $ajeno))->assertNotFound();
        $this->post(route('panel.estudiantes.verificacion.store', $ajeno))->assertNotFound();
        $this->delete(route('panel.estudiantes.verificacion.destroy', $ajeno))->assertNotFound();

        expect($ajeno->fresh()->estado_expediente)->toBe(EstadoExpediente::Completo);
    });

    it('oculta todo a una unidad académica sin unidad asignada', function () {
        $expediente = expedienteDeUnidad(UnidadAcademica::factory()->create());
        $this->actingAs(usuarioDeUnidad());

        $this->get(route('panel.estudiantes.show', $expediente))->assertNotFound();
    });

    it('permite descargar la orden de impresión de un EPS de su unidad', function () {
        $unidad = UnidadAcademica::factory()->create();
        $expediente = expedienteDeUnidad($unidad, ['estado_expediente' => EstadoExpediente::Completo]);
        Adjunto::factory()->for($expediente, 'entidad')->conContenido('%PDF-1.4 orden')->create();
        $this->actingAs(usuarioDeUnidad($unidad));

        $respuesta = $this->get(route('panel.estudiantes.orden-impresion', $expediente))->assertOk()->assertDownload('orden-de-impresion.pdf');

        expect($respuesta->streamedContent())->toBe('%PDF-1.4 orden');
    });
});

describe('aprobación (doble verificación)', function () {
    it('permite a la unidad aprobar un EPS completo', function () {
        $unidad = UnidadAcademica::factory()->create();
        $expediente = expedienteDeUnidad($unidad, ['estado_expediente' => EstadoExpediente::Completo]);
        $coordinador = usuarioDeUnidad($unidad);
        $this->actingAs($coordinador);

        $this->post(route('panel.estudiantes.verificacion.store', $expediente), ['acepto' => true])
            ->assertRedirect(route('panel.estudiantes.show', $expediente));

        expect($expediente->fresh())
            ->estado_expediente->toBe(EstadoExpediente::Verificado)
            ->verificado_por->toBe($coordinador->id)
            ->verificado_at->not->toBeNull();
    });

    it('permite a DIGEU aprobar y retirar la aprobación', function () {
        $expediente = expedienteDeUnidad(UnidadAcademica::factory()->create(), ['estado_expediente' => EstadoExpediente::Completo]);
        $this->actingAs(User::factory()->administrador()->create());

        $this->post(route('panel.estudiantes.verificacion.store', $expediente), ['acepto' => true]);
        expect($expediente->fresh()->estado_expediente)->toBe(EstadoExpediente::Verificado);

        $this->delete(route('panel.estudiantes.verificacion.destroy', $expediente));
        expect($expediente->fresh())
            ->estado_expediente->toBe(EstadoExpediente::Completo)
            ->verificado_at->toBeNull()
            ->verificado_por->toBeNull();
    });

    it('exige confirmar que lo descrito está comprobado y se ejecutó', function (array $cuerpo) {
        $expediente = expedienteDeUnidad(UnidadAcademica::factory()->create(), ['estado_expediente' => EstadoExpediente::Completo]);
        $this->actingAs(User::factory()->administrador()->create());

        $this->post(route('panel.estudiantes.verificacion.store', $expediente), $cuerpo)
            ->assertSessionHasErrors(['acepto' => 'Confirma que lo descrito en el EPS está comprobado y que se ejecutó para poder aprobarlo.']);

        expect($expediente->fresh()->estado_expediente)->toBe(EstadoExpediente::Completo);
    })->with([
        'sin el acepto' => [[]],
        'con el acepto en falso' => [['acepto' => false]],
    ]);

    it('permite aprobar sin orden de impresión un EPS con información aunque el estudiante no lo haya cerrado', function () {
        $unidad = UnidadAcademica::factory()->create();
        $expediente = expedienteDeUnidad($unidad);
        $expediente->bienesServicios()->create(['tipo' => 'servicio', 'descripcion' => 'Jornada médica', 'fecha' => '2026-03-01']);
        $this->actingAs(usuarioDeUnidad($unidad));

        $this->get(route('panel.estudiantes.show', $expediente))
            ->assertInertia(fn (Assert $page) => $page->where('puedeVerificar', true)->where('orden_impresion', null));

        $this->post(route('panel.estudiantes.verificacion.store', $expediente), ['acepto' => true])->assertRedirect();

        expect($expediente->fresh()->estado_expediente)->toBe(EstadoExpediente::Verificado)
            ->and(Bitacora::where('tipo_cambio', TipoCambioBitacora::Aprobacion)->sole()->detalle)
            ->toContain('(sin orden de impresión)')->toContain('está comprobado y que se ejecutó');
    });

    it('sigue exigiendo el acepto cuando no hay orden de impresión', function () {
        $unidad = UnidadAcademica::factory()->create();
        $expediente = expedienteDeUnidad($unidad, ['estado_expediente' => EstadoExpediente::Completo]);
        $this->actingAs(usuarioDeUnidad($unidad));

        $this->post(route('panel.estudiantes.verificacion.store', $expediente))->assertSessionHasErrors('acepto');

        expect($expediente->fresh()->estado_expediente)->toBe(EstadoExpediente::Completo);
    });

    it('no permite aprobar un EPS que el estudiante aún no completa', function () {
        $expediente = expedienteDeUnidad(UnidadAcademica::factory()->create());
        $this->actingAs(User::factory()->administrador()->create());

        $this->post(route('panel.estudiantes.verificacion.store', $expediente))->assertForbidden();

        expect($expediente->fresh()->estado_expediente)->toBe(EstadoExpediente::Activo);
    });
});
