<?php

namespace App\Domain\Validation\Rules;

use App\Domain\Application\Entities\Application;
use App\Domain\Validation\Ports\ValidationRuleInterface;

final class MinimumExperienceRule implements ValidationRuleInterface
{
    private const MINIMUM_YEARS = 2;

    public function validate(Application $application): bool
    {
        return $application->yearsOfExperience()->value() >= self::MINIMUM_YEARS;
    }

    public function getErrorMessage(): string
    {
        return sprintf('Minimum %d years of experience required.', self::MINIMUM_YEARS);
    }

    public function getRuleName(): string
    {
        return 'minimum_experience';
    }
}
