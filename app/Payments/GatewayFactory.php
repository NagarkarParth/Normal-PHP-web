<?php

declare(strict_types=1);

namespace App\Payments;

use App\Models\Setting;

final class GatewayFactory
{
    /** @return array<string, Gateway> */
    public static function all(): array
    {
        return [
            'razorpay' => new RazorpayGateway(),
            'stripe'   => new StripeGateway(),
            'paypal'   => new PaypalGateway(),
            'offline'  => new OfflineGateway(),
        ];
    }

    public static function make(?string $key = null): Gateway
    {
        $key ??= Setting::activeGateway();
        $gateways = self::all();
        $gateway = $gateways[$key] ?? null;

        // Fall back to the manual gateway when the configured one has no keys yet,
        // so a fresh install can still complete a purchase.
        if ($gateway === null || !$gateway->isConfigured()) {
            return $gateways['offline'];
        }

        return $gateway;
    }
}
