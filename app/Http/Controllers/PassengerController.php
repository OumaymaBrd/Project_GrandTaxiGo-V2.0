<?php

namespace App\Http\Controllers;

use App\Models\DriverAnnouncement;
use App\Models\RideRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use App\Notifications\RideConfirmedNotification;
use App\Notifications\RideCancelledNotification;
use App\Notifications\RideDeletedNotification;

class PassengerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:passager');
    }

    public function getAvailableDrivers(Request $request)
    {
        try {
            // Construction de la requête de base
            $query = DriverAnnouncement::query();

            // Chargement des relations avec sélection spécifique des colonnes
            $query->with([
                'driver' => function($query) {
                    $query->select([
                        'users.id',
                        'users.name',
                        'users.phone',
                        DB::raw("COALESCE(users.profile_image_url, 'images/default-avatar.png') as profile_image_url")
                    ]);
                }
            ]);

            // Filtres
            $query->where('is_active', true);

            // S'assurer que le chauffeur existe
            $query->whereHas('driver', function($query) {
                $query->whereNotNull('id');
            });

            // Filtres optionnels
            if ($request->has('country') && $request->country !== 'all') {
                $query->where('country', $request->country);
            }

            if ($request->has('city') && !empty($request->city)) {
                $query->where('city', 'LIKE', '%' . $request->city . '%');
            }

            // Tri
            $query->orderBy('created_at', 'desc');

            // Exécution de la requête
            $announcements = $query->get();

            // Transformation des données
            $announcements = $announcements->map(function ($announcement) {
                // Gestion du chauffeur
                if (!$announcement->driver) {
                    $announcement->driver = new \stdClass();
                    $announcement->driver->id = null;
                    $announcement->driver->name = 'Chauffeur inconnu';
                    $announcement->driver->phone = 'N/A';
                    $announcement->driver->profile_image_url = asset('images/default-avatar.png');
                } else {
                    // Assurer que l'URL de l'image est complète
                    $announcement->driver->profile_image_url = $announcement->driver->profile_image_url
                        ? (filter_var($announcement->driver->profile_image_url, FILTER_VALIDATE_URL)
                            ? $announcement->driver->profile_image_url
                            : asset($announcement->driver->profile_image_url))
                        : asset('images/default-avatar.png');
                }

                // Conversion des coordonnées en nombres
                $announcement->latitude = (float) $announcement->latitude;
                $announcement->longitude = (float) $announcement->longitude;

                // Vérification des demandes en cours
                $announcement->has_pending_request = RideRequest::where('announcement_id', $announcement->id)
                    ->where('passenger_id', auth()->id())
                    ->whereIn('status', ['pending', 'accepted'])
                    ->exists();

                // Formatage de la date
                $announcement->formatted_date = Carbon::parse($announcement->created_at)->format('d/m/Y H:i');

                return $announcement;
            });

            return response()->json([
                'success' => true,
                'announcements' => $announcements
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des annonces: ' . $e->getMessage());
            Log::error($e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des annonces',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function createRideRequest(Request $request)
    {
        DB::beginTransaction();

        try {
            $validated = $request->validate([
                'announcement_id' => 'required|exists:driver_announcements,id',
                'pickup_lat' => 'required|numeric|between:-90,90',
                'pickup_lng' => 'required|numeric|between:-180,180',
                'pickup_address' => 'required|string',
                'destination_lat' => 'required|numeric|between:-90,90',
                'destination_lng' => 'required|numeric|between:-180,180',
                'destination_address' => 'required|string',
                'scheduled_date' => 'required|date_format:Y-m-d',
                'scheduled_time' => 'required|date_format:H:i',
                'note' => 'nullable|string|max:500'
            ]);

            try {
                $scheduledAt = Carbon::createFromFormat(
                    'Y-m-d H:i',
                    $validated['scheduled_date'] . ' ' . $validated['scheduled_time'],
                    'Africa/Casablanca'
                );
            } catch (\Exception $e) {
                return response()->json([
                    'error' => 'Format de date ou heure invalide'
                ], 422);
            }

            if ($scheduledAt->isPast()) {
                return response()->json([
                    'error' => 'La date et l\'heure doivent être dans le futur'
                ], 422);
            }

            $announcement = DriverAnnouncement::findOrFail($validated['announcement_id']);

            $existingRequest = RideRequest::where('announcement_id', $announcement->id)
                ->where('passenger_id', auth()->id())
                ->whereIn('status', ['pending', 'accepted'])
                ->first();

            if ($existingRequest) {
                return response()->json([
                    'error' => 'Vous avez déjà une demande en cours pour ce chauffeur'
                ], 422);
            }

            $rideRequest = RideRequest::create([
                'passenger_id' => auth()->id(),
                'driver_id' => $announcement->user_id,
                'announcement_id' => $validated['announcement_id'],
                'pickup_lat' => $validated['pickup_lat'],
                'pickup_lng' => $validated['pickup_lng'],
                'pickup_address' => $validated['pickup_address'],
                'destination_lat' => $validated['destination_lat'],
                'destination_lng' => $validated['destination_lng'],
                'destination_address' => $validated['destination_address'],
                'scheduled_at' => $scheduledAt,
                'note' => $validated['note'],
                'status' => 'pending'
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Demande de course envoyée avec succès',
                'ride_request' => $rideRequest->load(['driver', 'passenger'])
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la création de la demande: ' . $e->getMessage());
            return response()->json([
                'error' => 'Erreur lors de l\'envoi de la demande: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getMyRideRequests()
    {
        try {
            $requests = RideRequest::with(['driver', 'announcement'])
                ->where('passenger_id', auth()->id())
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json($requests);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des demandes: ' . $e->getMessage());
            return response()->json([
                'error' => 'Erreur lors de la récupération des demandes'
            ], 500);
        }
    }

    public function cancelRideRequest($id)
    {
        try {
            $request = RideRequest::where('passenger_id', auth()->id())
                ->with(['driver'])
                ->findOrFail($id);

            if (!$request->isPending() && !$request->isAccepted()) {
                return response()->json([
                    'error' => 'Cette demande ne peut plus être annulée'
                ], 422);
            }

            if (!$request->getCanBeCancelledAttribute()) {
                $minutesLeft = $request->getMinutesUntilDepartureAttribute();
                return response()->json([
                    'error' => "Impossible d'annuler la réservation moins de 5 minutes avant le départ. Il reste {$minutesLeft} minutes."
                ], 422);
            }

            DB::beginTransaction();

            try {
                $request->update([
                    'status' => 'cancelled',
                    'passenger_confirmation_at' => now()
                ]);

                if ($request->driver) {
                    Notification::send($request->driver, new RideCancelledNotification($request));
                }

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Réservation annulée avec succès'
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'annulation: ' . $e->getMessage());
            return response()->json([
                'error' => 'Erreur lors de l\'annulation de la réservation'
            ], 500);
        }
    }

    public function confirmRideRequest($id)
    {
        try {
            $request = RideRequest::where('passenger_id', auth()->id())
                ->with(['driver'])
                ->findOrFail($id);

            if (!$request->isAccepted()) {
                return response()->json([
                    'error' => 'Cette demande ne peut pas être confirmée'
                ], 422);
            }

            if ($request->passenger_confirmation_at) {
                return response()->json([
                    'error' => 'Cette demande a déjà été confirmée'
                ], 422);
            }

            DB::beginTransaction();

            try {
                $request->update([
                    'passenger_confirmation_at' => now()
                ]);

                Notification::send($request->driver, new RideConfirmedNotification($request));

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Réservation confirmée avec succès'
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
        } catch (\Exception $e) {
            Log::error('Erreur lors de la confirmation: ' . $e->getMessage());
            return response()->json([
                'error' => 'Erreur lors de la confirmation de la réservation'
            ], 500);
        }
    }

    public function deleteRideRequest($id)
    {
        try {
            $request = RideRequest::where('passenger_id', auth()->id())
                ->with(['driver'])
                ->findOrFail($id);

            if (!$request->getCanBeDeletedAttribute()) {
                return response()->json([
                    'error' => 'Cette réservation ne peut pas être supprimée'
                ], 422);
            }

            DB::beginTransaction();

            try {
                // D'abord annuler la réservation
                $request->update([
                    'status' => 'cancelled',
                    'passenger_confirmation_at' => now()
                ]);

                // Notifier le chauffeur
                if ($request->driver) {
                    Notification::send($request->driver, new RideDeletedNotification($request));
                }

                // Supprimer la réservation
                $request->delete();

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Réservation supprimée avec succès'
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
        } catch (\Exception $e) {
            Log::error('Erreur lors de la suppression: ' . $e->getMessage());
            return response()->json([
                'error' => 'Erreur lors de la suppression de la réservation'
            ], 500);
        }
    }
}

