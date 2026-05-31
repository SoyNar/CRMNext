<?php

namespace App\Providers;

use App\Services\Impl\ClientsServiceImpl;
use App\Services\Impl\ContactServiceImpl;
use App\Services\InterfaceService\IClientService;
use App\Services\InterfaceService\IContactService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        app()->bind(IClientService::class, ClientsServiceImpl::class);
        app()->bind(IContactService::class, ContactServiceImpl::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
