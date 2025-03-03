<?php

namespace App\Http\Controllers;

use App\Models\RideRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DriverRideController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:chauffeur');
    }

    public function getPendingRequests()
    {
        try {
            $requests = RideRequest::with(['passenger'])
                ->where('driver_id', Auth::id())
                ->where('status', 'pending')
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'requests' => $requests
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des demandes en attente: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Une erreur est survenue lors du chargement des demandes',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    public function getAllRideRequests()
    {
        try {
            $requests = RideRequest::with(['passenger'])
                ->where('driver_id', Auth::id())
                ->orderBy('scheduled_at', 'desc')
                ->get()
                ->map(function ($request) {
                    return [
                        'id' => $request->id,
                        'passenger' => [
                            'name' => $request->passenger->name,
                            'phone' => $request->passenger->phone,
                            'profile_image_url' => $request->passenger->profile_image_url
                        ],
                        'pickup_address' => $request->pickup_address,
                        'destination_address' => $request->destination_address,
                        'scheduled_at' => $request->scheduled_at->format('Y-m-d H:i:s'),
                        'status' => $request->status,
                        'note' => $request->note,
                        'passenger_confirmation_at' => $request->passenger_confirmation_at ?
                            $request->passenger_confirmation_at->format('Y-m-d H:i:s') : null,
                        'created_at' => $request->created_at->format('Y-m-d H:i:s'),
                        'can_be_cancelled' => $request->can_be_cancelled,
                        'time_until_departure' => $request->time_until_departure,
                        'minutes_until_departure' => $request->minutes_until_departure
                    ];
                });

            return response()->json([
                'success' => true,
                'requests' => $requests
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des réservations: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Une erreur est survenue lors du chargement des réservations',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    public function respondToRequest(Request $request, $id)
    {
        DB::beginTransaction();

        try {
            $validated = $request->validate([
                'status' => 'required|in:accepted,rejected'
            ]);

            $rideRequest = RideRequest::where('driver_id', Auth::id())
                ->findOrFail($id);

            if (!$rideRequest->isPending()) {
                throw new \Exception('Cette demande a déjà été traitée');
            }

            $rideRequest->update([
                'status' => $validated['status'],
                'driver_response_at' => now()
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $validated['status'] === 'accepted' ?
                    'Demande acceptée avec succès' :
                    'Demande refusée avec succès',
                'ride_request' => $rideRequest->fresh(['passenger'])
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la réponse à la demande: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Une erreur est survenue lors du traitement de la demande',
                'details' => $e->getMessage()
            ], 500);
        }
    }
}

