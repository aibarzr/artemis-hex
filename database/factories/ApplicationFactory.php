<?php

namespace Database\Factories;

use App\Infrastructure\Persistence\Eloquent\Models\ApplicationModel;
use App\Infrastructure\Persistence\Eloquent\Models\EvaluatorModel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Infrastructure\Persistence\Eloquent\Models\ApplicationModel>
 */
class ApplicationFactory extends Factory
{
    protected $model = ApplicationModel::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'candidate_name' => fake()->name(),
            'candidate_email' => fake()->unique()->safeEmail(),
            'position' => fake()->randomElement([
                'Backend Developer',
                'Frontend Developer',
                'Full Stack Developer',
                'DevOps Engineer',
                'Mobile Developer',
                'Data Engineer',
            ]),
            'years_of_experience' => fake()->numberBetween(0, 15),
            'status' => 'pending',
            'cv_path' => fake()->optional()->filePath(),
            'cover_letter' => fake()->optional()->paragraph(),
            'evaluator_id' => null,
            'submitted_at' => fake()->dateTimeBetween('-30 days', 'now'),
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
            'evaluator_id' => null,
        ]);
    }

    public function inReview(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'in_review',
            'evaluator_id' => EvaluatorModel::factory(),
        ]);
    }

    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'approved',
            'evaluator_id' => EvaluatorModel::factory(),
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'rejected',
            'evaluator_id' => EvaluatorModel::factory(),
        ]);
    }

    public function withEvaluator(): static
    {
        return $this->state(fn (array $attributes) => [
            'evaluator_id' => EvaluatorModel::factory(),
        ]);
    }
}
