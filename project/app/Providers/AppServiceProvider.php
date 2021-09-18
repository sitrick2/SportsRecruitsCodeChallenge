<?php

namespace App\Providers;

use App\Repositories\Team\TeamRepository;
use App\Repositories\Team\TeamRepositoryInterface;
use App\Repositories\User\UserRepository;
use App\Repositories\User\UserRepositoryInterface;
use App\Services\TeamGeneration\TeamGenerationService;
use App\Services\TeamGeneration\TeamGenerationServiceInterface;
use Faker\Generator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        /**
         * Singleton Interface Binding
         */
        $this->app->singleton(TeamRepositoryInterface::class, function () {
            return app(TeamRepository::class);
        });

        $this->app->singleton(UserRepositoryInterface::class, function () {
            return app(UserRepository::class);
        });

        $this->app->singleton(TeamGenerationServiceInterface::class, function () {
            return app(TeamGenerationService::class);
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
