<?php

namespace App\Infrastructure\Persistence\Eloquent\Models;

use Database\Factories\ApplicationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property string $candidate_name
 * @property string $candidate_email
 * @property string $position
 * @property int $years_of_experience
 * @property string $status
 * @property string|null $cv_path
 * @property string|null $cover_letter
 * @property int|null $evaluator_id
 * @property \Illuminate\Support\Carbon $submitted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class ApplicationModel extends Model
{
    /** @use HasFactory<ApplicationFactory> */
    use HasFactory;

    protected $table = 'applications';

    protected $fillable = [
        'candidate_name',
        'candidate_email',
        'position',
        'years_of_experience',
        'status',
        'cv_path',
        'cover_letter',
        'evaluator_id',
        'submitted_at',
    ];

    protected function casts(): array
    {
        return [
            'submitted_at' => 'datetime',
            'years_of_experience' => 'integer',
        ];
    }

    protected static function newFactory(): ApplicationFactory
    {
        return ApplicationFactory::new();
    }

    /**
     * @return BelongsTo<EvaluatorModel, ApplicationModel>
     */
    public function evaluator(): BelongsTo
    {
        return $this->belongsTo(EvaluatorModel::class, 'evaluator_id');
    }
}
