<?php

namespace App\Http\Controllers\Api\Nurse;

use App\Http\Controllers\Controller;
use App\Http\Resources\Nurse\TransactionLogsNurseResource;
use App\Http\Resources\Nurse\WorkHourseNurseResource;
use App\Models\Nurse;
use App\Models\NurseHours;
use App\Models\Order;
use App\Models\OrderTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Kreait\Firebase\Database\Transaction;

class ProfileNurseController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:nurse');
    }

    public function profile(Request $request)
    {
        $nurse = $request->user();
        return response()->json([
            'status' => true,
            'message' => __('messages.nurse_profile_retrieved_successfully'),
            'data' => new \App\Http\Resources\Nurse\NurseInfoResource($nurse),
        ]);
    }

    public function updateLocation(Request $request)
    {

        $request->validate([
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'address' => 'nullable|string|max:255',
        ]);

        $nurse = $request->user();
        $nurse->update([
            'lat' => $request->lat,
            'lng' => $request->lng,
            'address' => $request->address,
        ]);

        return response()->json([
            'status' => true,
            'message' => __('messages.nurse_location_updated_successfully'),
        ]);
    }

    public function addWorkHour(Request $request)
    {
        $request->validate([
            "hours" => "required|integer|between:0,23",
            "price" => "required|numeric|min:0",
            'day' => 'nullable|in:saturday,sunday,monday,tuesday,wednesday,thursday,friday',
            'additional_hours' => 'nullable|integer',
            'additional_price' => 'nullable|numeric',
            'time' => 'nullable|in:morning,evening',
            'status' => 'nullable|in:active,inactive',
        ]);
        $nurse = $request->user();

        $workHour = NurseHours::create([
            'nurse_id' => $nurse->id,
            'hours' => $request->hours,
            'price' => $request->price,
            'day' => $request->day,
            'additional_hours' => $request->additional_hours,
            'additional_price' => $request->additional_price,
            'time' => $request->time,
            'status' => $request->status,
        ]);


        return response()->json([
            'status' => true,
            'message' => __('messages.work_hour_added_successfully'),
            'data' => $workHour,
        ]);
    }

    public function updateWorkHour(Request $request, $id)
    {

        $request->validate([
            "hours" => "required|integer|between:0,23",
            "price" => "required|numeric|min:0",
            'day' => 'nullable|in:saturday,sunday,monday,tuesday,wednesday,thursday,friday',
            'additional_hours' => 'nullable|integer',
            'additional_price' => 'nullable|numeric',
            'time' => 'nullable|in:morning,evening',
            'status' => 'nullable|in:active,inactive',
        ]);
        $nurse = $request->user();

        $workHour = NurseHours::where('id', $id)->where('nurse_id', $nurse->id)->firstOrFail();
        $workHour->update([
            'hours' => $request->hours,
            'price' => $request->price,
            'day' => $request->day,
            'additional_hours' => $request->additional_hours,
            'additional_price' => $request->additional_price,
            'time' => $request->time,
            'status' => $request->status,
        ]);
        return response()->json([
            'status' => true,
            'message' => __('messages.work_hour_updated_successfully'),
            'data' => $workHour,
        ]);
    }

    // get nurse work hours

    public function getWorkHours(Request $request)
    {
        $nurse = $request->user();
        $workHours = NurseHours::where('nurse_id', $nurse->id)->get();

        return response()->json([
            'status' => true,
            'message' => __('messages.work_hours_retrieved_successfully'),
            'data' => WorkHourseNurseResource::collection($workHours),
        ]);
    }


    public function deleteWorkHour(Request $request, $id)
    {
        $nurse = $request->user();
        $workHour = NurseHours::where('id', $id)->where('nurse_id', $nurse->id)->first();

        if (!$workHour) {
            return response()->json([
                'status' => false,
                'message' => __('messages.work_hour_not_found'),
            ], 404);
        }
        // Check if the work hour is already assigned to an order
        $order = Order::where('nurse_hours_id', $workHour->id)->first();
        if ($order) {
            return response()->json([
                'status' => false,
                'message' => __('messages.work_hour_assigned_to_order'),
            ], 422);
        }

        $workHour->delete();

        return response()->json([
            'status' => true,
            'message' => __('messages.work_hour_deleted_successfully'),
        ]);
    }


    public function transactionsLogs()
    {
        $nurse = auth('nurse')->user();

        // Get the transactions for the nurse
        $transactions = OrderTransaction::with('provider')->where('provider_id', $nurse->id)
            ->where('provider_type', Nurse::class)
            ->where('payment_method', 'cash')
            // ->where('payment_status', 'completed')
            ->where('settlement_status', 'pending')
            ->orderBy('created_at', 'desc')
            ->paginate(10);


        // Get Nurse balance provider total_price in credit_card - ( provider app_fee + vat )

        $creditEarnings = DB::table('order_transactions')
            ->where('provider_id', $nurse->id)
            ->where('provider_type', Nurse::class)
            ->where('payment_method', 'credit')
            ->where('payment_status', 'pending')
            ->where('settlement_status', 'pending')
            ->sum('provider_earning');

        $cashCosts = DB::table('order_transactions')
            ->where('provider_id', $nurse->id)
            ->where('provider_type', Nurse::class)
            ->where('payment_method', 'cash')
            ->where('payment_status', 'pending')
            ->where('settlement_status', 'pending')
            ->selectRaw('SUM(app_fee + vat_value) as total')
            ->value('total');

            $balance = ($creditEarnings ?? 0) - ($cashCosts ?? 0);
               
        return TransactionLogsNurseResource::collection($transactions)
            ->additional([
                'status' => true,
                'message' => __('messages.transactions_retrieved_successfully'),
                'balance' => $balance,
            ]);
    }
}
