<?php

use App\Enums\ClaveRol;
use App\Enums\EstadoExpediente;
use App\Enums\TipoCambioBitacora;
use App\Models\Bitacora;
use App\Models\Estudiante;
use App\Models\Expediente;
use App\Models\Rol;
use App\Models\UnidadAcademica;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

function coordinadorDeBandeja(?UnidadAcademica $unidad = null): User
{
    $usuario = User::factory()->create();
    $usuario->forceFill(['rol_id' => Rol::delSistema(ClaveRol::UnidadAcademica)->id])->save();
    $unidad?->forceFill(['administrador_id' => $usuario->id])->save();

    return $usuario;
}

/**
 * EPS en la bandeja de una unidad, enviado en el momento indicado.
 *
 * @param  array<string, mixed>  $atributos
 */
function solicitudEn(UnidadAcademica $unidad, string $enviada, string $nombre = 'Ana', array $atributos = []): Expediente
{
    $expediente = Expediente::factory()
        ->for(Estudiante::factory()->create(['nombre1' => $nombre, 'apellido1' => 'Solicitante']))
        ->create(['unidad_academica_id' => $unidad->id, 'aprobacion_solicitada_at' => $enviada, ...$atributos]);
    $expediente->bienesServicios()->create(['tipo' => 'servicio', 'descripcion' => 'Jornada', 'fecha' => '2026-03-01']);

    return $expediente;
}

describe('acceso', function () {
    it('envía a los invitados a iniciar sesión', function () {
        $this->get(route('panel.bandeja.index'))->assertRedirect(route('login'));
    });

    it('niega el acceso a invitados y estudiantes', function (string $estado) {
        $this->actingAs(User::factory()->{$estado}()->create());

        $this->get(route('panel.bandeja.index'))->assertForbidden();
    })->with(['invitado', 'estudiante']);
});

