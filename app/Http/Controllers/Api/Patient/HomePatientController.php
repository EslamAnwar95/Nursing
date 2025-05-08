<?php

namespace App\Http\Controllers\Api\Patient;

use App\Http\Controllers\Controller;
use App\Http\Resources\Patient\HomePatientMostCommonResource;
use App\Http\Resources\Patient\HomePatientResource;
use App\Models\Favorite;
use App\Models\Nurse;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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


    public function getMostCommonFavoriteProviders(Request $request)
    {
        // $patient = $request->user();

        $mostCommonFavorite = Favorite::select('provider_id','provider_type',DB::raw('count(*) as total'))
            // ->where('provider_type', Nurse::class)
            ->groupBy('provider_id','provider_type')
            ->orderBy('total', 'desc')
            ->with(['provider'])
            ->limit(5)
            ->get();

            // dd($mostCommonFavorite);
        if ($mostCommonFavorite->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => __('messages.no_favorites_found'),
                'data' => [],
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => __('messages.favorites_loaded_successfully'),
            'data' => HomePatientMostCommonResource::collection($mostCommonFavorite),
        ]);
    }

    public function cancelReservation(Request $request)
    {
        $patient = auth('patient')->user();

        $request->validate([
            'order_id' => 'required|exists:orders,id',
        ]);

        $order = Order::find($request->order_id);

        if ($order->patient_id != $patient->id) {
            return response()->json([
                'status' => false,
                'message' => __('messages.not_your_order'),
            ], 422);
        }
        if ($order->status_id != 1) {
            return response()->json([
                'status' => false,
                'message' => __('messages.order_not_pending'),
            ], 422);
        }

        $order->update(['status_id' => 6]);

        return response()->json([
            'status' => true,
            'message' => __('messages.order_cancelled_successfully'),
        ]);
    }


    public function rescheduleReservation(Request $request)
    {
        $patient = auth('patient')->user();

        $request->validate([
            'order_id' => 'required|exists:orders,id',
            "scheduled_at" => "required|date|after:today",
        ]);

        $order = Order::find($request->order_id);

        if ($order->patient_id != $patient->id) {
            return response()->json([
                'status' => false,
                'message' => __('messages.not_your_order'),
            ], 422);
        }

        if ($order->status_id == 6) {
            return response()->json([
                'status' => false,
                'message' => __('messages.order_already_cancelled'),
            ], 422);
        }

        $order->update(['status_id' => 1, 'scheduled_at' => $request->scheduled_at]);

        return response()->json([
            'status' => true,
            'message' => __('messages.order_rescheduled_successfully'),
        ]);
    }
}
