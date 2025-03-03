<?php

use App\Http\Controllers\DriverAnnouncementController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/driver/announce', [DriverAnnouncementController::class, 'store']);
    Route::get('/driver/announcements', [DriverAnnouncementController::class, 'getMyAnnouncements']);
    Route::post('/driver/announcement/{id}/toggle', [DriverAnnouncementController::class, 'toggleActive']);
});

