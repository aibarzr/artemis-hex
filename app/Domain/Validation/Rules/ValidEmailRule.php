<?php

namespace App\Domain\Validation\Rules;

use App\Domain\Application\Entities\Application;
use App\Domain\Validation\Ports\ValidationRuleInterface;

final class ValidEmailRule implements ValidationRuleInterface
{
    public function validate(Application $application): bool
    {
        return filter_var($application->candidateEmail()->value(), FILTER_VALIDATE_EMAIL) !== false;
    }

    public function getErrorMessage(): string
    {
        return 'The email address is not valid.';
    }

    public function getRuleName(): string
    {
        return 'valid_email';
    }
}
