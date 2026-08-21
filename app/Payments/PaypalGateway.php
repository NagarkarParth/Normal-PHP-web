<?php

declare(strict_types=1);

namespace App\Payments;

use App\Models\Setting;

final class PaypalGateway implements Gateway
{
    public function key(): string
    {
        return 'paypal';
    }

    public function label(): string
    {
        return 'PayPal';
    }

    public function isConfigured(): bool
    {
        return (string) Setting::get('paypal_client_id', '') !== ''
            && (string) Setting::get('paypal_secret', '') !== '';
    }

    private function baseUrl(): string
    {
        return Setting::get('paypal_mode', 'sandbox') === 'live'
            ? 'https://api-m.paypal.com'
            : 'https://api-m.sandbox.paypal.com';
    }

    private function token(): ?string
    {
        $response = HttpClient::request(
            'POST',
            $this->baseUrl() . '/v1/oauth2/token',
            'grant_type=client_credentials',
            ['Content-Type: application/x-www-form-urlencoded'],
            Setting::get('paypal_client_id', '') . ':' . Setting::get('paypal_secret', '')
        );

        $token = $response['body']['access_token'] ?? null;

        return is_string($token) ? $token : null;
    }

    /** @param array<string, mixed> $order */
    public function start(array $order): PaymentResult
    {
        $token = $this->token();
        if ($token === null) {
            return PaymentResult::failed('PayPal credentials were rejected.');
        }

        $response = HttpClient::request(
            'POST',
            $this->baseUrl() . '/v2/checkout/orders',
            [
                'intent'         => 'CAPTURE',
                'purchase_units' => [[
                    'reference_id' => (string) $order['order_number'],
                    'amount'       => [
                        'currency_code' => (string) $order['currency'],
                        'value'         => number_format((float) $order['total'], 2, '.', ''),
                    ],
                ]],
                'application_context' => [
                    'return_url' => url('checkout/return/paypal?order=' . $order['id']),
                    'cancel_url' => url('checkout/cancel/' . $order['id']),
                ],
            ],
            ['Content-Type: application/json', 'Authorization: Bearer ' . $token]
        );

        $approveUrl = '';
        foreach ($response['body']['links'] ?? [] as $link) {
            if (($link['rel'] ?? '') === 'approve') {
                $approveUrl = (string) $link['href'];
            }
        }

        if ($approveUrl === '' || empty($response['body']['id'])) {
            return PaymentResult::failed($response['body']['message'] ?? 'PayPal order could not be created.');
        }

        $_SESSION['paypal_order_' . $order['id']] = (string) $response['body']['id'];

        return PaymentResult::redirect($approveUrl);
    }

    /**
     * @param array<string, mixed> $order
     * @param array<string, mixed> $payload
     * @return array{success: bool, transaction_id: string, message: string}
     */
    public function confirm(array $order, array $payload): array
    {
        $paypalOrderId = (string) ($payload['token'] ?? $order['gateway_ref'] ?? '');
        $token = $this->token();
        if ($paypalOrderId === '' || $token === null) {
            return ['success' => false, 'transaction_id' => '', 'message' => 'PayPal order reference missing.'];
        }

        $response = HttpClient::request(
            'POST',
            $this->baseUrl() . '/v2/checkout/orders/' . $paypalOrderId . '/capture',
            [],
            ['Content-Type: application/json', 'Authorization: Bearer ' . $token]
        );

        $status = (string) ($response['body']['status'] ?? '');
        $captureId = $response['body']['purchase_units'][0]['payments']['captures'][0]['id'] ?? $paypalOrderId;

        return [
            'success'        => $status === 'COMPLETED',
            'transaction_id' => (string) $captureId,
            'message'        => $status === 'COMPLETED' ? 'Payment captured.' : 'PayPal did not complete the capture.',
        ];
    }
}
