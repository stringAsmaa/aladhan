<?php

namespace App\Http\Controllers;

use App\Http\Services\UserZekrLogService;
use Illuminate\Http\Request;

class UserZekrLogController extends Controller
{
    public function __construct(protected UserZekrLogService $service)
    {
          $this->service=$service;
    }
    public function markZekrAsRead(Request $request)
{
    $request->validate([
        'zekr_category_id' => 'required|exists:zekr_categories,id',
    ]);
   return  $this->service->markZekrAsRead($request);

}

public function get_staticsit_zekr()
{
   return  $this->service->get_staticsit_zekr();

}

}
