<?php

declare(strict_types=1);

namespace App\Core;

use App\Models\User;

final class Auth
{
    /** @var array<string, mixed>|null */
    private static ?array $cached = null;

    /** @return array<string, mixed>|null */
    public static function user(): ?array
    {
        if (self::$cached !== null) {
            return self::$cached;
        }

        $id = $_SESSION['user_id'] ?? null;
        if ($id === null) {
            return null;
        }

        $user = User::find((int) $id);
        if ($user === null || (int) $user['is_blocked'] === 1) {
            self::logout();

            return null;
        }

        return self::$cached = $user;
    }

    public static function id(): ?int
    {
        $user = self::user();

        return $user === null ? null : (int) $user['id'];
    }

    public static function check(): bool
    {
        return self::user() !== null;
    }

    public static function isAdmin(): bool
    {
        $user = self::user();

        return $user !== null && in_array($user['role'], ['super_admin', 'editor'], true);
    }

    /**
     * Editors can run the shop day to day; only super admins touch money, users and settings.
     */
    public static function can(string $permission): bool
    {
        $user = self::user();
        if ($user === null) {
            return false;
        }
        if ($user['role'] === 'super_admin') {
            return true;
        }
        if ($user['role'] !== 'editor') {
            return false;
        }

        return in_array($permission, ['products', 'orders', 'support', 'reports', 'coupons'], true);
    }

    public static function attempt(string $email, string $password): ?array
    {
        $user = User::findByEmail($email);
        if ($user === null || !password_verify($password, (string) $user['password_hash'])) {
            return null;
        }
        if ((int) $user['is_blocked'] === 1) {
            return null;
        }

        self::login((int) $user['id']);

        return $user;
    }

    public static function login(int $userId): void
    {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $userId;
        self::$cached = null;
        User::touchLogin($userId);
    }

    public static function logout(): void
    {
        unset($_SESSION['user_id']);
        self::$cached = null;
    }
}
