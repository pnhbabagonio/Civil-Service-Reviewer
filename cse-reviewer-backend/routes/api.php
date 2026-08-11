<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BookmarkController;
use App\Http\Controllers\Api\FlagController;
use App\Http\Controllers\Api\LeaderboardController;
use App\Http\Controllers\Api\QuestionController;
use App\Http\Controllers\Api\SessionController;
use App\Http\Controllers\Api\StatsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes (No Authentication Required)
|--------------------------------------------------------------------------
*/

// Authentication
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Questions (read-only for public)
Route::get('/questions', [QuestionController::class, 'index']);
Route::get('/questions/{id}', [QuestionController::class, 'show']);
Route::get('/passage-groups', [QuestionController::class, 'passageGroups']);

// Leaderboard
Route::get('/leaderboard/accuracy', [LeaderboardController::class, 'accuracy']);
Route::get('/leaderboard/streak', [LeaderboardController::class, 'streak']);
Route::get('/leaderboard/simulator', [LeaderboardController::class, 'simulator']);
Route::get('/leaderboard/weekly', [LeaderboardController::class, 'weekly']);

// Guest sessions
Route::post('/guest-sessions', [SessionController::class, 'store']);

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {

    // User
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Sessions
    Route::get('/sessions', [SessionController::class, 'index']);
    Route::post('/sessions', [SessionController::class, 'store']);
    Route::get('/sessions/{id}', [SessionController::class, 'show']);

    // Stats
    Route::get('/stats', [StatsController::class, 'index']);
    Route::get('/stats/categories', [StatsController::class, 'categories']);

    // Bookmarks
    Route::get('/bookmarks', [BookmarkController::class, 'index']);
    Route::post('/bookmarks/sync', [BookmarkController::class, 'sync']);
    Route::post('/bookmarks/{questionId}', [BookmarkController::class, 'store']);
    Route::delete('/bookmarks/{questionId}', [BookmarkController::class, 'destroy']);

    // Flags
    Route::get('/flags', [FlagController::class, 'index']);
    Route::post('/flags/{questionId}', [FlagController::class, 'store']);
    Route::delete('/flags/{questionId}', [FlagController::class, 'destroy']);

    /*
    |--------------------------------------------------------------------------
    | Admin Routes
    |--------------------------------------------------------------------------
    */
    Route::middleware('admin')->group(function () {
        Route::post('/questions', [QuestionController::class, 'store']);
        Route::put('/questions/{id}', [QuestionController::class, 'update']);
        Route::delete('/questions/{id}', [QuestionController::class, 'destroy']);
    });
});