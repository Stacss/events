<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    public function show($eventId)
    {
        $event = Event::with('participants')->find($eventId);

        $participants = $event->participants;

        if (!$participants->isEmpty()){
            $participantIds = $event->participants->pluck('id');

            if (!$participantIds->contains(Auth::user()->id)){
                $button = false;
            } else {
                $button = true;
            }
        } else {
            $button = false;
        }

        return view('events.show', compact('event', 'participants', 'button'));
    }

    public function removeParticipant($eventId, $userId)
    {
        $event = Event::findOrFail($eventId);

        $event->participants()->detach($userId);

        return redirect()->back()->with('participantRemoved', 'Вы больше не участник');

    }

    public function joinEvent($eventId)
    {
        $event = Event::findOrFail($eventId);
        $user = auth()->user();
        $event->participants()->attach($user->id);

        return redirect()->back()->with('participantAdded', 'Вы теперь участник события');
    }
}
