<?php

namespace App\Http\Controllers;

use App\Models\RideRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class DriverRequestController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:chauffeur');
    }

    public function getPendingRequests()
    {
        try {
            $requests = RideRequest::with(['passenger' => function($query) {
                    $query->select('id', 'name', 'phone', 'profile_image_url');
                }])
                ->where('driver_id', Auth::id())
                ->where('status', 'pending')
                ->orderBy('scheduled_at', 'asc')
                ->get()
                ->map(function ($request) {
                    return [
                        'id' => $request->id,
                        'passenger' => [
                            'id' => $request->passenger->id,
                            'name' => $request->passenger->name,
                            'phone' => $request->passenger->phone,
                            'profile_image_url' => $request->passenger->profile_image_url
                        ],
                        'pickup_address' => $request->pickup_address,
                        'destination_address' => $request->destination_address,
                        'scheduled_at' => Carbon::parse($request->scheduled_at)->format('Y-m-d H:i:s'),
                        'note' => $request->note,
                        'status' => $request->status
                    ];
                });

            return response()->json([
                'success' => true,
                'requests' => $requests
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des demandes: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors du chargement des demandes'
            ], 500);
        }
    }

    public function getAllRequests()
    {
        try {
            $requests = RideRequest::with(['passenger' => function($query) {
                    $query->select('id', 'name', 'phone', 'profile_image_url');
                }])
                ->where('driver_id', Auth::id())
                ->orderBy('scheduled_at', 'desc')
                ->get()
                ->map(function ($request) {
                    return [
                        'id' => $request->id,
                        'passenger' => [
                            'id' => $request->passenger->id,
                            'name' => $request->passenger->name,
                            'phone' => $request->passenger->phone,
                            'profile_image_url' => $request->passenger->profile_image_url
                        ],
                        'pickup_address' => $request->pickup_address,
                        'destination_address' => $request->destination_address,
                        'scheduled_at' => Carbon::parse($request->scheduled_at)->format('Y-m-d H:i:s'),
                        'note' => $request->note,
                        'status' => $request->status,
                        'passenger_confirmation_at' => $request->passenger_confirmation_at ?
                            Carbon::parse($request->passenger_confirmation_at)->format('Y-m-d H:i:s') : null
                    ];
                });

            return response()->json([
                'success' => true,
                'requests' => $requests
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des demandes: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors du chargement des demandes'
            ], 500);
        }
    }

    public function respond(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'status' => 'required|in:accepted,rejected'
            ]);

            $rideRequest = RideRequest::where('driver_id', Auth::id())
                ->findOrFail($id);

            if ($rideRequest->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Cette demande ne peut plus être modifiée'
                ], 400);
            }

            $rideRequest->update([
                'status' => $validated['status'],
                'response_at' => now()
            ]);

            // Notifier le passager
            $rideRequest->passenger->notify(new RideRequestResponded($rideRequest));

            return response()->json([
                'success' => true,
                'message' => $validated['status'] === 'accepted' ?
                    'Demande acceptée avec succès' :
                    'Demande refusée avec succès'
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la réponse à la demande: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors du traitement de la demande'
            ], 500);
        }
    }

    public function complete($id)
    {
        try {
            $rideRequest = RideRequest::where('driver_id', Auth::id())
                ->findOrFail($id);

            if ($rideRequest->status !== 'accepted') {
                return response()->json([
                    'success' => false,
                    'message' => 'Cette course ne peut pas être marquée comme terminée'
                ], 400);
            }

            $rideRequest->update([
                'status' => 'completed',
                'completed_at' => now()
            ]);

            // Notifier le passager
            $rideRequest->passenger->notify(new RideCompleted($rideRequest));

            return response()->json([
                'success' => true,
                'message' => 'Course marquée comme terminée avec succès'
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la complétion de la course: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de la mise à jour de la course'
            ], 500);
        }
    }
}

