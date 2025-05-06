<?php
namespace App\Payments;

use App\Interfaces\PaymentStrategyInterface;

use App\Interfaces\PaymentDataInterface;
use App\Models\OrderTransaction;
use App\Traits\OrderTrait;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CashPaymentStrategy implements PaymentStrategyInterface
{

    public function pay(PaymentDataInterface $data): string
    {        
        DB::beginTransaction();

        try {
            if ($data->getType() === 'order') {

                $order = $data->order;

                $order->update([
                    'payment_status' => 'paid',
                    'status' => 'confirmed', 
                ]);

                $order->transaction->update([
                    'order_id' => $order->id,
                    'provider_id' => $order->provider_id,
                    'provider_type' => $order->provider_type,
                    'payment_method' => 'cash',
                    'payment_status' => 'completed',                   
                    'status' => 'paid',
                    'paid_at' => Carbon::now(),
                    'currency' => 'EGP',
                    'type' => 'order',
                ]);
            }

            DB::commit();

            return "Cash payment recorded for {$data->getType()} [ref: {$data->getReferenceId()}] successfully.";
        } catch (\Throwable $e) {
            DB::rollBack();
            throw new \Exception("Cash payment failed: " . $e->getMessage());
        }

    }

   
}