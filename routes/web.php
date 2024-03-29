<?php

use App\Http\Controllers\AchievementsController;
use App\Http\Controllers\CommentCreateController;
use App\Http\Controllers\LessonWatchedController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/users/{user}/achievements', [AchievementsController::class, 'index']);

Route::post('/comments/create', CommentCreateController::class);
Route::post('/lessons/watched', LessonWatchedController::class);
