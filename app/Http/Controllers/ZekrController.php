<?php

namespace App\Http\Controllers;

use App\Models\ZekrCategory;
use Illuminate\Http\Request;
use App\Http\Resources\failResource;
use App\Http\Resources\successResource;

class ZekrController extends Controller
{
       public function getAzkarByCategory(Request $request)
    {
        $request->validate([
            'category_id' => 'required|integer|exists:zekr_categories,id',
        ]);

        $category = ZekrCategory::find($request->category_id);

        $azkar = $category->azkar()->get(['id', 'content', 'repetition']);

        if ($azkar->isEmpty()) {
            return response()->json(new failResource('لا توجد أدعية لهذه الفئة'));
        }

        return response()->json(new successResource([
            'category' => ['id' => $category->id, 'name' => $category->name],
            'azkar' => $azkar
        ]));
    }
}
