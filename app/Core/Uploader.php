<?php

declare(strict_types=1);

namespace App\Core;

final class Uploader
{
    private const IMAGE_TYPES = ['image/jpeg', 'image/png', 'image/webp', 'image/gif', 'image/svg+xml'];

    /**
     * Stores an image inside public/uploads and returns the web path.
     *
     * @param array<string, mixed> $file
     */
    public static function image(array $file, string $folder = 'products'): ?string
    {
        $type = self::mime($file);
        if (!in_array($type, self::IMAGE_TYPES, true)) {
            return null;
        }

        $base = rtrim((string) Config::get('storage.uploads'), '/\\') . DIRECTORY_SEPARATOR . $folder;
        if (!is_dir($base) && !@mkdir($base, 0775, true) && !is_dir($base)) {
            return null;
        }

        $name = bin2hex(random_bytes(8)) . '.' . self::extension((string) $file['name']);
        if (!move_uploaded_file((string) $file['tmp_name'], $base . DIRECTORY_SEPARATOR . $name)) {
            return null;
        }

        return 'uploads/' . $folder . '/' . $name;
    }

    /**
     * Stores a digital product file outside the web root.
     *
     * @param array<string, mixed> $file
     * @return array{path: string, name: string, size: int}|null
     */
    public static function productFile(array $file): ?array
    {
        $base = rtrim((string) Config::get('storage.products'), '/\\');
        if (!is_dir($base) && !@mkdir($base, 0775, true) && !is_dir($base)) {
            return null;
        }

        $original = (string) $file['name'];
        $stored = bin2hex(random_bytes(12)) . '.' . self::extension($original);
        if (!move_uploaded_file((string) $file['tmp_name'], $base . DIRECTORY_SEPARATOR . $stored)) {
            return null;
        }

        return [
            'path' => $stored,
            'name' => $original,
            'size' => (int) $file['size'],
        ];
    }

    /** @param array<string, mixed> $file */
    private static function mime(array $file): string
    {
        if (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            if ($finfo !== false) {
                $type = (string) finfo_file($finfo, (string) $file['tmp_name']);
                finfo_close($finfo);

                return $type;
            }
        }

        return (string) ($file['type'] ?? '');
    }

    private static function extension(string $filename): string
    {
        $ext = strtolower((string) pathinfo($filename, PATHINFO_EXTENSION));

        return preg_match('/^[a-z0-9]{1,8}$/', $ext) === 1 ? $ext : 'bin';
    }
}
