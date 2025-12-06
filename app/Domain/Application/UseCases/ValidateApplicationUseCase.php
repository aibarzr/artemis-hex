<?php

namespace App\Domain\Application\UseCases;

use App\Domain\Application\Ports\ApplicationRepositoryInterface;
use App\Domain\Validation\Ports\ValidationServiceInterface;
use App\Domain\Validation\ValueObjects\ValidationResult;

final readonly class ValidateApplicationUseCase
{
    public function __construct(
        private ApplicationRepositoryInterface $applicationRepository,
        private ValidationServiceInterface $validationService,
    ) {}

    public function execute(int $applicationId): ValidationResult
    {
        $application = $this->applicationRepository->findById($applicationId);

        if ($application === null) {
            throw new \InvalidArgumentException("Application with ID {$applicationId} not found.");
        }

        return $this->validationService->validate($application);
    }
}
