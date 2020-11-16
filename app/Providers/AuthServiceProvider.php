<?php
declare(strict_types=1);

namespace App\Providers;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\FilmsController;
use App\Http\Controllers\UserController;
use App\Policies\AuthPolicy;
use App\Policies\FilmPolicy;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        AuthController::class => AuthPolicy::class,
        FilmsController::class => FilmPolicy::class,
        UserController::class => UserPolicy::class,
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
