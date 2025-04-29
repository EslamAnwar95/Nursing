<?php

use App\Interfaces\PaymentStrategyInterface;
use App\Models\Order;

class CashPaymentStrategy implements PaymentStrategyInterface
{
    public function pay(int $orderId): bool
    {
        // Implement the logic for cash payment here
        // For example, you might want to update the order status in the database
        
        // Assuming the payment is successful, return true

        $order = Order::with('transaction')->find($orderId);

        if (!$order) {
            return false; // Order not found
        }


        $order->transaction()->create([
            'payment_method' => 'cash',
            'payment_status' => 'paid',
            'total_price' => $order->total_price,
            'vat_value' => $order->vat_value,
            'app_fee' => $order->app_fee,
            'provider_earning' => $order->provider_earning,
            'status' => 'paid', 
        ]);

        return true;
    }

    
   
}