<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DriverAnnouncementController;
use App\Http\Controllers\PassengerController;
use App\Http\Controllers\DriverRideController;
use App\Http\Controllers\DriverNotificationController;
// use App\Http\Controllers\SocialiteController;
// use App\Http\Controllers\FirebaseAuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\AdminAuthController;
use App\Mail\HelloMail;
use TCG\Voyager\Http\Controllers\VoyagerUserController;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\SmsController;
// use App\Http\Controllers\Driver\RideCompletionController;

// Ancien code (incorrect)
// use App\Http\Controllers\RideCompletionController;
use App\Http\Controllers\RatingController;

Route::get('/driver/{id}/reviews', [App\Http\Controllers\RatingController::class, 'getDriverReviews'])
    ->name('driver.reviews');

// Route pour marquer une course comme terminée
Route::post('/driver/ride-request/{id}/complete', [App\Http\Controllers\Driver\RideCompletionController::class, 'complete'])
    ->name('driver.ride.complete')
    ->middleware('auth');


// Dans routes/web.php
Route::post('/driver/ride-request/{id}/complete', [App\Http\Controllers\Driver\RideCompletionController::class, 'complete'])
    ->name('driver.ride.complete')
    ->middleware(['auth']);
// Dans routes/web.php
// Dans routes/web.php
Route::get('/test-complete-ride/{id}', function ($id, App\Services\InfobipService $infobipService) {
    try {
        $phoneNumber = '0701237397';

        // Simuler la logique du contrôleur
        $ride = App\Models\RideRequest::findOrFail($id);
        $ride->status = 'completed';
        $ride->save();

        // Envoyer le SMS
        $message = "Test: Une course a été marquée comme terminée. ID: {$id}";
        $result = $infobipService->sendSms($phoneNumber, $message);

        return response()->json([
            'success' => true,
            'message' => 'Test de complétion réussi et SMS envoyé',
            'phone_number' => $phoneNumber,
            'sms_result' => $result
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Erreur: ' . $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ], 500);
    }
});
Route::post('/driver/ride-request/{id}/complete', [RideCompletionController::class, 'complete'])
    ->name('driver.ride.complete')
    ->middleware(['auth']);

// Nouveau code (correct)
use App\Http\Controllers\Driver\RideCompletionController;

Route::post('/driver/ride-request/{id}/complete', [RideCompletionController::class, 'complete'])
    ->name('driver.ride.complete')
    ->middleware(['auth']);

Route::post('/sms/delivery-report', function() {
    Log::info('Rapport de livraison SMS reçu', ['data' => request()->all()]);
    return response()->json(['success' => true]);
})->name('sms.delivery-report');


// Routes d'authentification
Route::middleware('guest')->group(function () {
  // Routes de connexion standard
  Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
  Route::post('/login', [LoginController::class, 'login']);
  Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
  Route::post('/register', [RegisterController::class, 'register']);

  // Routes d'authentification Google
//   Route::get('/auth/google', [SocialiteController::class, 'googleLogin'])->name('auth.google');
//   Route::get('/auth/google-callback', [SocialiteController::class, 'googleAuthentication'])->name('auth.google-callback');

  // Route pour Firebase Authentication
//   Route::post('/auth/firebase-callback', [FirebaseAuthController::class, 'handleCallback'])->name('auth.firebase-callback');
});

// Route pour choisir un rôle (accessible uniquement si authentifié mais sans rôle défini)
// Route::middleware('auth')->group(function () {
//   Route::get('/choose-role', [SocialiteController::class, 'showChooseRoleForm'])->name('choose.role');
//   Route::post('/choose-role', [SocialiteController::class, 'updateRole'])->name('update.role');
// });

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


// Chat routes
Route::get('/chat', [ChatController::class, 'index']);
Route::post('/chat', [ChatController::class, 'store']);

// Ride chat route
Route::get('/chat/ride/{rideId}', [ChatController::class, 'showRideChat'])
    ->middleware('auth')
    ->name('chat.ride');


Route::get('/send-email', function() {
    try {
        Mail::to('oumaymabramid@gmail.com')->send(new HelloMail());
        return 'Email has been sent successfully!';
    } catch (\Exception $e) {
        return 'Error sending email: ' . $e->getMessage();
    }
});

Route::get('/send-email', function() {
    try {
        Mail::to('oumaymabramid@gmail.com')->send(new HelloMail());
        return 'Email has been sent successfully!';
    } catch (\Exception $e) {
        return 'Error sending email: ' . $e->getMessage();
    }
});

Route::get('/send-email', function() {
    try {
        Mail::to('oumaymabramid@gmail.com')->send(new HelloMail());
        return 'Email has been sent successfully!';
    } catch (\Exception $e) {
        return 'Error sending email: ' . $e->getMessage();
    }
});

// Routes pour les SMS
Route::get('/sms', [SmsController::class, 'showForm'])->name('sms.form');
Route::post('/sms', [SmsController::class, 'sendSms'])->name('sms.send');

// Routes pour le système de notation
Route::post('/passenger/submit-rating', [RatingController::class, 'store'])->middleware('auth');
Route::get('/passenger/check-completed-rides', [RatingController::class, 'checkCompletedRides'])->middleware('auth');

