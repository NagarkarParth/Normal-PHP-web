<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class Category
{
    /** @return array<int, array<string, mixed>> */
    public static function all(): array
    {
        return Database::all('SELECT * FROM categories ORDER BY name');
    }

    /** @return array<int, array<string, mixed>> */
    public static function active(): array
    {
        return Database::all(
            'SELECT c.*, (SELECT COUNT(*) FROM products p WHERE p.category_id = c.id AND p.status = "active") AS products_count
             FROM categories c WHERE c.is_active = 1 ORDER BY c.name'
        );
    }

    /** @return array<string, mixed>|null */
    public static function find(int $id): ?array
    {
        return Database::first('SELECT * FROM categories WHERE id = ?', [$id]);
    }

    /** @return array<string, mixed>|null */
    public static function findBySlug(string $slug): ?array
    {
        return Database::first('SELECT * FROM categories WHERE slug = ?', [$slug]);
    }

    public static function create(string $name, string $description, bool $active): int
    {
        return Database::insert('categories', [
            'name'        => $name,
            'slug'        => self::uniqueSlug($name),
            'description' => $description,
            'is_active'   => $active ? 1 : 0,
        ]);
    }

    public static function update(int $id, string $name, string $description, bool $active): void
    {
        Database::update('categories', [
            'name'        => $name,
            'description' => $description,
            'is_active'   => $active ? 1 : 0,
        ], ['id' => $id]);
    }

    public static function delete(int $id): void
    {
        Database::delete('categories', ['id' => $id]);
    }

    public static function uniqueSlug(string $name, ?int $exceptId = null): string
    {
        $base = str_slug($name) ?: 'category';
        $slug = $base;
        $i = 2;
        while (true) {
            $sql = 'SELECT COUNT(*) FROM categories WHERE slug = ?';
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
}
