<?php

use App\Domain\Application\Entities\Application;
use App\Domain\Application\ValueObjects\Email;
use App\Domain\Application\ValueObjects\YearsOfExperience;
use App\Infrastructure\Persistence\Eloquent\Models\ApplicationModel;
use App\Infrastructure\Persistence\Eloquent\Models\EvaluatorModel;
use App\Infrastructure\Persistence\Eloquent\Repositories\EloquentApplicationRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->repository = new EloquentApplicationRepository;
});

it('saves a new application to the database', function () {
    $application = Application::create(
        candidateName: 'John Doe',
        candidateEmail: new Email('john.doe@example.com'),
        position: 'Backend Developer',
        yearsOfExperience: new YearsOfExperience(5),
        cvPath: '/path/to/cv.pdf',
        coverLetter: 'I am interested in this position.',
    );

    $savedApplication = $this->repository->save($application);

    expect($savedApplication->id())->not->toBeNull()
        ->and($savedApplication->candidateName())->toBe('John Doe')
        ->and($savedApplication->candidateEmail()->value())->toBe('john.doe@example.com')
        ->and($savedApplication->position())->toBe('Backend Developer')
        ->and($savedApplication->yearsOfExperience()->value())->toBe(5);

    $this->assertDatabaseHas('applications', [
        'candidate_name' => 'John Doe',
        'candidate_email' => 'john.doe@example.com',
        'position' => 'Backend Developer',
        'years_of_experience' => 5,
    ]);
});

it('updates an existing application in the database', function () {
    $model = ApplicationModel::factory()->create([
        'candidate_name' => 'Original Name',
        'years_of_experience' => 3,
    ]);

    $application = $this->repository->findById($model->id);
    $updatedApplication = $application->assignEvaluator(1);

    $savedApplication = $this->repository->save($updatedApplication);

    expect($savedApplication->id())->toBe($model->id)
        ->and($savedApplication->evaluatorId())->toBe(1);

    $this->assertDatabaseHas('applications', [
        'id' => $model->id,
        'evaluator_id' => 1,
    ]);
});

it('finds an application by id from the database', function () {
    $model = ApplicationModel::factory()->create([
        'candidate_name' => 'Jane Doe',
        'candidate_email' => 'jane@example.com',
        'years_of_experience' => 7,
    ]);

    $application = $this->repository->findById($model->id);

    expect($application)->not->toBeNull()
        ->and($application->id())->toBe($model->id)
        ->and($application->candidateName())->toBe('Jane Doe')
        ->and($application->candidateEmail()->value())->toBe('jane@example.com')
        ->and($application->yearsOfExperience()->value())->toBe(7);
});

it('returns null when application not found by id', function () {
    $application = $this->repository->findById(99999);

    expect($application)->toBeNull();
});

it('finds an application by email from the database', function () {
    $model = ApplicationModel::factory()->create([
        'candidate_email' => 'unique@example.com',
    ]);

    $application = $this->repository->findByEmail('unique@example.com');

    expect($application)->not->toBeNull()
        ->and($application->candidateEmail()->value())->toBe('unique@example.com');
});

it('deletes an application from the database', function () {
    $model = ApplicationModel::factory()->create();

    $result = $this->repository->delete($model->id);

    expect($result)->toBeTrue();

    $this->assertDatabaseMissing('applications', [
        'id' => $model->id,
    ]);
});

it('returns false when trying to delete non-existent application', function () {
    $result = $this->repository->delete(99999);

    expect($result)->toBeFalse();
});

it('retrieves consolidated list with evaluator data from database', function () {
    $evaluator1 = EvaluatorModel::factory()->create(['name' => 'Evaluator One']);
    $evaluator2 = EvaluatorModel::factory()->create(['name' => 'Evaluator Two']);

    ApplicationModel::factory()->count(3)->create([
        'evaluator_id' => $evaluator1->id,
        'years_of_experience' => 5,
    ]);

    ApplicationModel::factory()->count(2)->create([
        'evaluator_id' => $evaluator2->id,
        'years_of_experience' => 3,
    ]);

    ApplicationModel::factory()->count(2)->create([
        'evaluator_id' => null,
    ]);

    $result = $this->repository->getConsolidatedList(
        filters: [],
        orderBy: 'years_of_experience',
        orderDirection: 'desc',
        perPage: 10,
        page: 1,
    );

    expect($result->total)->toBe(5)
        ->and($result->items)->toHaveCount(5)
        ->and($result->perPage)->toBe(10)
        ->and($result->currentPage)->toBe(1);

    $firstItem = $result->items[0];
    expect($firstItem->yearsOfExperience)->toBe(5)
        ->and($firstItem->evaluatorName)->toBe('Evaluator One')
        ->and($firstItem->totalApplicationsForEvaluator)->toBe(3);
});

