<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class InfobipService
{
    protected $baseUrl;
    protected $apiKey;
    protected $sender;

    public function __construct()
    {
        $this->baseUrl = env('INFOBIP_BASE_URL');
        $this->apiKey = env('INFOBIP_API_KEY');
        $this->sender = env('INFOBIP_SENDER');
    }

    public function sendSms($phoneNumber, $message)
    {
        try {
            $phoneNumber = $this->formatPhoneNumber($phoneNumber);

            Log::info('Configuration Infobip', [
                'base_url' => $this->baseUrl,
                'sender' => $this->sender,
                'has_api_key' => !empty($this->apiKey)
            ]);

            $payload = [
                'messages' => [
                    [
                        'from' => $this->sender,
                        'destinations' => [
                            ['to' => $phoneNumber]
                        ],
                        'text' => $message
                    ]
                ]
            ];

            Log::info('Payload Infobip', $payload);

            $response = Http::withHeaders([
                'Authorization' => 'App ' . $this->apiKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->post($this->baseUrl . '/sms/2/text/advanced', $payload);

            Log::info('Réponse Infobip complète', [
                'status' => $response->status(),
                'headers' => $response->headers(),
                'body' => $response->json()
            ]);

            if (!$response->successful()) {
                throw new \Exception('Erreur Infobip: ' . $response->body());
            }

            return $response->json();

        } catch (\Exception $e) {
            Log::error('Erreur détaillée Infobip', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
   // Dans app/Services/InfobipService.php
private function formatPhoneNumber($phoneNumber)
{
    // Supprimer tous les caractères non numériques
    $phoneNumber = preg_replace('/[^0-9]/', '', $phoneNumber);

    // Pour le Maroc, vérifiez le format exact requis par Infobip
    if (substr($phoneNumber, 0, 1) === '0') {
        // Essayez avec +212 au lieu de juste 212
        $phoneNumber = '+212' . substr($phoneNumber, 1);
    }

    Log::info('Numéro formaté', ['original' => $phoneNumber, 'formatted' => $phoneNumber]);

    return $phoneNumber;
}
}
