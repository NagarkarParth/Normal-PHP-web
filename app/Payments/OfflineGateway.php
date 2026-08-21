<?php

declare(strict_types=1);

namespace App\Payments;

/**
 * Test / manual gateway. Used when no live keys are configured so the whole
 * purchase flow (order -> payment -> download) can be exercised end to end.
 */
final class OfflineGateway implements Gateway
{
    public function key(): string
    {
        return 'offline';
    }

    public function label(): string
    {
        return 'Manual / Test payment';
    }

    public function isConfigured(): bool
    {
        return true;
    }

    /** @param array<string, mixed> $order */
    public function start(array $order): PaymentResult
    {
        return PaymentResult::instant();
    }

    /**
     * @param array<string, mixed> $order
     * @param array<string, mixed> $payload
     * @return array{success: bool, transaction_id: string, message: string}
     */
    public function confirm(array $order, array $payload): array
    {
        return [
            'success'        => true,
            'transaction_id' => 'MANUAL-' . strtoupper(bin2hex(random_bytes(4))),
            'message'        => 'Payment recorded manually.',
        ];
    }
}
