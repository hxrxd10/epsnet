<?php

namespace App\Providers;

use App\Enums\TipoCambioBitacora;
use App\Models\Bitacora;
use App\Models\Expediente;
use App\Services\RegistroAcademico\TransporteRegistroAcademico;
use App\Services\RegistroAcademico\TransporteSoap;
use Carbon\CarbonImmutable;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(TransporteRegistroAcademico::class, TransporteSoap::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();
        $this->configureRateLimiting();
        $this->configureMorphMap();
        $this->configureBitacora();
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }

    /**
     * Limita los intentos de acceso de estudiantes por carné e IP, y por IP en general.
     */
    protected function configureRateLimiting(): void
    {
        RateLimiter::for('acceso-estudiante', fn (Request $request): array => [
            Limit::perMinute(5)->by($request->string('registro_academico')->replaceMatches('/\D+/', '').'|'.$request->ip()),
            Limit::perMinute(30)->by((string) $request->ip()),
        ]);
    }

    /**
     * Alias estables para los registros a los que se adjuntan archivos.
     */
    protected function configureMorphMap(): void
    {
        Relation::morphMap([
            'expediente' => Expediente::class,
        ]);
    }

    /**
     * Anota en la bitácora cuándo entra y sale cada usuario; los cambios en los datos los anota `Auditable`.
     */
    protected function configureBitacora(): void
    {
        Event::listen(Login::class, fn (Login $evento) => Bitacora::registrar('Acceso al sistema', TipoCambioBitacora::Acceso, 'Inició sesión', actor: $evento->user));
        Event::listen(Logout::class, fn (Logout $evento) => $evento->user === null
            ? null
            : Bitacora::registrar('Acceso al sistema', TipoCambioBitacora::Acceso, 'Cerró sesión', actor: $evento->user));
    }
}
