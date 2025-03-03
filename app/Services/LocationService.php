<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Exception;

class LocationService
{
    protected $nominatimBaseUrl = 'https://nominatim.openstreetmap.org';
    protected $cacheDuration = 3600; // 1 heure

    public function reverseGeocode($latitude, $longitude)
    {
        try {
            $cacheKey = "reverse_geocode_{$latitude}_{$longitude}";

            // Vérifier le cache
            if (Cache::has($cacheKey)) {
                return Cache::get($cacheKey);
            }

            // Faire la requête à l'API
            $response = Http::withHeaders([
                'User-Agent' => 'Laravel/1.0'
            ])->get($this->nominatimBaseUrl . '/reverse', [
                'format' => 'json',
                'lat' => $latitude,
                'lon' => $longitude,
                'zoom' => 18,
                'addressdetails' => 1
            ]);

            if (!$response->successful()) {
                throw new Exception('Erreur lors de la requête à l\'API de géocodage');
            }

            $data = $response->json();

            // Mettre en cache
            Cache::put($cacheKey, $data, $this->cacheDuration);

            return $data;

        } catch (Exception $e) {
            throw new Exception('Service de géocodage indisponible: ' . $e->getMessage());
        }
    }

    public function validateCoordinates($latitude, $longitude): bool
    {
        return is_numeric($latitude) &&
               is_numeric($longitude) &&
               $latitude >= -90 &&
               $latitude <= 90 &&
               $longitude >= -180 &&
               $longitude <= 180;
    }
}

