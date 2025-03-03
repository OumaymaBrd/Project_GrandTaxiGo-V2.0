<?php

namespace App\Http\Controllers;

use App\Models\DriverPost;
use App\Services\LocationService;
use Illuminate\Http\Request;
use Exception;

class DriverPostController extends Controller
{
    protected $locationService;

    public function __construct(LocationService $locationService)
    {
        $this->locationService = $locationService;
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'content' => 'required|string|max:500',
                'latitude' => 'required|numeric|between:-90,90',
                'longitude' => 'required|numeric|between:-180,180'
            ]);

            // Valider les coordonnées
            if (!$this->locationService->validateCoordinates($request->latitude, $request->longitude)) {
                throw new Exception('Coordonnées invalides');
            }

            // Obtenir l'adresse à partir des coordonnées
            $addressData = $this->locationService->reverseGeocode($request->latitude, $request->longitude);
            $address = $addressData['display_name'] ?? 'Adresse inconnue';

            $post = DriverPost::create([
                'user_id' => auth()->id(),
                'content' => $request->content,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'address' => $address,
                'is_available' => true
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Publication créée avec succès',
                'post' => $post->load('driver')
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getActivePosts()
    {
        try {
            $posts = DriverPost::with('driver')
                ->where('is_available', true)
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json($posts);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des publications'
            ], 500);
        }
    }

    public function toggleAvailability(DriverPost $post)
    {
        try {
            if ($post->user_id !== auth()->id()) {
                throw new Exception('Non autorisé');
            }

            $post->update([
                'is_available' => !$post->is_available
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Disponibilité mise à jour',
                'post' => $post
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}

