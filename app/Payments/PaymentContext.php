<?php


namespace App\Payments;

use App\Interfaces\PaymentDataInterface;
use App\Interfaces\PaymentStrategyInterface;
use App\Models\Order;

class PaymentContext
{
    private $strategy;

    public function setStrategy(PaymentStrategyInterface $strategy)
    {
        $this->strategy = $strategy;
    }

    public function process(PaymentDataInterface $data)
    {
        if (!isset($this->strategy)) {
            
            return false;
        }

        return $this->strategy->pay($data);
    }
}