describe('orden de llegada', function () {
    it('lista primero la solicitud que llegó primero, con su posición', function () {
        $unidad = UnidadAcademica::factory()->create();
        solicitudEn($unidad, '2026-09-10 15:00:00', 'Segunda');
        solicitudEn($unidad, '2026-09-08 09:30:00', 'Primera');
        solicitudEn($unidad, '2026-09-12 08:00:00', 'Tercera');
        $this->actingAs(coordinadorDeBandeja($unidad));

        $this->get(route('panel.bandeja.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('panel/bandeja')
                ->where('pagina.total', 3)
                ->where('solicitudes', fn ($lista) => collect($lista)->map(fn ($s) => [$s['posicion'], explode(' ', $s['estudiante'])[0]])->all() === [[1, 'Primera'], [2, 'Segunda'], [3, 'Tercera']])
                ->where('solicitudes.0.carrera', 'Licenciatura en Pedagogía y Administración Educativa')
                ->where('solicitudes.0.registros', 1));
    });

    it('desempata por el orden en que se crearon cuando llegaron en el mismo instante', function () {
        $unidad = UnidadAcademica::factory()->create();
        $primero = solicitudEn($unidad, '2026-09-08 09:30:00', 'Uno');
        $segundo = solicitudEn($unidad, '2026-09-08 09:30:00', 'Dos');
        $this->actingAs(coordinadorDeBandeja($unidad));

        $this->get(route('panel.bandeja.index'))
            ->assertInertia(fn (Assert $page) => $page->where('solicitudes.0.id', $primero->id)->where('solicitudes.1.id', $segundo->id));
    });

    it('cuenta los días de espera', function () {
        $unidad = UnidadAcademica::factory()->create();
        solicitudEn($unidad, now()->subDays(3)->subHours(2)->toDateTimeString());
        $this->actingAs(coordinadorDeBandeja($unidad));

        $this->get(route('panel.bandeja.index'))->assertInertia(fn (Assert $page) => $page->where('solicitudes.0.dias_espera', 3));
    });
});

describe('quién ve qué', function () {
    it('muestra a cada unidad solo las solicitudes de sus estudiantes', function () {
        $humanidades = UnidadAcademica::factory()->create();
        $ingenieria = UnidadAcademica::factory()->create();
        solicitudEn($humanidades, '2026-09-08 09:00:00', 'Propia');
        solicitudEn($ingenieria, '2026-09-07 09:00:00', 'Ajena');
        $this->actingAs(coordinadorDeBandeja($humanidades));

        $this->get(route('panel.bandeja.index'))
            ->assertInertia(fn (Assert $page) => $page->has('solicitudes', 1)->where('solicitudes.0.estudiante', fn (string $nombre) => str_starts_with($nombre, 'Propia')));
    });

    it('muestra a DIGEU las de todas las unidades y le deja filtrar por unidad', function () {
        $humanidades = UnidadAcademica::factory()->create();
        $ingenieria = UnidadAcademica::factory()->create();
        solicitudEn($humanidades, '2026-09-08 09:00:00');
        solicitudEn($ingenieria, '2026-09-07 09:00:00');
        $this->actingAs(User::factory()->administrador()->create());

        $this->get(route('panel.bandeja.index'))->assertInertia(fn (Assert $page) => $page->has('solicitudes', 2)->has('unidades', 2));
        $this->get(route('panel.bandeja.index', ['unidad' => $ingenieria->id]))
            ->assertInertia(fn (Assert $page) => $page->has('solicitudes', 1)->where('solicitudes.0.unidad', $ingenieria->nombre));
    });

    it('ignora el filtro de unidad cuando quien consulta es una unidad', function () {
        $humanidades = UnidadAcademica::factory()->create();
        $ingenieria = UnidadAcademica::factory()->create();
        solicitudEn($humanidades, '2026-09-08 09:00:00');
        solicitudEn($ingenieria, '2026-09-07 09:00:00');
        $this->actingAs(coordinadorDeBandeja($humanidades));

        $this->get(route('panel.bandeja.index', ['unidad' => $ingenieria->id]))
            ->assertInertia(fn (Assert $page) => $page->has('solicitudes', 1)->where('unidades', []));
    });

    it('avisa a una unidad sin unidad asignada y no le muestra nada', function () {
        solicitudEn(UnidadAcademica::factory()->create(), '2026-09-08 09:00:00');
        $this->actingAs(coordinadorDeBandeja());

        $this->get(route('panel.bandeja.index'))->assertInertia(fn (Assert $page) => $page->where('sinUnidad', true)->has('solicitudes', 0));
    });
});

describe('qué entra en la bandeja', function () {
    it('solo muestra lo que se envió a aprobación y aún no se aprueba', function () {
        $unidad = UnidadAcademica::factory()->create();
        solicitudEn($unidad, '2026-09-08 09:00:00', 'Pendiente');
        solicitudEn($unidad, '2026-09-07 09:00:00', 'Aprobada', ['estado_expediente' => EstadoExpediente::Verificado, 'verificado_at' => now()]);
        Expediente::factory()->create(['unidad_academica_id' => $unidad->id]);
        $this->actingAs(coordinadorDeBandeja($unidad));

        $this->get(route('panel.bandeja.index'))
            ->assertInertia(fn (Assert $page) => $page->where('pagina.total', 1)->where('solicitudes.0.estudiante', fn (string $nombre) => str_starts_with($nombre, 'Pendiente')));
    });

    it('busca por nombre, carné o carrera', function () {
        $unidad = UnidadAcademica::factory()->create();
        solicitudEn($unidad, '2026-09-08 09:00:00', 'Marta');
        $otra = solicitudEn($unidad, '2026-09-09 09:00:00', 'Luis', ['nombre_carrera' => 'Ingeniería Civil']);
        $this->actingAs(coordinadorDeBandeja($unidad));

        $this->get(route('panel.bandeja.index', ['q' => 'Marta']))->assertInertia(fn (Assert $page) => $page->has('solicitudes', 1));
        $this->get(route('panel.bandeja.index', ['q' => 'Civil']))->assertInertia(fn (Assert $page) => $page->has('solicitudes', 1)->where('solicitudes.0.id', $otra->id));
        $this->get(route('panel.bandeja.index', ['q' => $otra->estudiante->carnet]))->assertInertia(fn (Assert $page) => $page->has('solicitudes', 1));
        $this->get(route('panel.bandeja.index', ['q' => 'nadie']))->assertInertia(fn (Assert $page) => $page->has('solicitudes', 0));
    });

    it('pagina de 15 en 15 conservando la posición', function () {
        $unidad = UnidadAcademica::factory()->create();
        foreach (range(1, 17) as $numero) {
            solicitudEn($unidad, now()->subMinutes(100 - $numero)->toDateTimeString());
        }
        $this->actingAs(coordinadorDeBandeja($unidad));

        $this->get(route('panel.bandeja.index', ['page' => 2]))
            ->assertInertia(fn (Assert $page) => $page->has('solicitudes', 2)->where('solicitudes.0.posicion', 16)->where('pagina.ultima', 2));
    });
});

describe('aviso en el menú', function () {
    it('informa cuántas solicitudes esperan a la unidad y a DIGEU, y nada al resto', function () {
        $unidad = UnidadAcademica::factory()->create();
        solicitudEn($unidad, '2026-09-08 09:00:00');
        solicitudEn($unidad, '2026-09-09 09:00:00');
        solicitudEn(UnidadAcademica::factory()->create(), '2026-09-09 09:00:00');

        $this->actingAs(coordinadorDeBandeja($unidad))->get(route('dashboard'))->assertInertia(fn (Assert $page) => $page->where('solicitudesPendientes', 2));
        $this->actingAs(User::factory()->administrador()->create())->get(route('dashboard'))->assertInertia(fn (Assert $page) => $page->where('solicitudesPendientes', 3));
        $this->actingAs(User::factory()->invitado()->create())->get(route('dashboard'))->assertInertia(fn (Assert $page) => $page->where('solicitudesPendientes', 0));
    });
});

describe('revisar y aprobar', function () {
    it('lleva al detalle con el aviso de la solicitud y permite volver a la bandeja', function () {
        $unidad = UnidadAcademica::factory()->create();
        $solicitud = solicitudEn($unidad, '2026-09-08 09:30:00');
        $this->actingAs(coordinadorDeBandeja($unidad));

        $this->get(route('panel.estudiantes.show', [$solicitud, 'desde' => 'bandeja']))
            ->assertInertia(fn (Assert $page) => $page
                ->where('desdeBandeja', true)
                ->where('puedeVerificar', true)
                ->where('expediente.solicitud_at', fn (string $iso) => str_starts_with($iso, '2026-09-08')));
    });

    it('al aprobar desde la bandeja sale de la lista y se vuelve a ella', function () {
        $unidad = UnidadAcademica::factory()->create();
        $primera = solicitudEn($unidad, '2026-09-08 09:00:00', 'Primera');
        $segunda = solicitudEn($unidad, '2026-09-09 09:00:00', 'Segunda');
        $this->actingAs(coordinadorDeBandeja($unidad));

        $this->post(route('panel.estudiantes.verificacion.store', $primera), ['acepto' => true, 'desde_bandeja' => true])
            ->assertRedirect(route('panel.bandeja.index'));

        expect($primera->fresh())->estado_expediente->toBe(EstadoExpediente::Verificado)->aprobacion_solicitada_at->toBeNull();

        $this->get(route('panel.bandeja.index'))
            ->assertInertia(fn (Assert $page) => $page->has('solicitudes', 1)->where('solicitudes.0.id', $segunda->id)->where('solicitudes.0.posicion', 1));
    });

    it('al aprobar desde el detalle se queda en el detalle', function () {
        $unidad = UnidadAcademica::factory()->create();
        $solicitud = solicitudEn($unidad, '2026-09-08 09:00:00');
        $this->actingAs(coordinadorDeBandeja($unidad));

        $this->post(route('panel.estudiantes.verificacion.store', $solicitud), ['acepto' => true])
            ->assertRedirect(route('panel.estudiantes.show', $solicitud));
    });

    it('sigue exigiendo el acepto para aprobar una solicitud', function () {
        $unidad = UnidadAcademica::factory()->create();
        $solicitud = solicitudEn($unidad, '2026-09-08 09:00:00');
        $this->actingAs(coordinadorDeBandeja($unidad));

        $this->post(route('panel.estudiantes.verificacion.store', $solicitud), ['desde_bandeja' => true])->assertSessionHasErrors('acepto');

        expect($solicitud->fresh()->estado_expediente)->toBe(EstadoExpediente::Activo);
    });

    it('retirar la aprobación no vuelve a poner el EPS en la bandeja', function () {
        $unidad = UnidadAcademica::factory()->create();
        $solicitud = solicitudEn($unidad, '2026-09-08 09:00:00');
        $this->actingAs(coordinadorDeBandeja($unidad));

        $this->post(route('panel.estudiantes.verificacion.store', $solicitud), ['acepto' => true]);
        $this->delete(route('panel.estudiantes.verificacion.destroy', $solicitud));

        expect($solicitud->fresh())->estado_expediente->toBe(EstadoExpediente::Completo)->aprobacion_solicitada_at->toBeNull();
        $this->get(route('panel.bandeja.index'))->assertInertia(fn (Assert $page) => $page->has('solicitudes', 0));
    });

    it('anota en la bitácora quién aprobó la solicitud', function () {
        $unidad = UnidadAcademica::factory()->create();
        $solicitud = solicitudEn($unidad, '2026-09-08 09:00:00');
        $coordinador = coordinadorDeBandeja($unidad);
        $this->actingAs($coordinador);

        $this->post(route('panel.estudiantes.verificacion.store', $solicitud), ['acepto' => true, 'desde_bandeja' => true]);

        expect(Bitacora::where('tipo_cambio', TipoCambioBitacora::Aprobacion)->sole())
            ->usuario_correo->toBe($coordinador->email)
            ->detalle->toContain('(sin orden de impresión)');
    });
});
