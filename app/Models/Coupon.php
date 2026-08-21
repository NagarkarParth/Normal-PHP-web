<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class Coupon
{
    /** @return array<int, array<string, mixed>> */
    public static function all(): array
    {
        return Database::all('SELECT * FROM coupons ORDER BY created_at DESC');
    }

    /** @return array<string, mixed>|null */
    public static function find(int $id): ?array
    {
        return Database::first('SELECT * FROM coupons WHERE id = ?', [$id]);
    }

    /** @return array<string, mixed>|null */
    public static function findValid(string $code): ?array
    {
        return Database::first(
            'SELECT * FROM coupons
             WHERE code = ? AND is_active = 1
               AND (starts_at IS NULL OR starts_at <= NOW())
               AND (expires_at IS NULL OR expires_at >= NOW())
               AND (usage_limit IS NULL OR used_count < usage_limit)',
            [strtoupper(trim($code))]
        );
    }

    /** @param array<string, mixed> $coupon */
    public static function discountFor(array $coupon, float $subtotal): float
    {
        if ($subtotal < (float) $coupon['min_order_amount']) {
            return 0.0;
        }

        $discount = $coupon['type'] === 'percent'
            ? $subtotal * (float) $coupon['value'] / 100
            : (float) $coupon['value'];

        $max = $coupon['max_discount'] === null ? null : (float) $coupon['max_discount'];
        if ($max !== null && $max > 0) {
            $discount = min($discount, $max);
        }

        return round(min($discount, $subtotal), 2);
    }

    /** @param array<string, mixed> $coupon */
    public static function reason(array $coupon, float $subtotal): ?string
    {
        if ($subtotal < (float) $coupon['min_order_amount']) {
            return 'This coupon needs a minimum order of ' . money((float) $coupon['min_order_amount']) . '.';
        }

        return null;
    }

    /** @param array<string, mixed> $data */
    public static function create(array $data): int
    {
        return Database::insert('coupons', $data);
    }

    /** @param array<string, mixed> $data */
    public static function update(int $id, array $data): void
    {
        Database::update('coupons', $data, ['id' => $id]);
    }

    public static function delete(int $id): void
    {
        Database::delete('coupons', ['id' => $id]);
    }

    public static function markUsed(int $id): void
    {
        Database::run('UPDATE coupons SET used_count = used_count + 1 WHERE id = ?', [$id]);
    }

    public static function codeExists(string $code, ?int $exceptId = null): bool
    {
        $sql = 'SELECT COUNT(*) FROM coupons WHERE code = ?';
        $params = [strtoupper($code)];
        if ($exceptId !== null) {
            $sql .= ' AND id <> ?';
            $params[] = $exceptId;
        }

        return (int) Database::value($sql, $params) > 0;
    }
}
