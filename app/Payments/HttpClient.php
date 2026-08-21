<?php

declare(strict_types=1);

namespace App\Payments;

final class HttpClient
{
    /**
     * @param array<string, mixed>|string $body
     * @param array<int, string> $headers
     * @return array{status: int, body: array<string, mixed>, raw: string}
     */
    public static function request(string $method, string $url, array|string $body = [], array $headers = [], ?string $basicAuth = null): array
    {
        $ch = curl_init($url);
        if ($ch === false) {
            return ['status' => 0, 'body' => [], 'raw' => 'curl unavailable'];
        }

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST  => strtoupper($method),
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_HTTPHEADER     => $headers,
        ]);

        if ($basicAuth !== null) {
            curl_setopt($ch, CURLOPT_USERPWD, $basicAuth);
        }

        if ($body !== [] && $body !== '') {
            curl_setopt($ch, CURLOPT_POSTFIELDS, is_array($body) ? json_encode($body) : $body);
        }

        $raw = curl_exec($ch);
        $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($raw === false) {
            $raw = curl_error($ch);
        }
        curl_close($ch);

        $decoded = json_decode((string) $raw, true);

        return [
            'status' => $status,
            'body'   => is_array($decoded) ? $decoded : [],
            'raw'    => (string) $raw,
        ];
    }
}
