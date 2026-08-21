<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class PasswordReset
{
    public static function issue(int $userId): string
    {
        Database::run('DELETE FROM password_resets WHERE user_id = ?', [$userId]);
        $token = bin2hex(random_bytes(32));
        Database::insert('password_resets', [
            'user_id'    => $userId,
            'token_hash' => hash('sha256', $token),
            'expires_at' => date('Y-m-d H:i:s', time() + 3600),
        ]);

        return $token;
    }

    /** @return array<string, mixed>|null */
    public static function findValid(string $token): ?array
    {
        return Database::first(
            'SELECT * FROM password_resets WHERE token_hash = ? AND used_at IS NULL AND expires_at > NOW()',
            [hash('sha256', $token)]
        );
    }

    public static function consume(int $id): void
    {
        Database::update('password_resets', ['used_at' => date('Y-m-d H:i:s')], ['id' => $id]);
    }
}
