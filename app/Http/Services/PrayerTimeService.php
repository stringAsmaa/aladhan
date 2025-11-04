<?php
namespace App\Http\Services;

use Illuminate\Support\Facades\Http;

class PrayerTimeService{

    public function getPrayerTimes(float $latitude, float $longitude, int $method = 3)
{
    $timestamp = time();

    $response = Http::get('https://api.aladhan.com/v1/timings/' . $timestamp, [
        'latitude' => $latitude,
        'longitude' => $longitude,
        'method' => $method,
    ]);

    if ($response->failed()) {
        return null;
    }

    $data = $response->json();

    if (! isset($data['data']['timings'])) {
        return null;
    }

    return $data['data']['timings'];
}

}
