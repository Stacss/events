<?php

namespace App\Providers;

use App\Models\Event;
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
    public function boot(Dispatcher $events)
    {
        Schema::defaultStringLength(191);

        $events->listen(BuildingMenu::class, function (BuildingMenu $event) {
            $menus = Event::all();

            foreach($menus as $menu){
                $arrayMenu[] = array(
                    'text' => $menu->title,
                    'url' => $menu->id,
                    'icon' => 'fas fa-genderless',
                );
            }

            $myEvents = Event::where('creator_id', Auth::user()->id)->get();

            foreach($myEvents as $myEvent){
                $arrayMenuEvent[] = array(
                    'text' => $myEvent->title,
                    'url' => $myEvent->id,
                    'icon' => 'fas fa-genderless',
                );
            }
            $event->menu->add([
                'header' => 'Привет, ' . Auth::user()->first_name . '!',
                ]);

            $event->menu->add(['text' => 'Все События',
                'icon' => 'fas fa-fw fa-share',
                'submenu' => $arrayMenu,]);

            $event->menu->add(['text' => 'Мои События',
                'icon' => 'fas fa-fw fa-share',
                'submenu' => $arrayMenuEvent,]);
        });


    }
}
