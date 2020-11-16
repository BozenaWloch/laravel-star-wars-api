<?php
declare(strict_types=1);

namespace App\Providers;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\FilmsController;
use App\Http\Controllers\PlanetsController;
use App\Http\Controllers\SpeciesController;
use App\Http\Controllers\StarshipsController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\VehiclesController;
use App\Policies\AuthPolicy;
use App\Policies\FilmPolicy;
use App\Policies\PlanetPolicy;
use App\Policies\SpeciePolicy;
use App\Policies\StarshipPolicy;
use App\Policies\UserPolicy;
use App\Policies\VehiclePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        AuthController::class      => AuthPolicy::class,
        FilmsController::class     => FilmPolicy::class,
        PlanetsController::class   => PlanetPolicy::class,
        SpeciesController::class   => SpeciePolicy::class,
        StarshipsController::class => StarshipPolicy::class,
        UsersController::class     => UserPolicy::class,
        VehiclesController::class  => VehiclePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->registerPolicies();

        //
    }
}
