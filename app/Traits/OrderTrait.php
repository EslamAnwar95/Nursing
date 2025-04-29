<?php

namespace App\Traits;

use App\Models\Order;

trait OrderTrait
{
    public function calculateTotalPrice(Order $order): array
    {
       
        $prices = [];
        
        $prices['total_price'] = $order->price;
        $prices['vat_value'] = $order->price * (config('order.vat')/ 100);
        $prices['app_fee'] = $order->price * (config('order.app_fee') / 100);
        $prices['provider_earning'] = $order->price - $prices['app_fee'] - $prices['vat_value'];
        

        return $prices;
    }
}
