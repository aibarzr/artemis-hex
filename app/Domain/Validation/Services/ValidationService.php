<?php

namespace App\Domain\Validation\Services;

use App\Domain\Application\Entities\Application;
use App\Domain\Validation\Ports\ValidationRuleInterface;
use App\Domain\Validation\Ports\ValidationServiceInterface;
use App\Domain\Validation\ValueObjects\ValidationResult;

final class ValidationService implements ValidationServiceInterface
{
    /**
     * @var array<ValidationRuleInterface>
     */
    private array $rules = [];

    public function addRule(ValidationRuleInterface $rule): void
    {
        $this->rules[] = $rule;
    }

    public function validate(Application $application): ValidationResult
    {
        $errors = [];

        foreach ($this->rules as $rule) {
            if (! $rule->validate($application)) {
                $errors[$rule->getRuleName()] = $rule->getErrorMessage();
            }
        }

        if (empty($errors)) {
            return ValidationResult::success();
        }

        return ValidationResult::failure($errors);
    }
}
