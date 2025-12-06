<?php

namespace App\Infrastructure\Persistence\Eloquent\Repositories;

use App\Domain\Application\Entities\Application;
use App\Domain\Evaluator\Entities\Evaluator;
use App\Domain\Evaluator\Ports\EvaluatorRepositoryInterface;
use App\Infrastructure\Persistence\Eloquent\Mappers\ApplicationMapper;
use App\Infrastructure\Persistence\Eloquent\Mappers\EvaluatorMapper;
use App\Infrastructure\Persistence\Eloquent\Models\ApplicationModel;
use App\Infrastructure\Persistence\Eloquent\Models\EvaluatorModel;

final class EloquentEvaluatorRepository implements EvaluatorRepositoryInterface
{
    public function save(Evaluator $evaluator): Evaluator
    {
        $data = EvaluatorMapper::toDatabase($evaluator);

        if ($evaluator->id() !== null) {
            $model = EvaluatorModel::query()->findOrFail($evaluator->id());
            $model->update($data);
        } else {
            $model = EvaluatorModel::query()->create($data);
        }

        return EvaluatorMapper::toDomain($model);
    }

    public function findById(int $id): ?Evaluator
    {
        $model = EvaluatorModel::query()->find($id);

        if ($model === null) {
            return null;
        }

        return EvaluatorMapper::toDomain($model);
    }

    public function delete(int $id): bool
    {
        return EvaluatorModel::query()->where('id', $id)->delete() > 0;
    }

    public function findByEmail(string $email): ?Evaluator
    {
        $model = EvaluatorModel::query()->where('email', $email)->first();

        if ($model === null) {
            return null;
        }

        return EvaluatorMapper::toDomain($model);
    }

    /**
     * @return array<int, Evaluator>
     */
    public function getAll(): array
    {
        return EvaluatorModel::query()
            ->withCount('applications')
            ->get()
            ->map(fn (EvaluatorModel $model) => EvaluatorMapper::toDomain($model, $model->applications_count))
            ->all();
    }

    public function assignToApplication(int $evaluatorId, int $applicationId): void
    {
        ApplicationModel::query()
            ->where('id', $applicationId)
            ->update([
                'evaluator_id' => $evaluatorId,
                'status' => 'in_review',
            ]);
    }

    /**
     * @return array<int, Application>
     */
    public function getApplicationsByEvaluator(int $evaluatorId): array
    {
        return ApplicationModel::query()
            ->where('evaluator_id', $evaluatorId)
            ->get()
            ->map(fn (ApplicationModel $model) => ApplicationMapper::toDomain($model))
            ->all();
    }
}
