<?php

use App\Domain\Application\Entities\Application;
use App\Domain\Application\ValueObjects\Email;
use App\Domain\Application\ValueObjects\YearsOfExperience;
use App\Domain\Validation\Rules\CVNotEmptyRule;
use App\Domain\Validation\Rules\MinimumExperienceRule;
use App\Domain\Validation\Rules\ValidEmailRule;
use App\Domain\Validation\Services\ValidationService;

it('validates successfully when all rules pass', function () {
    $application = Application::create(
        candidateName: 'John Doe',
        candidateEmail: new Email('john@example.com'),
        position: 'Backend Developer',
        yearsOfExperience: new YearsOfExperience(5),
        cvPath: '/path/to/cv.pdf',
    );

    $service = new ValidationService;
    $service->addRule(new CVNotEmptyRule);
    $service->addRule(new ValidEmailRule);
    $service->addRule(new MinimumExperienceRule);

    $result = $service->validate($application);

    expect($result->isValid())->toBeTrue()
        ->and($result->isFailed())->toBeFalse()
        ->and($result->errors())->toBeEmpty()
        ->and($result->errorsCount())->toBe(0);
});

it('fails validation when one rule fails', function () {
    $application = Application::create(
        candidateName: 'John Doe',
        candidateEmail: new Email('john@example.com'),
        position: 'Backend Developer',
        yearsOfExperience: new YearsOfExperience(5),
        cvPath: null, // Missing CV
    );

    $service = new ValidationService;
    $service->addRule(new CVNotEmptyRule);
    $service->addRule(new ValidEmailRule);
    $service->addRule(new MinimumExperienceRule);

    $result = $service->validate($application);

    expect($result->isValid())->toBeFalse()
        ->and($result->isFailed())->toBeTrue()
        ->and($result->errorsCount())->toBe(1)
        ->and($result->hasError('cv_not_empty'))->toBeTrue()
        ->and($result->getError('cv_not_empty'))->toBe('The CV is required and cannot be empty.');
});

it('collects multiple validation errors', function () {
    $application = Application::create(
        candidateName: 'John Doe',
        candidateEmail: new Email('john@example.com'),
        position: 'Backend Developer',
        yearsOfExperience: new YearsOfExperience(1), // Less than 2 years
        cvPath: null, // Missing CV
    );

    $service = new ValidationService;
    $service->addRule(new CVNotEmptyRule);
    $service->addRule(new ValidEmailRule);
    $service->addRule(new MinimumExperienceRule);

    $result = $service->validate($application);

    expect($result->isValid())->toBeFalse()
        ->and($result->errorsCount())->toBe(2)
        ->and($result->hasError('cv_not_empty'))->toBeTrue()
        ->and($result->hasError('minimum_experience'))->toBeTrue()
        ->and($result->hasError('valid_email'))->toBeFalse();
});

it('executes rules in the order they were added (Chain of Responsibility)', function () {
    $application = Application::create(
        candidateName: 'John Doe',
        candidateEmail: new Email('john@example.com'),
        position: 'Backend Developer',
        yearsOfExperience: new YearsOfExperience(0),
        cvPath: '',
    );

    $service = new ValidationService;
    $service->addRule(new CVNotEmptyRule);
    $service->addRule(new MinimumExperienceRule);
    $service->addRule(new ValidEmailRule);

    $result = $service->validate($application);

    $errors = $result->errors();
    $errorKeys = array_keys($errors);

    expect($errorKeys[0])->toBe('cv_not_empty')
        ->and($errorKeys[1])->toBe('minimum_experience');
});

it('allows adding rules dynamically', function () {
    $application = Application::create(
        candidateName: 'John Doe',
        candidateEmail: new Email('john@example.com'),
        position: 'Backend Developer',
        yearsOfExperience: new YearsOfExperience(5),
        cvPath: '/path/to/cv.pdf',
    );

    $service = new ValidationService;
    $service->addRule(new CVNotEmptyRule);

    $result1 = $service->validate($application);
    expect($result1->isValid())->toBeTrue();

    $service->addRule(new ValidEmailRule);
    $service->addRule(new MinimumExperienceRule);

    $result2 = $service->validate($application);
    expect($result2->isValid())->toBeTrue();
});

it('works with empty rule set', function () {
    $application = Application::create(
        candidateName: 'John Doe',
        candidateEmail: new Email('john@example.com'),
        position: 'Backend Developer',
        yearsOfExperience: new YearsOfExperience(1),
    );

    $service = new ValidationService;

    $result = $service->validate($application);

    expect($result->isValid())->toBeTrue()
        ->and($result->errors())->toBeEmpty();
});
