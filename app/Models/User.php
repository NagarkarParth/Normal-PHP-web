<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class User
{
    /** @return array<string, mixed>|null */
    public static function find(int $id): ?array
    {
        return Database::first('SELECT * FROM users WHERE id = ?', [$id]);
    }

    /** @return array<string, mixed>|null */
    public static function findByEmail(string $email): ?array
    {
        return Database::first('SELECT * FROM users WHERE email = ?', [strtolower(trim($email))]);
    }

    public static function emailExists(string $email, ?int $exceptId = null): bool
    {
        $sql = 'SELECT COUNT(*) FROM users WHERE email = ?';
        $params = [strtolower(trim($email))];
        if ($exceptId !== null) {
            $sql .= ' AND id <> ?';
            $params[] = $exceptId;
        }

        return (int) Database::value($sql, $params) > 0;
    }

    public static function create(string $name, string $email, string $password, string $role = 'customer'): int
    {
        return Database::insert('users', [
            'name'          => $name,
            'email'         => strtolower(trim($email)),
            'password_hash' => password_hash($password, PASSWORD_DEFAULT),
            'role'          => $role,
        ]);
    }

    /** @param array<string, mixed> $data */
    public static function update(int $id, array $data): void
    {
        Database::update('users', $data, ['id' => $id]);
    }

    public static function updatePassword(int $id, string $password): void
    {
        Database::update('users', ['password_hash' => password_hash($password, PASSWORD_DEFAULT)], ['id' => $id]);
    }

    public static function touchLogin(int $id): void
    {
        Database::update('users', ['last_login_at' => date('Y-m-d H:i:s')], ['id' => $id]);
    }

    public static function setBlocked(int $id, bool $blocked): void
    {
        Database::update('users', ['is_blocked' => $blocked ? 1 : 0], ['id' => $id]);
    }

    /** @return array<int, array<string, mixed>> */
    public static function paginate(string $search, int $limit, int $offset): array
    {
        $sql = 'SELECT u.*,
                       (SELECT COUNT(*) FROM orders o WHERE o.user_id = u.id AND o.payment_status = "paid") AS orders_count,
                       (SELECT COALESCE(SUM(o.total), 0) FROM orders o WHERE o.user_id = u.id AND o.payment_status = "paid") AS lifetime_value
                FROM users u';
        $params = [];
        if ($search !== '') {
            $sql .= ' WHERE u.name LIKE ? OR u.email LIKE ?';
            $params = ['%' . $search . '%', '%' . $search . '%'];
        }
        $sql .= ' ORDER BY u.created_at DESC LIMIT ' . $limit . ' OFFSET ' . $offset;

        return Database::all($sql, $params);
    }

    public static function countAll(string $search = ''): int
    {
        if ($search === '') {
            return (int) Database::value('SELECT COUNT(*) FROM users');
        }

        return (int) Database::value(
            'SELECT COUNT(*) FROM users WHERE name LIKE ? OR email LIKE ?',
            ['%' . $search . '%', '%' . $search . '%']
        );
    }
}
