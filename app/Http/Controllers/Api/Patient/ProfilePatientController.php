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
            'message' => 'Patient profile retrieved successfully',
            'data' => new \App\Http\Resources\Patient\PatientInfoResource($patient),
        ]);
    }

    public function updateLocation(Request $request)
    {
        $request->validate([
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
        ]);

        $patient = $request->user();
        $patient->update([
            'lat' => $request->lat,
            'lng' => $request->lng,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Patient location updated successfully',
        ]);
    }
}
