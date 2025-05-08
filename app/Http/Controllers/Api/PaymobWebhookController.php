<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\PaymobWebhookService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymobWebhookController extends Controller
{
    public function handle(Request $request)
    {
        // Log the raw request body and headers
        Log::info('RAW BODY', ['body' => $request->getContent()]);
        Log::info('ALL()', ['data' => $request->all()]);
        Log::info('JSON()', ['json' => $request->json()->all()]);
        Log::info('Headers', ['headers' => $request->headers->all()]);
        $service = new PaymobWebhookService($request->all());
   
        if (! $service->isValid()) {
            Log::info('WEBHOOK DEBUG', [
                'received_payload' => $request->all(),
            ]);
            return response()->json(['error' => 'Invalid HMAC'], 401);
        }

        // ✅ Valid webhook
        $orderId = $service->getOrderId();
        $amount  = $service->getAmount();
        $success = $service->getSuccessStatus();

        
        $order = Order::where('id', $orderId)->first();

        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }


        if ($success) {
            $order->transaction()->update([
                'payment_status' => 'completed',
                'status' => 'paid',
                'paid_at' => Carbon::now(),
            ]);
    
            $order()->update([
                'payment_status' => 'paid',
                'status' => 'confirmed', 
            ]);
        } else {
            $order->transaction()->update([
                'payment_status' => 'failed',
                'status' => 'cancelled',
            ]);

            $order->update([
                'payment_status' => 'failed',
                'status' => 'cancelled', 
            ]);
        }
        
        // هنا تبدأ تحديث حالة الطلب بناءً على $payload['order']['merchant_order_id'] أو ID تاني

        return response()->json(['message' => 'Webhook verified'], 200);
     
    }
}
