<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Ride; // Assurez-vous que ce modèle existe

class SmsController extends Controller
{
    public function sendCompletionSms(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'rideId' => 'required|integer'
        ]);

        try {
            // Récupérer les informations de la course si nécessaire
            $ride = Ride::findOrFail($request->rideId);

            // Message à envoyer
            $message = "Une course a été marquée comme terminée. ID de course: " . $ride->id;

            // Utiliser un service d'envoi de SMS (exemple avec une API fictive)
            $response = $this->sendSms($request->phone, $message);

            return response()->json([
                'success' => true,
                'message' => 'SMS envoyé avec succès'
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'envoi du SMS: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'envoi du SMS: ' . $e->getMessage()
            ], 500);
        }
    }

    private function sendSms($phoneNumber, $message)
    {
        // Implémentez ici l'envoi de SMS avec le service de votre choix
        // Exemple avec Twilio (vous devrez installer le package Twilio)

        // Si vous utilisez Twilio, décommentez ce code et installez le package:
        // composer require twilio/sdk

        /*
        $sid = env('TWILIO_SID');
        $token = env('TWILIO_AUTH_TOKEN');
        $twilioNumber = env('TWILIO_NUMBER');

        $twilio = new \Twilio\Rest\Client($sid, $token);

        return $twilio->messages->create(
            $phoneNumber,
            [
                'from' => $twilioNumber,
                'body' => $message
            ]
        );
        */

        // Pour l'instant, on simule juste l'envoi
        Log::info("SMS envoyé à $phoneNumber: $message");
        return true;
    }
}
