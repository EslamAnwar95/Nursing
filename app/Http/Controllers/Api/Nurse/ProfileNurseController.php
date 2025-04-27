<?php

namespace App\Http\Controllers\Api\Nurse;

use App\Http\Controllers\Controller;
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
}
