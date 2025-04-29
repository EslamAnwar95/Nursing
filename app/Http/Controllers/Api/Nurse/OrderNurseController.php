<?php

namespace App\Http\Controllers\Api\Nurse;

use App\Http\Controllers\Controller;
use App\Http\Resources\Nurse\OrderInfoNurseResource;
use App\Http\Resources\Nurse\StatusResource;
use App\Models\Nurse;
use App\Models\NurseOrderDetail;
use App\Models\Order;
use App\Models\Status;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderNurseController extends Controller
{


    public function update(Request $request, $id)
    {
        $request->validate([
            'status_id' => 'required|exists:statuses,id',
        ]);

        $order = Order::where('patient_id', auth('patient')->id())->find($id);

        if (!$order) {
            return response()->json([
                'status' => false,
                'message' => __('messages.order_not_found'),
            ], 404);
        }

        $order->update(['status_id' => $request->status_id]);

        return response()->json([
            'status' => true,
            'message' => __('messages.order_status_updated_successfully'),
        ]);
    }



    public function getStatuses(Request $request)
    {
        $statuses = Status::where('type', 'nurse')
            ->orderBy('order', 'asc')
            ->get();

        return response()->json([
            'status' => true,
            'message' => __('messages.order_statuses_retrieved_successfully'),
            'data' => StatusResource::collection($statuses),
        ]);
    }

    public function getOrders(Request $request)
    {
      
        $nurse = auth('nurse')->user();

        $orders = Order::with('patient', 'nurseOrderDetail','status','provider')
            ->where('provider_id', $nurse->id)
            ->where('provider_type', Nurse::class)
            ->orderBy('created_at', 'desc')
            ->paginate(10);


        
        // return response()->json([
        //     'status' => true,
        //     'message' => __('messages.orders_retrieved_successfully'),
        //     'data' => OrderInfoNurseResource::collection($orders),
        // ]);

        return OrderInfoNurseResource::collection($orders)
        ->additional([
            'status' => true,
            'message' => __('messages.orders_retrieved_successfully'),
        ]);
    }


    public function show($id)
    {
        $nurse = auth('nurse')->user();

        $order = Order::with('patient', 'nurseOrderDetail')
            ->where('provider_id', $nurse->id)
            ->where('provider_type', Nurse::class)
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
            'data' => new OrderInfoNurseResource($order),
        ]);
    }
    public function acceptOrder(Request $request, $id)
    {
        $nurse = auth('nurse')->user();

        $order = Order::where('provider_id', $nurse->id)
            ->where('provider_type', Nurse::class)
            ->find($id);

        if (!$order) {
            return response()->json([
                'status' => false,
                'message' => __('messages.order_not_found'),
            ], 404);
        }

        
        if ($order->status_id != 1) { 
            return response()->json([
                'status' => false,
                'message' => __('messages.order_already_accepted_or_rejected'),
            ], 400);
        }

        try {
            $order->update(['status_id' => 2]); // Assuming 2 is the ID for "Accepted" status

            return response()->json([
                'status' => true,
                'message' => __('messages.order_accepted_successfully'),
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'status' => false,
                'message' => __('messages.order_acceptance_failed'),
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function rejectOrder(Request $request, $id)
    {
        $nurse = auth('nurse')->user();

        $order = Order::where('provider_id', $nurse->id)
            ->where('provider_type', Nurse::class)
            ->find($id);

        if (!$order) {
            return response()->json([
                'status' => false,
                'message' => __('messages.order_not_found'),
            ], 404);
        }


        try {
            $order->update(['status_id' => 3]); // Assuming 3 is the ID for "Rejected" status
           return response()->json([
                'status' => true,
                'message' => __('messages.order_rejected_successfully'),
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'status' => false,
                'message' => __('messages.order_rejection_failed'),
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
