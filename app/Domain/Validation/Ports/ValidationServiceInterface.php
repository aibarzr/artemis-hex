<?php

namespace App\Domain\Validation\Ports;

use App\Domain\Application\Entities\Application;
use App\Domain\Validation\ValueObjects\ValidationResult;

interface ValidationServiceInterface
{
    public function addRule(ValidationRuleInterface $rule): void;

    public function validate(Application $application): ValidationResult;
}
