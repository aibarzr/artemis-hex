<?php

namespace App\Domain\Application\UseCases;

use App\Domain\Application\DTOs\ApplicationSummary;
use App\Domain\Application\Ports\ApplicationRepositoryInterface;
use App\Domain\Evaluator\Ports\EvaluatorRepositoryInterface;
use App\Domain\Validation\Ports\ValidationServiceInterface;

final readonly class GetApplicationSummaryUseCase
{
    public function __construct(
        private ApplicationRepositoryInterface $applicationRepository,
        private EvaluatorRepositoryInterface $evaluatorRepository,
        private ValidationServiceInterface $validationService,
    ) {}

    public function execute(int $applicationId): ApplicationSummary
    {
        $application = $this->applicationRepository->findById($applicationId);

        if ($application === null) {
            throw new \InvalidArgumentException("Application with ID {$applicationId} not found.");
        }

        $validationResult = $this->validationService->validate($application);

        $evaluatorData = null;
        if ($application->hasEvaluator()) {
            $evaluator = $this->evaluatorRepository->findById($application->evaluatorId());
            if ($evaluator !== null) {
                $evaluatorData = [
                    'id' => $evaluator->id(),
                    'name' => $evaluator->name(),
                    'email' => $evaluator->email()->value(),
                    'specialization' => $evaluator->specialization()->value(),
                ];
            }
        }

        $applicationData = [
            'id' => $application->id(),
            'candidate_name' => $application->candidateName(),
            'candidate_email' => $application->candidateEmail()->value(),
            'position' => $application->position(),
            'years_of_experience' => $application->yearsOfExperience()->value(),
            'cv_path' => $application->cvPath(),
            'cover_letter' => $application->coverLetter(),
            'submitted_at' => $application->submittedAt()->format('Y-m-d H:i:s'),
            'created_at' => $application->createdAt()?->format('Y-m-d H:i:s'),
            'updated_at' => $application->updatedAt()?->format('Y-m-d H:i:s'),
        ];

        return new ApplicationSummary(
            applicationData: $applicationData,
            validationResult: $validationResult,
            evaluatorData: $evaluatorData,
            status: $application->status()->value(),
        );
    }
}
