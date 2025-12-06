<?php

namespace App\Domain\Validation\Rules;

use App\Domain\Application\Entities\Application;
use App\Domain\Validation\Ports\ValidationRuleInterface;

final class CVNotEmptyRule implements ValidationRuleInterface
{
    public function validate(Application $application): bool
    {
        return $application->hasCv();
    }

    public function getErrorMessage(): string
    {
        return 'The CV is required and cannot be empty.';
    }

    public function getRuleName(): string
    {
        return 'cv_not_empty';
    }
}
