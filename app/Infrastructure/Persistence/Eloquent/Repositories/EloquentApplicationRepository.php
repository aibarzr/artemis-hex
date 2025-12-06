<?php

namespace App\Infrastructure\Persistence\Eloquent\Repositories;

use App\Domain\Application\DTOs\ConsolidatedListItem;
use App\Domain\Application\DTOs\ConsolidatedListResult;
use App\Domain\Application\Entities\Application;
use App\Domain\Application\Ports\ApplicationRepositoryInterface;
use App\Infrastructure\Persistence\Eloquent\Mappers\ApplicationMapper;
use App\Infrastructure\Persistence\Eloquent\Models\ApplicationModel;

final class EloquentApplicationRepository implements ApplicationRepositoryInterface
{
    public function save(Application $application): Application
    {
        $data = ApplicationMapper::toDatabase($application);

        if ($application->id() !== null) {
            $model = ApplicationModel::query()->findOrFail($application->id());
            $model->update($data);
        } else {
            $model = ApplicationModel::query()->create($data);
        }

        return ApplicationMapper::toDomain($model);
    }

    public function findById(int $id): ?Application
    {
        $model = ApplicationModel::query()->find($id);

        if ($model === null) {
            return null;
        }

        return ApplicationMapper::toDomain($model);
    }

    public function findByIdForUpdate(int $id): ?Application
    {
        $model = ApplicationModel::query()->lockForUpdate()->find($id);

        if ($model === null) {
            return null;
        }

        return ApplicationMapper::toDomain($model);
    }

    public function delete(int $id): bool
    {
        return ApplicationModel::query()->where('id', $id)->delete() > 0;
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    public function getConsolidatedList(array $filters = [], string $orderBy = 'years_of_experience', string $orderDirection = 'desc', int $perPage = 15, int $page = 1): ConsolidatedListResult
    {
        $query = ApplicationModel::query()
            ->whereNotNull('evaluator_id')
            ->with('evaluator');

        if (isset($filters['evaluator_id'])) {
            $query->where('evaluator_id', $filters['evaluator_id']);
        }

        if (isset($filters['min_experience'])) {
            $query->where('years_of_experience', '>=', $filters['min_experience']);
        }

        if (isset($filters['max_experience'])) {
            $query->where('years_of_experience', '<=', $filters['max_experience']);
        }

        $query->orderBy($orderBy, $orderDirection);

        $paginator = $query->paginate($perPage, ['*'], 'page', $page);

        // Get unique evaluator IDs from paginated results
        $evaluatorIds = $paginator->pluck('evaluator_id')->unique();

        // Eager load all applications for these evaluators in a single query
        // This prevents N+1 problem: instead of 1 query per application (N queries),
        // we make just 1 query to fetch all related data
        $evaluatorApplicationsMap = ApplicationModel::query()
            ->whereIn('evaluator_id', $evaluatorIds)
            ->get()
            ->groupBy('evaluator_id');

        $items = [];
        foreach ($paginator->items() as $model) {
            $evaluatorApplications = $evaluatorApplicationsMap->get($model->evaluator_id) ?? collect();

            $candidateEmails = $evaluatorApplications->pluck('candidate_email')->implode(', ');

            $items[] = new ConsolidatedListItem(
                applicationId: $model->id,
                candidateName: $model->candidate_name,
                candidateEmail: $model->candidate_email,
                yearsOfExperience: $model->years_of_experience,
                evaluatorName: $model->evaluator?->name,
                assignedAt: $model->updated_at?->format('Y-m-d H:i:s'),
                totalApplicationsForEvaluator: $evaluatorApplications->count(),
                evaluatorCandidatesEmails: $candidateEmails,
            );
        }

        return new ConsolidatedListResult(
            items: $items,
            total: $paginator->total(),
            perPage: $paginator->perPage(),
            currentPage: $paginator->currentPage(),
            lastPage: $paginator->lastPage(),
        );
    }

    public function findByEmail(string $email): ?Application
    {
        $model = ApplicationModel::query()->where('candidate_email', $email)->first();

        if ($model === null) {
            return null;
        }

        return ApplicationMapper::toDomain($model);
    }

    /**
     * @return array<int, Application>
     */
    public function getApplicationsWithEvaluator(): array
    {
        $models = ApplicationModel::query()
            ->whereNotNull('evaluator_id')
            ->with('evaluator')
            ->get();

        return $models->map(fn (ApplicationModel $model) => ApplicationMapper::toDomain($model))->all();
    }

    public function countByEvaluator(int $evaluatorId): int
    {
        return ApplicationModel::query()->where('evaluator_id', $evaluatorId)->count();
    }
}
