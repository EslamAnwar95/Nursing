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
        $perPage = $request->per_page ?? 15;
        $results = $this->handleNearbySearch($type, $lat, $lng, $radius, $perPage);
    
        if (empty($results)) {
            return response()->json([
                'status' => false,
                'message' => "No results found for type: {$type}",
                'data' => [],
            ]);
        }
    
        return HomePatientResource::collection($results)->additional([
            'status' => true,
            'message' => "Nearby {$type}s loaded successfully",
        ]);
    }

    private function handleNearbySearch(string $type, float $lat, float $lng, int $radius, int $perPage = 15)
    {
        switch ($type) {
            case 'nurse':
                return \App\Models\Nurse::nearby($lat, $lng, $radius)->paginate($perPage);

            case 'hospital':
                // return \App\Models\Hospital::nearby($lat, $lng, $radius)->paginate($perPage);

                return [];

                default:
                throw new \InvalidArgumentException("Unsupported type: {$type}");
        }
    }

    
}
