<?php

namespace App\Http\Controllers\Api\Patient;

use App\Http\Controllers\Controller;
use App\Http\Resources\Patient\NotificationLogPatientResource;
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


    public function getNorifications(Request $request)
    {
        $patient = auth('patient')->user();
        if (!$patient) {
            return response()->json([
                'status' => false,
                'message' => __('messages.patient_not_found'),
            ], 404);
        }
        $notifications = $patient->notifications()->orderBy('created_at', 'desc')->paginate(10);

        return NotificationLogPatientResource::collection($notifications)->additional([
            'status' => true,
            'message' => __('messages.notifications_retrieved_successfully'),
        ]);
    }


    public function readNotification(Request $request, $id)
    {
        $patient = auth('patient')->user();
        if (!$patient) {
            return response()->json([
                'status' => false,
                'message' => __('messages.patient_not_found'),
            ], 404);
        }
        $notification = $patient->notifications()->find($id);
        if (!$notification) {
            return response()->json([
                'status' => false,
                'message' => __('messages.notification_not_found'),
            ], 404);
        }
        
        $notification->update(['read_at' => now(),'is_read' => 1]);

        return response()->json([
            'status' => true,
            'message' => __('messages.notification_read_successfully'),
        ]);
    }


    public function markAllNotificationsAsRead(Request $request)
    {
        $patient = auth('patient')->user();
        if (!$patient) {
            return response()->json([
                'status' => false,
                'message' => __('messages.patient_not_found'),
            ], 404);
        }
        $notifications = $patient->notifications()->where('is_read', 0)->get();
        foreach ($notifications as $notification) {
            $notification->update(['read_at' => now(),'is_read' => 1]);
        }

        return response()->json([
            'status' => true,
            'message' => __('messages.all_notifications_marked_as_read'),
        ]);
    }


}
