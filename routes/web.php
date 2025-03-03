<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DriverAnnouncementController;
use App\Http\Controllers\PassengerController;
use App\Http\Controllers\DriverRideController;
use App\Http\Controllers\DriverNotificationController;
use Illuminate\Support\Facades\Route;

// Routes publiques
Route::get('/', function () {
    return view('welcome');
});

// Routes d'authentification
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});



Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Routes protégées
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Routes pour les chauffeurs
    Route::middleware('role:chauffeur')->group(function () {
        Route::post('/driver/announce', [DriverAnnouncementController::class, 'store'])->name('driver.announce');
        Route::get('/driver/announcements', [DriverAnnouncementController::class, 'getMyAnnouncements'])->name('driver.announcements');
        Route::post('/driver/announcement/{id}/toggle', [DriverAnnouncementController::class, 'toggleActive'])->name('driver.announcement.toggle');

        // Routes pour les notifications
        Route::get('/driver/notifications', [DriverNotificationController::class, 'index'])
            ->name('driver.notifications');
        Route::post('/driver/notifications/mark-all-read', [DriverNotificationController::class, 'markAllAsRead'])
            ->name('driver.notifications.markAllAsRead');

        // Routes pour les réservations
        Route::get('/driver/pending-requests', [DriverRideController::class, 'getPendingRequests'])
            ->name('driver.requests');
        Route::get('/driver/ride-requests', [DriverRideController::class, 'getAllRideRequests'])
            ->name('driver.ride.requests');
        Route::post('/driver/ride-request/{id}/respond', [DriverRideController::class, 'respondToRequest'])
            ->name('driver.ride.respond');

        // Ajouter cette route dans le groupe des routes pour les chauffeurs
        Route::get('/driver/available-countries', [DriverAnnouncementController::class, 'getAvailableCountries'])
            ->name('driver.countries');
    });

    // Routes pour les passagers
    Route::middleware('role:passager')->group(function () {
        Route::get('/passenger/available-drivers', [PassengerController::class, 'getAvailableDrivers'])->name('driver.available');
        Route::post('/passenger/ride-request', [PassengerController::class, 'createRideRequest'])->name('ride.request');
        Route::get('/passenger/ride-requests', [PassengerController::class, 'getMyRideRequests'])->name('ride.requests');
        Route::post('/passenger/ride-request/{id}/cancel', [PassengerController::class, 'cancelRideRequest'])->name('ride.cancel');
        Route::post('/passenger/ride-request/{id}/confirm', [PassengerController::class, 'confirmRideRequest'])->name('ride.confirm');
        Route::delete('/passenger/ride-request/{id}', [PassengerController::class, 'deleteRideRequest'])
        ->name('ride.delete');
    });

    // Routes pour les passagers
Route::middleware(['auth', 'role:passager'])->group(function () {
    // Dashboard
    Route::get('/passenger/dashboard', 'PassengerController@dashboard')->name('passenger.dashboard');

    // Chauffeurs disponibles
    Route::get('/passenger/available-drivers', 'PassengerController@getAvailableDrivers');
    Route::get('/passenger/available-countries', 'PassengerController@getAvailableCountries');

    // Gestion des demandes de course
    Route::post('/passenger/ride-request', 'PassengerController@createRideRequest');
    Route::get('/passenger/my-requests', 'PassengerController@getMyRideRequests');
    Route::post('/passenger/ride-request/{id}/cancel', 'PassengerController@cancelRideRequest');
    Route::post('/passenger/ride-request/{id}/confirm', 'PassengerController@confirmRideRequest');
    Route::delete('/passenger/ride-request/{id}', 'PassengerController@deleteRideRequest');
});




});



