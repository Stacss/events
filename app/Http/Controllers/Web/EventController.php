<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Services\EventParticipantService;
use App\Services\EventViewService;
use Illuminate\Support\Facades\Auth;

/**
 * Контроллер EventController отвечает за управление действиями событий.
 */
class EventController extends Controller
{
    /**
     * @var \App\Services\EventViewService Сервис отображения информации о событиях.
     */
    protected $eventViewService;

    /**
     * @var \App\Services\EventParticipantService Сервис работы с участниками событий.
     */
    protected $eventParticipantService;

    /**
     * Создает новый экземпляр класса EventController.
     *
     * @param \App\Services\EventViewService $eventViewService Сервис отображения информации о событиях.
     * @param \App\Services\EventParticipantService $eventParticipantService Сервис работы с участниками событий.
     */
    public function __construct(
        EventViewService $eventViewService,
        EventParticipantService $eventParticipantService
    ) {
        $this->eventViewService = $eventViewService;
        $this->eventParticipantService = $eventParticipantService;
    }

    /**
     * Отображает информацию о событии.
     *
     * @param int $eventId ID события, информацию о котором нужно отобразить.
     * @return \Illuminate\Contracts\View\View Возвращает представление с информацией о событии и участниках.
     */
    public function show($eventId)
    {
        $data = $this->eventViewService->showEvent($eventId);

        return view('events.show', $data);
    }

    /**
     * Удаляет участника из события.
     *
     * @param int $eventId ID события, из которого нужно удалить участника.
     * @param int $userId ID пользователя, которого нужно удалить из участников события.
     * @return \Illuminate\Http\RedirectResponse Возвращает ответ с перенаправлением обратно на предыдущую страницу.
     */
    public function removeParticipant($eventId, $userId)
    {
        $event = Event::findOrFail($eventId);
        $this->eventParticipantService->removeParticipant($event, $userId);

        return redirect()->back()->with('participantRemoved', 'Вы больше не участник');
    }

    /**
     * Добавляет пользователя в событие в качестве участника.
     *
     * @param int $eventId ID события, в которое нужно добавить участника.
     * @return \Illuminate\Http\RedirectResponse Возвращает ответ с перенаправлением обратно на предыдущую страницу.
     */
    public function joinEvent($eventId)
    {
        $event = Event::findOrFail($eventId);
        $this->eventParticipantService->joinEvent($event, Auth::id());

        return redirect()->back()->with('participantAdded', 'Вы теперь участник события');
    }
}

