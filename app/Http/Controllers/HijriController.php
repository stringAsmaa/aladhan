<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Services\HijriService;

class HijriController extends Controller
{
    protected HijriService $service;

    public function __construct(HijriService $service)
    {
        $this->service = $service;
    }

    /**
     * تحويل التاريخ الميلادي إلى هجري
     */
    public function convertToHijri(Request $request)
    {
  $now = Carbon::now();

 $date = $now->format('d-m-Y');
        $result = $this->service->gregorianToHijri($date);

        return response()->json($result);
    }

    /**
     * تقويم هجري لشهر معيّن
     */
    public function calendar(Request $request)
    {
  $now = Carbon::now();
        $result = $this->service->hijriCalendar( $now->month, $now->year);

        return response()->json($result);
    }
}
