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

        $service = new PaymobWebhookService($request->all());



        $payload = $request->all();
        dd($payload, $request->all(), $request->getContent());
        if (!($payload['success'] ?? false)) {
            Log::warning('Paymob: failed payment webhook', $payload);
            return response()->json(['status' => false], 200); // no retry
        }
        // ✅ Valid webhook
        // $orderId = $service->getOrderId();
        // $amount  = $service->getAmount();
        // $success = $service->getSuccessStatus();

        $orderId = $payload['order'] ?? null;

        if (!$orderId) {
            Log::warning('Paymob: missing order ID in webhook', $payload);
            return response()->json(['status' => false], 422);
        }


        $order = Order::where('paymob_order_id', $orderId)->first();

        if (!$order) {
            Log::error("Paymob: Order not found for ID {$orderId}");
            return response()->json(['status' => false], 404);
        }


        $order->update([
            'payment_status' => 'paid',
            'paid_at' => now(),
        ]);

        return response()->json(['status' => true], 200);
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
