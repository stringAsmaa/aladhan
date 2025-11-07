<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Services\PrayerTimeService;

class PrayerTimeController extends Controller
{
    public function __construct(protected PrayerTimeService $service)
    {
        $this->service = $service;
    }
    public function getPrayerTimes(Request $request)
    {
        $request->validate([
            'method' => 'required|in:2,3,4,5',
        ]);
        /*
أم القرى (السعودية) → method = 4

الجمعية الإسلامية لأمريكا الشمالية (ISNA) → method = 2

رابطة العالم الإسلامي (Muslim World League) → method = 3

الهيئة العامة المصرية للمساحة (Egyptian General Authority of Survey) → method = 5
*/
        $user = Auth::user();
        $timings = $this->service->getPrayerTimes($user->latitude, $user->longitude, $request->method);

        if (! $timings) {
            return response()->json([
                'error' => 'لم نتمكّن من جلب أوقات الصلاة'
            ], 500);
        }

        return response()->json([
            'timings' => $timings
        ]);
    }

    public function getQiblaDirection(Request $request)
    {
        $user = Auth::user();
        return $this->service->getQiblaDirection($user->latitude, $user->longitude);
    }
}
