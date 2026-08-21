<?php

declare(strict_types=1);

namespace App\Core;

final class Validator
{
    /** @var array<string, string> */
    private array $errors = [];

    /** @param array<string, mixed> $data */
    public function __construct(private array $data)
    {
    }

    /** @param array<string, mixed> $data */
    public static function make(array $data): self
    {
        return new self($data);
    }

    public function required(string $field, string $label): self
    {
        $value = $this->data[$field] ?? '';
        if (!is_array($value) && trim((string) $value) === '') {
            $this->fail($field, "$label is required.");
        }

        return $this;
    }

    public function email(string $field, string $label): self
    {
        $value = (string) ($this->data[$field] ?? '');
        if ($value !== '' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->fail($field, "$label must be a valid email address.");
        }

        return $this;
    }

    public function min(string $field, int $length, string $label): self
    {
        $value = (string) ($this->data[$field] ?? '');
        if ($value !== '' && mb_strlen($value) < $length) {
            $this->fail($field, "$label must be at least $length characters.");
        }

        return $this;
    }

    public function numeric(string $field, string $label): self
    {
        $value = $this->data[$field] ?? '';
        if ($value !== '' && !is_numeric($value)) {
            $this->fail($field, "$label must be a number.");
        }

        return $this;
    }

    public function matches(string $field, string $other, string $label): self
    {
        if (($this->data[$field] ?? null) !== ($this->data[$other] ?? null)) {
            $this->fail($field, "$label does not match.");
        }

        return $this;
    }

    public function condition(string $field, bool $passes, string $message): self
    {
        if (!$passes) {
            $this->fail($field, $message);
        }

        return $this;
    }

    private function fail(string $field, string $message): void
    {
        $this->errors[$field] ??= $message;
    }

    public function fails(): bool
    {
        return $this->errors !== [];
    }

    /** @return array<string, string> */
    public function errors(): array
    {
        return $this->errors;
    }

    public function firstError(): ?string
    {
        foreach ($this->errors as $error) {
            return $error;
        }

        return null;
    }
}
