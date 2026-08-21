<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class Testimonial
{
    /** @return array<int, array<string, mixed>> */
    public static function active(int $limit = 6): array
    {
        return Database::all("SELECT * FROM testimonials WHERE is_active = 1 ORDER BY sort_order, id LIMIT $limit");
    }
}
