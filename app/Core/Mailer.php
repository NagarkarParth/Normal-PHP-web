<?php

declare(strict_types=1);

namespace App\Core;

use App\Models\Setting;

final class Mailer
{
    /**
     * Sends a mail through PHP's mail() when configured, otherwise appends to storage/logs/mail.log
     * so local development never silently drops notifications.
     */
    public static function send(string $to, string $subject, string $body, string $notificationKey = ''): bool
    {
        if ($notificationKey !== '' && !Setting::notificationEnabled($notificationKey)) {
            return false;
        }

        $from = (string) Config::get('mail.from', 'no-reply@example.com');
        $siteName = Setting::branding()['site_name'];
        $headers = implode("\r\n", [
            'MIME-Version: 1.0',
            'Content-Type: text/html; charset=UTF-8',
            sprintf('From: %s <%s>', $siteName, $from),
        ]);

        if (Config::get('mail.driver') === 'mail' && function_exists('mail')) {
            return @mail($to, $subject, $body, $headers);
        }

        $logDir = (string) Config::get('storage.logs');
        if (!is_dir($logDir)) {
            @mkdir($logDir, 0775, true);
        }

        $entry = sprintf(
            "[%s] To: %s\nSubject: %s\n%s\n%s\n\n",
            date('Y-m-d H:i:s'),
            $to,
            $subject,
            str_repeat('-', 40),
            strip_tags($body)
        );

        return (bool) @file_put_contents($logDir . '/mail.log', $entry, FILE_APPEND);
    }
}
