<?php

namespace App\Infrastructure\Persistence\Eloquent\Mappers;

use App\Domain\Application\ValueObjects\Email;
use App\Domain\Evaluator\Entities\Evaluator;
use App\Domain\Evaluator\ValueObjects\Specialization;
use App\Infrastructure\Persistence\Eloquent\Models\EvaluatorModel;
use DateTimeImmutable;

final class EvaluatorMapper
{
    public static function toDomain(EvaluatorModel $model, ?int $applicationsCount = null): Evaluator
    {
        return new Evaluator(
            id: $model->id,
            name: $model->name,
            email: new Email($model->email),
            specialization: new Specialization($model->specialization),
            maxApplications: $model->max_applications,
            isActive: $model->is_active,
            currentApplicationsCount: $applicationsCount ?? $model->applications()->count(),
            createdAt: $model->created_at ? new DateTimeImmutable($model->created_at->toDateTimeString()) : null,
            updatedAt: $model->updated_at ? new DateTimeImmutable($model->updated_at->toDateTimeString()) : null,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public static function toDatabase(Evaluator $entity): array
    {
        return [
            'name' => $entity->name(),
            'email' => $entity->email()->value(),
            'specialization' => $entity->specialization()->value(),
            'max_applications' => $entity->maxApplications(),
            'is_active' => $entity->isActive(),
        ];
    }
}
