<?php

namespace App\Services;

use App\Models\Event;

/**
 * Класс EventParticipantService предоставляет методы для работы с участниками событий.
 */
class EventParticipantService
{
    /**
     * Проверяет, является ли определенный пользователь участником события.
     *
     * @param \App\Models\Event $event Событие, для которого нужно проверить участие.
     * @param int $userId ID пользователя, чье участие нужно проверить.
     * @return bool Возвращает true, если пользователь участвует в событии, иначе - false.
     */
    public function isUserParticipant(Event $event, $userId)
    {
        $participantIds = $event->participants->pluck('id');
        return $participantIds->contains($userId);
    }

    /**
     * Добавляет пользователя в список участников определенного события.
     *
     * @param \App\Models\Event $event Событие, в которое нужно добавить участника.
     * @param int $userId ID пользователя, которого нужно добавить в участники.
     * @return void
     */
    public function joinEvent(Event $event, $userId)
    {
        $event->participants()->attach($userId);
    }

    /**
     * Удаляет пользователя из списка участников определенного события.
     *
     * @param \App\Models\Event $event Событие, из которого нужно удалить участника.
     * @param int $userId ID пользователя, которого нужно удалить из участников.
     * @return void
     */
    public function removeParticipant(Event $event, $userId)
    {
        $event->participants()->detach($userId);
    }
}

