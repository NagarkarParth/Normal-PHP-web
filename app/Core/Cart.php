<?php

declare(strict_types=1);

namespace App\Core;

use App\Models\Coupon;
use App\Models\Product;
use App\Models\Setting;

/**
 * Session backed cart. Digital products are licence based, so every line is quantity 1.
 */
final class Cart
{
    /** @return array<int, int> */
    public static function raw(): array
    {
        return $_SESSION['cart'] ?? [];
    }

    public static function add(int $productId): void
    {
        $cart = self::raw();
        $cart[$productId] = 1;
        $_SESSION['cart'] = $cart;
    }

    public static function remove(int $productId): void
    {
        $cart = self::raw();
        unset($cart[$productId]);
        $_SESSION['cart'] = $cart;
    }

    public static function clear(): void
    {
        unset($_SESSION['cart'], $_SESSION['coupon']);
    }

    public static function has(int $productId): bool
    {
        return array_key_exists($productId, self::raw());
    }

    public static function count(): int
    {
        return count(self::raw());
    }

    /** @return array<int, array<string, mixed>> */
    public static function items(): array
    {
        $ids = array_keys(self::raw());
        if ($ids === []) {
            return [];
        }

        $products = Product::findManyActive($ids);
        // Drop products that were deactivated or deleted after being added.
        $_SESSION['cart'] = array_fill_keys(array_map(static fn (array $p): int => (int) $p['id'], $products), 1);

        return $products;
    }

    public static function applyCoupon(string $code): void
    {
        $_SESSION['coupon'] = strtoupper($code);
    }

    public static function removeCoupon(): void
    {
        unset($_SESSION['coupon']);
    }

    public static function couponCode(): ?string
    {
        return $_SESSION['coupon'] ?? null;
    }

    /**
     * @return array{items: array<int, array<string, mixed>>, subtotal: float, discount: float, taxable: float, tax: float, tax_rate: float, total: float, coupon: array<string, mixed>|null}
     */
    public static function totals(): array
    {
        $items = self::items();
        $subtotal = 0.0;
        foreach ($items as $item) {
            $subtotal += (float) $item['price'];
        }

        $coupon = null;
        $discount = 0.0;
        $code = self::couponCode();
        if ($code !== null) {
            $coupon = Coupon::findValid($code);
            if ($coupon === null) {
                self::removeCoupon();
            } else {
                $discount = Coupon::discountFor($coupon, $subtotal);
            }
        }

        $taxable = max(0.0, $subtotal - $discount);
        $taxRate = (float) Setting::get('tax_rate', 0);
        $tax = round($taxable * $taxRate / 100, 2);

        return [
            'items'    => $items,
            'subtotal' => round($subtotal, 2),
            'discount' => round($discount, 2),
            'taxable'  => round($taxable, 2),
            'tax'      => $tax,
            'tax_rate' => $taxRate,
            'total'    => round($taxable + $tax, 2),
            'coupon'   => $coupon,
        ];
    }
}
