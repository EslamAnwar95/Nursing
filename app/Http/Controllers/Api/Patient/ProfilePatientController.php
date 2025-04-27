<?php

namespace App\Http\Controllers\Api\Patient;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfilePatientController extends Controller
{
    
    public function __construct()
    {
        $this->middleware('auth:patient');
    }

    public function profile(Request $request)
    {
        $patient = $request->user();
        return response()->json([
            'status' => true,
            'message' => __('messages.patient_profile_retrieved_successfully'),
            'data' => new \App\Http\Resources\Patient\PatientInfoResource($patient),
        ]);
    }

    public function updateLocation(Request $request)
    {
     
        $request->validate([
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'address' => 'nullable|string|max:255',
        ]);

        $patient = $request->user();
        $patient->update([
            'lat' => $request->lat,
            'lng' => $request->lng,
            'address' => $request->address,
        ]);

        return response()->json([
            'status' => true,
            'message' => __('messages.patient_location_updated_successfully'),
        ]);
    }
}
