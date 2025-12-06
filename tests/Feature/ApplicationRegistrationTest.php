<?php

use App\Infrastructure\Persistence\Eloquent\Models\ApplicationModel;
use App\Infrastructure\Persistence\Eloquent\Models\EvaluatorModel;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('registers a new application successfully', function () {
    $data = [
        'candidate_name' => 'John Doe',
        'candidate_email' => 'john.doe@example.com',
        'position' => 'Backend Developer',
        'years_of_experience' => 5,
        'cv_path' => '/path/to/cv.pdf',
        'cover_letter' => 'I am very interested in this position.',
    ];

    $response = $this->postJson('/api/v1/applications', $data);

    $response->assertCreated()
        ->assertJsonStructure([
            'message',
            'data' => [
                'id',
                'candidate_name',
                'candidate_email',
                'position',
                'years_of_experience',
                'status',
                'submitted_at',
            ],
        ])
        ->assertJson([
            'data' => [
                'candidate_name' => 'John Doe',
                'candidate_email' => 'john.doe@example.com',
                'position' => 'Backend Developer',
                'years_of_experience' => 5,
                'status' => 'pending',
            ],
        ]);

    $this->assertDatabaseHas('applications', [
        'candidate_name' => 'John Doe',
        'candidate_email' => 'john.doe@example.com',
        'position' => 'Backend Developer',
        'years_of_experience' => 5,
    ]);
});

it('validates required fields when registering application', function () {
    $response = $this->postJson('/api/v1/applications', []);

    $response->assertStatus(422)
        ->assertJsonValidationErrors([
            'candidate_name',
            'candidate_email',
            'position',
            'years_of_experience',
        ]);
});

it('validates email format when registering application', function () {
    $data = [
        'candidate_name' => 'John Doe',
        'candidate_email' => 'invalid-email',
        'position' => 'Backend Developer',
        'years_of_experience' => 5,
    ];

    $response = $this->postJson('/api/v1/applications', $data);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['candidate_email']);
});

it('validates years of experience range', function () {
    $data = [
        'candidate_name' => 'John Doe',
        'candidate_email' => 'john@example.com',
        'position' => 'Backend Developer',
        'years_of_experience' => -1,
    ];

    $response = $this->postJson('/api/v1/applications', $data);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['years_of_experience']);
});

it('validates application with domain validation rules', function () {
    $application = ApplicationModel::factory()->create([
        'candidate_email' => 'valid@example.com',
        'years_of_experience' => 5,
        'cv_path' => '/path/to/cv.pdf',
    ]);

    $response = $this->postJson("/api/v1/applications/{$application->id}/validate");

    $response->assertSuccessful()
        ->assertJson([
            'data' => [
                'is_valid' => true,
                'errors' => [],
                'errors_count' => 0,
            ],
        ]);
});

it('detects validation errors for missing CV', function () {
    $application = ApplicationModel::factory()->create([
        'candidate_email' => 'valid@example.com',
        'years_of_experience' => 5,
        'cv_path' => null,
    ]);

    $response = $this->postJson("/api/v1/applications/{$application->id}/validate");

    $response->assertSuccessful()
        ->assertJson([
            'data' => [
                'is_valid' => false,
                'errors_count' => 1,
            ],
        ]);

    expect($response->json('data.errors'))->toHaveKey('cv_not_empty');
});

it('detects validation errors for insufficient experience', function () {
    $application = ApplicationModel::factory()->create([
        'candidate_email' => 'valid@example.com',
        'years_of_experience' => 1,
        'cv_path' => '/path/to/cv.pdf',
    ]);

    $response = $this->postJson("/api/v1/applications/{$application->id}/validate");

    $response->assertSuccessful()
        ->assertJson([
            'data' => [
                'is_valid' => false,
                'errors_count' => 1,
            ],
        ]);

    expect($response->json('data.errors'))->toHaveKey('minimum_experience');
});

it('assigns evaluator to application successfully', function () {
    $application = ApplicationModel::factory()->create(['evaluator_id' => null]);
    $evaluator = EvaluatorModel::factory()->create();

    $response = $this->postJson("/api/v1/applications/{$application->id}/assign-evaluator", [
        'evaluator_id' => $evaluator->id,
    ]);

    $response->assertSuccessful()
        ->assertJson([
            'data' => [
                'id' => $application->id,
                'evaluator_id' => $evaluator->id,
            ],
        ]);

    $this->assertDatabaseHas('applications', [
        'id' => $application->id,
        'evaluator_id' => $evaluator->id,
    ]);
});

it('validates evaluator existence when assigning', function () {
    $application = ApplicationModel::factory()->create();

    $response = $this->postJson("/api/v1/applications/{$application->id}/assign-evaluator", [
        'evaluator_id' => 99999,
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['evaluator_id']);
});

it('returns application summary with all details', function () {
    $evaluator = EvaluatorModel::factory()->create(['name' => 'John Evaluator']);
    $application = ApplicationModel::factory()->create([
        'candidate_name' => 'Jane Doe',
        'evaluator_id' => $evaluator->id,
        'years_of_experience' => 5,
        'cv_path' => '/path/to/cv.pdf',
    ]);

    $response = $this->getJson("/api/v1/applications/{$application->id}/summary");

    $response->assertSuccessful()
        ->assertJsonStructure([
            'message',
            'data' => [
                'application' => [
                    'id',
                    'candidate_name',
                    'candidate_email',
                    'position',
                    'years_of_experience',
                    'cv_path',
                    'cover_letter',
                    'submitted_at',
                    'created_at',
                    'updated_at',
                ],
                'validation' => [
                    'is_valid',
                    'errors',
                    'errors_count',
                ],
                'evaluator' => [
                    'id',
                    'name',
                    'email',
                    'specialization',
                ],
                'status',
            ],
        ])
        ->assertJson([
            'data' => [
                'application' => [
                    'candidate_name' => 'Jane Doe',
                ],
                'evaluator' => [
                    'name' => 'John Evaluator',
                ],
            ],
        ]);
});

it('returns 404 when application not found for validation', function () {
    $response = $this->postJson('/api/v1/applications/99999/validate');

    $response->assertNotFound();
});

it('returns 404 when application not found for summary', function () {
    $response = $this->getJson('/api/v1/applications/99999/summary');

    $response->assertNotFound();
});

it('is idempotent when assigning same evaluator multiple times', function () {
    $application = ApplicationModel::factory()->create(['evaluator_id' => null]);
    $evaluator = EvaluatorModel::factory()->create();

    // First assignment
    $response1 = $this->postJson("/api/v1/applications/{$application->id}/assign-evaluator", [
        'evaluator_id' => $evaluator->id,
    ]);

    $response1->assertSuccessful();
    $firstUpdatedAt = $application->fresh()->updated_at;

    // Wait a moment to ensure timestamp would change if updated
    sleep(1);

    // Second assignment (should be idempotent - no changes)
    $response2 = $this->postJson("/api/v1/applications/{$application->id}/assign-evaluator", [
        'evaluator_id' => $evaluator->id,
    ]);

    $response2->assertSuccessful()
        ->assertJson([
            'data' => [
                'id' => $application->id,
                'evaluator_id' => $evaluator->id,
            ],
        ]);

    // Verify the application was not updated (idempotent behavior)
    $application->refresh();
    expect($application->evaluator_id)->toBe($evaluator->id)
        ->and($application->updated_at->timestamp)->toBe($firstUpdatedAt->timestamp);
});
