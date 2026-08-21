<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class Report
{
    /** @return array<string, mixed> */
    public static function dashboard(): array
    {
        return [
            'revenue_today'   => (float) Database::value('SELECT COALESCE(SUM(total),0) FROM orders WHERE payment_status = "paid" AND DATE(paid_at) = CURDATE()'),
            'revenue_month'   => (float) Database::value('SELECT COALESCE(SUM(total),0) FROM orders WHERE payment_status = "paid" AND YEAR(paid_at) = YEAR(CURDATE()) AND MONTH(paid_at) = MONTH(CURDATE())'),
            'revenue_total'   => (float) Database::value('SELECT COALESCE(SUM(total),0) FROM orders WHERE payment_status = "paid"'),
            'tax_total'       => (float) Database::value('SELECT COALESCE(SUM(tax),0) FROM orders WHERE payment_status = "paid"'),
            'orders_total'    => (int) Database::value('SELECT COUNT(*) FROM orders'),
            'orders_paid'     => (int) Database::value('SELECT COUNT(*) FROM orders WHERE payment_status = "paid"'),
            'orders_pending'  => (int) Database::value('SELECT COUNT(*) FROM orders WHERE payment_status = "pending"'),
            'users_total'     => (int) Database::value('SELECT COUNT(*) FROM users WHERE role = "customer"'),
            'products_active' => (int) Database::value('SELECT COUNT(*) FROM products WHERE status = "active"'),
            'tickets_open'    => (int) Database::value('SELECT COUNT(*) FROM tickets WHERE status <> "closed"'),
        ];
    }

    /** @return array<int, array<string, mixed>> */
    public static function dailySales(int $days = 14): array
    {
        return Database::all(
            'SELECT DATE(paid_at) AS day, COUNT(*) AS orders, COALESCE(SUM(total),0) AS revenue, COALESCE(SUM(tax),0) AS tax
             FROM orders WHERE payment_status = "paid" AND paid_at >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
             GROUP BY DATE(paid_at) ORDER BY day',
            [$days]
        );
    }

    /** @return array<int, array<string, mixed>> */
    public static function monthlySales(int $months = 12): array
    {
        return Database::all(
            'SELECT DATE_FORMAT(paid_at, "%Y-%m") AS month, COUNT(*) AS orders,
                    COALESCE(SUM(total),0) AS revenue, COALESCE(SUM(tax),0) AS tax,
                    COALESCE(SUM(discount),0) AS discount
             FROM orders WHERE payment_status = "paid" AND paid_at >= DATE_SUB(CURDATE(), INTERVAL ? MONTH)
             GROUP BY month ORDER BY month',
            [$months]
        );
    }

    /** @return array<int, array<string, mixed>> */
    public static function topProducts(int $limit = 10): array
    {
        return Database::all(
            "SELECT oi.product_id, oi.title, COUNT(*) AS units, COALESCE(SUM(oi.price),0) AS revenue
             FROM order_items oi JOIN orders o ON o.id = oi.order_id
             WHERE o.payment_status = 'paid'
             GROUP BY oi.product_id, oi.title ORDER BY units DESC, revenue DESC LIMIT $limit"
        );
    }

    /** @return array<int, array<string, mixed>> */
    public static function recentOrders(int $limit = 8): array
    {
        return Database::all(
            "SELECT o.*, u.name AS user_name FROM orders o LEFT JOIN users u ON u.id = o.user_id
             ORDER BY o.created_at DESC LIMIT $limit"
        );
    }
}
