<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\UserZekrLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Services\UserZekrLogService;

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

public function get_category_read(){
    $id=Auth::id();
    return  UserZekrLog::where('user_id',$id)->where('date',Carbon::today())->get();

}

}
