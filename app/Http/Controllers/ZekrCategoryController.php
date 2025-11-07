<?php

namespace App\Http\Controllers;

use App\Models\ZekrCategory;
use App\Http\Resources\failResource;
use App\Http\Resources\successResource;

class ZekrCategoryController extends Controller
{
    public function getCategories()
    {
        $categories = ZekrCategory::all(['id', 'name']);

        if ($categories->isEmpty()) {
            return response()->json(new failResource('لا توجد فئات أذكار'));
        }

        return response()->json(new successResource($categories));
    }

}
