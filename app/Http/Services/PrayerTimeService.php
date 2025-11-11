<?php
namespace App\Http\Services;

use Carbon\Carbon;
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

  public function getNextPrayer(float $lat, float $lng, int $method, string $timezone = 'Asia/Riyadh'): ?array
    {
        $now = now($timezone);
        $timings = $this->getPrayerTimes($lat, $lng, $method);

        if (! $timings) {
            return null;
        }

        $nextPrayer = null;
        $nextTime = null;

        foreach ($timings as $name => $time) {
            // تجاهل القيم غير الوقتية (مثل "Sunrise")
            if (!preg_match('/^\d{2}:\d{2}$/', $time)) continue;

            $prayerTime = Carbon::parse($time, $timezone);
            if ($prayerTime->greaterThan($now)) {
                $nextPrayer = $name;
                $nextTime = $prayerTime;
                break;
            }
        }

        // إذا لم نجد صلاة لاحقة، فالصلاة القادمة هي فجر الغد
        if (! $nextPrayer) {
            $tomorrowTimings = $this->getPrayerTimes($lat, $lng, $method, now()->addDay()->toDateString());
            $nextPrayer = 'Fajr';
            $nextTime = Carbon::parse($tomorrowTimings['Fajr'], $timezone);
        }

        return [
            'name' => $nextPrayer,
            'time' => $nextTime->toDateTimeString(),
            'remaining_minutes' => $now->diffInMinutes($nextTime)
        ];
    }
}
