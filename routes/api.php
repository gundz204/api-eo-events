<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\ParticipantController;
use App\Http\Controllers\Api\SertifikatController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;





/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
    });
});

Route::prefix('events')->group(function () {
    Route::get('/{id}', [EventController::class, 'show']);
    Route::get('/', [EventController::class, 'index']);

    Route::middleware(['auth:sanctum', 'admin'])->group(function () {
        Route::post('/', [EventController::class, 'store']);
        Route::put('/{id}', [EventController::class, 'update']);
        Route::delete('/{id}', [EventController::class, 'destroy']);
    });
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('event/{id}/register', [EventController::class, 'registerToEvent']);
    Route::get('event/history-events', [ParticipantController::class, 'myEvents']);
    Route::get('event/my-events', [EventController::class, 'myEvents']);
});

Route::middleware(['auth:sanctum', 'admin'])->prefix('participants')->group(function () {
    Route::get('/{eventId}', [ParticipantController::class, 'index']);
    Route::put('/{id}/status', [ParticipantController::class, 'updateStatus']);
    Route::get('/{id}/status', [ParticipantController::class, 'updateStatusWithQR']);
    Route::get('/{id}/statistic', [ParticipantController::class, 'getEventStatistics']);
    Route::get('/{id}/certificate', [ParticipantController::class, 'generateCertificate']);
});

Route::middleware(['auth:sanctum', 'admin'])->prefix('users')->group(function () {
    Route::get('/', [AuthController::class, 'getAllUser']);
});

Route::get('participant/{id}/status', [ParticipantController::class, 'updateStatusWithQRView']);

Route::post('/generate-sertifikat', [SertifikatController::class, 'generate']);
Route::get('/generate-sertifikat/{registration_id}', [SertifikatController::class, 'generateByRegistrationId']);

