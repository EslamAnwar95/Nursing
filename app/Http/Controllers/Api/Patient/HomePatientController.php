<?php

namespace App\Http\Controllers\Api\Patient;

use App\Http\Controllers\Controller;
use App\Http\Resources\Patient\HomePatientResource;
use App\Models\Nurse;
use Illuminate\Http\Request;

class HomePatientController extends Controller
{
    
    // public function __construct()
    // {
    //     $this->middleware('auth:patient');
    // }

 
public function home(Request $request)
{
    $patient = $request->user();
    $lat = $patient->lat;
    $lng = $patient->lng;
    $radius = $request->radius ?? 10;
    $type = $request->type ?? 'nurse';
    // $nurses = Nurse::nearby($lat, $lng, $radius)->paginate(15);

    switch ($type) {
        case 'nurse':
            return \App\Models\Nurse::nearby($lat, $lng, $radius)->paginate(15);

        case 'hospital':
            // return \App\Models\Hospital::nearby($lat, $lng, $radius)->paginate($perPage);
            return [];

        default:
            throw new \InvalidArgumentException("Unsupported type: {$type}");
    }

    return HomePatientResource::collection($nurses)->additional([
        'status' => true,
        'message' => 'Nearby nurses loaded successfully',
    ]);
}
    
}
