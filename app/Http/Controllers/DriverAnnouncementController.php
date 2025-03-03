<?php

namespace App\Http\Controllers;

use App\Models\DriverAnnouncement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class DriverAnnouncementController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:chauffeur');
    }

    public function getMyAnnouncements()
    {
        try {
            $announcements = DriverAnnouncement::where('user_id', Auth::id())
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json($announcements);

        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des annonces: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors du chargement des annonces'
            ], 500);
        }
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $validated = $request->validate([
                'latitude' => 'required|numeric|between:-90,90',
                'longitude' => 'required|numeric|between:-180,180',
                'location_name' => 'nullable|string|max:255', // Rendu optionnel
                'address' => 'nullable|string|max:255',
                'note' => 'nullable|string|max:500'
            ]);

            // Récupérer les informations de géocodage
            $geocodeData = $this->getGeocodeData($validated['latitude'], $validated['longitude']);

            // Désactiver les anciennes annonces
            DriverAnnouncement::where('user_id', Auth::id())
                ->where('is_active', true)
                ->update(['is_active' => false]);

            // Créer une nouvelle annonce
            $announcement = DriverAnnouncement::create([
                'user_id' => Auth::id(),
                'latitude' => $validated['latitude'],
                'longitude' => $validated['longitude'],
                'location_name' => $validated['location_name'] ?? $geocodeData['formatted_address'] ?? null,
                'address' => $validated['address'] ?? $geocodeData['formatted_address'] ?? null,
                'city' => $geocodeData['city'] ?? null,
                'country' => $geocodeData['country'] ?? null,
                'note' => $validated['note'] ?? null,
                'is_active' => true
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Position publiée avec succès',
                'announcement' => $announcement
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la création de l\'annonce: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la publication: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Récupère les informations de géocodage depuis Nominatim
     */
    private function getGeocodeData($latitude, $longitude)
    {
        try {
            $url = "https://nominatim.openstreetmap.org/reverse?format=json&lat={$latitude}&lon={$longitude}&addressdetails=1";
            $response = file_get_contents($url);
            $data = json_decode($response, true);

            return [
                'formatted_address' => $data['display_name'] ?? null,
                'city' => $data['address']['city'] ??
                         $data['address']['town'] ??
                         $data['address']['village'] ?? null,
                'country' => $data['address']['country'] ?? null
            ];
        } catch (\Exception $e) {
            Log::warning('Erreur de géocodage: ' . $e->getMessage());
            return [
                'formatted_address' => null,
                'city' => null,
                'country' => null
            ];
        }
    }

    public function toggleActive($id)
    {
        DB::beginTransaction();

        try {
            $announcement = DriverAnnouncement::where('user_id', Auth::id())
                ->findOrFail($id);

            $announcement->update([
                'is_active' => !$announcement->is_active
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Statut mis à jour avec succès',
                'announcement' => $announcement
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la mise à jour de l\'annonce: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour'
            ], 500);
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            $announcement = DriverAnnouncement::where('user_id', Auth::id())
                ->findOrFail($id);

            $announcement->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Annonce supprimée avec succès'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la suppression de l\'annonce: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression'
            ], 500);
        }
    }
}

