<?php

namespace App\Http\Controllers;

use App\Models\DriverAvailability;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DriverAvailabilityController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'latitude' => 'required|numeric|between:-90,90',
                'longitude' => 'required|numeric|between:-180,180',
                'address' => 'required|string',
                'city' => 'required|string',
                'postal_code' => 'nullable|string',
                'country' => 'nullable|string',
                'note' => 'nullable|string|max:500'
            ]);

            $availability = DriverAvailability::create([
                'user_id' => Auth::id(),
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'address' => $request->address,
                'city' => $request->city,
                'postal_code' => $request->postal_code,
                'country' => $request->country,
                'note' => $request->note,
                'announced_at' => now(),
                'is_available' => true
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Disponibilité publiée avec succès',
                'availability' => $availability
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la création de la disponibilité: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la publication: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getMyAnnouncements()
    {
        try {
            $announcements = DriverAvailability::where('user_id', Auth::id())
                ->orderBy('announced_at', 'desc')
                ->get();

            return response()->json($announcements);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des annonces: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des annonces'
            ], 500);
        }
    }

    public function toggleAvailability($id)
    {
        try {
            $availability = DriverAvailability::findOrFail($id);

            if ($availability->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Non autorisé'
                ], 403);
            }

            $availability->update([
                'is_available' => !$availability->is_available
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Statut mis à jour',
                'availability' => $availability
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la mise à jour de la disponibilité: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour'
            ], 500);
        }
    }
}

