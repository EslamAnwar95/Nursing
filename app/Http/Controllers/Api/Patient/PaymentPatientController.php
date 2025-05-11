<?php

namespace App\Http\Controllers\Api\Patient;

use App\Http\Controllers\Controller;
use App\Interfaces\PaymentDataInterface;
use App\Interfaces\PaymentStrategyInterface;
use App\Models\Order;
use App\Payments\CashPaymentStrategy;
use App\Payments\OrderPaymentData;
use App\Payments\PaymentContext;
use App\Payments\PaymobPaymentStrategy;
use App\Payments\SettlementPaymentData;
use Illuminate\Http\Request;
use App\Payments\WalletPaymentStrategy;

class PaymentPatientController extends Controller
{

    protected PaymentContext $paymentContext;

    public function __construct(PaymentContext $paymentContext)
    {
        $this->paymentContext = $paymentContext;
    }
   

    public function pay(Request $request)
    {
        $request->validate([
            'type' => 'required|in:order,settlement',
            'payment_method' => 'required|in:cash,credit,wallet',
            'order_id' => 'required_if:type,order|exists:orders,id',
            'amount' => 'required_if:type,settlement|numeric|min:0.01',
        ]);


        $paymentData = $this->resolvePaymentData($request);

        if($paymentData->getType() === 'order') {
            $order = Order::find($request->order_id);
         

            if ($order->order_status->id !== 5) {
                return response()->json([
                    'status' => false,
                    'message' => __('messages.order_not_completed'),
                ], 422);
            }
        }

        if (! $paymentData instanceof PaymentDataInterface) {
            return response()->json([
                'status' => false,
                'message' => __('messages.invalid_payment_data'),
            ], 422);
        }


        $strategy = match ($request->payment_method) {
            'cash' => new CashPaymentStrategy(),
            'credit' => new PaymobPaymentStrategy(),
            'wallet' => new WalletPaymentStrategy(),
            default => null
        };


        try {
            $this->paymentContext->setStrategy($strategy);
            $result = $this->paymentContext->process($paymentData);

            return response()->json([
                'status' => $result->success,
                'message' => $result->message,
                'data' => $result->data,
            ], $result->status);

            
        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => __('messages.payment_failed'),
                'error' => $e->getMessage(),
            ], 500);
        }                          
        
        return response()->json([
            'status' => true,
            'message' => __('messages.payment_successful'),
        ]);
    }


    private function resolvePaymentData(Request $request): ?PaymentDataInterface
    {

        $provider = $request->user();

        return match ($request->type) {
            'order' => new OrderPaymentData(Order::findOrFail($request->order_id)),
            // 'settlement' => new SettlementPaymentData(
            //     amount: $request->amount,
            //     providerId: $provider->id,
            //     providerType: get_class($provider)
            // ),
            default => null
        };
    }
}
