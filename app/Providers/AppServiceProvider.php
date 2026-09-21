<?php

namespace App\Providers;

use App\Models\Expediente;
use Carbon\CarbonImmutable;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
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
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();
        $this->configureRateLimiting();
        $this->configureMorphMap();
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
}
