<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function toggleFavorite(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:zekr_categories,id',
        ]);

        $user = auth()->user();
        $categoryId = $request->category_id;

        // فحص إذا كانت الفئة مضافة مسبقًا
        if ($user->favoriteAzkar()->where('zekr_category_id', $categoryId)->exists()) {
            $user->favoriteAzkar()->detach($categoryId);
            return response()->json([
                'success' => true,
                'message' => 'تمت إزالة الفئة من المفضلة',
            ]);
        } else {
            $user->favoriteAzkar()->attach($categoryId);
            return response()->json([
                'success' => true,
                'message' => 'تمت إضافة الفئة إلى المفضلة',
            ]);
        }
    }

    public function getFavorites()
    {
        $user = auth()->user();
        $favorites = $user->favoriteAzkar()->select('zekr_categories.id', 'zekr_categories.name')->get();

        return response()->json([
            'success' => true,
            'data' => $favorites
        ]);
    }
}
