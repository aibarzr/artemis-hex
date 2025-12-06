<?php

use App\Domain\Application\Entities\Application;
use App\Domain\Application\ValueObjects\Email;
use App\Domain\Application\ValueObjects\YearsOfExperience;
use App\Domain\Validation\Rules\ValidEmailRule;

it('validates when email is valid', function () {
    $application = Application::create(
        candidateName: 'John Doe',
        candidateEmail: new Email('john@example.com'),
        position: 'Backend Developer',
        yearsOfExperience: new YearsOfExperience(5),
    );

    $rule = new ValidEmailRule;

    expect($rule->validate($application))->toBeTrue();
});

it('validates various valid email formats', function (string $email) {
    $application = Application::create(
        candidateName: 'John Doe',
        candidateEmail: new Email($email),
        position: 'Backend Developer',
        yearsOfExperience: new YearsOfExperience(5),
    );

    $rule = new ValidEmailRule;

    expect($rule->validate($application))->toBeTrue();
})->with([
    'simple' => 'user@example.com',
    'subdomain' => 'user@mail.example.com',
    'plus' => 'user+tag@example.com',
    'dots' => 'first.last@example.com',
    'numbers' => 'user123@example.com',
]);

it('returns correct error message', function () {
    $rule = new ValidEmailRule;

    expect($rule->getErrorMessage())->toBe('The email address is not valid.');
});

it('returns correct rule name', function () {
    $rule = new ValidEmailRule;

    expect($rule->getRuleName())->toBe('valid_email');
});
