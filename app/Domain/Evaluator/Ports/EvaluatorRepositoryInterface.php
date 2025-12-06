<?php

namespace App\Domain\Evaluator\Ports;

use App\Domain\Application\Entities\Application;
use App\Domain\Evaluator\Entities\Evaluator;

interface EvaluatorRepositoryInterface
{
    public function save(Evaluator $evaluator): Evaluator;

    public function findById(int $id): ?Evaluator;

    public function delete(int $id): bool;

    public function findByEmail(string $email): ?Evaluator;

    /**
     * @return array<int, Evaluator>
     */
    public function getAll(): array;

    public function assignToApplication(int $evaluatorId, int $applicationId): void;

    /**
     * @return array<int, Application>
     */
    public function getApplicationsByEvaluator(int $evaluatorId): array;
}
