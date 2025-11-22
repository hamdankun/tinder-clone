<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Contracts\UserRepositoryContract;
use App\Repositories\Contracts\PictureRepositoryContract;
use App\Repositories\Contracts\LikeRepositoryContract;
use App\Repositories\Contracts\DislikeRepositoryContract;
use App\Repositories\Implementations\UserRepository;
use App\Repositories\Implementations\PictureRepository;
use App\Repositories\Implementations\LikeRepository;
use App\Repositories\Implementations\DislikeRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Bind repository interfaces to their implementations
        $this->app->bind(
            UserRepositoryContract::class,
            UserRepository::class
        );

        $this->app->bind(
            PictureRepositoryContract::class,
            PictureRepository::class
        );

        $this->app->bind(
            LikeRepositoryContract::class,
            LikeRepository::class
        );

        $this->app->bind(
            DislikeRepositoryContract::class,
            DislikeRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
