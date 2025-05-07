<?php

namespace App\Payments;

use App\Interfaces\PaymentDataInterface;
use App\Interfaces\PaymentStrategyInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymobPaymentStrategy implements PaymentStrategyInterface
{



    public function pay(PaymentDataInterface $data): PaymentResult
    {

        try {

            $authResponse = Http::post('https://accept.paymob.com/api/auth/tokens', [
                'api_key' => config('services.paymob.api_key'),
            ]);

            if ($authResponse->failed()) {

                return PaymentResult::failure('Payment error: Failed to authenticate with Paymob');
            }



            $token = $authResponse['token'];

            $orderResponse = Http::post('https://accept.paymob.com/api/ecommerce/orders', [
                'auth_token' => $token,
                'delivery_needed' => false,
                'amount_cents' => $data->getAmount() * 100,
                'currency' => 'EGP',
                'items' => [],
            ]);

            if ($orderResponse->failed()) {

                return PaymentResult::failure('Payment error: Failed to create order on Paymob');
            }
            $orderData = $data->getBillingData();


            $orderId = $orderResponse['id'];
            $paymentKeyResponse = Http::post('https://accept.paymob.com/api/acceptance/payment_keys', [
                'auth_token' => $token,
                'amount_cents' => $data->getAmount() * 100,
                'expiration' => 3600,
                'order_id' => $orderId,
                'currency' => 'EGP',
                'integration_id' => config('services.paymob.integration_id'),
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

                Log::error('PAYMOB PAYMENT KEY ERROR', [
                    'stage' => 'payment_key',
                    'response' => $paymentKeyResponse->json(),
                    'status' => $paymentKeyResponse->status(),
                ]);

                return PaymentResult::failure('Payment error: Failed to get payment key from Paymob');

            }

            $paymentToken = $paymentKeyResponse['token'];


            return PaymentResult::success('Redirect to Paymob', [
                'redirect_url' => route('paymob.redirect', ['token' => $paymentToken]),
            ]);
     
        } catch (\Throwable $e) {
            Log::error('PAYMOB PAYMENT KEY ERROR', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw new \Exception("Paymob payment failed: " . $e->getMessage());
        }


    }
}
