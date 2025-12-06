<?php

namespace App\Domain\Validation\ValueObjects;

final readonly class ValidationResult
{
    /**
     * @param  array<string, string>  $errors
     */
    private function __construct(
        private bool $isValid,
        private array $errors = [],
    ) {}

    public static function success(): self
    {
        return new self(true, []);
    }

    /**
     * @param  array<string, string>  $errors
     */
    public static function failure(array $errors): self
    {
        return new self(false, $errors);
    }

    public function isValid(): bool
    {
        return $this->isValid;
    }

    public function isFailed(): bool
    {
        return ! $this->isValid;
    }

    /**
     * @return array<string, string>
     */
    public function errors(): array
    {
        return $this->errors;
    }

    public function hasError(string $ruleName): bool
    {
        return isset($this->errors[$ruleName]);
    }

    public function getError(string $ruleName): ?string
    {
        return $this->errors[$ruleName] ?? null;
    }

    public function errorsCount(): int
    {
        return count($this->errors);
    }
}
