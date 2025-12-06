<?php

namespace Database\Factories;

use App\Infrastructure\Persistence\Eloquent\Models\EvaluatorModel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Infrastructure\Persistence\Eloquent\Models\EvaluatorModel>
 */
class EvaluatorFactory extends Factory
{
    protected $model = EvaluatorModel::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'specialization' => fake()->randomElement([
                'backend',
                'frontend',
                'fullstack',
                'devops',
                'mobile',
                'data',
            ]),
            'max_applications' => fake()->numberBetween(5, 20),
            'is_active' => true,
        ];
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    public function backend(): static
    {
        return $this->state(fn (array $attributes) => [
            'specialization' => 'backend',
        ]);
    }

    public function frontend(): static
    {
        return $this->state(fn (array $attributes) => [
            'specialization' => 'frontend',
        ]);
    }

    public function fullstack(): static
    {
        return $this->state(fn (array $attributes) => [
            'specialization' => 'fullstack',
        ]);
    }
}
