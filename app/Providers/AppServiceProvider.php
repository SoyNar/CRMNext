<?php

namespace App\Providers;

use App\Services\Impl\ClientsServiceImpl;
use App\Services\InterfaceService\IClientService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        app()->bind(IClientService::class, ClientsServiceImpl::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
