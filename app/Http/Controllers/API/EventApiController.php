<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
}
