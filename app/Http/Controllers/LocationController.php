<?php

namespace App\Http\Controllers;

use App\Http\Services\LocationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class LocationController extends Controller
{
    public function __construct(protected LocationService $service){
    $this->service=$service;
    }
    public function getCity()
    {
     $user=Auth::user();
 return  $this->service->getCity( $user->latitude, $user->longitude);

    }
    public function nearestMosque(Request $request)
{

     $user=Auth::user();

   return$this->service->findNearestMosque( $user->latitude, $user->longitude);


}

}
