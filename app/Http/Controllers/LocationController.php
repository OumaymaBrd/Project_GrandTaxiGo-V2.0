<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class LocationController extends Controller
{
    public function updateLocation(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);

        try {
            $user = auth()->user();

            // Mettre à jour la position du chauffeur
            $user->location()->updateOrCreate(
                ['user_id' => $user->id],
                [
                    'latitude' => $request->latitude,
                    'longitude' => $request->longitude,
                    'is_online' => true,
                    'last_update' => now()
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Position mise à jour avec succès'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour de la position'
            ], 500);
        }
    }

    public function getAddress(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        try {
            $response = Http::get('https://nominatim.openstreetmap.org/reverse', [
                'format' => 'json',
                'lat' => $request->latitude,
                'lon' => $request->longitude,
                'zoom' => 18,
                'addressdetails' => 1
            ]);

            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'address' => $response->json()
                ]);
            }

            throw new \Exception('Erreur lors de la récupération de l\'adresse');
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}

