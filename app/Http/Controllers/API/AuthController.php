<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * @OA\Post(
     *      path="/api/register",
     *      tags={"Аутентификация"},
     *      summary="Регистрация нового пользователя",
     *      description="Регистрирует нового пользователя с предоставленным логином, email и паролем",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"login", "email", "password"},
     *              @OA\Property(property="login", type="string", example="Ivan123"),
     *              @OA\Property(property="email", type="string", format="email", example="Ivan123@example.com"),
     *              @OA\Property(property="password", type="string", format="password", example="password123")
     *          )
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Пользователь успешно зарегистрирован",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Пользователь успешно зарегистрирован"),
     *              @OA\Property(property="user", ref="/docs/swagger.yaml#/components/schemas/User")
     *          )
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Ошибка валидации",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Переданы некорректные данные."),
     *              @OA\Property(property="errors", type="object", example={"email": {"Поле email обязательно для заполнения."}})
     *          )
     *      )
     * )
     */
    public function register(Request $request)
    {
        try {
            // Валидация данных запроса
            $validatedData = $request->validate([
                'login' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:6',
            ]);

            // Создание пользователя
            $user = User::create([
                'login' => $validatedData['login'],
                'email' => $validatedData['email'],
                'password' => Hash::make($validatedData['password']),
            ]);

            return response()->json(['error' => null, 'message' => 'User registered successfully', 'user' => $user], 201);
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->errors(), 'message' => 'Validation failed'], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
