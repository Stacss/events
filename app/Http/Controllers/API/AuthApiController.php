<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthApiController extends Controller
{
    /**
     * @OA\Post(
     *      path="/api/register",
     *      tags={"Пользователи"},
     *      summary="Регистрация нового пользователя",
     *      description="Регистрирует нового пользователя с предоставленным логином, email, паролем, и датой рождения (необязательно), а также именем и фамилией.",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"login", "email", "password", "first_name", "last_name"},
     *              @OA\Property(property="login", type="string", example="Ivan123"),
     *              @OA\Property(property="email", type="string", format="email", example="Ivan123@example.com"),
     *              @OA\Property(property="password", type="string", format="password", example="password123"),
     *              @OA\Property(property="date_of_birth", type="string", format="date", nullable=true, example="1990-01-01"),
     *              @OA\Property(property="first_name", type="string", example="Ivan"),
     *              @OA\Property(property="last_name", type="string", example="Petrov")
     *          )
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Пользователь успешно зарегистрирован",
     *          @OA\JsonContent(
     *              @OA\Property(property="error", type="string", example="null"),
     *              @OA\Property(property="result", ref="/docs/swagger.yaml#/components/schemas/User")
     *          )
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Ошибка валидации",
     *          @OA\JsonContent(
     *              @OA\Property(property="errors", type="object", example={"email": {"The login has already been taken."}}),
     *              @OA\Property(property="message", type="string", example="Переданы некорректные данные.")
     *          )
     *      )
     * )
     */
    public function register(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'login' => 'required|string|max:255|unique:users,login',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:6',
                'date_of_birth' => 'nullable|date',
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
            ]);

            $user = User::create([
                'login' => $validatedData['login'],
                'email' => $validatedData['email'],
                'password' => Hash::make($validatedData['password']),
                'date_of_birth' => isset($validatedData['date_of_birth']) ? $validatedData['date_of_birth'] : null,
                'first_name' => $validatedData['first_name'],
                'last_name' => $validatedData['last_name'],
            ]);

            return response()->json(['error' => null, 'result' => $user], 201);
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->errors(), 'result' => 'Переданы некорректные данные.'], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Post(
     *      path="/api/login",
     *      tags={"Пользователи"},
     *      summary="Авторизация пользователя",
     *      description="Авторизует пользователя на основе предоставленных учетных данных, и выдает токен для дальнейшего взаимодействия",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"login", "password"},
     *              @OA\Property(property="login", type="string", example="Ivan123"),
     *              @OA\Property(property="password", type="string", format="password", example="password123")
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Успешная авторизация",
     *          @OA\JsonContent(
     *              @OA\Property(property="error", type="string", example="null"),
     *              @OA\Property(property="result", type="string", example="Успешная авторизация"),
     *              @OA\Property(property="token", type="string")
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Неправильные учетные данные",
     *          @OA\JsonContent(
     *              @OA\Property(property="error", type="string", example="Неправильные учетные данные")
     *          )
     *      )
     * )
     */
    public function login(Request $request)
    {
        try {
            $credentials = $request->only('login', 'password');

            if (!Auth::attempt($credentials)) {
                throw new \Exception('Неправильные учетные данные', 401);
            }

            $user = Auth::user();
            $token = $user->createToken('AuthToken')->plainTextToken;

            return response()->json([
                'error' => null,
                'result' => 'Успешная авторизация',
                'token' => $token,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], $e->getCode());
        }
    }
}
