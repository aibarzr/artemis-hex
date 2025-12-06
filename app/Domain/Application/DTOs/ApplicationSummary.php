<?php

namespace App\Domain\Application\DTOs;

use App\Domain\Validation\ValueObjects\ValidationResult;

final readonly class ApplicationSummary
{
    /**
     * @param  array<string, mixed>  $applicationData
     */
    public function __construct(
        public array $applicationData,
        public ValidationResult $validationResult,
        public ?array $evaluatorData,
        public string $status,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'application' => $this->applicationData,
            'validation' => [
                'is_valid' => $this->validationResult->isValid(),
                'errors' => $this->validationResult->errors(),
                'errors_count' => $this->validationResult->errorsCount(),
            ],
            'evaluator' => $this->evaluatorData,
            'status' => $this->status,
        ];
    }
}
