<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class Ticket
{
    /** @param array<string, mixed> $data */
    public static function create(array $data): int
    {
        return Database::insert('tickets', $data);
    }

    /** @return array<string, mixed>|null */
    public static function find(int $id): ?array
    {
        return Database::first(
            'SELECT t.*, u.name AS user_name FROM tickets t LEFT JOIN users u ON u.id = t.user_id WHERE t.id = ?',
            [$id]
        );
    }

    /** @return array<int, array<string, mixed>> */
    public static function forUser(int $userId): array
    {
        return Database::all('SELECT * FROM tickets WHERE user_id = ? ORDER BY updated_at DESC', [$userId]);
    }

    /** @return array<int, array<string, mixed>> */
    public static function adminList(string $status, int $limit, int $offset): array
    {
        $where = $status === '' ? '' : 'WHERE t.status = ?';
        $params = $status === '' ? [] : [$status];

        return Database::all(
            "SELECT t.*, u.name AS user_name,
                    (SELECT COUNT(*) FROM ticket_replies r WHERE r.ticket_id = t.id) AS replies_count
             FROM tickets t LEFT JOIN users u ON u.id = t.user_id
             $where ORDER BY FIELD(t.status, 'open', 'pending', 'closed'), t.updated_at DESC
             LIMIT $limit OFFSET $offset",
            $params
        );
    }

    public static function adminCount(string $status): int
    {
        if ($status === '') {
            return (int) Database::value('SELECT COUNT(*) FROM tickets');
        }

        return (int) Database::value('SELECT COUNT(*) FROM tickets WHERE status = ?', [$status]);
    }

    /** @return array<int, array<string, mixed>> */
    public static function replies(int $ticketId): array
    {
        return Database::all(
            'SELECT r.*, u.name AS author_name FROM ticket_replies r
             LEFT JOIN users u ON u.id = r.user_id WHERE r.ticket_id = ? ORDER BY r.created_at',
            [$ticketId]
        );
    }

    public static function reply(int $ticketId, ?int $userId, bool $isAdmin, string $message): void
    {
        Database::insert('ticket_replies', [
            'ticket_id' => $ticketId,
            'user_id'   => $userId,
            'is_admin'  => $isAdmin ? 1 : 0,
            'message'   => $message,
        ]);
        Database::update('tickets', [
            'status'     => $isAdmin ? 'pending' : 'open',
            'updated_at' => date('Y-m-d H:i:s'),
        ], ['id' => $ticketId]);
    }

    public static function setStatus(int $ticketId, string $status): void
    {
        Database::update('tickets', ['status' => $status, 'updated_at' => date('Y-m-d H:i:s')], ['id' => $ticketId]);
    }
}
