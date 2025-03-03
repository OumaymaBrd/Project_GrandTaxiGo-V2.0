<?php

namespace App\Http\Controllers;

use App\Models\DriverAnnouncement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PassengerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:passager');
    }

    public function getAvailableDrivers()
    {
        try {
            $announcements = DriverAnnouncement::with(['driver' => function($query) {
                $query->select('id', 'name', 'phone', 'profile_image');
            }])
            ->where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->get();

            return response()->json($announcements);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des annonces: ' . $e->getMessage());
            return response()->json([
                'error' => 'Erreur lors de la récupération des annonces'
            ], 500);
        }
    }

    public function contactDriver($id)
    {
        try {
            $announcement = DriverAnnouncement::with('driver')->findOrFail($id);

            // Ici vous pouvez ajouter la logique pour enregistrer le contact
            // Par exemple, créer une demande de course

            return response()->json([
                'success' => true,
                'message' => 'Demande envoyée au chauffeur',
                'driver' => [
                    'name' => $announcement->driver->name,
                    'phone' => $announcement->driver->phone
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la tentative de contact: ' . $e->getMessage());
            return response()->json([
                'error' => 'Impossible de contacter le chauffeur'
            ], 500);
        }
    }
}

