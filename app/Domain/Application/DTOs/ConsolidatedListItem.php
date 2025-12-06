<?php

namespace App\Domain\Application\DTOs;

final readonly class ConsolidatedListItem
{
    public function __construct(
        public int $applicationId,
        public string $candidateName,
        public string $candidateEmail,
        public int $yearsOfExperience,
        public ?string $evaluatorName,
        public ?string $assignedAt,
        public int $totalApplicationsForEvaluator,
        public string $evaluatorCandidatesEmails,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'application_id' => $this->applicationId,
            'candidate_name' => $this->candidateName,
            'candidate_email' => $this->candidateEmail,
            'years_of_experience' => $this->yearsOfExperience,
            'evaluator_name' => $this->evaluatorName,
            'assigned_at' => $this->assignedAt,
            'total_applications_for_evaluator' => $this->totalApplicationsForEvaluator,
            'evaluator_candidates_emails' => $this->evaluatorCandidatesEmails,
        ];
    }
}
