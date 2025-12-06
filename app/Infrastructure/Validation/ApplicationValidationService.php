<?php

namespace App\Infrastructure\Validation;

use App\Domain\Application\Entities\Application;
use App\Domain\Validation\Ports\ValidationServiceInterface;
use App\Domain\Validation\Rules\CVNotEmptyRule;
use App\Domain\Validation\Rules\MinimumExperienceRule;
use App\Domain\Validation\Rules\ValidEmailRule;
use App\Domain\Validation\Services\ValidationService;
use App\Domain\Validation\ValueObjects\ValidationResult;

final class ApplicationValidationService implements ValidationServiceInterface
{
    private ValidationService $validationService;

    public function __construct()
    {
        $this->validationService = new ValidationService;
        $this->registerDefaultRules();
    }

    private function registerDefaultRules(): void
    {
        $this->validationService->addRule(new CVNotEmptyRule);
        $this->validationService->addRule(new ValidEmailRule);
        $this->validationService->addRule(new MinimumExperienceRule);
    }

    public function addRule(\App\Domain\Validation\Ports\ValidationRuleInterface $rule): void
    {
        $this->validationService->addRule($rule);
    }

    public function validate(Application $application): ValidationResult
    {
        return $this->validationService->validate($application);
    }
}
