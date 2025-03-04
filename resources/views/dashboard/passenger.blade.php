<?php

namespace App\Http\Controllers;

use App\Models\DriverAnnouncement;
use App\Models\RideRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Notifications\RideConfirmedNotification;

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
            $query = DriverAnnouncement::with(['driver' => function($query) {
                $query->select('id', 'name', 'phone', 'profile_image');
            }])
            ->where('is_active', true);

            $announcements = $query->orderBy('created_at', 'desc')->get();

            // Ajouter l'information sur les demandes en attente
            $announcements->each(function ($announcement) {
                $announcement->has_pending_request = RideRequest::where('announcement_id', $announcement->id)
                    ->where('passenger_id', auth()->id())
                    ->whereIn('status', ['pending', 'accepted'])
                    ->exists();
            });

            return response()->json([
                'success' => true,
                'announcements' => $announcements
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des annonces: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors de la récupération des annonces'
            ], 500);
        }
    }

    public function getMyRideRequests()
    {
        try {
            $requests = RideRequest::with(['driver', 'announcement'])
                ->where('passenger_id', Auth::id())
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json($requests);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des demandes: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors de la récupération des demandes'
            ], 500);
        }
    }

    public function confirmRideRequest($id)
    {
        DB::beginTransaction();

        try {
            $request = RideRequest::where('passenger_id', Auth::id())
                ->with(['driver'])
                ->findOrFail($id);

            if (!$request->canBeConfirmed()) {
                throw new \Exception('Cette demande ne peut pas être confirmée');
            }

            $request->update([
                'passenger_confirmation_at' => now(),
                'status' => 'confirmed'
            ]);

            if ($request->driver) {
                $request->driver->notify(new RideConfirmedNotification($request));
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Réservation confirmée avec succès'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la confirmation: ' . $e->getMessage());
            Log::error($e->getTraceAsString());

            return response()->json([
                'success' => false,
                'error' => 'Erreur lors de la confirmation de la réservation: ' . $e->getMessage()
            ], 500);
        }
    }

    public function cancelRideRequest($id)
    {
        DB::beginTransaction();

        try {
            $request = RideRequest::where('passenger_id', Auth::id())
                ->findOrFail($id);

            if (!$request->canBeCancelled) {
                throw new \Exception('Cette réservation ne peut plus être annulée');
            }

            $request->update([
                'status' => 'cancelled',
                'cancelled_at' => now()
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Réservation annulée avec succès'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de l\'annulation: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'error' => 'Erreur lors de l\'annulation de la réservation: ' . $e->getMessage()
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

            $scheduledAt = Carbon::createFromFormat(
                'Y-m-d H:i',
                $validated['scheduled_date'] . ' ' . $validated['scheduled_time']
            );

            if ($scheduledAt->isPast()) {
                throw new \Exception('La date et l\'heure doivent être dans le futur');
            }

            $announcement = DriverAnnouncement::findOrFail($validated['announcement_id']);

            // Vérifier s'il n'y a pas déjà une demande en cours
            $existingRequest = RideRequest::where('announcement_id', $announcement->id)
                ->where('passenger_id', Auth::id())
                ->whereIn('status', ['pending', 'accepted'])
                ->first();

            if ($existingRequest) {
                throw new \Exception('Vous avez déjà une demande en cours pour ce chauffeur');
            }

            $rideRequest = RideRequest::create([
                'passenger_id' => Auth::id(),
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
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

