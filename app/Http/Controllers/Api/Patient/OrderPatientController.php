<?php

namespace App\Http\Controllers\Api\Patient;

use App\Http\Controllers\Controller;
use App\Http\Resources\Nurse\StatusResource;
use App\Http\Resources\Nurse\WorkHourseNurseResource;
use App\Models\Nurse;
use App\Models\NurseHours;
use App\Models\NurseOrderDetail;
use App\Models\Order;
use App\Models\OrderTransaction;
use App\Models\Status;
use App\Traits\OrderTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class OrderPatientController extends Controller
{
    use OrderTrait;

    public function store(Request $request)
    {
        $request->validate([
            'nurse_id' => 'required|exists:nurses,id',
            'scheduled_at' => 'required|date',
            // 'price' => 'required|numeric|min:0',
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
            'payment_method' => 'required|in:cash,credit_card',
        ]);
        try {

            $nurse = Nurse::where('id', $request->nurse_id)->where('is_active', true)->first();

            if (! $nurse) {
                return response()->json([
                    'status' => false,
                    'message' => __('messages.nurse_not_available'),
                ], 422);
            }   

            $priceModel = NurseHours::where('id', $request->nurse_hours_id)
                ->where('nurse_id', $request->nurse_id)
                ->where('status', 'active')
                ->first();

            if (!$priceModel) {
                return response()->json([
                    'status' => false,
                    'message' => __('messages.nurse_hours_not_available'),
                ], 422);
            }

            DB::beginTransaction();
            $order = Order::create([
                'patient_id' => auth('patient')->id(),
                'provider_id' => $request->nurse_id,
                'provider_type' => Nurse::class,
                'status_id' => '1',
                'scheduled_at' => $request->scheduled_at,
                'price' => $priceModel->price,
                "payment_status" => 'pending',
                'status' => 'pending',
                'notes' => $request->notes,
            ]);

             // 📤 ابعت Socket Trigger
            Http::post('http://localhost:3000/order-created', [
                'order_id' => $order->id,
                'patient_id' => $order->patient_id,
                'provider_id' => $order->provider_id,
                'provider_type' => $order->provider_type,
            ]);

            NurseOrderDetail::create([
                'order_id' => $order->id,
                'patient_condition' => $request->patient_condition,
                // 'visit_hours' => $request->visit_hours,
                'nurse_hours_id' => $request->nurse_hours_id,
                'address' => $request->address,
                'city' => $request->city,
                'area' => $request->area,
                'street' => $request->street,
                'building' => $request->building,
                'floor' => $request->floor,
                'apartment' => $request->apartment,
            ]);

            $prices = $this->calculateTotalPrice($order);

            OrderTransaction::create([
                'order_id' => $order->id,               
                'provider_id' => $request->nurse_id,
                'provider_type' => Nurse::class,
                'total_price' => $priceModel->price,
                'vat_value' => $prices['vat_value'],
                'app_fee' => $prices['app_fee'],
                'provider_earning' => $prices['provider_earning'],
                'payment_method' => $request->payment_method,
                "payment_status" => 'pending',
                'status' => 'pending',
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
   

    public function getNurseWorkHours($id)
    {
        
        $nurse = Nurse::where('id', $id)->where('is_active', true)->first();

        if (! $nurse) {
            return response()->json([
                'status' => false,
                'message' => __('messages.nurse_not_available'),
            ], 422);
        }   

        $nurseHours = NurseHours::where('nurse_id', $id)
            ->where('status', 'active')
            ->get();

        if ($nurseHours->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => __('messages.nurse_hours_not_available'),
            ], 422);
        }

        return response()->json([
            'status' => true,
            'message' => __('messages.nurse_hours_retrieved_successfully'),
            'data' => WorkHourseNurseResource::collection($nurseHours),
        ]);
    }
   

   
}


