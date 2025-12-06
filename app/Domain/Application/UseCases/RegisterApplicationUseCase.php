<?php

namespace App\Domain\Application\UseCases;

use App\Domain\Application\Entities\Application;
use App\Domain\Application\Ports\ApplicationRepositoryInterface;
use App\Domain\Application\ValueObjects\Email;
use App\Domain\Application\ValueObjects\YearsOfExperience;

final readonly class RegisterApplicationUseCase
{
    public function __construct(
        private ApplicationRepositoryInterface $applicationRepository,
    ) {}

    public function execute(
        string $candidateName,
        string $candidateEmail,
        string $position,
        int $yearsOfExperience,
        ?string $cvPath = null,
        ?string $coverLetter = null,
    ): Application {
        $application = Application::create(
            candidateName: $candidateName,
            candidateEmail: new Email($candidateEmail),
            position: $position,
            yearsOfExperience: new YearsOfExperience($yearsOfExperience),
            cvPath: $cvPath,
            coverLetter: $coverLetter,
        );

        return $this->applicationRepository->save($application);
    }
}
