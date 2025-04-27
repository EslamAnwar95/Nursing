<?php

namespace App\Http\Controllers\Api\Nurse;

use App\Http\Controllers\Controller;
use App\Models\Nurse;
use App\Models\NurseOrderDetail;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderNurseController extends Controller
{
    public function store(Request $request)
    {
        // dd(1);
        $request->validate([
            'nurse_id' => 'required|exists:nurses,id',
            'schedule_at' => 'required|date',
            'price' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:255',
            'patient_condition' => 'nullable|string|max:255',
            'visit_date' => 'required|date|after_or_equal:today',

            'visit_time' => 'nullable|in:morning,evening',
            'nurse_hours_id' => 'required|exists:nurse_hours,id',
            'address' => 'required|string|max:255',
            'city' => 'nullable|string|max:255',
            'area' => 'nullable|string|max:255',
            'street' => 'nullable|string|max:255',
            'building' => 'nullable|string|max:255',
            'floor' => 'nullable|string|max:255',
            'apartment' => 'nullable|string|max:255',
        ]);
        try {


            $nurse = Nurse::where('id', $request->nurse_id)->where('is_active', true)->first();

            if (! $nurse) {
                return response()->json([
                    'status' => false,
                    'message' => __('messages.nurse_not_available'),
                ], 422);
            }

            DB::beginTransaction();
            $order = Order::create([
                'patient_id' => auth('patient')->id(),
                'provider_id' => $request->nurse_id,
                'provider_type' => Nurse::class,
                'status' => 'pending',
                'scheduled_at' => $request->scheduled_at,
                'price' => $request->price,
                'notes' => $request->notes,
            ]);


            NurseOrderDetail::create([
                'order_id' => $order->id,
                'patient_condition' => $request->patient_condition,
                'visit_hours' => $request->visit_hours,
                'nurse_hours_id' => $request->nurse_hours_id,
                'address' => $request->address,
                'city' => $request->city,
                'area' => $request->area,
                'street' => $request->street,
                'building' => $request->building,
                'floor' => $request->floor,
                'apartment' => $request->apartment,
            ]);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => __('messages.order_created_successfully'),
                'data' => [
                    'order_id' => $order->id
                ],
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => __('messages.order_creation_failed'),
                'error' => $e->getMessage(),
            ], 500);
        }
        
    }

    public function show($id)
    {
        $order = Order::with('nurseOrderDetail', 'provider')
        ->where('patient_id', auth('patient')->id())
        ->find($id);

        if (!$order) {
            return response()->json([
                'status' => false,
                'message' => __('messages.order_not_found'),
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => __('messages.order_retrieved_successfully'),
            'data' => $order,
        ]);
    }
}
