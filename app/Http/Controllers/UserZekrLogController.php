<?php

namespace App\Http\Controllers;

use App\Models\UserZekrLog;
use Illuminate\Http\Request;

class UserZekrLogController extends Controller
{
    public function markZekrAsRead(Request $request)
{
    $request->validate([
        'zekr_category_id' => 'required|exists:zekr_categories,id',
    ]);

    $userId = auth()->id();
    $today = now()->toDateString();
    $exists = UserZekrLog::where('user_id', $userId)
        ->where('zekr_category_id', $request->zekr_category_id)
        ->whereDate('date', $today)
        ->exists();

    if ($exists) {
        return response()->json([
            'success' => false,
            'message' => 'لقد سجلت قراءة هذا الذكر اليوم بالفعل',
        ], 400);
    }

    UserZekrLog::create([
        'user_id' => $userId,
        'zekr_category_id' => $request->zekr_category_id,
        'date' => $today,
    ]);

    return response()->json([
        'success' => true,
        'message' => 'تم تسجيل قراءة الذكر اليوم',
    ]);
}

public function get_staticsit_zekr(){
    $userId = auth()->id();
$weekStart = now()->startOfWeek();
$weekEnd = now()->endOfWeek();
$logs = UserZekrLog::where('user_id', $userId)
    ->whereBetween('date', [$weekStart, $weekEnd])
    ->with('zekrCategory')
    ->get()
    ->groupBy('zekr_category_id');
    UserZekrLog::where('user_id', $userId)
        ->where('date', '<', $weekStart)
        ->delete();
$stats = $logs->map(function ($logsPerCategory) {
    return [
        'zekr_name' => $logsPerCategory->first()->zekrCategory->name,
        'days_read' => $logsPerCategory->pluck('date')->map(fn($d) => $d->format('Y-m-d'))->unique(),
        'count' => $logsPerCategory->count(),
    ];
});

return response()->json([
    'success' => true,
    'weekly_statistics' => $stats,
]);


}
}
