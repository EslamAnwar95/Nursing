<?php

namespace App\Http\Controllers\Api\Patient;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class RatingController extends Controller
{



    public function rateOrder(Request $request, Order $order)
    {
        $patient = auth('patient')->user();

        if ($order->patient_id !== $patient->id ) {
            return response()->json(['message' => 'Cannot rate this order'], 403);
        }

        if ($order->rating) {
            return response()->json(['message' => 'Order already rated'], 400);
        }

        $request->validate([
            'rate' => 'required|integer|min:0|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $order->rating()->create([
            'patient_id' => $patient->id,
            'provider_id' => $order->provider_id,
            'provider_type' => $order->provider_type,
            'rate' => $request->rate,
            'comment' => $request->comment,
        ]);

        return response()->json([
            'status' => true,
            'message' => __('messages.rating_added_successfully'),
        ]);
    }
}
