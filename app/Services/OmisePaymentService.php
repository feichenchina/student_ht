<?php
namespace App\Services;

use OmiseCharge;

class OmisePaymentService
{
    public function processPayment($amount, $currency, $cardToken)
    {
        try {
            $apiKey = env('OMISE_PUBLIC_KEY');
            $apiSecret = env('OMISE_SECRET_KEY');
            $charge = OmiseCharge::create([
                'amount' => $amount,
                'currency' => $currency,
                'card' => $cardToken,
            ], $apiKey, $apiSecret);

            // 处理支付成功逻辑
            return $charge;
        } catch (\Throwable $e) {
            // 处理支付失败逻辑
            return false;
        }
    }
}
