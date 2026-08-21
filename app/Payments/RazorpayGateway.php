<?php

declare(strict_types=1);

namespace App\Payments;

use App\Models\Setting;

final class RazorpayGateway implements Gateway
{
    public function key(): string
    {
        return 'razorpay';
    }

    public function label(): string
    {
        return 'Razorpay (cards, UPI, netbanking)';
    }

    public function isConfigured(): bool
    {
        return (string) Setting::get('razorpay_key_id', '') !== ''
            && (string) Setting::get('razorpay_key_secret', '') !== '';
    }

    /** @param array<string, mixed> $order */
    public function start(array $order): PaymentResult
    {
        $keyId = (string) Setting::get('razorpay_key_id', '');
        $secret = (string) Setting::get('razorpay_key_secret', '');

        $response = HttpClient::request(
            'POST',
            'https://api.razorpay.com/v1/orders',
            [
                'amount'   => (int) round((float) $order['total'] * 100),
                'currency' => (string) $order['currency'],
                'receipt'  => (string) $order['order_number'],
                'notes'    => ['order_id' => (string) $order['id']],
            ],
            ['Content-Type: application/json'],
            $keyId . ':' . $secret
        );

        if ($response['status'] !== 200 || empty($response['body']['id'])) {
            return PaymentResult::failed($response['body']['error']['description'] ?? 'Razorpay order could not be created.');
        }

        return PaymentResult::jsCheckout([
            'gateway_ref' => (string) $response['body']['id'],
            'script'      => 'https://checkout.razorpay.com/v1/checkout.js',
            'options'     => [
                'key'      => $keyId,
                'amount'   => $response['body']['amount'],
                'currency' => $order['currency'],
                'name'     => Setting::branding()['site_name'],
                'order_id' => $response['body']['id'],
                'prefill'  => [
                    'name'  => $order['customer_name'],
                    'email' => $order['customer_email'],
                ],
            ],
        ]);
    }

    /**
     * @param array<string, mixed> $order
     * @param array<string, mixed> $payload
     * @return array{success: bool, transaction_id: string, message: string}
     */
    public function confirm(array $order, array $payload): array
    {
        $secret = (string) Setting::get('razorpay_key_secret', '');
        $orderRef = (string) ($payload['razorpay_order_id'] ?? '');
        $paymentId = (string) ($payload['razorpay_payment_id'] ?? '');
        $signature = (string) ($payload['razorpay_signature'] ?? '');

        if ($orderRef === '' || $paymentId === '' || $signature === '') {
            return ['success' => false, 'transaction_id' => '', 'message' => 'Incomplete Razorpay response.'];
        }

        $expected = hash_hmac('sha256', $orderRef . '|' . $paymentId, $secret);
        if (!hash_equals($expected, $signature)) {
            return ['success' => false, 'transaction_id' => $paymentId, 'message' => 'Razorpay signature mismatch.'];
        }

        return ['success' => true, 'transaction_id' => $paymentId, 'message' => 'Payment verified.'];
    }
}
