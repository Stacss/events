<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Web\EventController;
use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Auth\LoginController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [HomeController::class, 'index'])->name('home');

Auth::routes();

Route::get('/home', [HomeController::class, 'index'])->name('home');

Route::get('/events/{eventId}', [EventController::class, 'show'])->name('event.show');

Route::delete('/events/{eventId}/participants/{userId}', [EventController::class, 'removeParticipant'])->name('event.removeParticipant');

Route::get('/events/{eventId}/join', [EventController::class, 'joinEvent'])->name('events.join');

Route::get('/logout', [LoginController::class, 'logout'])->name('logout');
