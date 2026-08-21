<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class Faq
{
    /** @return array<int, array<string, mixed>> */
    public static function active(): array
    {
        return Database::all('SELECT * FROM faqs WHERE is_active = 1 ORDER BY sort_order, id');
    }

    /** @return array<int, array<string, mixed>> */
    public static function all(): array
    {
        return Database::all('SELECT * FROM faqs ORDER BY sort_order, id');
    }

    /** @return array<string, mixed>|null */
    public static function find(int $id): ?array
    {
        return Database::first('SELECT * FROM faqs WHERE id = ?', [$id]);
    }

    /** @param array<string, mixed> $data */
    public static function create(array $data): int
    {
        return Database::insert('faqs', $data);
    }

    /** @param array<string, mixed> $data */
    public static function update(int $id, array $data): void
    {
        Database::update('faqs', $data, ['id' => $id]);
    }

    public static function delete(int $id): void
    {
        Database::delete('faqs', ['id' => $id]);
    }
}
