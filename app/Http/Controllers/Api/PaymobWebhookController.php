<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\PaymentWebhookLog;
use App\Services\PaymobWebhookService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymobWebhookController extends Controller
{
    public function handle(Request $request)
    {

        // Log::info('RAW BODY', ['body' => $request->getContent()]);
        // Log::info('ALL()', ['data' => $request->all()]);
        // Log::info('JSON()', ['json' => $request->json()->all()]);
        // Log::info('Headers', ['headers' => $request->headers->all()]);
        $service = new PaymobWebhookService($request->all());

        if (! $service->isValid()) {
            Log::info('WEBHOOK DEBUG', [
                'received_payload' => $request->all(),
            ]);

            PaymentWebhookLog::create([
                'source' => 'paymob',
                'status' => 'invalid_hmac',
                'raw_payload' => json_encode($request->all()),
                'notes' => 'HMAC verification failed',
            ]);

            return response()->json(['error' => 'Invalid HMAC'], 401);
        }

        // ✅ Valid webhook
        $orderId = $service->getOrderId();
        $amount  = $service->getAmount();
        $success = $service->getSuccessStatus();


        $order = Order::where('paymob_order_id', $orderId)->first();

        if (! $order) {
            Log::error('Paymob Webhook: Order not found', ['paymob_order_id' => $orderId]);

            PaymentWebhookLog::create([
                'source' => 'paymob',
                'status' => 'order_not_found',
                'raw_payload' => json_encode($request->all()),
                'notes' => "Order with paymob_order_id={$orderId} not found",
            ]);

            return response()->json(['error' => 'Order not found'], 404);
        }

        if ($success) {
            DB::beginTransaction();
            $order->transaction()->update([
                'payment_status' => 'completed',
                'status' => 'paid',
                'paid_at' => Carbon::now(),
            ]);

            $order()->update([
                'payment_status' => 'paid',
                'status' => 'confirmed',
                'paymob_order_id' => $orderId,
            ]);

            PaymentWebhookLog::create([
                'source' => 'paymob',
                'status' => 'success',
                'raw_payload' => json_encode($request->all()),
                'notes' => "Order #{$order->id} updated successfully",
            ]);
            DB::commit();
        } else {
            $order->transaction()->update([
                'payment_status' => 'failed',
                'status' => 'cancelled',
                'paid_at' => null,
            ]);

            $order->update([
                'payment_status' => 'failed',
                'status' => 'cancelled',
                'paymob_order_id' => $orderId,
            ]);
        }
        // هنا تبدأ تحديث حالة الطلب بناءً على $payload['order']['merchant_order_id'] أو ID تاني

        return response()->json(['message' => 'Webhook verified'], 200);
    }
}
