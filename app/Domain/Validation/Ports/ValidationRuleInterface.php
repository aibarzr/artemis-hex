<?php

namespace App\Domain\Validation\Ports;

use App\Domain\Application\Entities\Application;

interface ValidationRuleInterface
{
    public function validate(Application $application): bool;

    public function getErrorMessage(): string;

    public function getRuleName(): string;
}
