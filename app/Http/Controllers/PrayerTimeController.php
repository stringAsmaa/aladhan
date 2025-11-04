<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Services\PrayerTimeService;

class PrayerTimeController extends Controller
{
    public function __construct(protected PrayerTimeService $service)
    {
      $this->service=$service;
    }
    public function getPrayerTimes(Request $request)
    {

        $user = Auth::user();
        $timings = $this->service->getPrayerTimes($user->latitude, $user->longitude);

        if (! $timings) {
            return response()->json([
                'error' => 'لم نتمكّن من جلب أوقات الصلاة'
            ], 500);
        }

        return response()->json([
            'timings' => $timings
        ]);
    }
}
