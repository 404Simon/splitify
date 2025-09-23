<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Http;

final class GeolocationService
{
    private string $baseUrl = 'https://nominatim.openstreetmap.org/search';

    public function getCoordinates(string $address): ?array
    {
        $response = Http::withHeaders([
            'User-Agent' => env('APP_NAME', 'Splitify'),
        ])->get($this->baseUrl, [
            'q' => $address,
            'format' => 'json',
            'limit' => 1,  // Only return the best match
        ]);

        if ($response->successful() && ! empty($response->json())) {
            $data = $response->json()[0];

            return [
                'lat' => $data['lat'],
                'lon' => $data['lon'],
            ];
        }

        return null;
    }
}
