<?php

namespace App\Http\Controllers\Api\Nurse;

use App\Http\Controllers\Controller;
use App\Http\Resources\Nurse\OrderInfoNurseResource;
use App\Http\Resources\Nurse\StatusResource;
use App\Models\Nurse;
use App\Models\NurseOrderDetail;
use App\Models\Order;
use App\Models\Status;
use App\Services\FirebaseService;
use App\Services\NotificationDispatcherService;
use Illuminate\Http\JsonResponse;
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



    public function getStatuses(): JsonResponse
    {
        $statuses = Status::orderBy('order')->whereType('nurse')->get();

        return response()->json([
            'status' => true,
            'message' => __('messages.order_statuses_retrieved_successfully'),
            'data' => StatusResource::collection($statuses),
        ]);
    }

    public function getOrders(Request $request)
    {

        $nurse = auth('nurse')->user();

        $orders = Order::with('patient', 'nurseOrderDetail', 'status', 'provider')
            ->where('provider_id', $nurse->id)
            ->where('provider_type', Nurse::class)
            ->orderBy('created_at', 'desc')
            ->paginate(10);


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

        $pendingStatus = Status::where('type', 'nurse')->where('order', '1')->value('id');

        if ($order->status_id != $pendingStatus) {
            return response()->json([
                'status' => false,
                'message' => __('messages.order_already_accepted_or_rejected'),
            ], 400);
        }

        $acceptedStatus = Status::where('type', 'nurse')->where('order', '2')->value('id');

        try {
            $order->update(['status_id' => $acceptedStatus]); 

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


        $pendingStatus = Status::where('type', 'nurse')->where('order', '1')->value('id');

        if ($order->status_id != $pendingStatus) {
            return response()->json([
                'status' => false,
                'message' => __('messages.order_already_accepted_or_rejected'),
            ], 400);
        }
        
        // get just the id 
        $rejectedStatus = Status::where('type', 'nurse')->where('order', '0')->value('id');
       
        try {
            $order->update(['status_id' => $rejectedStatus]); 
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


    public function updateStatus(Request $request, $id, NotificationDispatcherService $dispatcher)
    {
        $request->validate([
            'status_id' => 'required|exists:statuses,id',
        ]);

        // Check if the status id is valid for nurse     
        if (!Status::where('id', $request->status_id)->where('type', 'nurse')->exists()) {
            return response()->json([
                'status' => false,
                'message' => __('messages.invalid_status_for_nurse'),
            ], 422);
        }

        try {


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
           
            // check if the status id not the same as the current order status id
            if ($order->status_id == $request->status_id) {
                return response()->json([
                    'status' => false,
                    'message' => __('messages.order_status_already_same_as_current'),
                ], 422);
            }

            $order->update(['status_id' => $request->status_id]);



            // Send notification to the patient

            $patient = $order->patient;

            

            if ($patient) {

                $token = $patient->deviceTokens()->latest()->first();

                if (!$token) {
                    return response()->json([
                        'status' => false,
                        'message' => __('messages.patient_firebase_token_not_found'),
                    ], 404);
                }

              
             
                $dispatcher->sendToUser(
                    $patient,
                    __('Order Status Update'),
                    __('Hello, your order status has been updated to: ') . $order->status->name_en,
                    
                    [
                        'order_id' => $order->id,
                        'type' => 'order_status',
                        
                    ]
                );
                
            }

            return response()->json([
                'status' => true,
                'message' => __('messages.order_status_updated_successfully'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => __('messages.order_status_update_failed'),
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
