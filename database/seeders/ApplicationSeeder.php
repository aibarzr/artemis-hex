<?php

namespace Database\Seeders;

use App\Infrastructure\Persistence\Eloquent\Models\ApplicationModel;
use App\Infrastructure\Persistence\Eloquent\Models\EvaluatorModel;
use Illuminate\Database\Seeder;

class ApplicationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $evaluators = EvaluatorModel::query()->where('is_active', true)->get();

        if ($evaluators->isEmpty()) {
            $this->command->warn('No active evaluators found. Run EvaluatorSeeder first.');

            return;
        }

        ApplicationModel::factory()->count(15)->pending()->create();

        ApplicationModel::factory()->count(20)->create()->each(function (ApplicationModel $application) use ($evaluators) {
            $evaluator = $evaluators->random();
            $application->evaluator_id = $evaluator->id;
            $application->status = 'in_review';
            $application->save();
        });

        ApplicationModel::factory()->count(10)->create()->each(function (ApplicationModel $application) use ($evaluators) {
            $evaluator = $evaluators->random();
            $application->evaluator_id = $evaluator->id;
            $application->status = 'approved';
            $application->save();
        });

        ApplicationModel::factory()->count(8)->create()->each(function (ApplicationModel $application) use ($evaluators) {
            $evaluator = $evaluators->random();
            $application->evaluator_id = $evaluator->id;
            $application->status = 'rejected';
            $application->save();
        });
    }
}
