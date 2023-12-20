<?php

namespace App\Services;

use App\Models\Event;
use Illuminate\Support\Facades\Auth;

/**
 * Класс EventViewService предоставляет методы для работы с отображением событий.
 */
class EventViewService
{
    /**
     * @var \App\Services\EventParticipantService Сервис работы с участниками событий.
     */
    protected $eventParticipantService;

    /**
     * Создает новый экземпляр класса EventViewService.
     *
     * @param \App\Services\EventParticipantService $eventParticipantService Сервис работы с участниками событий.
     */
    public function __construct(EventParticipantService $eventParticipantService)
    {
        $this->eventParticipantService = $eventParticipantService;
    }

    /**
     * Отображает информацию о событии.
     *
     * @param int $eventId ID события, информацию о котором нужно отобразить.
     * @return array Массив данных о событии и его участниках для отображения.
     */
    public function showEvent($eventId)
    {
        $event = Event::with('participants')->find($eventId);
        $participants = $event->participants;
        $button = $this->eventParticipantService->isUserParticipant($event, Auth::id());

        return compact('event', 'participants', 'button');
    }
}
