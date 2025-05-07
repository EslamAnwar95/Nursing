<?php

namespace App\Http\Controllers\Api\Patient;

use App\Http\Controllers\Controller;
use App\Http\Resources\Patient\ReservationResource;
use App\Models\Order;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    public function index(Request $request)
    {
        $patient = auth('patient')->user();

        // reservations paginated
        $reservations = Order::where('patient_id', $patient->id)
            ->with(['provider','order_status']);
            if($request->has('status')) {
                $reservations->where('status', $request->status);
            }
            // if($request->has('scheduled_at')) {
            //     $reservations->whereDate('scheduled_at', $request->scheduled_at);
            // }
          $reservations  ->orderBy('created_at', 'desc');
          $reservations = $reservations  ->paginate(10);



            return ReservationResource::collection($reservations)->additional([
                'status' => true,
                'message' => __('messages.reservations_list'),
            ]);
    }
}
