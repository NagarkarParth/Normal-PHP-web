<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class Product
{
    /** @return array<string, mixed>|null */
    public static function find(int $id): ?array
    {
        return Database::first(
            'SELECT p.*, c.name AS category_name, c.slug AS category_slug
             FROM products p LEFT JOIN categories c ON c.id = p.category_id WHERE p.id = ?',
            [$id]
        );
    }

    /** @return array<string, mixed>|null */
    public static function findBySlug(string $slug): ?array
    {
        return Database::first(
            'SELECT p.*, c.name AS category_name, c.slug AS category_slug
             FROM products p LEFT JOIN categories c ON c.id = p.category_id WHERE p.slug = ?',
            [$slug]
        );
    }

    /**
     * @param array<int, int> $ids
     * @return array<int, array<string, mixed>>
     */
    public static function findManyActive(array $ids): array
    {
        if ($ids === []) {
            return [];
        }
        $placeholders = implode(',', array_fill(0, count($ids), '?'));

        return Database::all(
            "SELECT p.*, c.name AS category_name FROM products p
             LEFT JOIN categories c ON c.id = p.category_id
             WHERE p.id IN ($placeholders) AND p.status = 'active'",
            array_map('intval', $ids)
        );
    }

    /**
     * @param array<string, mixed> $filters
     * @return array{items: array<int, array<string, mixed>>, total: int}
     */
    public static function search(array $filters, int $limit = 12, int $offset = 0): array
    {
        $where = ["p.status = 'active'"];
        $params = [];

        if (!empty($filters['q'])) {
            $where[] = '(p.title LIKE ? OR p.short_description LIKE ? OR p.description LIKE ?)';
            $term = '%' . $filters['q'] . '%';
            array_push($params, $term, $term, $term);
        }
        if (!empty($filters['category'])) {
            $where[] = 'c.slug = ?';
            $params[] = $filters['category'];
        }
        if (isset($filters['min_price']) && $filters['min_price'] !== '') {
            $where[] = 'p.price >= ?';
            $params[] = (float) $filters['min_price'];
        }
        if (isset($filters['max_price']) && $filters['max_price'] !== '') {
            $where[] = 'p.price <= ?';
            $params[] = (float) $filters['max_price'];
        }

        $order = match ($filters['sort'] ?? 'newest') {
            'price_asc'  => 'p.price ASC',
            'price_desc' => 'p.price DESC',
            'popular'    => 'p.sales_count DESC, p.views DESC',
            'title'      => 'p.title ASC',
            default      => 'p.created_at DESC',
        };

        $whereSql = 'WHERE ' . implode(' AND ', $where);
        $total = (int) Database::value(
            "SELECT COUNT(*) FROM products p LEFT JOIN categories c ON c.id = p.category_id $whereSql",
            $params
        );

        $items = Database::all(
            "SELECT p.*, c.name AS category_name, c.slug AS category_slug
             FROM products p LEFT JOIN categories c ON c.id = p.category_id
             $whereSql ORDER BY $order LIMIT $limit OFFSET $offset",
            $params
        );

        return ['items' => $items, 'total' => $total];
    }

    /** @return array<int, array<string, mixed>> */
    public static function featured(int $limit = 6): array
    {
        return Database::all(
            "SELECT p.*, c.name AS category_name FROM products p
             LEFT JOIN categories c ON c.id = p.category_id
             WHERE p.status = 'active'
             ORDER BY p.is_featured DESC, p.sales_count DESC, p.created_at DESC LIMIT $limit"
        );
    }

    /** @return array<int, array<string, mixed>> */
    public static function related(int $productId, ?int $categoryId, int $limit = 4): array
    {
        return Database::all(
            "SELECT p.* FROM products p
             WHERE p.status = 'active' AND p.id <> ? AND (? IS NULL OR p.category_id = ?)
             ORDER BY p.sales_count DESC, RAND() LIMIT $limit",
            [$productId, $categoryId, $categoryId]
        );
    }

    /** @return array<int, array<string, mixed>> */
    public static function adminList(string $search, string $status, int $limit, int $offset): array
    {
        $where = [];
        $params = [];
        if ($search !== '') {
            $where[] = 'p.title LIKE ?';
            $params[] = '%' . $search . '%';
        }
        if ($status !== '') {
            $where[] = 'p.status = ?';
            $params[] = $status;
        }
        $whereSql = $where === [] ? '' : 'WHERE ' . implode(' AND ', $where);

        return Database::all(
            "SELECT p.*, c.name AS category_name FROM products p
             LEFT JOIN categories c ON c.id = p.category_id
             $whereSql ORDER BY p.created_at DESC LIMIT $limit OFFSET $offset",
            $params
        );
    }

    public static function adminCount(string $search, string $status): int
    {
        $where = [];
        $params = [];
        if ($search !== '') {
            $where[] = 'title LIKE ?';
            $params[] = '%' . $search . '%';
        }
        if ($status !== '') {
            $where[] = 'status = ?';
            $params[] = $status;
        }
        $whereSql = $where === [] ? '' : 'WHERE ' . implode(' AND ', $where);

        return (int) Database::value("SELECT COUNT(*) FROM products $whereSql", $params);
    }

    /** @param array<string, mixed> $data */
    public static function create(array $data): int
    {
        return Database::insert('products', $data);
    }

    /** @param array<string, mixed> $data */
    public static function update(int $id, array $data): void
    {
        Database::update('products', $data, ['id' => $id]);
    }

    public static function delete(int $id): void
    {
        Database::delete('products', ['id' => $id]);
    }

    public static function incrementViews(int $id): void
    {
        Database::run('UPDATE products SET views = views + 1 WHERE id = ?', [$id]);
    }

    public static function incrementSales(int $id): void
    {
        Database::run('UPDATE products SET sales_count = sales_count + 1 WHERE id = ?', [$id]);
    }

    public static function uniqueSlug(string $title, ?int $exceptId = null): string
    {
        $base = str_slug($title) ?: 'product';
        $slug = $base;
        $i = 2;
        while (true) {
            $sql = 'SELECT COUNT(*) FROM products WHERE slug = ?';
            $params = [$slug];
            if ($exceptId !== null) {
                $sql .= ' AND id <> ?';
                $params[] = $exceptId;
            }
            if ((int) Database::value($sql, $params) === 0) {
                return $slug;
            }
            $slug = $base . '-' . $i++;
        }
    }

    /** @return array<int, array<string, mixed>> */
    public static function screenshots(int $productId): array
    {
        return Database::all('SELECT * FROM product_screenshots WHERE product_id = ? ORDER BY sort_order, id', [$productId]);
    }

    public static function addScreenshot(int $productId, string $path): void
    {
        Database::insert('product_screenshots', ['product_id' => $productId, 'path' => $path]);
    }

    public static function deleteScreenshot(int $id, int $productId): void
    {
        Database::run('DELETE FROM product_screenshots WHERE id = ? AND product_id = ?', [$id, $productId]);
    }
}