it('filters consolidated list by evaluator from database', function () {
    $evaluator1 = EvaluatorModel::factory()->create();
    $evaluator2 = EvaluatorModel::factory()->create();

    ApplicationModel::factory()->count(3)->create(['evaluator_id' => $evaluator1->id]);
    ApplicationModel::factory()->count(2)->create(['evaluator_id' => $evaluator2->id]);

    $result = $this->repository->getConsolidatedList(
        filters: ['evaluator_id' => $evaluator1->id],
        orderBy: 'years_of_experience',
        orderDirection: 'desc',
        perPage: 10,
        page: 1,
    );

    expect($result->total)->toBe(3);
});

it('filters consolidated list by experience range from database', function () {
    $evaluator = EvaluatorModel::factory()->create();

    ApplicationModel::factory()->create(['evaluator_id' => $evaluator->id, 'years_of_experience' => 2]);
    ApplicationModel::factory()->create(['evaluator_id' => $evaluator->id, 'years_of_experience' => 5]);
    ApplicationModel::factory()->create(['evaluator_id' => $evaluator->id, 'years_of_experience' => 8]);

    $result = $this->repository->getConsolidatedList(
        filters: ['min_experience' => 4, 'max_experience' => 9],
        orderBy: 'years_of_experience',
        orderDirection: 'desc',
        perPage: 10,
        page: 1,
    );

    expect($result->total)->toBe(2);

    foreach ($result->items as $item) {
        expect($item->yearsOfExperience)->toBeGreaterThanOrEqual(4)
            ->and($item->yearsOfExperience)->toBeLessThanOrEqual(9);
    }
});

it('paginates consolidated list correctly from database', function () {
    $evaluator = EvaluatorModel::factory()->create();
    ApplicationModel::factory()->count(10)->create(['evaluator_id' => $evaluator->id]);

    $page1 = $this->repository->getConsolidatedList(
        filters: [],
        orderBy: 'years_of_experience',
        orderDirection: 'desc',
        perPage: 3,
        page: 1,
    );

    $page2 = $this->repository->getConsolidatedList(
        filters: [],
        orderBy: 'years_of_experience',
        orderDirection: 'desc',
        perPage: 3,
        page: 2,
    );

    expect($page1->total)->toBe(10)
        ->and($page1->items)->toHaveCount(3)
        ->and($page1->currentPage)->toBe(1)
        ->and($page1->lastPage)->toBe(4)
        ->and($page2->items)->toHaveCount(3)
        ->and($page2->currentPage)->toBe(2);
});

it('counts applications by evaluator from database', function () {
    $evaluator = EvaluatorModel::factory()->create();

    ApplicationModel::factory()->count(5)->create(['evaluator_id' => $evaluator->id]);
    ApplicationModel::factory()->count(3)->create(['evaluator_id' => null]);

    $count = $this->repository->countByEvaluator($evaluator->id);

    expect($count)->toBe(5);
});

it('retrieves applications with evaluator relationship from database', function () {
    $evaluator = EvaluatorModel::factory()->create(['name' => 'Test Evaluator']);

    ApplicationModel::factory()->count(3)->create(['evaluator_id' => $evaluator->id]);
    ApplicationModel::factory()->count(2)->create(['evaluator_id' => null]);

    $applications = $this->repository->getApplicationsWithEvaluator();

    expect($applications)->toHaveCount(3);

    foreach ($applications as $application) {
        expect($application->evaluatorId())->not->toBeNull();
    }
});

it('maintains data integrity across multiple operations', function () {
    $application = Application::create(
        candidateName: 'Integration Test',
        candidateEmail: new Email('integration@example.com'),
        position: 'Developer',
        yearsOfExperience: new YearsOfExperience(4),
        cvPath: '/cv.pdf',
    );

    $saved = $this->repository->save($application);
    $applicationId = $saved->id();

    $found = $this->repository->findById($applicationId);
    expect($found)->not->toBeNull();

    $updated = $found->assignEvaluator(1);
    $this->repository->save($updated);

    $refetched = $this->repository->findById($applicationId);
    expect($refetched->evaluatorId())->toBe(1);

    $deleted = $this->repository->delete($applicationId);
    expect($deleted)->toBeTrue();

    $notFound = $this->repository->findById($applicationId);
    expect($notFound)->toBeNull();
});
