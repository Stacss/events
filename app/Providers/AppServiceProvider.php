<?php

namespace App\Providers;

use App\Models\Event;
use App\Services\MenuBuilderService;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use JeroenNoten\LaravelAdminLte\Events\BuildingMenu;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(MenuBuilderService $menuBuilder)
    {
        Schema::defaultStringLength(191);

        $menuBuilder->listenBuildingMenu();
    }
}
