<?php 


namespace App\Interfaces;


interface PaymentDataInterface
{

    
    /**
     * Get the amount to be paid.
     */
    public function getAmount(): float;

    /**
     * Get a unique reference for this payment (e.g. order ID, settlement ID).
     */
    public function getReferenceId(): string;

    /**
     * Get the type of this payment (e.g. 'order', 'settlement').
     */
    public function getType(): string;


    /**
     * Get the billing data for this payment.
     * This should include sender and receiver information.
     */
    public function getBillingData(): array;

}
