<?php

namespace App\Payments;

use App\Interfaces\PaymentDataInterface;
use App\Interfaces\PaymentStrategyInterface;
use App\Payments\PaymentResult;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WalletPaymentStrategy implements PaymentStrategyInterface
{

    public function pay(PaymentDataInterface $data): PaymentResult
    {
        // TODO: Implement pay() method.


        try{

            $authResponse = Http::post('https://accept.paymob.com/api/auth/tokens', [
                'api_key' => config('services.paymob.api_key'),
            ]);

            if ($authResponse->failed()) {
                // dd($authResponse , config('services.paymob.api_key'));
                return PaymentResult::failure('Payment error: Failed to authenticate with Paymob Wallet');
            }

            $token = $authResponse['token'];
            

            if (!$token) {
                return PaymentResult::failure('Paymob auth failed');
            }

            // Step 2: Create Order
            $orderResponse = Http::post('https://accept.paymob.com/api/ecommerce/orders', [
                'auth_token' => $token,
                'amount_cents' => $data->getAmount() * 100,
                'currency' => 'EGP',
                'delivery_needed' => false,
                'items' => [],
                'merchant_order_id' => 'order-' . $data->getReferenceId() . '-' . time(),
            ]);
            // dd($orderResponse , $data->getReferenceId());


            $orderData = $data->getBillingData();


            $orderId = $orderResponse['id'] ?? null;

            if (!$orderId) {
                return PaymentResult::failure('Paymob order creation failed');
            }



             // Step 3: Generate Payment Key
             $paymentKeyResponse = Http::post('https://accept.paymob.com/api/acceptance/payment_keys', [
                'auth_token' => $token,
                'amount_cents' => $data->getAmount() * 100,
                'expiration' => 3600,
                'order_id' => $orderId,
                'currency' => 'EGP',
                'integration_id' => config('services.paymob.mobile_wallet_integration_id'),
                'billing_data' => [
                    "first_name"    => $orderData['sender_name'] ?? 'Guest',
                    "last_name"     => $orderData['sender_name'] ?? 'User',
                    "email"         => $orderData['email'] ?? 'guest@example.com',
                    "phone_number"  => $orderData['phone'] ?? '+201000000000',
                    "city"          => $orderData['city'] ?? 'Cairo',
                    "country"       => 'EG',
                    "street"        => $orderData['street'] ?? 'N/A',
                    "building"      => $orderData['building'] ?? 'N/A',
                    "floor"         => $orderData['floor'] ?? 'N/A',
                    "apartment"     => $orderData['apartment'] ?? 'N/A',
                    "postal_code"   => $orderData['postal_code'] ?? '0000',
                    "state"         => $orderData['state'] ?? 'Cairo',
                ]
            ]);


            if ($paymentKeyResponse->failed()) {

                Log::error('PAYMOB Wallet PAYMENT KEY ERROR', [
                    'stage' => 'payment_key',
                    'response' => $paymentKeyResponse->json(),
                    'status' => $paymentKeyResponse->status(),
                ]);

                return PaymentResult::failure('Payment error: Failed to get payment key from Paymob');

            }

            $paymentToken = $paymentKeyResponse['token'];
            // dd($paymentToken , $data->getReferenceId());

            return PaymentResult::success('Redirect to Paymob', [
                'redirect_url' => route('paymob.redirect', ['token' => $paymentToken]),
            ]);
        }  catch (\Throwable $e) {
            Log::error('PAYMOB PAYMENT Wallet ERROR', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw new \Exception("Paymob payment failed: " . $e->getMessage());
        }

    }
}