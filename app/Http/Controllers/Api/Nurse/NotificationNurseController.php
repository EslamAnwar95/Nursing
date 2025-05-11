<?php

namespace App\Http\Controllers\Api\Nurse;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\Nurse\NotificationLogNurseResource;
class NotificationNurseController extends Controller
{
    



    public function getNorifications(Request $request)
    {
        $nurse = auth('nurse')->user();
        if (!$nurse) {
            return response()->json([
                'status' => false,
                'message' => __('messages.nurse_not_found'),
            ], 404);
        }
        $notifications = $nurse->notifications()->orderBy('created_at', 'desc')->paginate(10);

        return NotificationLogNurseResource::collection($notifications)->additional([
            'status' => true,
            'message' => __('messages.notifications_retrieved_successfully'),
        ]);
    }


    public function readNotification(Request $request, $id)
    {
        $nurse = auth('nurse')->user();
        if (!$nurse) {
            return response()->json([
                'status' => false,
                'message' => __('messages.nurse_not_found'),
            ], 404);
        }
        $notification = $nurse->notifications()->find($id);
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
        $nurse = auth('nurse')->user();
        if (!$nurse) {
            return response()->json([
                'status' => false,
                'message' => __('messages.nurse_not_found'),
            ], 404);
        }
        $notifications = $nurse->notifications()->where('is_read', 0)->get();
        foreach ($notifications as $notification) {
            $notification->update(['read_at' => now(),'is_read' => 1]);
        }

        return response()->json([
            'status' => true,
            'message' => __('messages.all_notifications_marked_as_read'),
        ]);
    }

}
