<?php

use App\Http\Controllers\DriverAnnouncementController;
use App\Http\Controllers\MessageController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/driver/announce', [DriverAnnouncementController::class, 'store']);
    Route::get('/driver/announcements', [DriverAnnouncementController::class, 'getMyAnnouncements']);
    Route::post('/driver/announcement/{id}/toggle', [DriverAnnouncementController::class, 'toggleActive']);
    Route::get('/rides/{rideId}/messages', [MessageController::class, 'getMessages']);
    Route::post('/messages', [MessageController::class, 'store']);
});

