<?php

namespace App\Domain\Application\Entities;

use App\Domain\Application\ValueObjects\ApplicationStatus;
use App\Domain\Application\ValueObjects\Email;
use App\Domain\Application\ValueObjects\YearsOfExperience;
use DateTimeImmutable;

final class Application
{
    public function __construct(
        private ?int $id,
        private string $candidateName,
        private Email $candidateEmail,
        private string $position,
        private YearsOfExperience $yearsOfExperience,
        private ApplicationStatus $status,
        private ?string $cvPath,
        private ?string $coverLetter,
        private ?int $evaluatorId,
        private DateTimeImmutable $submittedAt,
        private ?DateTimeImmutable $createdAt = null,
        private ?DateTimeImmutable $updatedAt = null,
    ) {}

    public static function create(
        string $candidateName,
        Email $candidateEmail,
        string $position,
        YearsOfExperience $yearsOfExperience,
        ?string $cvPath = null,
        ?string $coverLetter = null,
    ): self {
        return new self(
            id: null,
            candidateName: $candidateName,
            candidateEmail: $candidateEmail,
            position: $position,
            yearsOfExperience: $yearsOfExperience,
            status: ApplicationStatus::pending(),
            cvPath: $cvPath,
            coverLetter: $coverLetter,
            evaluatorId: null,
            submittedAt: new DateTimeImmutable,
        );
    }

    public function id(): ?int
    {
        return $this->id;
    }

    public function candidateName(): string
    {
        return $this->candidateName;
    }

    public function candidateEmail(): Email
    {
        return $this->candidateEmail;
    }

    public function position(): string
    {
        return $this->position;
    }

    public function yearsOfExperience(): YearsOfExperience
    {
        return $this->yearsOfExperience;
    }

    public function status(): ApplicationStatus
    {
        return $this->status;
    }

    public function cvPath(): ?string
    {
        return $this->cvPath;
    }

    public function coverLetter(): ?string
    {
        return $this->coverLetter;
    }

    public function evaluatorId(): ?int
    {
        return $this->evaluatorId;
    }

    public function submittedAt(): DateTimeImmutable
    {
        return $this->submittedAt;
    }

    public function createdAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function updatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function hasEvaluator(): bool
    {
        return $this->evaluatorId !== null;
    }

    public function hasCv(): bool
    {
        return $this->cvPath !== null && $this->cvPath !== '';
    }

    public function markAsInReview(): self
    {
        return new self(
            id: $this->id,
            candidateName: $this->candidateName,
            candidateEmail: $this->candidateEmail,
            position: $this->position,
            yearsOfExperience: $this->yearsOfExperience,
            status: ApplicationStatus::inReview(),
            cvPath: $this->cvPath,
            coverLetter: $this->coverLetter,
            evaluatorId: $this->evaluatorId,
            submittedAt: $this->submittedAt,
            createdAt: $this->createdAt,
            updatedAt: new DateTimeImmutable,
        );
    }

    public function approve(): self
    {
        return new self(
            id: $this->id,
            candidateName: $this->candidateName,
            candidateEmail: $this->candidateEmail,
            position: $this->position,
            yearsOfExperience: $this->yearsOfExperience,
            status: ApplicationStatus::approved(),
            cvPath: $this->cvPath,
            coverLetter: $this->coverLetter,
            evaluatorId: $this->evaluatorId,
            submittedAt: $this->submittedAt,
            createdAt: $this->createdAt,
            updatedAt: new DateTimeImmutable,
        );
    }

    public function reject(): self
    {
        return new self(
            id: $this->id,
            candidateName: $this->candidateName,
            candidateEmail: $this->candidateEmail,
            position: $this->position,
            yearsOfExperience: $this->yearsOfExperience,
            status: ApplicationStatus::rejected(),
            cvPath: $this->cvPath,
            coverLetter: $this->coverLetter,
            evaluatorId: $this->evaluatorId,
            submittedAt: $this->submittedAt,
            createdAt: $this->createdAt,
            updatedAt: new DateTimeImmutable,
        );
    }

    public function assignEvaluator(int $evaluatorId): self
    {
        return new self(
            id: $this->id,
            candidateName: $this->candidateName,
            candidateEmail: $this->candidateEmail,
            position: $this->position,
            yearsOfExperience: $this->yearsOfExperience,
            status: $this->status,
            cvPath: $this->cvPath,
            coverLetter: $this->coverLetter,
            evaluatorId: $evaluatorId,
            submittedAt: $this->submittedAt,
            createdAt: $this->createdAt,
            updatedAt: new DateTimeImmutable,
        );
    }

    public function withId(int $id): self
    {
        return new self(
            id: $id,
            candidateName: $this->candidateName,
            candidateEmail: $this->candidateEmail,
            position: $this->position,
            yearsOfExperience: $this->yearsOfExperience,
            status: $this->status,
            cvPath: $this->cvPath,
            coverLetter: $this->coverLetter,
            evaluatorId: $this->evaluatorId,
            submittedAt: $this->submittedAt,
            createdAt: $this->createdAt,
            updatedAt: $this->updatedAt,
        );
    }
}
