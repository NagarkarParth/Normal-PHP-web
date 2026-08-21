<?php

declare(strict_types=1);

namespace App\Core;

final class Flash
{
    public static function add(string $type, string $message): void
    {
        $_SESSION['_flash'][] = ['type' => $type, 'message' => $message];
    }

    public static function success(string $message): void
    {
        self::add('success', $message);
    }

    public static function error(string $message): void
    {
        self::add('danger', $message);
    }

    public static function info(string $message): void
    {
        self::add('info', $message);
    }

    /** @return array<int, array{type: string, message: string}> */
    public static function pull(): array
    {
        $messages = $_SESSION['_flash'] ?? [];
        unset($_SESSION['_flash']);

        return $messages;
    }
}
