<?php

use App\Domain\Application\Entities\Application;
use App\Domain\Application\ValueObjects\Email;
use App\Domain\Application\ValueObjects\YearsOfExperience;
use App\Domain\Validation\Rules\CVNotEmptyRule;

it('validates when CV is present', function () {
    $application = Application::create(
        candidateName: 'John Doe',
        candidateEmail: new Email('john@example.com'),
        position: 'Backend Developer',
        yearsOfExperience: new YearsOfExperience(5),
        cvPath: '/path/to/cv.pdf',
    );

    $rule = new CVNotEmptyRule;

    expect($rule->validate($application))->toBeTrue();
});

it('fails validation when CV is null', function () {
    $application = Application::create(
        candidateName: 'John Doe',
        candidateEmail: new Email('john@example.com'),
        position: 'Backend Developer',
        yearsOfExperience: new YearsOfExperience(5),
        cvPath: null,
    );

    $rule = new CVNotEmptyRule;

    expect($rule->validate($application))->toBeFalse();
});

it('fails validation when CV is empty string', function () {
    $application = Application::create(
        candidateName: 'John Doe',
        candidateEmail: new Email('john@example.com'),
        position: 'Backend Developer',
        yearsOfExperience: new YearsOfExperience(5),
        cvPath: '',
    );

    $rule = new CVNotEmptyRule;

    expect($rule->validate($application))->toBeFalse();
});

it('returns correct error message', function () {
    $rule = new CVNotEmptyRule;

    expect($rule->getErrorMessage())->toBe('The CV is required and cannot be empty.');
});

it('returns correct rule name', function () {
    $rule = new CVNotEmptyRule;

    expect($rule->getRuleName())->toBe('cv_not_empty');
});
