<?php

namespace App\Http\Controllers;

use App\Models\Rating;
use App\Models\RideRequest;
use Illuminate\Http\Request;

class RatingController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'ride_id' => 'required|exists:ride_requests,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:500',
        ]);

        $ride = RideRequest::findOrFail($validated['ride_id']);

        // Vérifier que le passager est bien celui de la course
        if ($ride->passenger_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Vous n\'êtes pas autorisé à évaluer cette course'
            ], 403);
        }

        // Vérifier que la course est bien terminée
        if ($ride->status !== 'completed') {
            return response()->json([
                'success' => false,
                'message' => 'Vous ne pouvez évaluer que des courses terminées'
            ], 400);
        }

        // Vérifier qu'il n'y a pas déjà une évaluation
        if (Rating::where('ride_id', $ride->id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Vous avez déjà évalué cette course'
            ], 400);
        }

        // Créer la notation
        $rating = new Rating();
        $rating->ride_id = $ride->id;
        $rating->passenger_id = auth()->id();
        $rating->driver_id = $ride->driver_id;
        $rating->rating = $validated['rating'];
        $rating->comment = $validated['comment'] ?? null;
        $rating->save();

        // Marquer la course comme n'ayant plus besoin de notation
        $ride->needs_rating = false;
        $ride->save();

        return response()->json([
            'success' => true,
            'message' => 'Merci pour votre évaluation !'
        ]);
    }

    public function checkCompletedRides()
    {
        // Trouver les courses terminées qui ont besoin d'être évaluées
        $completedRide = RideRequest::where('passenger_id', auth()->id())
            ->where('status', 'completed')
            ->where('needs_rating', true)
            ->orderBy('completed_at', 'desc')
            ->first();

        if (!$completedRide) {
            return response()->json([
                'success' => true,
                'ride' => null
            ]);
        }

        return response()->json([
            'success' => true,
            'ride' => [
                'id' => $completedRide->id,
                'driver_name' => $completedRide->driver->name,
                'driver_image' => $completedRide->driver->profile_image_url ?? '/images/default-avatar.png'
            ]
        ]);
    }
}
