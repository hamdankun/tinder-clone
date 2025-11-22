<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Contracts\UserRepositoryContract;
use App\Repositories\Contracts\LikeRepositoryContract;
use App\Repositories\Contracts\PictureRepositoryContract;
use App\Repositories\Implementations\UserRepository;
use App\Repositories\Implementations\LikeRepository;
use App\Repositories\Implementations\PictureRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Bind repository interfaces to implementations
        $this->app->bind(
            UserRepositoryContract::class,
            UserRepository::class
        );

        $this->app->bind(
            LikeRepositoryContract::class,
            LikeRepository::class
        );

        $this->app->bind(
            PictureRepositoryContract::class,
            PictureRepository::class
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
