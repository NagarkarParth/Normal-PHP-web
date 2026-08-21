<?php

declare(strict_types=1);

namespace App\Payments;

interface Gateway
{
    public function key(): string;

    public function label(): string;

    public function isConfigured(): bool;

    /** @param array<string, mixed> $order */
    public function start(array $order): PaymentResult;

    /**
     * Confirms a payment coming back from the gateway.
     *
     * @param array<string, mixed> $order
     * @param array<string, mixed> $payload
     * @return array{success: bool, transaction_id: string, message: string}
     */
    public function confirm(array $order, array $payload): array;
}
