<?php

namespace App\Services;

use App\Models\Event;
use Illuminate\Events\Dispatcher;
use Illuminate\Support\Facades\Auth;
use JeroenNoten\LaravelAdminLte\Events\BuildingMenu;

/**
 * Класс MenuBuilderService представляет собой сервис для построения меню
 * и прослушивания события BuildingMenu для внедрения пунктов меню в приложение.
 */
class MenuBuilderService
{
    /**
     * @var \Illuminate\Contracts\Events\Dispatcher Сервис событий Laravel.
     */
    protected $events;

    /**
     * Создает новый экземпляр класса MenuBuilderService.
     *
     * @param \Illuminate\Contracts\Events\Dispatcher $events Сервис событий Laravel.
     */
    public function __construct(Dispatcher $events)
    {
        $this->events = $events;
    }

    /**
     * Слушает событие BuildingMenu и добавляет пункты меню в соответствии с событием.
     *
     * @return void
     */
    public function listenBuildingMenu()
    {
        $this->events->listen(BuildingMenu::class, function (BuildingMenu $event) {
            $arrayMenu = [];
            $arrayMenuEvent = [];

            $menus = Event::all();

            foreach ($menus as $menu) {
                $arrayMenu[] = [
                    'text' => $menu->title,
                    'url' => 'events/' . $menu->id,
                    'icon' => 'fas fa-genderless',
                ];
            }

            $myEvents = Event::where('creator_id', Auth::id())
                ->orWhereHas('participants', function ($query) {
                    $query->where('user_id', Auth::id());
                })
                ->with('participants')
                ->get();

            foreach ($myEvents as $myEvent) {
                $arrayMenuEvent[] = [
                    'text' => $myEvent->title,
                    'url' => 'events/' . $myEvent->id,
                    'icon' => 'fas fa-genderless',
                ];
            }

            $event->menu->add([
                'header' => 'Привет, ' . Auth::user()->first_name . '!',
            ]);

            $event->menu->add([
                'text' => 'Все События',
                'icon' => 'fas fa-fw fa-share',
                'submenu' => $arrayMenu,
            ]);

            $event->menu->add([
                'text' => 'Мои События',
                'icon' => 'fas fa-fw fa-share',
                'submenu' => $arrayMenuEvent,
            ]);
        });
    }
}
