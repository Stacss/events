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
}
