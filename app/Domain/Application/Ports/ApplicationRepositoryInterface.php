<?php

namespace App\Domain\Application\Ports;

use App\Domain\Application\DTOs\ConsolidatedListResult;
use App\Domain\Application\Entities\Application;

interface ApplicationRepositoryInterface
{
    public function save(Application $application): Application;

    public function findById(int $id): ?Application;

    public function findByIdForUpdate(int $id): ?Application;

    public function delete(int $id): bool;

    /**
     * @param  array<string, mixed>  $filters
     */
    public function getConsolidatedList(array $filters = [], string $orderBy = 'years_of_experience', string $orderDirection = 'desc', int $perPage = 15, int $page = 1): ConsolidatedListResult;

    public function findByEmail(string $email): ?Application;

    /**
     * @return array<int, Application>
     */
    public function getApplicationsWithEvaluator(): array;

    public function countByEvaluator(int $evaluatorId): int;
}
