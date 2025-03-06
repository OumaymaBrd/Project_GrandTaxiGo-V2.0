<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DriverAnnouncementController;
use App\Http\Controllers\PassengerController;
use App\Http\Controllers\DriverRideController;
use App\Http\Controllers\DriverNotificationController;
use App\Http\Controllers\SocialiteController;
use App\Http\Controllers\FirebaseAuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminAuthController;



// Routes publiques
Route::get('/', function () {
  return view('welcome');
});


// Routes d'authentification
Route::middleware('guest')->group(function () {
  // Routes de connexion standard
  Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
  Route::post('/login', [LoginController::class, 'login']);
  Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
  Route::post('/register', [RegisterController::class, 'register']);

  // Routes d'authentification Google
  Route::get('/auth/google', [SocialiteController::class, 'googleLogin'])->name('auth.google');
  Route::get('/auth/google-callback', [SocialiteController::class, 'googleAuthentication'])->name('auth.google-callback');

  // Route pour Firebase Authentication
  Route::post('/auth/firebase-callback', [FirebaseAuthController::class, 'handleCallback'])->name('auth.firebase-callback');
});

// Route pour choisir un rôle (accessible uniquement si authentifié mais sans rôle défini)
Route::middleware('auth')->group(function () {
  Route::get('/choose-role', [SocialiteController::class, 'showChooseRoleForm'])->name('choose.role');
  Route::post('/choose-role', [SocialiteController::class, 'updateRole'])->name('update.role');
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Routes protégées
Route::middleware('auth')->group(function () {
  Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

  // Routes pour les chauffeurs
  Route::middleware('role:chauffeur')->group(function () {
      // Annonces
      Route::post('/driver/announce', [DriverAnnouncementController::class, 'store'])
          ->name('driver.announce');
      Route::get('/driver/announcements', [DriverAnnouncementController::class, 'getMyAnnouncements'])
          ->name('driver.announcements');
      Route::post('/driver/announcement/{id}/toggle', [DriverAnnouncementController::class, 'toggleActive'])
          ->name('driver.announcement.toggle');
      Route::get('/driver/available-countries', [DriverAnnouncementController::class, 'getAvailableCountries'])
          ->name('driver.countries');

      // Notifications
      Route::get('/driver/notifications', [DriverNotificationController::class, 'index'])
          ->name('driver.notifications');
      Route::post('/driver/notifications/mark-all-read', [DriverNotificationController::class, 'markAllAsRead'])
          ->name('driver.notifications.markAllAsRead');

      // Réservations
      Route::get('/driver/pending-requests', [DriverRideController::class, 'getPendingRequests'])
          ->name('driver.requests');
      Route::get('/driver/ride-requests', [DriverRideController::class, 'getAllRideRequests'])
          ->name('driver.ride.requests');
      Route::post('/driver/ride-request/{id}/respond', [DriverRideController::class, 'respondToRequest'])
          ->name('driver.ride.respond');
  });

  // Routes pour les passagers
  Route::middleware('role:passager')->group(function () {
      // Dashboard
      Route::get('/passenger/dashboard', [PassengerController::class, 'dashboard'])
          ->name('passenger.dashboard');

      // Chauffeurs disponibles
      Route::get('/passenger/available-drivers', [PassengerController::class, 'getAvailableDrivers'])
          ->name('driver.available');
      Route::get('/passenger/available-countries', [PassengerController::class, 'getAvailableCountries'])
          ->name('passenger.countries');

      // Gestion des demandes de course
      Route::post('/passenger/ride-request', [PassengerController::class, 'createRideRequest'])
          ->name('ride.request');
      Route::get('/passenger/ride-requests', [PassengerController::class, 'getMyRideRequests'])
          ->name('ride.requests');
      Route::post('/passenger/ride-request/{id}/cancel', [PassengerController::class, 'cancelRideRequest'])
          ->name('ride.cancel');
      Route::post('/passenger/ride-request/{id}/confirm', [PassengerController::class, 'confirmRideRequest'])
          ->name('ride.confirm');
      Route::delete('/passenger/ride-request/{id}', [PassengerController::class, 'deleteRideRequest'])
          ->name('ride.delete');
  });
});


// Routes d'administration avec le préfixe admin-panel
// Routes d'authentification admin personnalisées

// Routes Voyager avec authentification
// Route::group(['prefix' => 'admin-panel'], function () {
//     Voyager::routes();
// });
// Route::group(['prefix' => 'admin-panel'], function () {
//     Route::get('login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
//     Route::post('login', [AdminAuthController::class, 'login'])->name('admin.postlogin');
//     Route::post('logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

//     // Routes protégées par le middleware admin
//     Route::group(['middleware' => 'admin.user'], function () {
//         // Voyager::routes() sera appelé ici
//     });
// });

// // Routes Voyager (avec le préfixe admin-panel défini dans config/voyager.php)
// Route::group(['prefix' => 'admin-panel', 'middleware' => 'admin.user'], function () {
//     Voyager::routes();
// });

// Routes Voyager avec authentification

Route::group(['prefix' => 'admin'], function () {
    Route::get('login', [AdminAuthController::class, 'showLoginForm'])->name('voyager.login');
    Route::post('login', [AdminAuthController::class, 'login'])->name('voyager.postlogin');

    Route::group(['middleware' => 'admin'], function () {
        Voyager::routes();
    });
});
