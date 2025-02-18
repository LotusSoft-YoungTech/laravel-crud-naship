<?php

namespace App\Providers;

use App\Repositories\Interfaces\ProductApiRepositoryInterface;
use App\Repositories\Interfaces\ProductRepositoryInterface;
use App\Repositories\ProductApiRepository;
use App\Repositories\ProductRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    { 
        $this->app->bind(ProductRepositoryInterface::class,ProductRepository::class);
        $this->app->bind(ProductApiRepositoryInterface::class,ProductApiRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
