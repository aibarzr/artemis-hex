<?php

namespace App\Domain\Evaluator\ValueObjects;

use InvalidArgumentException;

final readonly class Specialization
{
    private const BACKEND = 'backend';

    private const FRONTEND = 'frontend';

    private const FULLSTACK = 'fullstack';

    private const DEVOPS = 'devops';

    private const MOBILE = 'mobile';

    private const DATA = 'data';

    private const VALID_SPECIALIZATIONS = [
        self::BACKEND,
        self::FRONTEND,
        self::FULLSTACK,
        self::DEVOPS,
        self::MOBILE,
        self::DATA,
    ];

    public function __construct(private string $value)
    {
        $this->validate();
    }

    private function validate(): void
    {
        if (! in_array($this->value, self::VALID_SPECIALIZATIONS, true)) {
            throw new InvalidArgumentException(
                "Invalid specialization: {$this->value}. Valid specializations are: ".implode(', ', self::VALID_SPECIALIZATIONS)
            );
        }
    }

    public static function backend(): self
    {
        return new self(self::BACKEND);
    }

    public static function frontend(): self
    {
        return new self(self::FRONTEND);
    }

    public static function fullstack(): self
    {
        return new self(self::FULLSTACK);
    }

    public static function devops(): self
    {
        return new self(self::DEVOPS);
    }

    public static function mobile(): self
    {
        return new self(self::MOBILE);
    }

    public static function data(): self
    {
        return new self(self::DATA);
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(Specialization $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
