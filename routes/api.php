<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthApiController;
use App\Http\Controllers\API\EventApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('/register', [AuthApiController::class, 'register']);
Route::post('/login', [AuthApiController::class, 'login']);
Route::middleware('auth:sanctum')->post('/events', [EventApiController::class, 'create']);
Route::middleware('auth:sanctum')->get('/events', [EventApiController::class, 'index']);
Route::middleware('auth:sanctum')->get('/events/{eventId}/join', [EventApiController::class, 'joinEvent']);
Route::middleware('auth:sanctum')->delete('/events/{eventId}/cancel-participation', [EventApiController::class, 'cancelEventParticipation']);
Route::middleware('auth:sanctum')->delete('/events/{eventId}/delete', [EventApiController::class, 'deleteEvent']);




