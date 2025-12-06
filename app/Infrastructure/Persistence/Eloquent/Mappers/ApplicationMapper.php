<?php

namespace App\Infrastructure\Persistence\Eloquent\Mappers;

use App\Domain\Application\Entities\Application;
use App\Domain\Application\ValueObjects\ApplicationStatus;
use App\Domain\Application\ValueObjects\Email;
use App\Domain\Application\ValueObjects\YearsOfExperience;
use App\Infrastructure\Persistence\Eloquent\Models\ApplicationModel;
use DateTimeImmutable;

final class ApplicationMapper
{
    public static function toDomain(ApplicationModel $model): Application
    {
        return new Application(
            id: $model->id,
            candidateName: $model->candidate_name,
            candidateEmail: new Email($model->candidate_email),
            position: $model->position,
            yearsOfExperience: new YearsOfExperience($model->years_of_experience),
            status: new ApplicationStatus($model->status),
            cvPath: $model->cv_path,
            coverLetter: $model->cover_letter,
            evaluatorId: $model->evaluator_id,
            submittedAt: new DateTimeImmutable($model->submitted_at->toDateTimeString()),
            createdAt: $model->created_at ? new DateTimeImmutable($model->created_at->toDateTimeString()) : null,
            updatedAt: $model->updated_at ? new DateTimeImmutable($model->updated_at->toDateTimeString()) : null,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public static function toDatabase(Application $entity): array
    {
        return [
            'candidate_name' => $entity->candidateName(),
            'candidate_email' => $entity->candidateEmail()->value(),
            'position' => $entity->position(),
            'years_of_experience' => $entity->yearsOfExperience()->value(),
            'status' => $entity->status()->value(),
            'cv_path' => $entity->cvPath(),
            'cover_letter' => $entity->coverLetter(),
            'evaluator_id' => $entity->evaluatorId(),
            'submitted_at' => $entity->submittedAt()->format('Y-m-d H:i:s'),
        ];
    }
}
