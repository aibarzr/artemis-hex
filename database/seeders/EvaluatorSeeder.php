<?php

namespace Database\Seeders;

use App\Infrastructure\Persistence\Eloquent\Models\EvaluatorModel;
use Illuminate\Database\Seeder;

class EvaluatorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        EvaluatorModel::factory()->backend()->create([
            'name' => 'Juan Pérez',
            'email' => 'juan.perez@example.com',
            'max_applications' => 15,
        ]);

        EvaluatorModel::factory()->frontend()->create([
            'name' => 'María García',
            'email' => 'maria.garcia@example.com',
            'max_applications' => 12,
        ]);

        EvaluatorModel::factory()->fullstack()->create([
            'name' => 'Carlos Rodríguez',
            'email' => 'carlos.rodriguez@example.com',
            'max_applications' => 10,
        ]);

        EvaluatorModel::factory()->create([
            'name' => 'Ana Martínez',
            'email' => 'ana.martinez@example.com',
            'specialization' => 'devops',
            'max_applications' => 8,
        ]);

        EvaluatorModel::factory()->create([
            'name' => 'Luis Sánchez',
            'email' => 'luis.sanchez@example.com',
            'specialization' => 'mobile',
            'max_applications' => 10,
        ]);

        EvaluatorModel::factory()->inactive()->create([
            'name' => 'Pedro López',
            'email' => 'pedro.lopez@example.com',
            'specialization' => 'backend',
        ]);

        EvaluatorModel::factory()->count(5)->create();
    }
}
