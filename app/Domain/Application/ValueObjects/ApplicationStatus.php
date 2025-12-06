<?php

namespace App\Domain\Application\ValueObjects;

use InvalidArgumentException;

final readonly class ApplicationStatus
{
    private const PENDING = 'pending';

    private const IN_REVIEW = 'in_review';

    private const APPROVED = 'approved';

    private const REJECTED = 'rejected';

    private const VALID_STATUSES = [
        self::PENDING,
        self::IN_REVIEW,
        self::APPROVED,
        self::REJECTED,
    ];

    public function __construct(private string $value)
    {
        $this->validate();
    }

    private function validate(): void
    {
        if (! in_array($this->value, self::VALID_STATUSES, true)) {
            throw new InvalidArgumentException(
                "Invalid status: {$this->value}. Valid statuses are: ".implode(', ', self::VALID_STATUSES)
            );
        }
    }

    public static function pending(): self
    {
        return new self(self::PENDING);
    }

    public static function inReview(): self
    {
        return new self(self::IN_REVIEW);
    }

    public static function approved(): self
    {
        return new self(self::APPROVED);
    }

    public static function rejected(): self
    {
        return new self(self::REJECTED);
    }

    public function isPending(): bool
    {
        return $this->value === self::PENDING;
    }

    public function isInReview(): bool
    {
        return $this->value === self::IN_REVIEW;
    }

    public function isApproved(): bool
    {
        return $this->value === self::APPROVED;
    }

    public function isRejected(): bool
    {
        return $this->value === self::REJECTED;
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(ApplicationStatus $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
