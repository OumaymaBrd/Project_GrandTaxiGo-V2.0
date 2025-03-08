<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use App\Models\RideRequest;
use Illuminate\Http\Request;

class RideCompletionController extends Controller
{
    public function complete(Request $request, $id)
    {
        $ride = RideRequest::findOrFail($id);

        // Vérifier que le chauffeur est bien celui de la course
        if ($ride->driver_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Vous n\'êtes pas autorisé à effectuer cette action'
            ], 403);
        }

        // Vérifier que la course n'est pas déjà terminée
        if ($ride->status === 'completed') {
            return response()->json([
                'success' => false,
                'message' => 'Cette course est déjà marquée comme terminée'
            ], 400);
        }

        // Mettre à jour le statut
        $ride->status = 'completed';
        $ride->completed_at = now();
        $ride->needs_rating = true; // Ajoutez ce champ à votre table ride_requests
        $ride->save();

        return response()->json([
            'success' => true,
            'message' => 'Course marquée comme terminée'
        ]);
    }
}
