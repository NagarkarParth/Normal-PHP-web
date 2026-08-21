<?php

declare(strict_types=1);

namespace App\Payments;

/**
 * Describes what the checkout page should do next for a given gateway.
 */
final class PaymentResult
{
    private function __construct(
        public readonly string $mode,
        public readonly string $redirectUrl = '',
        public readonly array $options = [],
        public readonly string $error = ''
    ) {
    }

    /** @param array<string, mixed> $options */
    public static function jsCheckout(array $options): self
    {
        return new self('js', '', $options);
    }

    public static function redirect(string $url): self
    {
        return new self('redirect', $url);
    }

    public static function instant(): self
    {
        return new self('instant');
    }

    public static function failed(string $error): self
    {
        return new self('failed', '', [], $error);
    }
}
