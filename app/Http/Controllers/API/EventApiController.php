<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class EventApiController extends Controller
{
    /**
     * @OA\Post(
     *      path="/api/events",
     *      operationId="createEvent",
     *      tags={"События"},
     *      summary="Создание события",
     *      description="Метод для создания нового события для авторизованного пользователя.",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"title", "text"},
     *              @OA\Property(property="title", type="string", maxLength=255, example="Название события"),
     *              @OA\Property(property="text", type="string", example="Описание события")
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="Authorization",
     *          in="header",
     *          required=true,
     *          description="Bearer {token}",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="Accept",
     *          in="header",
     *          required=true,
     *          description="application/json",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Событие успешно создано",
     *          @OA\JsonContent(
     *              @OA\Property(property="error", type="string",
     *                  example="null"),
     *              @OA\Property(
     *                  property="result",
     *                  ref="/docs/swagger.yaml#/components/schemas/Event"
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Ошибка аутентификации",
     *          @OA\JsonContent(
     *              @OA\Property(property="error", type="string", example="Ошибка аутентификации"),
     *              @OA\Property(property="message", type="string", example="Текст сообщения об ошибке")
     *          )
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Ошибка валидации",
     *          @OA\JsonContent(
     *              @OA\Property(property="error", type="string", example="Ошибка валидации"),
     *              @OA\Property(property="message", type="string", example="Текст сообщения об ошибке"),
     *              @OA\Property(
     *                  property="errors",
     *                  type="object",
     *                  example={"title": {"Название события обязательно."}, "text": {"Описание события обязательно."}}
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Внутренняя ошибка сервера",
     *          @OA\JsonContent(
     *              @OA\Property(property="error", type="string", example="Внутренняя ошибка сервера"),
     *              @OA\Property(property="message", type="string", example="Текст сообщения об ошибке")
     *          )
     *      )
     * )
     */
    public function create(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'title' => 'required|string|max:255',
                'text' => 'required|string',
            ]);

            $event = Event::create([
                'title' => $validatedData['title'],
                'text' => $validatedData['text'],
                'creator_id' => auth()->user()->id,
            ]);

            return response()->json([
                'error' => null,
                'result' => $event,
            ], 201);
        } catch (AuthenticationException $e) {
            return response()->json(['error' => 'Ошибка аутентификации'], 401);
        } catch (ValidationException $validationException) {
            return response()->json([
                'error' => $validationException->getMessage(),
                'errors' => $validationException->errors(),
            ], 422);
        } catch (\Throwable $th) {
            return response()->json([
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *      path="/api/events",
     *      operationId="getEventsList",
     *      tags={"События"},
     *      summary="Получение списка событий",
     *      description="Возвращает список событий для аутентифицированного пользователя которые он создал",
     *      security={ {"bearerAuth": {} }},
     *     @OA\Parameter(
     *          name="Authorization",
     *          in="header",
     *          required=true,
     *          description="Bearer {token}",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="Accept",
     *          in="header",
     *          required=true,
     *          description="application/json",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Успешное выполнение",
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="error",
     *                  type="string",
     *                  example="null"
     *              ),
     *              @OA\Property(
     *                  property="events",
     *                  type="array",
     *                  @OA\Items(ref="/docs/swagger.yaml#/components/schemas/Event")
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Ошибка авторизации",
     *          @OA\JsonContent(
     *              @OA\Property(property="error", type="string", example="Unauthenticated")
     *          )
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Ошибка сервера",
     *          @OA\JsonContent(
     *              @OA\Property(property="error", type="string", example="Ошибка сервера")
     *          )
     *      )
     * )
     */
    public function index()
    {
        try {
            $events = Event::where('creator_id', auth()->id())->get();

            return response()->json([
                'error' => null,
                'events' => $events,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'error' => 'Ошибка при получении списка событий',
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *      path="/api/events/{eventId}/join",
     *      operationId="joinEvent",
     *      tags={"События"},
     *      summary="Участие в событии",
     *      description="Присоединение к событию для авторизованного пользователя",
     *      security={ {"bearerAuth": {} }},
     *      @OA\Parameter(
     *          name="Authorization",
     *          in="header",
     *          required=true,
     *          description="Bearer {token}",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="Accept",
     *          in="header",
     *          required=true,
     *          description="application/json",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="eventId",
     *          in="path",
     *          required=true,
     *          description="ID события",
     *          @OA\Schema(
     *              type="integer",
     *              format="int64"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Успешное участие в событии",
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="error",
     *                  type="string",
     *                  example="null"
     *              ),
     *              @OA\Property(
     *                  property="result",
     *                  type="string",
     *                  example="Вы успешно присоединились к событию"
     *              ),
     *              @OA\Property(
     *                  property="event",
     *                  ref="/docs/swagger.yaml#/components/schemas/Event"
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Неверный запрос",
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="error",
     *                  type="string",
     *                  example="Вы уже участвуете в этом событии"
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Ошибка аутентификации",
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="error",
     *                  type="string",
     *                  example="Unauthenticated"
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Ошибка сервера",
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="error",
     *                  type="string",
     *                  example="Ошибка сервера: текст_ошибки"
     *              )
     *          )
     *      )
     * )
     */
    public function joinEvent(Request $request, $eventId)
    {
        try {
            $event = Event::findOrFail($eventId);

            if ($event->participants->contains(auth()->user()->id)) {
                return response()->json([
                    'error' => 'Вы уже участвуете в этом событии'
                ], 400);
            }

            // Добавление пользователя в список участников события
            $event->participants()->attach(auth()->user()->id);

            return response()->json([
                'error' => null,
                'result' => 'Вы успешно присоединились к событию',
                'event' => $event
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Событие не найдено'
            ],
                404);
        } catch (\Throwable $th) {
            return response()->json([
                'error' => 'Ошибка сервера: ' . $th->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Delete(
     *      path="/api/events/{eventId}/cancel-participation",
     *      operationId="cancelEventParticipation",
     *      tags={"События"},
     *      summary="Отмена участия в событии",
     *      description="Отмена участия в событии для авторизованного пользователя",
     *      security={ {"bearerAuth": {} }},
     *      @OA\Parameter(
     *          name="Authorization",
     *          in="header",
     *          required=true,
     *          description="Bearer {token}",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="Accept",
     *          in="header",
     *          required=true,
     *          description="application/json",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="eventId",
     *          in="path",
     *          required=true,
     *          description="ID события",
     *          @OA\Schema(
     *              type="integer",
     *              format="int64"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Успешная отмена участия в событии",
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="error",
     *                  type="string",
     *                  example="null"
     *              ),
     *              @OA\Property(
     *                  property="result",
     *                  type="string",
     *                  example="Участие в событии успешно отменено"
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Неверный запрос",
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="error",
     *                  type="string",
     *                  example="Вы не участвуете в этом событии"
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Ошибка аутентификации",
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="error",
     *                  type="string",
     *                  example="Unauthenticated"
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Ошибка сервера",
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="error",
     *                  type="string",
     *                  example="Ошибка сервера: текст_ошибки"
     *              )
     *          )
     *      )
     * )
     */
    public function cancelEventParticipation($eventId)
    {
        try {
            $event = Event::findOrFail($eventId);

            if (!$event->participants()->detach(auth()->user()->id)) {
                return response()->json([
                    'error' => 'Вы не участвуете в этом событии'
                ], 400);
            }

            return response()->json([
                'error' => null,
                'result' => 'Участие в событии успешно отменено'
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Событие не найдено'
            ],
                404);
        } catch (\Throwable $th) {
            return response()->json([
                'error' => 'Ошибка сервера: ' . $th->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Delete(
     *      path="/api/events/{eventId}",
     *      operationId="deleteEvent",
     *      tags={"События"},
     *      summary="Удаление события",
     *      description="Удаление события создателем",
     *      security={ {"bearerAuth": {} }},
     *      @OA\Parameter(
     *          name="Authorization",
     *          in="header",
     *          required=true,
     *          description="Bearer {token}",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="Accept",
     *          in="header",
     *          required=true,
     *          description="application/json",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="eventId",
     *          in="path",
     *          required=true,
     *          description="ID события",
     *          @OA\Schema(
     *              type="integer",
     *              format="int64"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Событие успешно удалено",
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="error",
     *                  type="string",
     *                  example="null"
     *              ),
     *              @OA\Property(
     *                  property="result",
     *                  type="string",
     *                  example="Событие успешно удалено"
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Ошибка доступа",
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="error",
     *                  type="string",
     *                  example="Вы не можете удалить это событие, так как не являетесь его создателем"
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Событие не найдено",
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="error",
     *                  type="string",
     *                  example="Событие не найдено"
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Ошибка сервера",
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="error",
     *                  type="string",
     *                  example="Ошибка сервера: текст_ошибки"
     *              )
     *          )
     *      )
     * )
     */
    public function deleteEvent($eventId)
    {
        try {
            $event = Event::findOrFail($eventId);

            if ($event->creator_id !== auth()->user()->id) {
                return response()->json([
                    'error' => 'Вы не можете удалить это событие, так как не являетесь его создателем'
                ], 403);
            }

            $event->delete();

            return response()->json([
                'error' => null,
                'result' => 'Событие успешно удалено'
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Событие не найдено'
            ],
            404);
        } catch (\Throwable $th) {
            return response()->json([
                'error' => 'Ошибка сервера: ' . $th->getMessage()
            ], 500);
        }
    }
}
