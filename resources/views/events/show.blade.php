@extends('adminlte::page')

@section('content')

    <div>
        @if(session('participantRemoved'))
            <div class="alert alert-success">
                {{ session('participantRemoved') }}
            </div>
        @endif
        @if(session('participantAdded'))
            <div class="alert alert-success">
                {{ session('participantAdded') }}
            </div>
        @endif

        <h1>Тема: {{ $event->title }}</h1>

        <p>Описание: {{ $event->text }}</p>

        <p>Дата создания: {{ $event->created_at }}</p>

        <h2>Участники:</h2>

        @if($participants->isEmpty())

            <p>У задачи еще нет участников</p>

        @else

            <ul class="list-group">
                @foreach($participants as $participant)

                    {{--<li>{{ $participant->first_name }} {{ $participant->last_name }}</li>--}}
                    <li style="list-style-type: none">

                        <button data-toggle="modal" data-target="#userModal{{ $loop->index }}" class="btn btn-light">
                            <i class="fas fa-user text-green"></i>
                        </button>

                        {{ $participant->first_name }} {{ $participant->last_name }}

                    </li>

                    <!-- Модальное окно для каждого пользователя -->
                    <div class="modal fade" id="userModal{{ $loop->index }}" tabindex="-1" role="dialog" aria-labelledby="userModalLabel{{ $loop->index }}" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="userModalLabel{{ $loop->index }}">{{ $participant->first_name }} {{ $participant->last_name }}</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <p><strong>Логин:</strong> {{ $participant->login }}</p>
                                    <p><strong>Email:</strong> {{ $participant->email }}</p>
                                    <p><strong>Имя:</strong> {{ $participant->first_name }}</p>
                                    <p><strong>Фамилия:</strong> {{ $participant->last_name }}</p>
                                    <p><strong>Дата рождения:</strong> {{ $participant->date_of_birth }}</p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    <!-- Дополнительные кнопки или действия -->
                                </div>
                            </div>
                        </div>
                    </div>

                @endforeach
            </ul>

        @endif

        @if($button == false)

                <a href="{{ route('events.join', ['eventId' => $event->id]) }}" class="btn btn-primary">Принять участие</a>

        @else

            <p class="mt-5">Вы участник события.</p>
            <a href="{{ route('event.removeParticipant', ['eventId' => $event->id, 'userId' => $participant->id]) }}" class="btn btn-danger"
               onclick="event.preventDefault();
            if(confirm('Вы уверены, что хотите хотите отказаться от участия?')) {
                document.getElementById('remove-participant-form-{{ $participant->id }}').submit();
            }">
                Отказаться от участия
            </a>

            <form id="remove-participant-form-{{ $participant->id }}"
                  action="{{ route('event.removeParticipant', ['eventId' => $event->id, 'userId' => $participant->id]) }}"
                  method="POST"
                  style="display: none;">
                @csrf
                @method('DELETE')
            </form>

        @endif


    </div>



@endsection
