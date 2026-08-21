<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class Order
{
    /**
     * @param array<string, mixed> $data
     * @param array<int, array<string, mixed>> $items
     */
    public static function create(array $data, array $items): int
    {
        return (int) Database::transaction(static function () use ($data, $items): int {
            $orderId = Database::insert('orders', $data);
            foreach ($items as $item) {
                Database::insert('order_items', [
                    'order_id'       => $orderId,
                    'product_id'     => (int) $item['id'],
                    'title'          => (string) $item['title'],
                    'price'          => (float) $item['price'],
                    'download_token' => bin2hex(random_bytes(16)),
                ]);
            }

            return $orderId;
        });
    }

    /** @return array<string, mixed>|null */
    public static function find(int $id): ?array
    {
        return Database::first(
            'SELECT o.*, u.name AS user_name, u.email AS user_email FROM orders o
             LEFT JOIN users u ON u.id = o.user_id WHERE o.id = ?',
            [$id]
        );
    }

    /** @return array<string, mixed>|null */
    public static function findForUser(int $id, int $userId): ?array
    {
        return Database::first('SELECT * FROM orders WHERE id = ? AND user_id = ?', [$id, $userId]);
    }

    /** @return array<string, mixed>|null */
    public static function findByNumber(string $number): ?array
    {
        return Database::first('SELECT * FROM orders WHERE order_number = ?', [$number]);
    }

    /** @return array<int, array<string, mixed>> */
    public static function items(int $orderId): array
    {
        return Database::all(
            'SELECT oi.*, p.slug, p.thumbnail, p.file_name, p.status AS product_status
             FROM order_items oi LEFT JOIN products p ON p.id = oi.product_id
             WHERE oi.order_id = ? ORDER BY oi.id',
            [$orderId]
        );
    }

    /** @return array<string, mixed>|null */
    public static function itemByToken(string $token): ?array
    {
        return Database::first(
            'SELECT oi.*, o.user_id, o.payment_status, o.paid_at, p.file_path, p.file_name
             FROM order_items oi
             JOIN orders o ON o.id = oi.order_id
             LEFT JOIN products p ON p.id = oi.product_id
             WHERE oi.download_token = ?',
            [$token]
        );
    }

    /** @return array<int, array<string, mixed>> */
    public static function forUser(int $userId): array
    {
        return Database::all(
            'SELECT o.*, (SELECT COUNT(*) FROM order_items oi WHERE oi.order_id = o.id) AS items_count
             FROM orders o WHERE o.user_id = ? ORDER BY o.created_at DESC',
            [$userId]
        );
    }

    /** @return array<int, array<string, mixed>> */
    public static function purchasedProducts(int $userId): array
    {
        return Database::all(
            'SELECT oi.*, o.order_number, o.paid_at, p.slug, p.thumbnail
             FROM order_items oi
             JOIN orders o ON o.id = oi.order_id
             LEFT JOIN products p ON p.id = oi.product_id
             WHERE o.user_id = ? AND o.payment_status = "paid"
             ORDER BY o.paid_at DESC',
            [$userId]
        );
    }

    public static function markPaid(int $orderId, string $transactionId, string $payload = ''): void
    {
        Database::transaction(static function () use ($orderId, $transactionId, $payload): void {
            $order = Database::first('SELECT * FROM orders WHERE id = ?', [$orderId]);
            if ($order === null || $order['payment_status'] === 'paid') {
                return;
            }

            Database::update('orders', [
                'payment_status' => 'paid',
                'transaction_id' => $transactionId,
                'gateway_payload'=> $payload,
                'paid_at'        => date('Y-m-d H:i:s'),
            ], ['id' => $orderId]);

            $expiryDays = (int) Setting::get('download_expiry_days', 0);
            if ($expiryDays > 0) {
                Database::run(
                    'UPDATE order_items SET expires_at = ? WHERE order_id = ?',
                    [date('Y-m-d H:i:s', time() + $expiryDays * 86400), $orderId]
                );
            }

            foreach (Database::all('SELECT product_id FROM order_items WHERE order_id = ?', [$orderId]) as $item) {
                if ($item['product_id'] !== null) {
                    Product::incrementSales((int) $item['product_id']);
                }
            }

            if ($order['coupon_id'] !== null) {
                Coupon::markUsed((int) $order['coupon_id']);
            }
        });
    }

    public static function markFailed(int $orderId, string $payload = ''): void
    {
        Database::update('orders', ['payment_status' => 'failed', 'gateway_payload' => $payload], ['id' => $orderId]);
    }

    public static function refund(int $orderId, float $amount, string $note): void
    {
        Database::update('orders', [
            'payment_status'  => 'refunded',
            'refunded_amount' => $amount,
            'admin_note'      => $note,
        ], ['id' => $orderId]);
    }

    /** @param array<string, mixed> $data */
    public static function update(int $orderId, array $data): void
    {
        Database::update('orders', $data, ['id' => $orderId]);
    }

    public static function nextNumber(): string
    {
        return 'YBT-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(3)));
    }

    /** @return array<int, array<string, mixed>> */
    public static function adminList(string $search, string $status, int $limit, int $offset): array
    {
        $where = [];
        $params = [];
        if ($search !== '') {
            $where[] = '(o.order_number LIKE ? OR o.customer_email LIKE ? OR o.transaction_id LIKE ?)';
            $term = '%' . $search . '%';
            array_push($params, $term, $term, $term);
        }
        if ($status !== '') {
            $where[] = 'o.payment_status = ?';
            $params[] = $status;
        }
        $whereSql = $where === [] ? '' : 'WHERE ' . implode(' AND ', $where);

        return Database::all(
            "SELECT o.*, u.name AS user_name FROM orders o
             LEFT JOIN users u ON u.id = o.user_id
             $whereSql ORDER BY o.created_at DESC LIMIT $limit OFFSET $offset",
            $params
        );
    }

    public static function adminCount(string $search, string $status): int
    {
        $where = [];
        $params = [];
        if ($search !== '') {
            $where[] = '(order_number LIKE ? OR customer_email LIKE ? OR transaction_id LIKE ?)';
            $term = '%' . $search . '%';
            array_push($params, $term, $term, $term);
        }
        if ($status !== '') {
            $where[] = 'payment_status = ?';
            $params[] = $status;
        }
        $whereSql = $where === [] ? '' : 'WHERE ' . implode(' AND ', $where);

        return (int) Database::value("SELECT COUNT(*) FROM orders $whereSql", $params);
    }

    public static function logDownload(int $orderItemId, ?int $userId, string $ip): void
    {
        Database::insert('downloads', [
            'order_item_id' => $orderItemId,
            'user_id'       => $userId,
            'ip_address'    => $ip,
        ]);
        Database::run('UPDATE order_items SET download_count = download_count + 1 WHERE id = ?', [$orderItemId]);
    }
}
