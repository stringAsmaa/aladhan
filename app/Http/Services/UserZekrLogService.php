<?php
namespace App\Http\Services;

use App\Models\UserZekrLog;
use App\Models\ZekrCategory;


class UserZekrLogService{
  public function markZekrAsRead( $request)
{

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

    // حذف السجلات القديمة
    UserZekrLog::where('user_id', $userId)
        ->where('date', '<', $weekStart)
        ->delete();

    // جلب السجلات
    $logs = UserZekrLog::where('user_id', $userId)
        ->whereBetween('date', [$weekStart, $weekEnd])
        ->with('zekrCategory')
        ->get()
        ->groupBy('zekr_category_id');

    // تجهيز الإحصائيات
    $stats = $logs->map(function ($logsPerCategory) {
        return [
            'zekr_name' => $logsPerCategory->first()->zekrCategory->name,
            'days_read' => $logsPerCategory->pluck('date')->map(fn($d) => \Carbon\Carbon::parse($d)->format('Y-m-d'))->unique(),
            'count' => $logsPerCategory->count(),
        ];
    });

    // حساب نسبة الالتزام الكلية
    $totalCategories = ZekrCategory::count();
    $totalReadings = $logs->flatten()->count(); // عدد القراءات خلال الأسبوع
    $expectedTotal = $totalCategories * 7; // المتوقع خلال الأسبوع

    $percentage = $expectedTotal > 0 ? ($totalReadings / $expectedTotal) * 100 : 0;

    // تحديد الحالة
    if ($percentage >= 70) {
        $status = 'قارئ مواظب';
    } elseif ($percentage >= 40) {
        $status = 'قارئ متوسط';
    } else {
        $status = 'قارئ متراجع';
    }

    return response()->json([
        'success' => true,
        'weekly_statistics' => $stats,
        'overall_percentage' => round($percentage, 1) . '%',
        'status' => $status,
    ]);
}

}
