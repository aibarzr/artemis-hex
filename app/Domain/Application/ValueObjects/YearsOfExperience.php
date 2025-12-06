<?php

namespace App\Domain\Application\ValueObjects;

use InvalidArgumentException;

final readonly class YearsOfExperience
{
    private const MIN_YEARS = 0;

    private const MAX_YEARS = 50;

    public function __construct(private int $value)
    {
        $this->validate();
    }

    private function validate(): void
    {
        if ($this->value < self::MIN_YEARS || $this->value > self::MAX_YEARS) {
            throw new InvalidArgumentException(
                'Years of experience must be between '.self::MIN_YEARS.' and '.self::MAX_YEARS
            );
        }
    }

    public function value(): int
    {
        return $this->value;
    }

    public function equals(YearsOfExperience $other): bool
    {
        return $this->value === $other->value;
    }

    public function isJunior(): bool
    {
        return $this->value < 2;
    }

    public function isMid(): bool
    {
        return $this->value >= 2 && $this->value < 5;
    }

    public function isSenior(): bool
    {
        return $this->value >= 5;
    }

    public function __toString(): string
    {
        return (string) $this->value;
    }
}
