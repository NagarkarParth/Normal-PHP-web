<?php

declare(strict_types=1);

namespace App\Payments;

use App\Models\Setting;

final class StripeGateway implements Gateway
{
    public function key(): string
    {
        return 'stripe';
    }

    public function label(): string
    {
        return 'Stripe Checkout';
    }

    public function isConfigured(): bool
    {
        return (string) Setting::get('stripe_secret_key', '') !== '';
    }

    /** @param array<string, mixed> $order */
    public function start(array $order): PaymentResult
    {
        $secret = (string) Setting::get('stripe_secret_key', '');
        $params = http_build_query([
            'mode'                                    => 'payment',
            'success_url'                             => url('checkout/return/stripe?order=' . $order['id']),
            'cancel_url'                              => url('checkout/cancel/' . $order['id']),
            'client_reference_id'                     => (string) $order['order_number'],
            'customer_email'                          => (string) $order['customer_email'],
            'line_items' => [[
                'quantity'   => 1,
                'price_data' => [
                    'currency'     => strtolower((string) $order['currency']),
                    'unit_amount'  => (int) round((float) $order['total'] * 100),
                    'product_data' => ['name' => 'Order ' . $order['order_number']],
                ],
            ]],
        ]);

        $response = HttpClient::request(
            'POST',
            'https://api.stripe.com/v1/checkout/sessions',
            $params,
            ['Content-Type: application/x-www-form-urlencoded'],
            $secret . ':'
        );

        if ($response['status'] !== 200 || empty($response['body']['url'])) {
            return PaymentResult::failed($response['body']['error']['message'] ?? 'Stripe session could not be created.');
        }

        $_SESSION['stripe_session_' . $order['id']] = (string) $response['body']['id'];

        return PaymentResult::redirect((string) $response['body']['url']);
    }

    /**
     * @param array<string, mixed> $order
     * @param array<string, mixed> $payload
     * @return array{success: bool, transaction_id: string, message: string}
     */
    public function confirm(array $order, array $payload): array
    {
        $sessionId = (string) ($payload['session_id'] ?? $order['gateway_ref'] ?? '');
        if ($sessionId === '') {
            return ['success' => false, 'transaction_id' => '', 'message' => 'Missing Stripe session reference.'];
        }

        $response = HttpClient::request(
            'GET',
            'https://api.stripe.com/v1/checkout/sessions/' . urlencode($sessionId),
            [],
            [],
            (string) Setting::get('stripe_secret_key', '') . ':'
        );

        $paid = ($response['body']['payment_status'] ?? '') === 'paid';

        return [
            'success'        => $paid,
            'transaction_id' => (string) ($response['body']['payment_intent'] ?? $sessionId),
            'message'        => $paid ? 'Payment captured.' : 'Stripe reports the session as unpaid.',
        ];
    }
}
