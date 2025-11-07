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

public function findNearestMosque(float $lat, float $lng, int $radius = 2000)
{
    $response = Http::get("https://api.masjidnear.me/v1/masjids/search", [
        'lat' => $lat,
        'lng' => $lng,
        'rad' => $radius,
    ]);

    if ($response->failed()) {
        return response()->json([
            'success' => false,
            'message' => 'فشل في الاتصال بخدمة المساجد',
            'status' => $response->status(),
            'error_body' => $response->body(),
        ], $response->status());
    }

    $data = $response->json();

    // المساجد من المسار الصحيح
    $masjids = $data['debug_data']['data']['masjids'] ?? [];

    if (empty($masjids)) {
        return response()->json([
            'success' => false,
            'message' => 'لم يتم العثور على أي مسجد قريب',
            'debug_data' => $data,
        ], 404);
    }

    // نحسب أقرب مسجد فعلياً
    $nearest = collect($masjids)->map(function ($m) use ($lat, $lng) {
        $coords = $m['masjidLocation']['coordinates'] ?? [0, 0];
        $distance = $this->calculateDistance($lat, $lng, $coords[1], $coords[0]);
        $m['distance_km'] = $distance;
        return $m;
    })->sortBy('distance_km')->first();

// ... بعد حساب $nearest
return response()->json([
    'success' => true,
    'message' => 'تم العثور على أقرب مسجد',
    'nearest_mosque_name' => $nearest['masjidName'] ?? null,
], 200);

}

// دالة لحساب المسافة بين نقطتين بالإحداثيات (صيغة Haversine)
private function calculateDistance($lat1, $lon1, $lat2, $lon2)
{
    $earthRadius = 6371; // نصف قطر الأرض بالكيلومتر
    $dLat = deg2rad($lat2 - $lat1);
    $dLon = deg2rad($lon2 - $lon1);
    $a = sin($dLat / 2) * sin($dLat / 2) +
        cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
        sin($dLon / 2) * sin($dLon / 2);
    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
    return $earthRadius * $c;
}

}
