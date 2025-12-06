<?php

namespace App\Domain\Application\UseCases;

use App\Domain\Application\Entities\Application;
use App\Domain\Application\Ports\ApplicationRepositoryInterface;
use App\Domain\Evaluator\Ports\EvaluatorRepositoryInterface;
use App\Domain\Shared\Ports\TransactionServiceInterface;

final readonly class AssignEvaluatorUseCase
{
    public function __construct(
        private ApplicationRepositoryInterface $applicationRepository,
        private EvaluatorRepositoryInterface $evaluatorRepository,
        private TransactionServiceInterface $transactionService,
    ) {}

    public function execute(int $applicationId, int $evaluatorId): Application
    {
        return $this->transactionService->execute(function () use ($applicationId, $evaluatorId) {
            // Lock the application row to prevent concurrent modifications
            $application = $this->applicationRepository->findByIdForUpdate($applicationId);
            if ($application === null) {
                throw new \InvalidArgumentException("Application with ID {$applicationId} not found.");
            }

            // Idempotency: Check if evaluator is already assigned to this application
            if ($application->evaluatorId() === $evaluatorId) {
                return $application; // Already assigned, return without changes
            }

            // Verify evaluator exists
            $evaluator = $this->evaluatorRepository->findById($evaluatorId);
            if ($evaluator === null) {
                throw new \InvalidArgumentException("Evaluator with ID {$evaluatorId} not found.");
            }

            // Assign evaluator and save
            $updatedApplication = $application->assignEvaluator($evaluatorId);

            return $this->applicationRepository->save($updatedApplication);
        });
    }
}
