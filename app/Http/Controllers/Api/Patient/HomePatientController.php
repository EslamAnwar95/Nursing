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
        //validation
        $request->validate([
            'name' => 'string|nullable',
            'radius' => 'numeric|nullable',
            'type' => 'string|in:nurse,hospital,blood_bank,lab,ray|nullable',
            'per_page' => 'integer|nullable',
        ]);


        $patient = $request->user();
        $search = $request['name'] ?? '';
        $lat = $patient->lat;
        $lng = $patient->lng;
        $radius = $request->radius ?? 10;
        $type = $request->type ?? 'nurse';
        $perPage = $request->per_page ?? 15;

        if (empty($lat) || empty($lng)) {
            return response()->json([
                'status' => false,
                'message' => __('messages.location_not_set'),
                'data' => [],
            ]);
        }

        $results = $this->handleNearbySearch($type, $search, $lat, $lng, $radius, $perPage);

        if (empty($results)) {
            return response()->json([
                'status' => false,
                'message' => __('messages.no_results_found_for_type', ['type' => $type]),
                'data' => [],
            ]);
        }

        return HomePatientResource::collection($results)->additional([
            'status' => true,
            'message' => __('messages.nearby_type_loaded_successfully', ['type' => $type]),
        ]);
    }

    private function handleNearbySearch(string $type, string $search, float $lat, float $lng, int $radius, int $perPage = 15)
    {
        switch ($type) {
            case 'nurse':

                $result =  \App\Models\Nurse::query();

                $result->where('is_available', 1);

                if ($search !== '') {
                    $result->where('full_name', 'like', "{$search}%");
                }


                $result =  $result->nearby($lat, $lng, $radius)->paginate($perPage);

                return $result;
            case 'hospital':
                // return \App\Models\Hospital::nearby($lat, $lng, $radius)->paginate($perPage);

                return [];

            default:
                return [];
                throw new \InvalidArgumentException("Unsupported type: {$type}");
        }
    }
}
