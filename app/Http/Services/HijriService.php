<?php

namespace App\Http\Services;

use Illuminate\Support\Facades\Http;

class HijriService
{
    private string $baseUrl = 'https://api.aladhan.com/v1/';

    /**
     * تحويل التاريخ الميلادي إلى هجري
     */
    public function gregorianToHijri(string $date): array
    {
        $response = Http::get($this->baseUrl . 'gToH', [
            'date' => $date, // الصيغة: DD-MM-YYYY
        ]);

        if ($response->failed()) {
            return [
                'success' => false,
                'message' => 'فشل في الاتصال بخدمة التقويم الهجري',
                'status' => $response->status(),
            ];
        }

        $data = $response->json();

        return [
            'success' => true,
            'hijri_date' => $data['data']['hijri']['date'] ?? null,
            'day' => $data['data']['hijri']['day'] ?? null,
            'month' => $data['data']['hijri']['month']['ar'] ?? null,
            'year' => $data['data']['hijri']['year'] ?? null,
        ];
    }

    /**
     * جلب تقويم هجري لشهر كامل (اختياري)
     */
 public function hijriCalendar(int $month, int $year): array
{
    $response = Http::get($this->baseUrl . "gToHCalendar/{$month}/{$year}");

    if ($response->failed()) {
        return [
            'success' => false,
            'message' => 'فشل في جلب التقويم الهجري للشهر المحدد',
            'status' => $response->status(),
        ];
    }

    $data = $response->json();

    // اختصرنا الريسبونس ليشمل فقط الهجري وبالعربي
    $calendar = collect($data['data'] ?? [])->map(function ($day) {
        return [
            'day' => $day['hijri']['day'] ?? null,
            'weekday' => $day['hijri']['weekday']['ar'] ?? null,
            'month' => $day['hijri']['month']['ar'] ?? null,
            'year' => $day['hijri']['year'] ?? null,
        ];
    })->toArray();

    return [
        'success' => true,
        'calendar' => $calendar,
    ];
}

}
