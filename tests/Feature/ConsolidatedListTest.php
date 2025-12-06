<?php

use App\Infrastructure\Persistence\Eloquent\Models\ApplicationModel;
use App\Infrastructure\Persistence\Eloquent\Models\EvaluatorModel;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->evaluator1 = EvaluatorModel::factory()->create([
        'name' => 'John Evaluator',
        'is_active' => true,
    ]);

    $this->evaluator2 = EvaluatorModel::factory()->create([
        'name' => 'Jane Evaluator',
        'is_active' => true,
    ]);

    ApplicationModel::factory()->count(3)->create([
        'evaluator_id' => $this->evaluator1->id,
        'years_of_experience' => 5,
    ]);

    ApplicationModel::factory()->count(2)->create([
        'evaluator_id' => $this->evaluator2->id,
        'years_of_experience' => 3,
    ]);

    ApplicationModel::factory()->count(2)->create([
        'evaluator_id' => null,
    ]);
});

it('returns consolidated list with pagination', function () {
    $response = $this->getJson('/api/v1/applications/consolidated?per_page=2');

    $response->assertSuccessful()
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'application_id',
                    'candidate_name',
                    'candidate_email',
                    'years_of_experience',
                    'evaluator_name',
                    'assigned_at',
                    'total_applications_for_evaluator',
                    'evaluator_candidates_emails',
                ],
            ],
            'meta' => [
                'total',
                'per_page',
                'current_page',
                'last_page',
            ],
        ]);

    expect($response->json('data'))->toHaveCount(2)
        ->and($response->json('meta.per_page'))->toBe(2)
        ->and($response->json('meta.total'))->toBe(5);
});

it('filters applications by evaluator', function () {
    $response = $this->getJson("/api/v1/applications/consolidated?filter_evaluator_id={$this->evaluator1->id}");

    $response->assertSuccessful();

    expect($response->json('meta.total'))->toBe(3);

    foreach ($response->json('data') as $item) {
        expect($item['evaluator_name'])->toBe('John Evaluator');
    }
});

it('filters applications by minimum experience', function () {
    $response = $this->getJson('/api/v1/applications/consolidated?filter_min_experience=4');

    $response->assertSuccessful();

    expect($response->json('meta.total'))->toBe(3);

    foreach ($response->json('data') as $item) {
        expect($item['years_of_experience'])->toBeGreaterThanOrEqual(4);
    }
});

it('filters applications by maximum experience', function () {
    $response = $this->getJson('/api/v1/applications/consolidated?filter_max_experience=4');

    $response->assertSuccessful();

    expect($response->json('meta.total'))->toBe(2);

    foreach ($response->json('data') as $item) {
        expect($item['years_of_experience'])->toBeLessThanOrEqual(4);
    }
});

it('orders applications by years of experience descending by default', function () {
    $response = $this->getJson('/api/v1/applications/consolidated');

    $response->assertSuccessful();

    $data = $response->json('data');
    $firstItem = $data[0];

    expect($firstItem['years_of_experience'])->toBe(5);
});

it('orders applications by years of experience ascending', function () {
    $response = $this->getJson('/api/v1/applications/consolidated?order_by=years_of_experience&order_direction=asc');

    $response->assertSuccessful();

    $data = $response->json('data');
    $firstItem = $data[0];

    expect($firstItem['years_of_experience'])->toBe(3);
});

it('orders applications by candidate name', function () {
    $response = $this->getJson('/api/v1/applications/consolidated?order_by=candidate_name&order_direction=asc');

    $response->assertSuccessful()
        ->assertJsonStructure(['data', 'meta']);
});

it('only returns applications with assigned evaluators', function () {
    $response = $this->getJson('/api/v1/applications/consolidated');

    $response->assertSuccessful();

    expect($response->json('meta.total'))->toBe(5);

    foreach ($response->json('data') as $item) {
        expect($item['evaluator_name'])->not->toBeNull();
    }
});

it('includes total applications count for each evaluator', function () {
    $response = $this->getJson('/api/v1/applications/consolidated');

    $response->assertSuccessful();

    $data = $response->json('data');

    foreach ($data as $item) {
        if ($item['evaluator_name'] === 'John Evaluator') {
            expect($item['total_applications_for_evaluator'])->toBe(3);
        } elseif ($item['evaluator_name'] === 'Jane Evaluator') {
            expect($item['total_applications_for_evaluator'])->toBe(2);
        }
    }
});

it('includes concatenated candidate emails for evaluator', function () {
    $response = $this->getJson("/api/v1/applications/consolidated?filter_evaluator_id={$this->evaluator1->id}");

    $response->assertSuccessful();

    $data = $response->json('data');
    $firstItem = $data[0];

    expect($firstItem['evaluator_candidates_emails'])
        ->toBeString()
        ->toContain('@');
});

it('validates invalid order_by field', function () {
    $response = $this->getJson('/api/v1/applications/consolidated?order_by=invalid_field');

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['order_by']);
});

it('validates invalid order_direction', function () {
    $response = $this->getJson('/api/v1/applications/consolidated?order_direction=invalid');

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['order_direction']);
});

it('validates invalid per_page value', function () {
    $response = $this->getJson('/api/v1/applications/consolidated?per_page=999');

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['per_page']);
});

it('combines multiple filters correctly', function () {
    $response = $this->getJson("/api/v1/applications/consolidated?filter_evaluator_id={$this->evaluator1->id}&filter_min_experience=4");

    $response->assertSuccessful();

    expect($response->json('meta.total'))->toBe(3);

    foreach ($response->json('data') as $item) {
        expect($item['evaluator_name'])->toBe('John Evaluator')
            ->and($item['years_of_experience'])->toBeGreaterThanOrEqual(4);
    }
});

it('uses cache for consecutive identical requests', function () {
    // First request - should hit the database
    $response1 = $this->getJson('/api/v1/applications/consolidated');
    $response1->assertSuccessful();

    // Create a new application - this should not appear in cached results
    ApplicationModel::factory()->create([
        'evaluator_id' => $this->evaluator1->id,
        'years_of_experience' => 10,
    ]);

    // Second request - should return cached data (won't include the new application)
    $response2 = $this->getJson('/api/v1/applications/consolidated');
    $response2->assertSuccessful();

    // Both responses should have the same total (5, not 6)
    expect($response1->json('meta.total'))->toBe(5)
        ->and($response2->json('meta.total'))->toBe(5);

    // Clear cache
    $this->artisan('cache:clear');

    // Third request - should hit database again and get fresh data
    $response3 = $this->getJson('/api/v1/applications/consolidated');
    $response3->assertSuccessful();

    expect($response3->json('meta.total'))->toBe(6);
});
