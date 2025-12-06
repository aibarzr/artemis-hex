<?php

namespace App\Domain\Evaluator\Entities;

use App\Domain\Application\ValueObjects\Email;
use App\Domain\Evaluator\ValueObjects\Specialization;
use DateTimeImmutable;

final class Evaluator
{
    public function __construct(
        private ?int $id,
        private string $name,
        private Email $email,
        private Specialization $specialization,
        private int $maxApplications,
        private bool $isActive,
        private int $currentApplicationsCount = 0,
        private ?DateTimeImmutable $createdAt = null,
        private ?DateTimeImmutable $updatedAt = null,
    ) {}

    public static function create(
        string $name,
        Email $email,
        Specialization $specialization,
        int $maxApplications = 10,
    ): self {
        return new self(
            id: null,
            name: $name,
            email: $email,
            specialization: $specialization,
            maxApplications: $maxApplications,
            isActive: true,
        );
    }

    public function id(): ?int
    {
        return $this->id;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function email(): Email
    {
        return $this->email;
    }

    public function specialization(): Specialization
    {
        return $this->specialization;
    }

    public function maxApplications(): int
    {
        return $this->maxApplications;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function currentApplicationsCount(): int
    {
        return $this->currentApplicationsCount;
    }

    public function createdAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function updatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function hasReachedMaxApplications(): bool
    {
        return $this->currentApplicationsCount >= $this->maxApplications;
    }

    public function canEvaluate(): bool
    {
        return $this->isActive && ! $this->hasReachedMaxApplications();
    }

    public function activate(): self
    {
        return new self(
            id: $this->id,
            name: $this->name,
            email: $this->email,
            specialization: $this->specialization,
            maxApplications: $this->maxApplications,
            isActive: true,
            currentApplicationsCount: $this->currentApplicationsCount,
            createdAt: $this->createdAt,
            updatedAt: new DateTimeImmutable,
        );
    }

    public function deactivate(): self
    {
        return new self(
            id: $this->id,
            name: $this->name,
            email: $this->email,
            specialization: $this->specialization,
            maxApplications: $this->maxApplications,
            isActive: false,
            currentApplicationsCount: $this->currentApplicationsCount,
            createdAt: $this->createdAt,
            updatedAt: new DateTimeImmutable,
        );
    }

    public function withApplicationsCount(int $count): self
    {
        return new self(
            id: $this->id,
            name: $this->name,
            email: $this->email,
            specialization: $this->specialization,
            maxApplications: $this->maxApplications,
            isActive: $this->isActive,
            currentApplicationsCount: $count,
            createdAt: $this->createdAt,
            updatedAt: $this->updatedAt,
        );
    }

    public function withId(int $id): self
    {
        return new self(
            id: $id,
            name: $this->name,
            email: $this->email,
            specialization: $this->specialization,
            maxApplications: $this->maxApplications,
            isActive: $this->isActive,
            currentApplicationsCount: $this->currentApplicationsCount,
            createdAt: $this->createdAt,
            updatedAt: $this->updatedAt,
        );
    }
}
