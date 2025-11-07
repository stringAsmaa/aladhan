<?php
namespace App\Http\Services;

use Illuminate\Support\Facades\Http;

class PrayerTimeService{

    public function getPrayerTimes(float $latitude, float $longitude, int $method)
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

public function getQiblaDirection(float $latitude, float $longitude){
  $response = Http::get("https://api.aladhan.com/v1/qibla/{$latitude}/{$longitude}");

        if ($response->failed()) {
            return response()->json([
                'error' => 'فشل في جلب اتجاه القبلة',
                'status' => $response->status(),
                'body' => $response->body()
            ], 500);
        }

        $data = $response->json();

        return response()->json([
            'direction_degrees' => $data['data']['direction'],
            'latitude' => $latitude,
            'longitude' => $longitude,
            'description' => 'الزاوية تمثل الاتجاه من الشمال الحقيقي نحو الكعبة المشرفة بالدرجات.'
        ]);
}
}
