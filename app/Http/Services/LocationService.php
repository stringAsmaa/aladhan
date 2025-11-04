<?php
namespace App\Http\Services;

use Illuminate\Support\Facades\Http;

class LocationService{

    public function getCity($latitude,$longitude){
           //  باستخدام OpenStreetMap Nominatim API
     "https://nominatim.openstreetmap.org/reverse";
$response = Http::withHeaders([
    'User-Agent' => 'aladan/1.0 (asmaaabdaljalil54@gmail.com)'
])->get('https://nominatim.openstreetmap.org/reverse', [
    'lat' => $latitude,
    'lon' => $longitude,
    'format' => 'json'
]);


if ($response->failed()) {
    return response()->json([
        'error' => 'فشل في جلب اسم المدينة',
        'status' => $response->status(),
        'body' => $response->body()
    ], 500);
}

        $data = $response->json();

        $city = $data['address']['city'] ?? $data['address']['town'] ?? $data['address']['village'] ?? null;

        if (!$city) {
            return response()->json(['error' => 'لا يمكن تحديد المدينة'], 404);
        }

        return response()->json([
            'city' => $city,
        ]);
    }
}
