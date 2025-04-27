<?php

namespace App\Http\Controllers\Api\Nurse;

use App\Http\Controllers\Controller;
use App\Models\Nurse;
use App\Models\NurseHours;
use Illuminate\Http\Request;

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
        // dd($request->all());
        $request->validate([
            "hours" => "required|integer|between:0,23",
            "price" => "required|numeric|min:0",
            'day'=> 'nullable|in:saturday,sunday,monday,tuesday,wednesday,thursday,friday',
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
            'day'=> 'nullable|in:saturday,sunday,monday,tuesday,wednesday,thursday,friday',
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
}
