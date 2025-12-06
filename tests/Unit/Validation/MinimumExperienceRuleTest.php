<?php

use App\Domain\Application\Entities\Application;
use App\Domain\Application\ValueObjects\Email;
use App\Domain\Application\ValueObjects\YearsOfExperience;
use App\Domain\Validation\Rules\MinimumExperienceRule;

it('validates when experience is exactly 2 years', function () {
    $application = Application::create(
        candidateName: 'John Doe',
        candidateEmail: new Email('john@example.com'),
        position: 'Backend Developer',
        yearsOfExperience: new YearsOfExperience(2),
    );

    $rule = new MinimumExperienceRule;

    expect($rule->validate($application))->toBeTrue();
});

it('validates when experience is more than 2 years', function () {
    $application = Application::create(
        candidateName: 'John Doe',
        candidateEmail: new Email('john@example.com'),
        position: 'Backend Developer',
        yearsOfExperience: new YearsOfExperience(5),
    );

    $rule = new MinimumExperienceRule;

    expect($rule->validate($application))->toBeTrue();
});

it('fails validation when experience is less than 2 years', function (int $years) {
    $application = Application::create(
        candidateName: 'John Doe',
        candidateEmail: new Email('john@example.com'),
        position: 'Backend Developer',
        yearsOfExperience: new YearsOfExperience($years),
    );

    $rule = new MinimumExperienceRule;

    expect($rule->validate($application))->toBeFalse();
})->with([0, 1]);

it('returns correct error message', function () {
    $rule = new MinimumExperienceRule;

    expect($rule->getErrorMessage())->toBe('Minimum 2 years of experience required.');
});

it('returns correct rule name', function () {
    $rule = new MinimumExperienceRule;

    expect($rule->getRuleName())->toBe('minimum_experience');
});
